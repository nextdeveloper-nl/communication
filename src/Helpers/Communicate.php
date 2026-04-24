<?php

namespace NextDeveloper\Communication\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\MessagesService;
use NextDeveloper\Communication\Services\NotificationsService;
use NextDeveloper\Communication\Services\UserPreferencesService;
use NextDeveloper\IAM\Database\Models\Users;

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
        $preferences = UserPreferencesService::getForUser($this->user->id);

        if ($preferences->is_system_email_optout && $severity === 'info') {
            return;
        }

        $data = ['message' => $message];

        NotificationsService::create([
            'severity'       => $severity,
            'data'           => json_encode($data),
            'object_id'      => $object?->id,
            'object_type'    => $object ? get_class($object) : null,
            'iam_user_id'    => $this->user->id,
            'iam_account_id' => $this->user->iam_account_id,
        ]);
    }

    /**
     * Sends an email via the account's highest-priority active email channel.
     * Falls back to direct SMTP if no channel is configured.
     */
    public function sendEmail(string $subject, string $body): void
    {
        $channel = ChannelHelper::getPrimaryForAccount($this->user->iam_account_id, 'email');

        if (!$channel) {
            Log::warning('[Communicate::sendEmail] No active email channel found for account, falling back to direct SMTP', [
                'user_id'    => $this->user->id,
                'account_id' => $this->user->iam_account_id,
            ]);

            $this->sendDirectEmail($subject, $body);
            return;
        }

        $this->dispatchViaChannel($channel, $subject, $body);
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

    private function dispatchViaChannel(Channels $channel, string $subject, string $body): void
    {
        $available = ChannelHelper::getAvailableChannelByType($channel->type);

        if (!$available) {
            Log::error('[Communicate::dispatchViaChannel] No AvailableChannels entry for type: ' . $channel->type);
            return;
        }

        $class = ChannelHelper::getChannelClass($available);

        if (!$class) {
            return;
        }

        try {
            $processor = new $class(channel: $channel);
            $processor->send(['subject' => $subject, 'message' => $body, 'to' => $this->user->email]);
        } catch (Exception $e) {
            Log::error('[Communicate::dispatchViaChannel] Delivery failed via ' . $class, [
                'error'      => $e->getMessage(),
                'channel_id' => $channel->id,
                'user_id'    => $this->user->id,
            ]);
        }
    }
}
