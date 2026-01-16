<?php

namespace NextDeveloper\Communication\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\ChannelsService;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This class is used to email the user by using the communications module.
 */
class Communicate
{
    private ?Users $user = null;
    private ?string $email = null;

    /**
     *
     *
     * @param Users|string $receiver
     */
    public function __construct(Users|string $receiver = null)
    {
        if ($receiver instanceof Users) {
            $this->user = $receiver;
        }

        if (is_string($receiver)) {
            $this->email = $receiver;
        }
    }

    /**
     * This function is used to get the notification platforms that the user has set.
     *
     * @return mixed
     */
    public function getNotificationPlatforms(): mixed
    {
        if (!$this->user) {
            return collect();
        }

        /**
         * Here we will get the notification platforms that the user has set.
         */
        return Channels::withoutGlobalScopes()
            ->where('iam_user_id', $this->user->id)
            ->whereIsActive(true)
            ->whereIsVerified(true)
            ->get();
    }

    /**
     * This function is an alias of Emails service to make it easy to use, since we cannot use traits.
     *
     * @param $envelope
     * @return void
     */
    public function sendEnvelope($envelope): void
    {
        $this->sendEnvelopeNow($envelope);
    }

    public function sendEnvelopeNow($envelope): void
    {
        $to = $this->email;

        if ($this->user) {
            $to = $this->user->email;
        }

        Mail::driver('smtp')
            ->to($to)
            ->send($envelope);
    }

    public function sendNotification($subject, $message, $preferredChannel = null): void
    {
        if ($this->email && !$this->user) {
            $p = new \NextDeveloper\Communication\Channels\Email(
                user: $this->email,
            );
            $p->send([
                'subject' => $subject,
                'message' => $message
            ]);
            return;
        }

        if ($preferredChannel) {
            // get channel by name
            $userPreferredChannel = $this->preferredChannel($preferredChannel);

            if (!$userPreferredChannel) {

                UserHelper::runAsAdmin(function () use ($preferredChannel) {
                    ChannelsService::createChannelForUser($this->user, $preferredChannel);
                });

                $userPreferredChannel = $this->preferredChannel($preferredChannel);
            }


            // if still not found, fallback to all channels
            if ($userPreferredChannel) {
                // send it only to a preferred channel
                switch ($preferredChannel) {
                    case 'Email':
                        $p = new \NextDeveloper\Communication\Channels\Email(
                            user: $this->user,
                        );
                        $p->send([
                            'subject' => $subject,
                            'message' => $message
                        ]);
                        return;
                    case 'Mattermost':
                        $p = new \NextDeveloper\Communication\Channels\Mattermost(
                            config: $userPreferredChannel->config
                        );
                        $p->send([
                            'subject' => $subject,
                            'message' => $message
                        ]);
                        return;
                    case 'Sms':
                        $p = new \NextDeveloper\Communication\Channels\Sms(
                            config: $userPreferredChannel->config,
                            user: $this->user,
                        );
                        $p->send([
                            'message' => $message
                        ]);
                        return;
                    default:
                        break;
                }
            }
        }


        $userNotificationChannels = Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_user_id', $this->user->id)
            ->get();

        //  We should create an email message channel here if the user has not set any channel.
        if ($userNotificationChannels->count() == 0) {
            $user = $this->user;

            UserHelper::runAsAdmin(function () use ($user) {
                ChannelsService::createChannelForUser($user);
            });

            $userNotificationChannels = Channels::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_user_id', $this->user->id)
                ->get();
        }

        foreach ($userNotificationChannels as $userChannel) {
            $processor = AvailableChannels::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $userChannel->communication_available_channel_id)
                ->first();

            try {
                $config = $userChannel->config;

                switch ($processor->name) {
                    case 'Mattermost':
                        $p = new \NextDeveloper\Communication\Channels\Mattermost(
                            config: $config
                        );
                        $p->send([
                            'subject' => $subject,
                            'message' => $message
                        ]);
                        break;
                    case 'Email':
                        $p = new \NextDeveloper\Communication\Channels\Email(
                            user: $this->user,
                        );
                        $p->send([
                            'subject' => $subject,
                            'message' => $message
                        ]);
                        break;
                    case 'Sms':
                        $p = new \NextDeveloper\Communication\Channels\Sms(
                            config: $config,
                            user: $this->user,
                        );
                        $p->send([
                            'message' => $message
                        ]);
                        break;
                }
            } catch (InvalidArgumentException $e) {
                dd($e);
            } catch (\Exception $e) {
                Log::error(__METHOD__ . ' | The processor (' . $processor->name . ') is not found ' .
                    'in the packages. Please fix this error. This is important!!!');
            }
        }
    }


    protected function preferredChannel($preferredChannel): ?Channels
    {
        return Channels::withoutGlobalScope(AuthorizationScope::class)
            ->select('communication_channels.*')
            ->join('communication_available_channels', 'communication_channels.communication_available_channel_id', '=', 'communication_available_channels.id')
            ->where('communication_channels.iam_user_id', $this->user->id)
            ->where('communication_available_channels.name', $preferredChannel)
            ->first();
    }

}
