<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Tank;
use App\Models\TankRental;

class SendContractAlertsCommand extends Command
{
    protected $signature = 'emails:send-contract-alerts';
    protected $description = 'Send contract expiration alerts to super admins and CEO';

    public function handle()
    {
        // $recipients = ['hebagamal@ecoc-site.com'];
        $recipients = ['omaraymn411@gmail.com', 'omaraymn411@icloud.com'];

        $tanks = Tank::with('tankRentals.company')->get(); 
        $notifications30 = [];
        $notifications60 = [];

        foreach ($tanks as $tank) {
            foreach ($tank->tankRentals as $rental) {
                $startDate = Carbon::parse($rental->start_date);
                $endDate = $rental->end_date === null
                    ? $startDate->copy()->addMonths($rental->contract_duration ?? 0)
                    : Carbon::parse($rental->end_date);
                $daysUntilEnd = Carbon::now()->diffInDays($endDate, false);

                if ($daysUntilEnd <= 30 && !$rental->reminder_30_days_sent) {
                    $days = abs(round($daysUntilEnd));
                    $notifications30[] = [
                        'company_name' => $rental->company->name ?? 'Unknown Company',
                        'tank_id' => $tank->id,
                        'end_date' => $endDate->format('Y-m-d'),
                        'days' => $days,
                        'is_expired' => $daysUntilEnd < 0
                    ];
                    $rental->update(['reminder_30_days_sent' => true]);
                } elseif ($daysUntilEnd > 30 && $daysUntilEnd <= 60 && !$rental->reminder_60_days_sent) {
                    $days = abs(round($daysUntilEnd));
                    $notifications60[] = [
                        'company_name' => $rental->company->name ?? 'Unknown Company',
                        'tank_id' => $tank->id,
                        'end_date' => $endDate->format('Y-m-d'),
                        'days' => $days,
                        'is_expired' => $daysUntilEnd < 0
                    ];
                    $rental->update(['reminder_60_days_sent' => true]);
                }
            }
        }

        if (!empty($notifications30)) {
            $emailData30 = [
                'notifications' => $notifications30,
                'company_name' => 'ECOC Tank Rentals',
                'alert_type' => '30 Days or Less'
            ];

            Mail::send('emails.contract_alert_admin', $emailData30, function ($message) use ($recipients) {
                $message->to($recipients)
                    ->subject('⚠ Contract Expiration Alerts - 30 Days or Less');
            });
        }

        if (!empty($notifications60)) {
            $emailData60 = [
                'notifications' => $notifications60,
                'company_name' => 'ECOC Tank Rentals',
                'alert_type' => '31 to 60 Days'
            ];
            Mail::send('emails.contract_alert_admin', $emailData60, function ($message) use ($recipients) {
                $message->to($recipients)
                    ->subject('⚠ Contract Expiration Alerts - 31 to 60 Days');
            });
        }

        $this->info('Contract expiration alerts checked and sent if applicable.');
    }
}
