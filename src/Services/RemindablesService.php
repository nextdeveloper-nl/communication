<?php

namespace NextDeveloper\Communication\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use NextDeveloper\Communication\Database\Models\Remindables;
use NextDeveloper\Communication\Services\AbstractServices\AbstractRemindablesService;

/**
 * This class is responsible from managing the data for Remindables
 *
 * Class RemindablesService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class RemindablesService extends AbstractRemindablesService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Snoozes a reminder until the given datetime.
     */
    public static function snooze(string $ref, Carbon $until): Remindables
    {
        return self::update($ref, [
            'snooze_datetime' => $until,
            'is_reminded'     => false,
        ]);
    }

    /**
     * Marks a reminder as acknowledged by the user.
     */
    public static function acknowledge(string $ref): Remindables
    {
        return self::update($ref, ['is_acknowledged' => true]);
    }

    /**
     * Cancels a reminder so it is never fired.
     */
    public static function cancel(string $ref): Remindables
    {
        return self::update($ref, ['is_cancelled' => true]);
    }

    /**
     * Marks a reminder as fired. Called by the reminder dispatch job.
     */
    public static function markAsReminded(string $ref): Remindables
    {
        return self::update($ref, ['is_reminded' => true]);
    }

    /**
     * Returns all reminders that are due and have not been fired, snoozed, or cancelled.
     */
    public static function getDue(): Collection
    {
        return Remindables::where('is_reminded', false)
            ->where('is_cancelled', false)
            ->where('remind_datetime', '<=', now())
            ->where(function ($query) {
                $query->whereNull('snooze_datetime')
                      ->orWhere('snooze_datetime', '<=', now());
            })
            ->get();
    }
}