<?php

namespace NextDeveloper\Communication\Events\UserPreferences;

use Illuminate\Queue\SerializesModels;
use NextDeveloper\Communication\Database\Models\UserPreferences;

/**
 * Class UserPreferencesRestoringEvent
 *
 * @package NextDeveloper\Communication\Events
 */
class UserPreferencesRestoringEvent
{
    use SerializesModels;

    /**
     * @var UserPreferences
     */
    public $_model;

    /**
     * @var int|null
     */
    protected $timestamp = null;

    public function __construct(UserPreferences $model = null)
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