<?php

namespace NextDeveloper\Communication\Channels;

use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * SMTP Channel Implementation
 *
 * Delivers messages through a configurable SMTP server using Symfony Mailer.
 * Configuration is read from communication_channels.configuration:
 *   host, port, encryption (tls|ssl|none), username, password,
 *   from_address (optional), from_name (optional), timeout (optional).
 */
class Smtp implements ChannelAbstract
{
    public const NAME = 'smtp';

    public const FIELDS = [
        'host'       => 'required',
        'port'       => 'required',
        'encryption' => 'required',
        'username'   => 'required',
        'password'   => 'required',
        'from_address' => 'nullable',
        'from_name'    => 'nullable',
        'timeout'      => 'nullable',
        'max_messages_per_hour' => 'nullable',
    ];

    private Mailer $mailer;
    private string $fromAddress;
    private string $fromName;

    public function __construct(public readonly \NextDeveloper\Communication\Database\Models\Channels $channel)
    {
        $config = $channel->configuration ?? [];

        if (!$this->validateConfig($config)) {
            throw new InvalidArgumentException(__METHOD__ . ': Missing required SMTP configuration fields (host, port, encryption, username, password).');
        }

        $host       = $config['host'];
        $port       = (int) $config['port'];
        $encryption = strtolower($config['encryption'] ?? 'tls');
        $username   = $config['username'];
        $password   = $config['password'];
        $timeout    = (int) ($config['timeout'] ?? 30);

        $this->fromAddress = $config['from_address'] ?? $username;
        $this->fromName    = $config['from_name'] ?? '';

        // Build DSN: smtp[s]://user:pass@host:port
        $scheme = match ($encryption) {
            'ssl'  => 'smtps',
            'none' => 'smtp',
            default => 'smtp',  // tls uses STARTTLS on plain smtp scheme
        };

        $dsn = sprintf(
            '%s://%s:%s@%s:%d?timeout=%d',
            $scheme,
            urlencode($username),
            urlencode($password),
            $host,
            $port,
            $timeout
        );

        $transport   = Transport::fromDsn($dsn);
        $this->mailer = new Mailer($transport);
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
            if (!empty($ccs)) {
                $email->cc(...array_map(fn ($addr) => new Address($addr), $ccs));
            }

            $this->mailer->send($email);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ': SMTP delivery failed', [
                'to'    => $message['to'] ?? null,
                'error' => $e->getMessage(),
            ]);

            throw new Exception(__METHOD__ . ': Failed to send via SMTP: ' . $e->getMessage());
        }
    }

    public function validateConfig(array $config): bool
    {
        foreach (['host', 'port', 'username', 'password'] as $field) {
            if (empty($config[$field])) {
                return false;
            }
        }

        return true;
    }
}
