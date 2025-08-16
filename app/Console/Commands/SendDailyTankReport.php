<?php

namespace App\Console\Commands;

use App\Services\TankService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TankDailyReportExport;

class SendDailyTankReport extends Command
{
    protected $signature = 'emails:send-daily-tank-report';
    protected $description = 'Send daily tank status report to specified emails';

    protected $tankService;

    public function __construct(TankService $tankService)
    {
        parent::__construct();
        $this->tankService = $tankService;
    }

    public function handle()
    {
        $tanks = $this->tankService->getAllTanks();

        $fileName = 'ECOC_Daily_Tank_Status_Report_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);

        Excel::store(new TankDailyReportExport($tanks), $fileName);

        // Define recipients with names
        $recipients = [
            'to' => [
                'omaraymn411@gmail.com' => 'Aly Ayman',
                'omaraymn411@icloud.com' => 'Ramy Ayman',
                // 'hebagamal@ecoc-site.com' => 'Heba Gamal',
                // 'hussienomran@ecoc-site.com' => 'Hussien Omran',
                // 'ramy@ecoc-site.com' => 'Ramy',
            ],
            'cc' => [
                // 'heba@dominion-egypt.com' => 'Heba Dominion',
                // 'Hussien@Dominion-Egypt.Com' => 'Hussien Dominion',
            ],
        ];

        // Send individual email to each to recipient
        foreach ($recipients['to'] as $toEmail => $toName) {
            Mail::send('emails.tank_daily_report', ['tanks' => $tanks, 'recipients' => ['to' => [$toEmail => $toName], 'cc' => $recipients['cc']]], function ($message) use ($filePath, $fileName, $toEmail, $toName, $recipients) {
                $message->to($toEmail, $toName);

                foreach ($recipients['cc'] as $ccEmail => $ccName) {
                    if ($ccEmail !== $toEmail) { 
                        $message->cc($ccEmail, $ccName);
                    }
                }
                $message->subject('ECOC - Daily Status Report')
                    ->attach($filePath, ['as' => $fileName, 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
            });
        }

        // Remove the file after sending
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->info('Daily tank status report sent successfully.');
    }
}
