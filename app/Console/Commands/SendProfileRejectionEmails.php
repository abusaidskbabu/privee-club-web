<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{User, ProfileTimer, UserRate};
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendProfileRejectionEmails extends Command
{
    protected $signature = 'emails:send-rejections';
    protected $description = 'Send approval or rejection emails';
    public function handle()
    {
        $profiletimers = (int) ProfileTimer::value('time');

        $users = User::where('profile_approval', 0)
            ->whereNotNull('admin_approve_time')
            ->where('status', 1)
            ->whereRaw(
                'DATE_ADD(admin_approve_time, INTERVAL ? HOUR) <= NOW()',
                [$profiletimers]
            )
            ->get();

        // $this->info($users);

        foreach ($users as $user) {
            $timer = ProfileTimer::first();
            $stickness_level = $timer->stickness_level;

            $h = UserRate::where('reciever_id', $user->id)->where('reaction', 'HOT')->count() * 3;
            $y = UserRate::where('reciever_id', $user->id)->where('reaction', 'YES')->count() * 2;
            $n  = UserRate::where('reciever_id', $user->id)->where('reaction', 'No')->count() * (-3);
            $m = UserRate::where('reciever_id', $user->id)->where('reaction', 'Maybe')->count() * 0; //always 0

            $t = $h + $y + $n + $m;
            $p =  $h + $y;

            if (($p && $t >= 5) || ($p && $n <= ($stickness_level) * $p)) {
                //Active Members
                $view = $user->language == 'English'
                    ? 'admin::email.Approvedmember_Rating_email_UK'
                    : 'admin::email.Approvedmember_Rating_email_DK';
                Mail::send($view, compact('user'), function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Your Profile Has Been Approved');
                });
                $user->profile_approval = 1;
                $user->profie_rating_status = 'IN';
                $user->save();
            } else {
                //Rated Out Applicants
                $view = $user->language == 'English'
                    ? 'admin::email.Notapprovedmember_Rating_email_UK'
                    : 'admin::email.Notapprovedmember_Rating_email_DK';
                Mail::send($view, compact('user'), function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Your profile has not been approved');
                });
                $user->rejection_email_status = 1;
                $user->profie_rating_status = 'OUT';
                $user->save();
            }
        }

        $this->info('Profile approval/rejection cron executed successfully.');

        Log::info('emails:send-rejections executed', [
            'time' => now()->toDateTimeString(),
        ]);

        return Command::SUCCESS;
    }
}
