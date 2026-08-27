<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Commons\Database\GlobalScopes\LimitScope;
use NextDeveloper\Communication\Database\Models\Notifications;
use NextDeveloper\Communication\Services\AbstractServices\AbstractNotificationsService;

/**
 * This class is responsible from managing the data for Notifications
 *
 * Class NotificationsService.
 */
class NotificationsService extends AbstractNotificationsService
{
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    private const VALID_SEVERITIES = ['info', 'warning', 'error'];

    /**
     * Creates a notification. Validates severity — v1 used three booleans which allowed
     * invalid states; v2 uses a single constrained column.
     */
    public static function create(array $data): Notifications
    {
        if (! in_array($data['severity'] ?? '', self::VALID_SEVERITIES, true)) {
            throw new InvalidArgumentException(
                'Severity must be one of: '.implode(', ', self::VALID_SEVERITIES)
            );
        }

        return parent::create($data);
    }

    /**
     * Marks a notification as read.
     */
    public static function markAsRead(string $ref): Notifications
    {
        return self::update($ref, ['read_at' => now()]);
    }

    /**
     * Marks every unread notification of the current user as read and returns the row count.
     *
     * LimitScope is dropped because it appends a `limit` clause to any builder, including
     * this mass update, which would silently leave everything past the first page unread.
     * AuthorizationScope is kept — it supplies the iam_account_id/iam_user_id predicates
     * that keep this update to the caller's own rows.
     *
     * NotificationsTransformer caches each transformed notification under
     * `Notifications:{uuid}:Transformed` with no TTL, and CleanCache only evicts it from
     * model events. A mass update fires no events, so the rows were marked read in the
     * database while the list endpoint kept serving the cached `read_at: null` payload —
     * the client saw nothing change. Collect the uuids first and evict their keys by hand.
     */
    public static function markAllAsRead(): int
    {
        $unread = Notifications::withoutGlobalScope(LimitScope::class)
            ->whereNull('read_at')
            ->pluck('uuid', 'id');

        if ($unread->isEmpty()) {
            return 0;
        }

        $marked = 0;

        //  Chunked so an account sitting on a huge unread pile cannot blow the
        //  database's bound-parameter limit with a single whereIn.
        foreach ($unread->keys()->chunk(1000) as $ids) {
            $marked += Notifications::withoutGlobalScope(LimitScope::class)
                ->whereIn('id', $ids)
                ->update(['read_at' => now()]);
        }

        foreach ($unread as $uuid) {
            Cache::forget(CacheHelper::getKey('Notifications', $uuid, 'Transformed'));
        }

        return $marked;
    }

    /**
     * Returns all unread notifications for a given IAM user ID.
     */
    public static function getUnreadForUser(int $userId): Collection
    {
        return Notifications::where('iam_user_id', $userId)
            ->whereNull('read_at')
            ->orderByDesc('created_at')
            ->get();
    }
}
