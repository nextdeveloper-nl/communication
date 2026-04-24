<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Support\Collection;
use NextDeveloper\Communication\Database\Models\MessageEvents;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\Communication\Services\AbstractServices\AbstractMessageEventsService;

/**
 * This class is responsible from managing the data for MessageEvents
 *
 * Class MessageEventsService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class MessageEventsService extends AbstractMessageEventsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    private const VALID_EVENTS = [
        'opened', 'clicked', 'replied', 'bounced', 'complained', 'converted', 'unsubscribed',
    ];

    /**
     * Records an engagement event and applies the corresponding message status side-effect.
     */
    public static function record(string $messageRef, string $eventType, array $metadata = []): MessageEvents
    {
        $message = MessagesService::getByRef($messageRef);

        $event = parent::create([
            'communication_message_id' => $message->uuid,
            'event_type'               => $eventType,
            'metadata'                 => $metadata,
            'occurred_at'              => now(),
        ]);

        match ($eventType) {
            'bounced'      => MessagesService::markAsFailed($messageRef, $metadata['error'] ?? 'bounced'),
            'opened'       => MessagesService::markAsRead($messageRef),
            default        => null,
        };

        return $event;
    }

    /**
     * Returns event counts grouped by type for a campaign — use this for deliverability dashboards.
     */
    public static function getSummaryForCampaign(int $campaignId): Collection
    {
        return MessageEvents::join('communication_messages', 'communication_messages.id', '=', 'communication_message_events.communication_message_id')
            ->where('communication_messages.crm_campaign_id', $campaignId)
            ->groupBy('communication_message_events.event_type')
            ->selectRaw('communication_message_events.event_type, count(*) as total')
            ->get();
    }
}