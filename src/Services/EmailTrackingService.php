<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Support\Facades\Log;
use NextDeveloper\Commons\Helpers\MetaHelper;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\IAM\Database\Models\Accounts;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Provider-independent email engagement tracking (leo4#1254): an open pixel and click redirects handled by our
 * own public endpoints, for every email channel except Mailgun (which keeps its own tracking and event fetch,
 * so nothing is counted twice).
 *
 * Tokens are HMAC-signed with the application key: a click token carries the original URL, so the redirect only
 * ever goes to the URL embedded when the message was sent (no open redirect), and a token can not be forged.
 *
 * Off by default (COMMUNICATION_TRACKING_ENABLED). An account can turn it on or off with the account meta
 * `email_tracking`, and a single message with metadata `tracking` (true/false), most specific wins.
 */
class EmailTrackingService
{
    /** Channel types that get our tracking. Mailgun is left out on purpose (its own tracking, see class doc). */
    public const TRACKED_CHANNEL_TYPES = ['email', 'smtp', 'gmail', 'google_workspace', 'microsoft_365'];

    /** User agents of link scanners, image proxies and crawlers: their hits are recorded but flagged. */
    private const BOT_PATTERN = '/(bot|crawler|spider|scanner|HeadlessChrome|python-requests|curl\/|wget|GoogleImageProxy|YahooMailProxy|Barracuda|Proofpoint|Mimecast|Symantec|Trend Micro|Microsoft Office|SafeLinks)/i';

    /** A hit this soon after delivery is a prefetch by a mail server or client, not a person. */
    private const PREFETCH_SECONDS = 3;

    // ---- tokens ------------------------------------------------------------------------------------------------

    public static function openToken(string $messageUuid): string
    {
        return self::sign(['t' => 'o', 'm' => $messageUuid]);
    }

    public static function clickToken(string $messageUuid, string $url): string
    {
        return self::sign(['t' => 'c', 'm' => $messageUuid, 'u' => $url]);
    }

    /**
     * @return array<string, string>|null The payload when the signature is valid, else null.
     */
    public static function verify(string $token, string $type): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 2) {
            return null;
        }

        [$body, $signature] = $parts;

        if (! hash_equals(self::signature($body), $signature)) {
            return null;
        }

        $payload = json_decode((string) self::b64d($body), true);

        if (! is_array($payload) || ($payload['t'] ?? null) !== $type || empty($payload['m'])) {
            return null;
        }

        return $payload;
    }

    public static function openUrl(string $messageUuid): string
    {
        return self::baseUrl().'/public/communication/t/o/'.self::openToken($messageUuid).'.gif';
    }

    public static function clickUrl(string $messageUuid, string $url): string
    {
        return self::baseUrl().'/public/communication/t/c/'.self::clickToken($messageUuid, $url);
    }

    // ---- sending side ------------------------------------------------------------------------------------------

    /**
     * Whether this message, sent over this channel, is tracked.
     */
    public static function isEnabledFor(Messages $message, Channels $channel, array $metadata): bool
    {
        if (! in_array($channel->type, self::TRACKED_CHANNEL_TYPES, true)) {
            return false;
        }

        if (array_key_exists('tracking', $metadata)) {
            return (bool) $metadata['tracking'];
        }

        $default = (bool) config('communication.tracking.enabled', env('COMMUNICATION_TRACKING_ENABLED', false));

        $account = $message->iam_account_id ? Accounts::withoutGlobalScopes()->find($message->iam_account_id) : null;

        return $account ? (bool) MetaHelper::get($account, 'email_tracking', $default) : $default;
    }

    /**
     * Rewrites the links of an HTML body to the click endpoint and appends the open pixel. Plain text is returned
     * unchanged (no links to rewrite, no place for a pixel).
     */
    public static function instrument(string $html, string $messageUuid): string
    {
        if (! preg_match('/<[a-z][\s\S]*>/i', $html)) {
            return $html;
        }

        $html = preg_replace_callback(
            '/<a\b([^>]*?\bhref\s*=\s*)(["\'])(.*?)\2/is',
            function ($m) use ($messageUuid) {
                $url = html_entity_decode($m[3], ENT_QUOTES);

                return self::shouldTrackUrl($url)
                    ? '<a'.$m[1].$m[2].htmlspecialchars(self::clickUrl($messageUuid, $url), ENT_QUOTES).$m[2]
                    : $m[0];
            },
            $html
        ) ?? $html;

        $pixel = '<img src="'.self::openUrl($messageUuid).'" width="1" height="1" alt="" style="display:none;border:0" />';

        return stripos($html, '</body>') !== false
            ? preg_replace('/<\/body>/i', $pixel.'</body>', $html, 1)
            : $html.$pixel;
    }

    /**
     * http(s) links only; never mailto:, tel:, anchors, scripts, our own tracking links or unsubscribe links.
     */
    public static function shouldTrackUrl(string $url): bool
    {
        $url = trim($url);

        if (! preg_match('#^https?://#i', $url)) {
            return false;
        }

        if (stripos($url, 'unsubscribe') !== false || stripos($url, '/public/communication/t/') !== false) {
            return false;
        }

        return true;
    }

    // ---- receiving side ----------------------------------------------------------------------------------------

    /**
     * Records an open. Always safe to call with a token that does not verify (nothing is recorded).
     */
    public static function recordOpen(string $token, ?string $userAgent, ?string $ip): bool
    {
        $payload = self::verify($token, 'o');

        return $payload ? self::record($payload['m'], 'opened', $userAgent, $ip) : false;
    }

    /**
     * Records a click and returns the URL to redirect to (the one signed into the token), or null.
     */
    public static function recordClick(string $token, ?string $userAgent, ?string $ip): ?string
    {
        $payload = self::verify($token, 'c');

        if (! $payload || empty($payload['u']) || ! preg_match('#^https?://#i', $payload['u'])) {
            return null;
        }

        self::record($payload['m'], 'clicked', $userAgent, $ip, ['url' => $payload['u']]);

        return $payload['u'];
    }

    public static function isLikelyBot(?string $userAgent, ?Messages $message = null): bool
    {
        if ($userAgent === null || $userAgent === '' || preg_match(self::BOT_PATTERN, $userAgent)) {
            return true;
        }

        return $message?->delivered_at && now()->diffInSeconds($message->delivered_at, true) <= self::PREFETCH_SECONDS;
    }

    /**
     * Engagement of a campaign: unique opens / clicks (a person counted once, bots excluded) and their rates over the
     * delivered messages.
     *
     * @return array{delivered: int, opened: int, clicked: int, open_rate: float, click_rate: float}
     */
    public static function campaignEngagement(int $campaignId): array
    {
        return self::engagement('communication_messages.crm_campaign_id', $campaignId);
    }

    /**
     * Engagement of a thread, same definitions as the campaign.
     */
    public static function threadEngagement(int $threadId): array
    {
        return self::engagement('communication_messages.communication_thread_id', $threadId);
    }

    /**
     * Open / click counts and first / last open of one message (bots excluded from the counts).
     *
     * @return array{open_count: int, click_count: int, first_opened_at: ?string, last_opened_at: ?string}
     */
    public static function messageEngagement(int $messageId): array
    {
        $rows = \DB::table('communication_message_events')
            ->where('communication_message_id', $messageId)
            ->whereIn('event_type', ['opened', 'clicked'])
            ->where(fn ($q) => $q->whereNull('metadata')->orWhereRaw("coalesce((metadata->>'likely_bot')::boolean, false) = false"))
            ->selectRaw("event_type, count(*) as total, min(occurred_at) as first_at, max(occurred_at) as last_at")
            ->groupBy('event_type')
            ->get()
            ->keyBy('event_type');

        return [
            'open_count' => (int) ($rows['opened']->total ?? 0),
            'click_count' => (int) ($rows['clicked']->total ?? 0),
            'first_opened_at' => $rows['opened']->first_at ?? null,
            'last_opened_at' => $rows['opened']->last_at ?? null,
        ];
    }

    private static function engagement(string $column, int $id): array
    {
        $messages = fn () => \DB::table('communication_messages')->where($column, $id)->where('direction', 1);

        $delivered = (clone $messages())->whereIn('status', ['delivered', 'read'])->count();

        $unique = fn (string $type) => \DB::table('communication_message_events as e')
            ->join('communication_messages', 'communication_messages.id', '=', 'e.communication_message_id')
            ->where($column, $id)
            ->where('e.event_type', $type)
            ->where(fn ($q) => $q->whereNull('e.metadata')->orWhereRaw("coalesce((e.metadata->>'likely_bot')::boolean, false) = false"))
            ->distinct()
            ->count('e.communication_message_id');

        $opened = $unique('opened');
        $clicked = $unique('clicked');

        return [
            'delivered' => $delivered,
            'opened' => $opened,
            'clicked' => $clicked,
            'open_rate' => $delivered ? round($opened / $delivered * 100, 2) : 0.0,
            'click_rate' => $delivered ? round($clicked / $delivered * 100, 2) : 0.0,
        ];
    }

    private static function record(string $messageUuid, string $type, ?string $userAgent, ?string $ip, array $extra = []): bool
    {
        try {
            return (bool) UserHelper::runAsAdmin(function () use ($messageUuid, $type, $userAgent, $ip, $extra) {
                $message = Messages::withoutGlobalScopes()->where('uuid', $messageUuid)->first();

                if (! $message) {
                    return false;
                }

                MessageEventsService::record($messageUuid, $type, array_merge($extra, [
                    'user_agent' => $userAgent ? mb_substr($userAgent, 0, 255) : null,
                    'ip' => $ip,
                    'likely_bot' => self::isLikelyBot($userAgent, $message),
                    'source' => 'tracking',
                ]));

                \NextDeveloper\Commons\Common\Cache\CacheHelper::deleteKeys('Messages', $messageUuid);

                return true;
            });
        } catch (\Throwable $e) {
            // A tracking hit must never break the page the recipient is looking at
            Log::warning('[EmailTrackingService] Could not record '.$type.': '.$e->getMessage());

            return false;
        }
    }

    // ---- helpers -----------------------------------------------------------------------------------------------

    private static function sign(array $payload): string
    {
        $body = self::b64e(json_encode($payload, JSON_UNESCAPED_SLASHES));

        return $body.'.'.self::signature($body);
    }

    private static function signature(string $body): string
    {
        return self::b64e(hash_hmac('sha256', $body, self::key(), true));
    }

    private static function key(): string
    {
        $key = (string) config('app.key');

        return str_starts_with($key, 'base64:') ? (string) base64_decode(substr($key, 7)) : $key;
    }

    private static function baseUrl(): string
    {
        return rtrim((string) config('communication.tracking.base_url', config('app.url')), '/');
    }

    private static function b64e(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private static function b64d(string $encoded): string|false
    {
        return base64_decode(strtr($encoded, '-_', '+/'));
    }
}
