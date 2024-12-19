<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendReminderEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User;

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
            ->where('is_reminder_mail_sent', 0)
            ->whereDate('enrollment_end_date', $twoDaysFromNow)
            ->get();

        $userUpdateData = [
            'is_reminder_mail_sent' => 1,
        ];

        foreach ($users as $user) {
            Mail::to($user->email)->send(new SendReminderEmail($user));
            User::where('id', $user->id)->update($userUpdateData);
        }

        $this->info('Reminder emails sent successfully.');
    }
}
