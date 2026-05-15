<?php

namespace NextDeveloper\Communication\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Helpers\ChannelHelper;
use NextDeveloper\Communication\Services\MessagesService;
use NextDeveloper\Communication\Services\NotificationsService;
use NextDeveloper\Communication\Services\UserPreferencesService;
use NextDeveloper\IAM\Database\Models\AccountUsers;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Convenience wrapper for communicating with an IAM user via the V2 communication module.
 *
 * In V2, channels are account-level transports — not user-specific.
 * In-app notifications go to communication_notifications.
 * Email delivery routes through the account's highest-priority active email channel.
 */
class Communicate
{
    private Users $user;

    public function __construct(Users $receiver)
    {
        $this->user = $receiver;
    }

    /**
     * Sends a Laravel mailable directly to the user's email address (queued if the mailable implements ShouldQueue).
     */
    public function sendEnvelope($envelope): void
    {
        Mail::to($this->user->email)->send($envelope);
    }

    /**
     * Sends a Laravel mailable synchronously, bypassing any queue even if the mailable implements ShouldQueue.
     */
    public function sendEnvelopeNow($envelope): void
    {
        $envelope->to($this->user->email)->send(Mail::getFacadeRoot());
    }

    /**
     * Creates an in-app notification for the user, respecting their opt-out preferences.
     * Use severity: 'info' | 'warning' | 'error'
     */
    public function sendNotification(string $severity, string $message, mixed $object = null): void
    {
        UserHelper::runAsAdmin(function () use ($severity, $message, $object) {
            $accountUser = AccountUsers::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_user_id', $this->user->id)
                ->first();

            if (!$accountUser) {
                Log::warning('[Communicate::sendNotification] No account found for user, skipping notification', [
                    'user_id' => $this->user->id,
                ]);
                return;
            }

            $preferences = UserPreferencesService::getForUser($this->user->id);

            if ($preferences->is_system_email_optout) {
                return;
            }

            $data = ['message' => $message];

            NotificationsService::create([
                'severity'       => $severity,
                'data'           => json_encode($data),
                'object_id'      => $object?->id,
                'object_type'    => $object ? get_class($object) : null,
                'iam_user_id'    => $this->user->id,
                'iam_account_id' => $accountUser->iam_account_id,
            ]);
        });
    }

    /**
     * Sends an email via a channel and records it as a communication_messages entry.
     *
     * Channel resolution order:
     *   1. $preferredChannel argument — explicit override
     *   2. Account's highest-priority active email channel
     *   3. Direct SMTP fallback when no channel is configured
     *
     * Pass $threadId to associate the message with an existing conversation thread.
     * Additional metadata (e.g. 'subject') can be stored via $metadata.
     */
    public function sendEmail(
        string   $subject,
        string   $body,
        ?Channels $preferredChannel = null,
        ?int     $threadId = null,
        array    $metadata = []
    ): void {
        if (!$this->user->iam_account_id) {
            Log::warning('[Communicate::sendEmail] User has no iam_account_id, falling back to direct SMTP', [
                'user_id' => $this->user->id,
                'email'   => $this->user->email,
            ]);

            $this->sendDirectEmail($subject, $body);
            return;
        }

        $channel = $preferredChannel
            ?? ChannelHelper::getPrimaryForAccount($this->user->iam_account_id, 'email');

        if (!$channel) {
            Log::warning('[Communicate::sendEmail] No active email channel found for account, falling back to direct SMTP', [
                'user_id'    => $this->user->id,
                'account_id' => $this->user->iam_account_id,
            ]);

            $this->sendDirectEmail($subject, $body);
            return;
        }

        // Record the outbound message before dispatching so it is always logged.
        $message = MessagesService::create([
            'communication_thread_id'  => $threadId,
            'communication_channel_id' => $channel->id,
            'crm_campaign_id'          => null,
            'direction'                => 1, // outbound
            'content_type'             => 'text/html',
            'body'                     => $body,
            'status'                   => 'queued',
            'sent_by_user_id'          => $this->user->id,
            'iam_account_id'           => $this->user->iam_account_id,
            'metadata'                 => array_merge($metadata, ['subject' => $subject]),
        ]);

        MessagesService::deliver($message);
    }

    /**
     * Returns all active email channels for the user's account, ordered by priority.
     */
    public function getActiveChannels(string $type = 'email'): \Illuminate\Database\Eloquent\Collection
    {
        return Channels::where('iam_account_id', $this->user->iam_account_id)
            ->where('type', $type)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();
    }

    private function sendDirectEmail(string $subject, string $body): void
    {
        Mail::html($body, function ($message) use ($subject) {
            $message->to($this->user->email)->subject($subject);
        });
    }

}
