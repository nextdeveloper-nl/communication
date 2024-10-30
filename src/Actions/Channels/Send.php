<?php

namespace NextDeveloper\Communication\Actions\Channels;

use Illuminate\Support\Facades\Log;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\ChannelsService;

class Send extends AbstractAction
{

    /**
     * @throws NotAllowedException
     */
    public function __construct(
        Channels $channel,
        private string $message
    ) {
        $this->model = $channel;
        return parent::__construct();
    }

    /**
     * @throws \JsonException
     */
    public function handle(): void
    {
        // Get the channel id
        $availableChannelId = $this->model->communication_available_channel_id;

        // Get the channel
        $availableChannel = AvailableChannels::withoutGlobalScopes()
            ->where('id', $availableChannelId)
            ->first();

        // Check if the channel exists
        if (!$availableChannel) {
            Log::error('[\NextDeveloper\Communication\Actions\Channels\Send::handle] Channel not found: ' . $availableChannelId);
            return;
        }

        // Get the class
        $class = $availableChannel->class;

        // Check if the class exists
        if (!class_exists($class)) {
            Log::error('[\NextDeveloper\Communication\Actions\Channels\Send::handle] Class not found: ' . $class);
            return;
        }

        // validate channel fields and platform fields
        if (!ChannelsService::validateChannelFields($this->model, $availableChannel)) {
            Log::error(
                '[\NextDeveloper\Communication\Actions\Channels\Send::handle] Channel fields do not match platform fields: '
            );
            return;
        }

        // Send the notification
        (new $class($this->model->config, $this->message))->send();
    }
}
