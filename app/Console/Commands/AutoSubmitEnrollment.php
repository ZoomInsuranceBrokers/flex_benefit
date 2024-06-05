<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\SubmitEnrollment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\MapUserFYPolicy;

class AutoSubmitEnrollment extends Command
{
    
    protected $signature = 'enrollment:auto-submit';
    protected $description = 'Auto submit enrollments that have passed their end date';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentDate = date('Y-m-d');

        $nonSubmittedEnrollmentEntries = User::where('is_enrollment_submitted', 0)
            ->where('enrollment_end_date', '<', $currentDate)
            ->get();

        $updateData = [
            'is_submitted' => true,
            'modified_by' => 0,
            'updated_at' => now()
        ];
        $userUpdateData = [
            'is_enrollment_submitted' => true,
            'enrollment_submit_date' => now(),
            'submission_by' => '0'
        ];

        if ($nonSubmittedEnrollmentEntries->isNotEmpty()) {
            foreach ($nonSubmittedEnrollmentEntries as $enrolRow) {
                MapUserFYPolicy::where('user_id_fk', $enrolRow->id)->update($updateData);
                User::where('id', $enrolRow->id)->update($userUpdateData);

                $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', $enrolRow->id)
                    ->with(['fyPolicy'])
                    ->get()
                    ->toArray();

                Mail::to($enrolRow->email)->send(new SubmitEnrollment($enrolRow, $mapUserFYPolicyData));

                // Log the submission
                Log::info("Enrollment auto-submitted for user ID: {$enrolRow->id}");
            }
        } else {
            Log::info('No entries present for auto submission.');
            $this->info('No entries present for auto submission.');
        }

        return 0;
    }
}
