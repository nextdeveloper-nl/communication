<?php

namespace NextDeveloper\Communication\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Communication\Database\Models\SmtpServers;
use NextDeveloper\Communication\Services\AbstractServices\AbstractSmtpServersService;

/**
 * This class is responsible from managing the data for SmtpServers
 *
 * Class SmtpServersService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class SmtpServersService extends AbstractSmtpServersService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Tests the SMTP connection and updates verification status fields.
     * The model's 'encrypted' cast on password handles encrypt/decrypt transparently.
     */
    public static function verify(string $ref): bool
    {
        $server = self::getByRef($ref);

        try {
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                $server->host,
                $server->port,
                $server->encryption === 'tls'
            );

            $transport->setUsername($server->username);
            $transport->setPassword($server->password);
            $transport->start();

            self::update($server->uuid, [
                'is_verified'        => true,
                'verified_at'        => now(),
                'last_checked_at'    => now(),
                'last_check_status'  => 'ok',
                'last_check_message' => 'Connection successful',
            ]);

            return true;
        } catch (Exception $e) {
            self::update($server->uuid, [
                'last_checked_at'    => now(),
                'last_check_status'  => 'error',
                'last_check_message' => $e->getMessage(),
            ]);

            Log::error('[SmtpServersService::verify] SMTP connection failed', [
                'server_id' => $server->id,
                'host'      => $server->host,
                'error'     => $e->getMessage(),
            ]);

            return false;
        }
    }
}