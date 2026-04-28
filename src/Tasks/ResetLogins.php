<?php

namespace Sunnysideup\CmsNiceties\Tasks;

use Override;
use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class ResetLogins extends BuildTask
{
    protected string $title = 'SECURITY RISK: Reset Logins';

    protected static string $description = 'CAREFUL: Reset all login attempts for all members.';

    protected static string $commandName = 'resetlogins';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        if (!Permission::check('ADMIN')) {
            $output->writeln('<error>You must be an ADMIN to run this task.</error>');
            return Command::FAILURE;
        }

        $forreal = $input->getOption('forreal');
        if (!$forreal) {
            $output->writeln('<comment>Test run only. To run for real add --forreal option</comment>');
        }

        $members = Member::get()->filterAny([
            'FailedLoginCount:GreaterThan' => 0,
            'LockedOutUntil:GreaterThan' => '1970-01-01 00:00:00',
        ]);
        
        foreach ($members as $member) {
            $message = 'Resetting ' . $member->Email;
            $save = false;
            $output->writeln($message);
            
            if ((int) $member->FailedLoginCount > 0.1) {
                $output->writeln(' - resetting failed logins: ' . $member->FailedLoginCount);
                $member->FailedLoginCount = 0;
                $save = true;
            }

            if (strtotime($member->LockedOutUntil) > time()) {
                $output->writeln(' - <error>LOCKED!</error> resetting unlock until: ' . $member->LockedOutUntil);
                $member->LockedOutUntil = null;
                $save = true;
            } elseif ($member->LockedOutUntil) {
                $output->writeln(' - <comment>already unlocked after: ' . $member->LockedOutUntil . '</comment>');
            }

            if (!$forreal) {
                $output->writeln(' - <comment>not saving changes (test run only)</comment>');
                continue;
            }

            if (!$save) {
                $output->writeln(' - <comment>nothing to change</comment>');
                continue;
            }

            $output->writeln(' - <info>saving changes</info>');
            $member->write();
        }

        return Command::SUCCESS;
    }

    #[Override]
    public function getOptions(): array
    {
        return [
            new InputOption('forreal', 'r', InputOption::VALUE_NONE, 'Actually save changes (otherwise runs in test mode)'),
        ];
    }
}
