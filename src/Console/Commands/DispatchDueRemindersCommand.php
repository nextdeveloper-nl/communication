<?php

namespace NextDeveloper\Communication\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\Communication\Jobs\SendReminderJob;
use NextDeveloper\Communication\Services\RemindablesService;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Picks up every reminder (communication_remindables) whose remind time, or snooze time,
 * has passed, claims it and queues a SendReminderJob for it.
 *
 * Scheduled every minute from CommunicationServiceProvider.
 */
class DispatchDueRemindersCommand extends Command
{
    protected $signature = 'communication:dispatch-due-reminders
                            {--dry-run : List due reminders without sending them}';

    protected $description = 'Queue a notification for every reminder that is due';

    public function handle(): int
    {
        UserHelper::setAdminAsCurrentUser();

        $reminders = RemindablesService::getDue();

        if ($reminders->isEmpty()) {
            $this->info('No reminders due.');
            return 0;
        }

        $this->info("Found {$reminders->count()} due reminder(s).");

        if ($this->option('dry-run')) {
            $this->warn('Dry-run mode — no reminders will be sent.');
            $this->table(
                ['ID', 'User ID', 'Object', 'Remind At', 'Snoozed Until', 'Note'],
                $reminders->map(fn ($r) => [
                    $r->id,
                    $r->iam_user_id,
                    class_basename((string) $r->object_type).'#'.$r->object_id,
                    $r->remind_datetime,
                    $r->snooze_datetime,
                    mb_strimwidth((string) $r->note, 0, 40, '…'),
                ])->toArray()
            );
            return 0;
        }

        $queued = 0;

        foreach ($reminders as $reminder) {
            // Claim first: if another run already took this reminder, skip it, so it is
            // never sent twice.
            if (! RemindablesService::claimForSending($reminder)) {
                continue;
            }

            SendReminderJob::dispatch($reminder->id);
            $queued++;
        }

        $this->info("Queued {$queued} reminder(s).");

        return 0;
    }
}
