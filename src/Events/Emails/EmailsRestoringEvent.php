<?php

namespace NextDeveloper\Communication\Events\Emails;

use Illuminate\Queue\SerializesModels;
use NextDeveloper\Communication\Database\Models\Emails;

/**
 * Class EmailsRestoringEvent
 *
 * @package NextDeveloper\Communication\Events
 */
class EmailsRestoringEvent
{
    use SerializesModels;

    /**
     * @var Emails
     */
    public $_model;

    /**
     * @var int|null
     */
    protected $timestamp = null;

    public function __construct(Emails $model = null)
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