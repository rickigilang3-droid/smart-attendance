<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GateAttendance;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceReportExport;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now('Asia/Jakarta')->toDateString());
        $classId   = $request->input('class_id');

        $classes = SchoolClass::all();

        $query = GateAttendance::with(['student.schoolClass'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($classId) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        $attendances = $query->latest('date')->get();

        return view('admin.reports.index', compact('attendances', 'classes', 'startDate', 'endDate', 'classId'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now('Asia/Jakarta')->toDateString());
        $classId   = $request->input('class_id');

        $query = GateAttendance::with(['student.schoolClass'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($classId) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        $attendances = $query->latest('date')->get();
        $selectedClass = $classId ? SchoolClass::find($classId) : null;

        $pdf = Pdf::loadView('admin.reports.pdf', compact('attendances', 'startDate', 'endDate', 'selectedClass'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Kehadiran_' . $startDate . '_s-d_' . $endDate . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now('Asia/Jakarta')->toDateString());
        $classId   = $request->input('class_id');

        return Excel::download(
            new AttendanceReportExport($startDate, $endDate, $classId), 
            'Laporan_Kehadiran_' . $startDate . '_s-d_' . $endDate . '.xlsx'
        );
    }
}