<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InvalidArgumentException;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Commons\Common\Enums\GenericErrorCodes;
use NextDeveloper\Commons\Database\GlobalScopes\LimitScope;
use NextDeveloper\Commons\Helpers\ObjectHelper;
use NextDeveloper\Communication\Database\Models\Notifications;
use NextDeveloper\Communication\Services\AbstractServices\AbstractNotificationsService;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

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

        return parent::create(self::normalize($data));
    }

    public static function update($id, array $data)
    {
        return parent::update($id, self::normalize($data));
    }

    /**
     * Unread notifications of the caller, whatever their role may otherwise see.
     */
    public static function unreadCount(): int
    {
        return Notifications::withoutGlobalScope(LimitScope::class)
            ->where('iam_user_id', UserHelper::me()->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Turns the API's form into the columns: the record the notification is about arrives as
     * object_type (the model class, or its public Vendor\Package\Model form) and uuid and is
     * stored as class and internal id; the recipient arrives as a user uuid; data may be sent as
     * a JSON object and is stored as its text.
     *
     * Integer ids from internal callers are stored as given.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function normalize(array $data): array
    {
        if (array_key_exists('data', $data) && is_array($data['data'])) {
            $data['data'] = json_encode($data['data'], JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['iam_user_id']) && is_string($data['iam_user_id'])) {
            $recipient = Users::withoutGlobalScope(AuthorizationScope::class)
                ->where('uuid', $data['iam_user_id'])
                ->value('id');

            if (! $recipient) {
                self::refuse('iam_user_id', 'iam_user_id must be the id of an existing user.');
            }

            $data['iam_user_id'] = $recipient;
        }

        if (isset($data['object_id']) && ! is_int($data['object_id'])) {
            $class = ObjectHelper::getModelClass($data['object_type'] ?? null);

            if (! $class) {
                self::refuse('object_type', 'object_type must name a model, for example NextDeveloper\\Fixlean\\StationCards.');
            }

            $objectId = Str::isUuid((string) $data['object_id'])
                ? $class::withoutGlobalScopes()->where('uuid', $data['object_id'])->value('id')
                : null;

            if (! $objectId) {
                self::refuse('object_id', 'object_id must be the id of an existing record.');
            }

            $data['object_type'] = $class;
            $data['object_id'] = $objectId;
        }

        return $data;
    }

    private static function refuse(string $field, string $message): never
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed. Please fix the values you are providing and try again.',
            'code' => GenericErrorCodes::VALIDATION_FAILED,
            'errors' => [$field => [$message]],
        ], 422));
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
