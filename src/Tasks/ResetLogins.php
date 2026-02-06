<?php

namespace Sunnysideup\CmsNiceties\Tasks;

use SilverStripe\Control\Director;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;

class ResetLogins extends BuildTask
{
    protected $title = 'SECURITY RISK: Reset Logins';

    protected $description = 'CAREFUL: Reset all login attempts for all members.';

    private static $segment = 'resetlogins';

    public function run($request)
    {
        if (Director::is_cli() || Permission::check('ADMIN')) {

            $forreal = (string) $request->getVar('forreal');
            if ($forreal !== '1') {
                echo '<h2>Test run only. To run for real add ?forreal=1 to the URL</h2>';
            }
            $members = Member::get()->filterAny([
                'FailedLoginCount:GreaterThan' => 0,
                'LockedOutUntil:GreaterThan' => '1970-01-01 00:00:00',
            ]);
            foreach ($members as $member) {
                $message = 'Resetting ' . $member->Email;
                $save = false;
                DB::alteration_message($message, 'deleted');
                if ((int) $member->FailedLoginCount > 0.1) {
                    DB::alteration_message(' - resetting failed logins: ' . $member->FailedLoginCount, 'deleted');
                    $member->FailedLoginCount = 0;
                    $save = true;
                }
                if (strtotime($member->LockedOutUntil) > time()) {
                    DB::alteration_message(' - LOCKED! resetting unlock until: ' . $member->LockedOutUntil, 'deleted');
                    $member->LockedOutUntil = null;
                    $save = true;
                } elseif ($member->LockedOutUntil) {
                    DB::alteration_message(' - already unlocked after: ' . $member->LockedOutUntil, 'changed');
                }
                if ($forreal === '' || $forreal === '0') {
                    DB::alteration_message(" - not saving changes (test run only)", 'deleted');
                    continue;
                }
                if (! $save) {
                    DB::alteration_message(' - nothing to change', 'changed');
                    continue;
                }
                DB::alteration_message(' - saving changes', 'added');
                $member->write();
            }
        } else {
            echo 'You must be an ADMIN to run this task.';
        }
    }
}
