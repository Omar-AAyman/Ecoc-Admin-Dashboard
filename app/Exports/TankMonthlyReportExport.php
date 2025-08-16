<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TankMonthlyReportExport implements WithMultipleSheets
{
    protected $tanks;
    protected $previousMonth;
    protected $previousMonthEnd;

    public function __construct($tanks, $previousMonth, $previousMonthEnd)
    {
        $this->tanks = $tanks;
        $this->previousMonth = $previousMonth;
        $this->previousMonthEnd = $previousMonthEnd;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->tanks as $tank) {
            $sheets[] = new class($tank, $this->previousMonth, $this->previousMonthEnd) implements FromCollection, WithHeadings, WithStyles {
                protected $tank;
                protected $previousMonth;
                protected $previousMonthEnd;

                public function __construct($tank, $previousMonth, $previousMonthEnd)
                {
                    $this->tank = $tank;
                    $this->previousMonth = $previousMonth;
                    $this->previousMonthEnd = $previousMonthEnd;
                }

                public function collection()
                {
                    return $this->tank['transactions']->filter(function ($transaction) {
                        $date = \Carbon\Carbon::parse($transaction['date']);
                        return $date->between($this->previousMonth, $this->previousMonthEnd);
                    })->map(function ($transaction) {
                        return [
                            'Date' => $transaction['date'],
                            'Type' => $transaction['type'],
                            'Quantity (mt)' => (float)str_replace(' mt', '', $transaction['quantity']),
                        ];
                    });
                }

                public function headings(): array
                {
                    return ['Date', 'Type', 'Quantity (mt)'];
                }

                public function styles(Worksheet $sheet)
                {
                    $styles = [
                        1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E7FF']]],
                    ];

                    // Add borders to transaction table headings
                    $styles[1]['borders'] = ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]];

                    // Add tank details to the right (starting at column E, row 2)
                    $sheet->setCellValue('E1', 'Tank Details');
                    $styles[1] = ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D3D3D3']], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];

                    $sheet->setCellValue('E2', 'Tank Number');
                    $sheet->setCellValue('F2', $this->tank['id']);
                    $sheet->setCellValue('E3', 'Assigned Company');
                    $sheet->setCellValue('F3', $this->tank['companyName']);
                    $sheet->setCellValue('E4', 'Product Inside');
                    $sheet->setCellValue('F4', $this->tank['product']);
                    $sheet->setCellValue('E5', 'Capacity Inside (mt)');
                    $sheet->setCellValue('F5', $this->tank['currentLevel']);

                    // Calculate Capacity at First of Month
                    $totalLoad = $this->tank['transactions']->filter(function ($t) {
                        return in_array($t['type'], ['loading', 'load (transfer)']) && \Carbon\Carbon::parse($t['date'])->between($this->previousMonth, $this->previousMonthEnd);
                    })->sum(function ($t) {
                        return (float)str_replace(' mt', '', $t['quantity']);
                    });
                    $totalDischarge = $this->tank['transactions']->filter(function ($t) {
                        return in_array($t['type'], ['discharging', 'discharge (transfer)']) && \Carbon\Carbon::parse($t['date'])->between($this->previousMonth, $this->previousMonthEnd);
                    })->sum(function ($t) {
                        return (float)str_replace(' mt', '', $t['quantity']);
                    });
                    $capacityAtFirst = (float)$this->tank['currentLevel'] + $totalDischarge - $totalLoad;

                    $sheet->setCellValue('E6', 'Capacity at First of Month (mt)');
                    $sheet->setCellValue('F6', number_format($capacityAtFirst, 2));
                    for ($i = 2; $i <= 6; $i++) {
                        $styles[$i] = ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9F9F9']], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
                    }

                    // Add alert text
                    $alertRow = 7;
                    $sheet->setCellValue('E' . $alertRow, 'Note: This data might not be accurate if the tank capacity has been manually edited.');
                    $styles[$alertRow] = ['font' => ['italic' => true, 'size' => 10], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFACD']], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];

                    // Add totals row
                    $row = $sheet->getHighestRow() + 2;
                    $totalLoad = $this->tank['transactions']->filter(function ($t) {
                        return in_array($t['type'], ['loading', 'load (transfer)']) && \Carbon\Carbon::parse($t['date'])->between($this->previousMonth, $this->previousMonthEnd);
                    })->sum(function ($t) {
                        return (float)str_replace(' mt', '', $t['quantity']);
                    });
                    $totalDischarge = $this->tank['transactions']->filter(function ($t) {
                        return in_array($t['type'], ['discharging', 'discharge (transfer)']) && \Carbon\Carbon::parse($t['date'])->between($this->previousMonth, $this->previousMonthEnd);
                    })->sum(function ($t) {
                        return (float)str_replace(' mt', '', $t['quantity']);
                    });

                    $sheet->setCellValue('A' . $row, 'Totals');
                    $sheet->setCellValue('B' . $row, '');
                    $sheet->setCellValue('C' . $row, $totalLoad + $totalDischarge);
                    $sheet->setCellValue('A' . ($row + 1), 'Load Total');
                    $sheet->setCellValue('C' . ($row + 1), $totalLoad);
                    $sheet->setCellValue('A' . ($row + 2), 'Discharge Total');
                    $sheet->setCellValue('C' . ($row + 2), $totalDischarge);

                    $styles[$row] = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D3D3D3']], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
                    $styles[$row + 1] = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '90EE90']], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
                    $styles[$row + 2] = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB6C1']], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];

                    // Dynamically fit all columns to content
                    foreach (range('A', 'F') as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }

                    return $styles;
                }

                public function title(): string
                {
                    return 'Tank #' . str_replace('#', '', $this->tank['id']) . ' Report';
                }
            };
        }

        return $sheets;
    }
}
