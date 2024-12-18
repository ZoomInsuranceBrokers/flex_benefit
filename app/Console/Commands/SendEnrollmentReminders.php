<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendReminderEmail;
use Carbon\Carbon;

class SendEnrollmentReminders extends Command
{
    protected $signature = 'send:enrollment-reminders';
    protected $description = 'Send reminder emails to users who have not submitted their enrollment and the deadline is in two days';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $twoDaysFromNow = Carbon::now()->addDays(2)->format('Y-m-d');

        $users = DB::table('users')
            ->where('is_enrollment_submitted', 0)
            ->whereDate('enrollment_end_date', $twoDaysFromNow)
            ->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new SendReminderEmail($user));
        }

        $this->info('Reminder emails sent successfully.');
    }

   
}
