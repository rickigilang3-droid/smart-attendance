<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\GateAttendance;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class GateAttendanceController extends Controller
{
    public function index()
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();

        // ----------------------------------------------------
        // 1. LOG ABSENSI SISWA HARI INI
        // ----------------------------------------------------
        $todayLogs = collect();
        if (class_exists(GateAttendance::class)) {
            $todayLogs = GateAttendance::with(['student.schoolClass'])
                ->whereNotNull('student_id')
                ->where(function($q) use ($today) {
                    $q->where('date', $today)->orWhereDate('created_at', $today);
                })
                ->latest('updated_at')
                ->get();
        }

        if ($todayLogs->isEmpty()) {
            $todayLogs = Attendance::with(['student.schoolClass'])
                ->whereNotNull('student_id')
                ->where(function($q) use ($today) {
                    $q->where('date', $today)->orWhereDate('created_at', $today);
                })
                ->latest('updated_at')
                ->get();
        }

        // ----------------------------------------------------
        // 2. LOG ABSENSI GURU HARI INI
        // ----------------------------------------------------
        $teacherLogs = collect();
        if (class_exists(GateAttendance::class)) {
            $teacherLogs = GateAttendance::with('teacher')
                ->whereNotNull('teacher_id')
                ->where(function($q) use ($today) {
                    $q->where('date', $today)->orWhereDate('created_at', $today);
                })
                ->latest('updated_at')
                ->get();
        }

        // Fallback ke tabel attendances jika di gate_attendances belum ada
        if ($teacherLogs->isEmpty()) {
            $attTeacherQuery = Attendance::where(function($q) use ($today) {
                $q->where('date', $today)->orWhereDate('created_at', $today);
            });

            $hasTeacherIdInAtt = Schema::hasColumn('attendances', 'teacher_id');
            $hasUserIdInAtt    = Schema::hasColumn('attendances', 'user_id');

            if ($hasTeacherIdInAtt) {
                $teacherLogs = $attTeacherQuery->whereNotNull('teacher_id')
                    ->with('teacher')
                    ->latest('updated_at')
                    ->get();
            } elseif ($hasUserIdInAtt) {
                $teacherLogs = $attTeacherQuery->whereNotNull('user_id')
                    ->whereHas('user', function($q) {
                        $q->where('role', 'teacher')->orWhereHas('teacher');
                    })
                    ->with(['user.teacher', 'user'])
                    ->latest('updated_at')
                    ->get();
            }
        }

        return view('admin.attendances.gate_simulator', compact('todayLogs', 'teacherLogs'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'role'       => 'nullable|string|in:student,teacher',
        ]);

        $identifier = trim($request->identifier);
        $roleInput  = $request->role;
        $now        = Carbon::now('Asia/Jakarta');
        $today      = $now->toDateString();

        // ====================================================
        // FOKUS KHUSUS: SCAN GURU
        // ====================================================
        if ($roleInput === 'teacher') {
            $teacherQuery = Teacher::where('nip', $identifier);
            if (Schema::hasColumn('teachers', 'code')) {
                $teacherQuery->orWhere('code', $identifier);
            }
            if (Schema::hasColumn('teachers', 'nik')) {
                $teacherQuery->orWhere('nik', $identifier);
            }
            if (Schema::hasColumn('teachers', 'nuptk')) {
                $teacherQuery->orWhere('nuptk', $identifier);
            }
            $teacherQuery->orWhereHas('user', function ($q) use ($identifier) {
                $q->where('email', $identifier)->orWhere('name', $identifier);
            });

            $teacher = $teacherQuery->first();

            if (!$teacher) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Guru dengan NIP/Identifier '{$identifier}' tidak ditemukan!"
                ], 404);
            }

            // Batas Jam Masuk Guru: 07:00:00 WIB
            $jamBatasGuru = Carbon::createFromTimeString('07:00:00', 'Asia/Jakarta');
            $isLate       = $now->greaterThan($jamBatasGuru);
            $labelStatus  = $isLate ? 'TERLAMBAT' : 'TEPAT WAKTU';
            $statusValue  = $isLate ? 'late' : 'present';

            // Cari log absensi guru hari ini di gate_attendances
            $gateLog = GateAttendance::where('teacher_id', $teacher->id)
                ->where(function ($q) use ($today) {
                    $q->where('date', $today)->orWhereDate('created_at', $today);
                })->first();

            if (!$gateLog) {
                // SCAN MASUK GURU (Disimpan ke gate_attendances)
                GateAttendance::create([
                    'teacher_id' => $teacher->id,
                    'student_id' => null,
                    'date'       => $today,
                    'scan_in'    => $now->format('H:i:s'),
                    'status'     => $statusValue,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Sinkronkan ke tabel rekap Attendances jika ada
                if (class_exists(Attendance::class) && Schema::hasColumn('attendances', 'teacher_id')) {
                    Attendance::updateOrCreate(
                        ['teacher_id' => $teacher->id, 'date' => $today],
                        ['status' => $statusValue, 'updated_at' => $now]
                    );
                }

                return response()->json([
                    'status'  => 'success',
                    'role'    => 'teacher',
                    'is_late' => $isLate,
                    'message' => "SCAN MASUK GURU BERHASIL ({$labelStatus}): {$teacher->name} ({$now->format('H:i:s')} WIB)"
                ]);
            } else {
                // SCAN PULANG GURU
                $gateLog->scan_out = $now->format('H:i:s');
                $gateLog->touch();

                return response()->json([
                    'status'  => 'success',
                    'role'    => 'teacher',
                    'is_late' => false,
                    'message' => "SCAN PULANG GURU BERHASIL: {$teacher->name} ({$now->format('H:i:s')} WIB)"
                ]);
            }
        }

        // ====================================================
        // BAGIAN SISWA & INTEGRASI NOTIFIKASI WHATSAPP (SAFE-MODE)
        // ====================================================
        
        // Mengambil data siswa tanpa memanggil relasi 'parent' secara paksa di awal
        $student = Student::where('nisn', $identifier)
            ->orWhere('nis', $identifier)
            ->first();

        if (!$student) {
            return response()->json([
                'status'  => 'error',
                'message' => "Data Siswa dengan NISN/NIS '{$identifier}' tidak ditemukan!"
            ], 404);
        }

        $gateLog = null;
        if (class_exists(GateAttendance::class)) {
            $gateLog = GateAttendance::where('student_id', $student->id)
                ->where(function($q) use ($today) {
                    $q->where('date', $today)->orWhereDate('created_at', $today);
                })->first();
        }

        $attendance = Attendance::where('student_id', $student->id)
            ->where(function($q) use ($today) {
                $q->where('date', $today)->orWhereDate('created_at', $today);
            })->first();

        // PREPARASI PENCARIAN NOMOR HP ORANG TUA SECARA AMAN (TANPA MENCETUSKAN CRASH)
        $parentPhone = null;

        if (method_exists($student, 'parent') && $student->parent) {
            $parentPhone = $student->parent->phone 
                ?? $student->parent->whatsapp 
                ?? $student->parent->no_hp 
                ?? null;
        } elseif (method_exists($student, 'guardian') && $student->guardian) {
            $parentPhone = $student->guardian->phone 
                ?? $student->guardian->whatsapp 
                ?? $student->guardian->no_hp 
                ?? null;
        }

        // Fallback jika nomor HP tersimpan di atribut/kolom milik Student langsung
        if (!$parentPhone) {
            $parentPhone = $student->parent_phone 
                ?? $student->phone 
                ?? $student->no_hp 
                ?? null;
        }

        $formattedDate = $now->translatedFormat('l, d F Y');
        $formattedTime = $now->format('H:i');

        if (!$gateLog && !$attendance) {
            // ----------------------------------------------------
            // SCAN MASUK SISWA
            // ----------------------------------------------------
            $jamBatasSiswa = Carbon::createFromTimeString('07:15:00', 'Asia/Jakarta');
            $isLateSiswa   = $now->greaterThan($jamBatasSiswa);
            $statusSiswa   = $isLateSiswa ? 'late' : 'present';

            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'date'       => $today,
                ],
                [
                    'status'     => $statusSiswa,
                    'updated_at' => $now,
                ]
            );

            if (class_exists(GateAttendance::class)) {
                GateAttendance::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'date'       => $today,
                    ],
                    [
                        'scan_in'    => $now->format('H:i:s'),
                        'status'     => $statusSiswa,
                        'updated_at' => $now,
                    ]
                );
            }

            $labelStatus = $isLateSiswa ? 'TERLAMBAT' : 'TEPAT WAKTU';

            // PROSES KIRIM WHATSAPP MASUK
            if ($parentPhone) {
                $statusText = $isLateSiswa ? '⚠️ TERLAMBAT' : '✅ TEPAT WAKTU';
                $waMessage  = "🔔 *[PORTAL SEKOLAH PINTAR SMA XYZ JAKARTA]*\n\n";
                $waMessage .= "Assalamu'alaikum Wr. Wb. / Selamat Pagi,\n";
                $waMessage .= "Yth. Bapak/Ibu Wali Murid dari *{$student->name}*,\n\n";
                $waMessage .= "Diberitahukan bahwa putra/putri Anda telah berhasil melakukan *Tap Masuk* di gerbang sekolah.\n\n";
                $waMessage .= "📅 *Hari/Tanggal:* {$formattedDate}\n";
                $waMessage .= "⏰ *Waktu Tap:* {$formattedTime} WIB\n";
                $waMessage .= "📌 *Status Kehadiran:* {$statusText}\n\n";
                $waMessage .= "Terima kasih.\n";
                $waMessage .= "_SMA XYZ JAKARTA - Smart School System_";

                WhatsAppService::sendMessage($parentPhone, $waMessage);
            }

            return response()->json([
                'status'  => 'success',
                'role'    => 'student',
                'is_late' => $isLateSiswa,
                'message' => "SCAN MASUK SISWA BERHASIL ({$labelStatus}): {$student->name} ({$now->format('H:i:s')} WIB)"
            ]);

        } else {
            // ----------------------------------------------------
            // SCAN PULANG SISWA
            // ----------------------------------------------------
            if ($attendance) {
                $attendance->touch();
            }

            if ($gateLog) {
                $gateLog->scan_out = $now->format('H:i:s');
                $gateLog->touch();
            } else if (class_exists(GateAttendance::class)) {
                GateAttendance::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'date'       => $today,
                    ],
                    [
                        'scan_in'    => $attendance ? $attendance->created_at->format('H:i:s') : $now->format('H:i:s'),
                        'scan_out'   => $now->format('H:i:s'),
                        'status'     => $attendance ? $attendance->status : 'present',
                        'updated_at' => $now,
                    ]
                );
            }

            // PROSES KIRIM WHATSAPP PULANG
            if ($parentPhone) {
                $waMessage  = "🔔 *[PORTAL SEKOLAH PINTAR SMA XYZ JAKARTA]*\n\n";
                $waMessage .= "Assalamu'alaikum Wr. Wb. / Selamat Sore,\n";
                $waMessage .= "Yth. Bapak/Ibu Wali Murid dari *{$student->name}*,\n\n";
                $waMessage .= "Diberitahukan bahwa putra/putri Anda telah melakukan *Tap Pulang* di gerbang sekolah.\n\n";
                $waMessage .= "📅 *Hari/Tanggal:* {$formattedDate}\n";
                $waMessage .= "⏰ *Waktu Tap:* {$formattedTime} WIB\n\n";
                $waMessage .= "Selamat beristirahat di rumah.\n";
                $waMessage .= "_SMA XYZ JAKARTA - Smart School System_";

                WhatsAppService::sendMessage($parentPhone, $waMessage);
            }

            return response()->json([
                'status'  => 'success',
                'role'    => 'student',
                'is_late' => false,
                'message' => "SCAN PULANG SISWA BERHASIL: {$student->name} ({$now->format('H:i:s')} WIB)"
            ]);
        }
    }
}