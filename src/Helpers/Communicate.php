<?php

namespace NextDeveloper\Communication\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use NextDeveloper\Commons\Exceptions\NotFoundException;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\EmailsService;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use PharIo\Manifest\Email;
use function PHPUnit\Framework\isInstanceOf;

/**
 * This class is used to email the user by using the communications module.
 */
class Communicate
{
    private Users $user;

    /**
     *
     *
     * @param Users $receiver
     */
    public function __construct(Users $receiver)
    {
        $this->user = $receiver;

        return $this;
    }

    /**
     * This function is used to get the notification platforms that the user has set.
     *
     * @return mixed
     */
    public function getNotificationPlatforms(): mixed
    {
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
    public function sendEnvelope($envelope)
    {
        self::sendEnvelopeNow($envelope);
    }

    public function sendNotification($subject, $message) {
        dd($this->user);
        $userNotificationChannels = Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_user_id', $this->user->id)
            ->get();

        foreach ($userNotificationChannels as $userChannel) {
            $processor = AvailableChannels::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $userChannel->communication_available_channel_id)
                ->first();

            try {
                $config = json_decode($userChannel->config, true);

                switch ($processor->name) {
                    case 'Mattermost':
                        $p = new \NextDeveloper\Communication\Channels\Mattermost(
                            config: $config
                        );
                        $p->send($message);
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

    public function sendEnvelopeNow($envelope) {
        Mail::driver('smtp')
            ->to($this->user->email)
            ->send($envelope);
    }
}
