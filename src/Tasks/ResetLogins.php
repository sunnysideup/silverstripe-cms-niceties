<?php

namespace Sunnysideup\CmsNiceties\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Member;

class ResetLogins extends BuildTask
{
    protected $title = 'Reset Logins';

    protected $description = 'Reset all login attempts for all members.';

    private static $segment = 'resetlogins';

    public function run($request)
    {
        $forreal = (string) $request->getVar('forreal');
        if ($forreal !== '1') {
            echo '<h2>Test run only. To run for real add ?forreal=1 to the URL</h2>';
        }
        $members = Member::get()->filterAny([
            'LoginAttempts:GreaterThan' => 0,
            'LockedOutUntil:GreaterThan' => '1970-01-01 00:00:00',
        ]);
        foreach ($members as $member) {
            $message = 'Resetting ' . $member->Email . ' - Attempts: ' . $member->LoginAttempts . ' - LockedOutUntil: ' . $member->LockedOutUntil;
            DB::alteration_message($message, 'deleted');
            if (strtotime($member->LockedOutUntil) > time()) {
                $member->LockedOutUntil = null;
            }
            $member->LoginAttempts = 0;
            $member->write();
        }
    }
}
