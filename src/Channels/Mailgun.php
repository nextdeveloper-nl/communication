<?php

namespace NextDeveloper\Communication\Channels;

use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\Channels;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Mailgun Channel Implementation
 *
 * Delivers messages through Mailgun's HTTP API using Symfony's Mailgun
 * transport (symfony/mailgun-mailer), which ships with the application.
 *
 * Credentials live on the channel rather than in config, so each account can
 * send from its own Mailgun domain:
 *   configuration: domain, region (us|eu, optional), from_address, from_name
 *   credentials:   api_key
 *
 * The api_key is read from `credentials` rather than `configuration` so it is
 * kept with the other secrets on the channel, matching how the Gmail channel
 * stores its tokens.
 */
class Mailgun implements ChannelAbstract
{
    public const NAME = 'mailgun';

    public const FIELDS = [
        'domain' => 'required',
        'api_key' => 'required',
        'region' => 'nullable',
        'from_address' => 'nullable',
        'from_name' => 'nullable',
        'max_messages_per_hour' => 'nullable',
    ];

    private Mailer $mailer;

    private string $fromAddress;

    private string $fromName;

    public function __construct(public readonly Channels $channel)
    {
        $config = self::asArray($channel->configuration);
        $credentials = self::asArray($channel->credentials);

        // Accept the key from either bag: older rows kept it in configuration.
        $apiKey = $credentials['api_key'] ?? $config['api_key'] ?? null;
        $domain = $config['domain'] ?? null;

        if (empty($apiKey) || empty($domain)) {
            throw new InvalidArgumentException(
                __METHOD__.': Missing required Mailgun configuration (domain, api_key).'
            );
        }

        $this->fromAddress = $config['from_address'] ?? ('postmaster@'.$domain);
        $this->fromName = $config['from_name'] ?? '';

        // Mailgun keeps EU customers on a separate host; the transport selects
        // it from the region parameter rather than a different DSN scheme.
        $region = strtolower($config['region'] ?? 'us');

        $dsn = sprintf(
            'mailgun+https://%s:%s@default%s',
            urlencode($apiKey),
            urlencode($domain),
            $region === 'eu' ? '?region=eu' : ''
        );

        $this->mailer = new Mailer(Transport::fromDsn($dsn));
    }

    public function send(mixed $message): void
    {
        try {
            $email = (new Email())
                ->from(new Address($this->fromAddress, $this->fromName))
                ->to($message['to'])
                ->subject($message['subject'] ?? '(no subject)')
                ->html($message['message'] ?? $message['body'] ?? '');

            $ccs = array_filter((array) ($message['cc'] ?? []));

            if (! empty($ccs)) {
                $email->cc(...array_map(fn ($addr) => new Address($addr), $ccs));
            }

            $this->mailer->send($email);
        } catch (Exception $e) {
            Log::error(__METHOD__.': Mailgun delivery failed', [
                'to' => $message['to'] ?? null,
                'domain' => $this->channel->configuration['domain'] ?? null,
                'error' => $e->getMessage(),
            ]);

            throw new Exception(__METHOD__.': Failed to send via Mailgun: '.$e->getMessage());
        }
    }

    public function validateConfig(array $config): bool
    {
        return ! empty($config['domain']);
    }

    /**
     * Channel JSON columns come back as arrays, but rows written as text can
     * still arrive double-encoded.
     *
     * @return array<string, mixed>
     */
    private static function asArray(mixed $value): array
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return (array) ($value ?? []);
    }
}
