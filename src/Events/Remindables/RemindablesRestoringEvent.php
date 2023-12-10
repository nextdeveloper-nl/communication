<?php

namespace NextDeveloper\Communication\Events\Remindables;

use Illuminate\Queue\SerializesModels;
use NextDeveloper\Communication\Database\Models\Remindables;

/**
 * Class RemindablesRestoringEvent
 *
 * @package NextDeveloper\Communication\Events
 */
class RemindablesRestoringEvent
{
    use SerializesModels;

    /**
     * @var Remindables
     */
    public $_model;

    /**
     * @var int|null
     */
    protected $timestamp = null;

    public function __construct(Remindables $model = null)
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