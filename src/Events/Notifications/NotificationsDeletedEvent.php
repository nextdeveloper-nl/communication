<?php

namespace NextDeveloper\Communication\Events\Notifications;

use Illuminate\Queue\SerializesModels;
use NextDeveloper\Communication\Database\Models\Notifications;

/**
 * Class NotificationsDeletedEvent
 *
 * @package NextDeveloper\Communication\Events
 */
class NotificationsDeletedEvent
{
    use SerializesModels;

    /**
     * @var Notifications
     */
    public $_model;

    /**
     * @var int|null
     */
    protected $timestamp = null;

    public function __construct(Notifications $model = null)
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
}