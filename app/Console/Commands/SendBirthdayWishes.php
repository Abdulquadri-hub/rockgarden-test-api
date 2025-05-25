<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendBirthdayWishes extends Command
{
    protected $signature = 'send:birthday-wishes';
    protected $description = 'Send birthday wishes to staff and clients, and notify respective admins';

    private $staffAdminEmails = [
        'rockgardenhomeshr@gmail.com',
        'rockgardenh@gmail.com'
    ];

    private $clientAdminEmails = [
        'faith.rockgardenhomes@gmail.com',
        'rockgardencsm@gmail.com'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
       try {
            $today = Carbon::today()->format('m-d');
            $this->info("Checking birthdays for date: " . $today);
            

            $this->handleStaffBirthdays($today);


            $this->handleClientBirthdays($today);
            
            return 0;
        } catch (\Exception $e) {
            Log::error("Main birthday command error: " . $e->getMessage());
            $this->error("Main command error: " . $e->getMessage());
            return 1;
        }
    }

    private function handleStaffBirthdays($today)
    {
        $staffWithBirthdays = Employee::with(['user'])
            ->whereHas('user', function($query) use ($today) {
                $query->whereRaw('DATE_FORMAT(date_of_birth, "%m-%d") = ?', [$today]);
            })
            ->get();

        if ($staffWithBirthdays->isNotEmpty()) {
            foreach ($staffWithBirthdays as $staff) {
                try {

                    Mail::send('emails.birthday.birthday_wish', [
                        'staff' => $staff,
                        'type' => 'staff'
                    ], function ($message) use ($staff) {
                        $message->to($staff->user->email)
                               ->subject('Happy Birthday!');
                    });
                    
                    $this->info("Birthday wish email sent to staff ". $staff->user->email);

                    Mail::send('emails.birthday.notify_admin_birthday', [
                        'staff' => $staff,
                        'type' => 'staff'
                    ], function ($message) {
                        $message->to($this->staffAdminEmails)
                               ->subject('Staff Birthday Notification');
                    });
                    
                    $this->info("Admin notification sent");

                    $this->info("Birthday wishes sent to staff member: {$staff->user->first_name} {$staff->user->last_name}");
                } catch (\Exception $e) {
                    $this->error("Failed to send staff birthday email: " . $e->getMessage());
                    \Log::error("Birthday email error: " . $e->getMessage());
                }
            }
        }
    }

    private function handleClientBirthdays($today)
    {
        $clientsWithBirthdays = Client::with(['user'])
            ->whereHas('user', function($query) use ($today) {
                $query->whereRaw('DATE_FORMAT(date_of_birth, "%m-%d") = ?', [$today]);
            })
            ->get();

        if ($clientsWithBirthdays->isNotEmpty()) {
            foreach ($clientsWithBirthdays as $client) {
                try {

                    Mail::send('emails.birthday.birthday_wish', [
                        'staff' => $client,  // Using 'staff' for consistency in template
                        'type' => 'client'
                    ], function ($message) use ($client) {
                        $message->to($client->user->email)
                               ->subject('Happy Birthday from Rock Garden Homes!');
                    });

                    Mail::send('emails.birthday.notify_admin_birthday', [
                        'staff' => $client,
                        'type' => 'client'
                    ], function ($message) {
                        $message->to($this->clientAdminEmails)
                               ->subject('Client Birthday Notification');
                    });

                    $this->info("Birthday wishes sent to client: {$client->user->first_name} {$client->user->last_name}");
                } catch (\Exception $e) {
                    $this->error("Failed to send client birthday email: " . $e->getMessage());
                    \Log::error("Birthday email error: " . $e->getMessage());
                }
            }
        }
    }
}