<?php

namespace NextDeveloper\Communication\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Commons\Common\Enums\GenericErrorCodes;
use NextDeveloper\Commons\Helpers\ObjectHelper;
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
     * How many due reminders a single dispatch run picks up. Anything left over is
     * picked up by the next run, one minute later.
     */
    private const DUE_BATCH_SIZE = 500;

    /**
     * Creates a reminder.
     *
     * The API receives the record the reminder is about as object_type + its uuid (object_id),
     * but the column stores the internal integer id, so the uuid is resolved here. Integer ids
     * from internal callers are stored as given.
     */
    public static function create(array $data)
    {
        if (isset($data['object_id']) && ! is_int($data['object_id'])) {
            $class = self::resolveObjectClass($data['object_type'] ?? null);

            // The lookup keeps the global scopes, so a caller can only set a reminder on a
            // record they are allowed to see.
            $objectId = Str::isUuid((string) $data['object_id'])
                ? $class::where('uuid', $data['object_id'])->value('id')
                : null;

            if (! $objectId) {
                self::refuse('object_id', 'object_id must be the id of an existing record.');
            }

            $data['object_type'] = ltrim($class, '\\');
            $data['object_id']   = $objectId;
        }

        return parent::create($data);
    }

    /**
     * Returns all reminders that are due and have not been fired, acknowledged, snoozed, or cancelled.
     *
     * Called from the scheduled dispatcher, so the global scopes are dropped: AuthorizationScope
     * would limit it to the admin user's own rows and LimitScope would cap it at one page.
     */
    public static function getDue(int $limit = self::DUE_BATCH_SIZE): Collection
    {
        return Remindables::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where('is_reminded', false)
            ->where('is_acknowledged', false)
            ->where('is_cancelled', false)
            ->where('remind_datetime', '<=', now())
            ->where(function ($query) {
                $query->whereNull('snooze_datetime')
                      ->orWhere('snooze_datetime', '<=', now());
            })
            ->orderBy('remind_datetime')
            ->limit($limit)
            ->get();
    }

    /**
     * Atomically claims a due reminder for sending by flipping is_reminded in a single
     * conditional UPDATE. Returns false when another run already claimed it, so a reminder
     * is never sent twice even if two dispatcher runs overlap.
     *
     * A query-builder update fires no model events, so the cached transformer payload
     * is evicted by hand.
     */
    public static function claimForSending(Remindables $remindable): bool
    {
        $claimed = Remindables::withoutGlobalScopes()
            ->where('id', $remindable->id)
            ->where('is_reminded', false)
            ->where('is_cancelled', false)
            ->update(['is_reminded' => true, 'updated_at' => now()]);

        Cache::forget(CacheHelper::getKey('Remindables', $remindable->uuid, 'Transformed'));

        return $claimed === 1;
    }

    /**
     * Gives a claimed reminder back when sending it failed, so the next dispatcher run retries it.
     */
    public static function releaseClaim(int $remindableId): void
    {
        $uuid = Remindables::withoutGlobalScopes()->where('id', $remindableId)->value('uuid');

        Remindables::withoutGlobalScopes()
            ->where('id', $remindableId)
            ->update(['is_reminded' => false, 'updated_at' => now()]);

        if ($uuid) {
            Cache::forget(CacheHelper::getKey('Remindables', $uuid, 'Transformed'));
        }
    }

    /**
     * Resolves object_type (the full model class, or its public Vendor\Package\Model form)
     * to an existing model class.
     */
    private static function resolveObjectClass(?string $objectType): string
    {
        if ($objectType && count(explode('\\', trim($objectType, '\\'))) === 3) {
            $objectType = ObjectHelper::getPrivateObjectName(trim($objectType, '\\'));
        }

        if (! $objectType || ! class_exists($objectType)) {
            self::refuse('object_type', 'object_type must name a model, for example NextDeveloper\\CRM\\Accounts.');
        }

        return $objectType;
    }

    private static function refuse(string $field, string $message): never
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed. Please fix the values you are providing and try again.',
            'code'    => GenericErrorCodes::VALIDATION_FAILED,
            'errors'  => [$field => [$message]],
        ], 422));
    }
}
