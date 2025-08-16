<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TankDailyReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $tanks;

    public function __construct($tanks)
    {
        $this->tanks = collect($tanks);
    }

    public function collection()
    {
        return $this->tanks->map(function ($tank) {
            return [
                'Number' => $tank['id'],
                'Max Capacity (mt)' => $tank['maxCapacity'],
                'Current Capacity (mt)' => $tank['currentLevel'],
                'Fill %' => $tank['capacityUtilization'],
                'Status' => $tank['status'],
                'Product' => $tank['product'],
                'Company' => $tank['companyName'],
                'Temperature (°C)' => $tank['temperatureCelsius'],
                'Temperature (°F)' => $tank['temperatureFahrenheit'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Number',
            'Max Capacity (mt)',
            'Current Capacity (mt)',
            'Fill %',
            'Status',
            'Product',
            'Company',
            'Temperature (°C)',
            'Temperature (°F)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E0E7FF']]],
        ];
    }
}
