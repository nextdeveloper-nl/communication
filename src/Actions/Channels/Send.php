<?php

namespace NextDeveloper\Communication\Actions\Channels;

use Illuminate\Support\Facades\Log;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\ChannelTestService;

/**
 * Delivers a plain text message through a channel (used for the channel verification code).
 */
class Send extends AbstractAction
{
    /**
     * @param  ?string  $recipient  Email address, phone number or chat id depending on the channel type. Channels
     *                              that post to a fixed destination (webhooks) need none.
     *
     * @throws NotAllowedException
     */
    public function __construct(
        Channels $channel,
        private string $message,
        private ?string $recipient = null,
        private string $subject = 'Channel verification'
    ) {
        $this->model = $channel;

        return parent::__construct();
    }

    /**
     * @throws \Throwable With the provider's own error, so a failed delivery fails the job instead of passing silently
     */
    public function handle(): void
    {
        try {
            ChannelTestService::deliver($this->model, $this->recipient, $this->subject, $this->message);
        } catch (\Throwable $e) {
            Log::error('[\NextDeveloper\Communication\Actions\Channels\Send::handle] Delivery failed', [
                'channel_id' => $this->model->id,
                'type' => $this->model->type,
                // Class only: provider and HTTP client messages can echo credentials back
                'error' => get_class($e),
            ]);

            throw $e;
        }
    }
}
