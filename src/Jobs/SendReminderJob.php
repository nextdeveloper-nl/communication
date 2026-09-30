<?php

namespace NextDeveloper\Communication\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Communication\Database\Models\Remindables;
use NextDeveloper\Communication\Envelopes\NotificationEnvelope;
use NextDeveloper\Communication\Helpers\Communicate;
use NextDeveloper\Communication\Services\RemindablesService;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Sends one due reminder to its owner, as an in-app notification and an email.
 *
 * Dispatched by communication:dispatch-due-reminders after it has claimed the reminder
 * (is_reminded = true). If sending fails for good, the claim is released so the next
 * dispatcher run tries again.
 */
class SendReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = 60;

    public function __construct(private int $remindableId)
    {
    }

    public function handle(): void
    {
        UserHelper::runAsAdmin(function () {
            $remindable = Remindables::withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->where('id', $this->remindableId)
                ->first();

            // Cancelled or deleted between the claim and this job; nothing to send.
            if (! $remindable || $remindable->is_cancelled) {
                return;
            }

            $user = Users::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $remindable->iam_user_id)
                ->first();

            if (! $user) {
                Log::warning('[SendReminderJob] Reminder owner not found, skipping', [
                    'remindable_id' => $remindable->id,
                    'iam_user_id'   => $remindable->iam_user_id,
                ]);

                return;
            }

            $object  = $this->resolveObject($remindable);
            $subject = 'Reminder: '.$this->describe($remindable, $object);
            $body    = $remindable->note ?: $subject;

            $communicate = new Communicate($user);
            $errors      = [];

            // Each channel is sent on its own, so a failing mailer does not stop the in-app
            // notification (and a retry does not duplicate the one that already went out).
            try {
                // In-app notification, linked to the record the reminder is about.
                $communicate->sendNotification('info', $subject.($remindable->note ? ' — '.$remindable->note : ''), $object);
            } catch (\Throwable $e) {
                $errors['notification'] = $e->getMessage();
            }

            try {
                // Email copy, so the reminder is not missed when the user is not in the app.
                $communicate->sendEnvelope(new NotificationEnvelope($user, $subject, nl2br(e($body))));
            } catch (\Throwable $e) {
                $errors['email'] = $e->getMessage();
            }

            if ($errors) {
                Log::warning('[SendReminderJob] Reminder channel failed', [
                    'remindable_id' => $remindable->id,
                    'errors'        => $errors,
                ]);
            }

            // Only when nothing reached the user do we fail the job, so it is retried and,
            // after the last try, released back to the dispatcher.
            if (count($errors) === 2) {
                throw new \RuntimeException('Reminder #'.$remindable->id.' could not be sent through any channel.');
            }
        });
    }

    /**
     * All retries failed: give the claim back so the next dispatcher run retries the reminder.
     */
    public function failed(\Throwable $e): void
    {
        Log::error('[SendReminderJob] Could not send reminder, releasing claim', [
            'remindable_id' => $this->remindableId,
            'error'         => $e->getMessage(),
        ]);

        RemindablesService::releaseClaim($this->remindableId);
    }

    /**
     * Loads the record the reminder is about, or null if it is gone.
     */
    private function resolveObject(Remindables $remindable): mixed
    {
        $class = $remindable->object_type ? '\\'.ltrim($remindable->object_type, '\\') : null;

        if (! $class || ! class_exists($class)) {
            return null;
        }

        return $class::withoutGlobalScopes()->where('id', $remindable->object_id)->first();
    }

    /**
     * A short human readable label for the record, e.g. "Accounts: Acme Ltd".
     */
    private function describe(Remindables $remindable, mixed $object): string
    {
        $type = class_basename((string) $remindable->object_type) ?: 'Record';

        if (! $object) {
            return $type;
        }

        $label = $object->name ?? $object->title ?? $object->uuid ?? null;

        return $label ? $type.': '.$label : $type;
    }
}
