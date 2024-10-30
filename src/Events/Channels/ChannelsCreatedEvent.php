<?php

namespace NextDeveloper\Communication\Events\Channels;

use Illuminate\Queue\SerializesModels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\ChannelsService;
use NextDeveloper\Events\Services\Events;

/**
 * Class ChannelsCreatedEvent
 *
 * @package NextDeveloper\Communication\Events
 */
class ChannelsCreatedEvent extends Events
{
    use SerializesModels;

    /**
     * @var Channels
     */
    public $_model;

    /**
     * @var int|null
     */
    protected $timestamp = null;

    public function __construct(Channels $model = null)
    {
        $this->_model = $model;
    }

    /**
     * @param int $value
     *
     * @return AbstractEvent
     */
    public function setTimestamp($value)
    {
        $this->timestamp = $value;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * @throws \Exception
     */
    public function sendVerificationCode(): void
    {
        // Send verification code to the user
        ChannelsService::sendCode($this->_model);
    }

    public function handle()
    {
        $this->sendVerificationCode();
    }
}