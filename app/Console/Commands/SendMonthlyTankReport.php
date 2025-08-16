<?php

namespace App\Console\Commands;

use App\Exports\TankMonthlyReportExport;
use App\Services\TankService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendMonthlyTankReport extends Command
{
    protected $signature = 'app:send-monthly-tank-report';
    protected $description = 'Send monthly tank report on the first of each month';

    protected $tankService;

    public function __construct(TankService $tankService)
    {
        parent::__construct();
        $this->tankService = $tankService;
    }

    public function handle()
    {
        $tanks = $this->tankService->getAllTanks();
        $previousMonth = now()->subMonth()->startOfMonth();
        $previousMonthEnd = now()->subMonth()->endOfMonth();

        // Filter tanks with transactions in the previous month
        $tanksWithTransactions = array_filter($tanks, function ($tank) use ($previousMonth, $previousMonthEnd) {
            $transactions = $tank['transactions']->filter(function ($transaction) use ($previousMonth, $previousMonthEnd) {
                return isset($transaction['date']) && \Carbon\Carbon::parse($transaction['date'])->between($previousMonth, $previousMonthEnd);
            });
            return $transactions->isNotEmpty();
        });

        if (empty($tanksWithTransactions)) {
            $this->info('No transactions found for the previous month.');
            return;
        }

        $fileName = 'ECOC_Monthly_Tank_Report_' . now()->format('Ym') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);

        // Generate Excel with one sheet per tank
        Excel::store(new TankMonthlyReportExport($tanksWithTransactions, $previousMonth, $previousMonthEnd), $fileName);

        // Define recipients
        $recipients = [
            'to' => [
                'omaraymn411@gmail.com' => 'Aly Ayman',
                'omaraymn411@icloud.com' => 'Ramy Ayman',
                // 'karim.aly@ecoc-site.com' => 'Karim Aly',
                // 'karim.aly@dominion-egypt.com' => 'Karim Aly',
            ],
            'cc' => [
                'omaraymn411@gmail.com' => 'Aly Ayman',
                'omaraymn411@icloud.com' => 'Ramy Ayman',
                // 'hebagamal@ecoc-site.com' => 'Heba Gama',
                // 'heba@dominion-egypt.com' => 'Heba Gama',
                // 'hussienomran@ecoc-site.com' => 'Hussien Omran',
                // 'Hussien@Dominion-Egypt.Com' => 'Hussien Omran',
                // 'ramy@ecoc-site.com' => 'Ramy',
            ],
        ];

        // Send individual email to each to recipient
        foreach ($recipients['to'] as $toEmail => $toName) {
            Mail::send('emails.tank_monthly_report', [
                'tanks' => $tanksWithTransactions,
                'previousMonth' => $previousMonth->format('F Y'),
                'recipients' => ['to' => [$toEmail => $toName], 'cc' => $recipients['cc']],
            ], function ($message) use ($filePath, $fileName, $toEmail, $toName, $recipients) {
                $message->to($toEmail, $toName);
                foreach ($recipients['cc'] as $ccEmail => $ccName) {
                    if ($ccEmail !== $toEmail) {
                        $message->cc($ccEmail, $ccName);
                    }
                }
                $message->subject('📊 ECOC Monthly Tank Report - ' . now()->subMonth()->format('F Y'))
                    ->attach($filePath, ['as' => $fileName, 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
            });
        }

        // Remove the file after sending
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->info('Monthly tank report sent successfully to all recipients.');
    }
}
