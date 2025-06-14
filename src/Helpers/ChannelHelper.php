<?php

namespace NextDeveloper\Communication\Helpers;

use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use NextDeveloper\Communication\Actions\Emails\Deliver;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Database\Models\Emails;

class ChannelHelper
{
    /**
     * Get the channel for an email.
     *
     * @param Emails $email
     * @return Channels|null
     */
    public static function getChannel(Emails $email): ?Channels
    {
        $channel = Channels::where('id', $email->communication_channel_id)->first();

        if (!$channel) {
            Log::error(__METHOD__ . ": Channel with ID {$email->communication_channel_id} not found");
            return null;
        }

        return $channel;
    }

    /**
     * Get the available channel for a channel.
     *
     * @param Channels $channel
     * @param Emails $email
     * @return AvailableChannels|null
     */
    public static function getAvailableChannel(Channels $channel, Emails $email): ?AvailableChannels
    {
        $availableChannel = AvailableChannels::where('id', $channel->communication_available_channel_id)->first();

        if (!$availableChannel) {
            Log::error(__METHOD__ . ": Available channel with ID {$channel->communication_available_channel_id} not found");
            return null;
        }

        return $availableChannel;
    }

    /**
     * Get the channel class for an available channel.
     *
     * @param AvailableChannels $availableChannel
     * @param Emails $email
     * @return string|null
     */
    public static function getChannelClass(AvailableChannels $availableChannel, Emails $email): ?string
    {
        $class = $availableChannel->class;

        if (!class_exists($class)) {
            Log::error(__METHOD__ . ": Class {$class} not found");
            return null;
        }

        return $class;
    }

    /**
     * Mark an email as delivered.
     *
     * @param Emails $email
     */
    public static function markEmailAsDelivered(Emails $email): void
    {
        $email->delivered_at = Carbon::now();
        $email->save();
    }

    /**
     * Log an error message and exception.
     *
     * @param string $message
     * @param \Exception $exception
     */
    public static function logError(string $message, \Exception $exception): void
    {
        $errorMessage = __METHOD__ . ": {$message}: " . $exception->getMessage();
        Log::error($errorMessage);
        Log::error($exception->getTraceAsString());
    }

    /**
     * Get pending emails that are due for delivery.
     *
     * @param int $limit Maximum number of emails to retrieve
     * @return Collection
     */
    public static function getPendingEmails(int $limit): Collection
    {
        return Emails::whereNull('delivered_at')
            ->where(function ($query) {
                $query->whereNull('deliver_at')
                    ->orWhere('deliver_at', '<=', Carbon::now());
            })
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Deliver an email via the default channel.
     *
     * @param Emails $email
     * @throws BindingResolutionException
     */
    public static function deliverViaDefaultChannel(Emails $email): void
    {
        // Use Laravel's service container to resolve the Deliver class
        $deliver = App::makeWith(Deliver::class, ['email' => $email]);
        $deliver->handle();
    }
}
