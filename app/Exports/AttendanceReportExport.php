<?php

namespace App\Exports;

use App\Models\GateAttendance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $classId;

    public function __construct($startDate, $endDate, $classId = null)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->classId   = $classId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
    {
        $query = GateAttendance::with(['student.schoolClass'])
            ->whereBetween('date', [$this->startDate, $this->endDate]);

        if ($this->classId) {
            $query->whereHas('student', function ($q) {
                $q->where('school_class_id', $this->classId);
            });
        }

        return $query->latest('date')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Jam Masuk (WIB)',
            'Jam Pulang (WIB)',
            'Status'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->date,
            $row->student->nisn ?? '-',
            $row->student->name ?? '-',
            $row->student->schoolClass->name ?? '-',
            $row->scan_in ?? '-',
            $row->scan_out ?? '-',
            strtoupper($row->status ?? 'HADIR')
        ];
    }
}