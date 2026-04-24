<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\Notifications;
use NextDeveloper\Communication\Services\AbstractServices\AbstractNotificationsService;

/**
 * This class is responsible from managing the data for Notifications
 *
 * Class NotificationsService.
 *
 * @package NextDeveloper\Communication\Database\Models
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
        if (!in_array($data['severity'] ?? '', self::VALID_SEVERITIES, true)) {
            throw new InvalidArgumentException(
                'Severity must be one of: ' . implode(', ', self::VALID_SEVERITIES)
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