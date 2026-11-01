<?php

namespace NextDeveloper\Communication\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\ChannelsService;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Encrypts the channel credentials (OAuth tokens, API keys) that were stored in plain text before the model
 * encrypted them. Safe to run more than once: credentials that are already encrypted (or empty) are skipped.
 */
class EncryptChannelCredentialsCommand extends Command
{
    protected $signature = 'communication:encrypt-channel-credentials {--dry-run : Only count what would be encrypted}';

    protected $description = 'Encrypts plain-text credentials stored in communication_channels.';

    public function handle(): int
    {
        $encrypted = 0;
        $skipped   = 0;

        Channels::withoutGlobalScopes()
            ->withTrashed()
            ->whereNotNull('credentials')
            ->chunkById(100, function ($channels) use (&$encrypted, &$skipped) {
                foreach ($channels as $channel) {
                    $decoded = json_decode((string) $channel->getRawOriginal('credentials'), true);

                    // empty credentials or already {"_enc": ...}: nothing to do
                    if (empty($decoded) || (isset($decoded['_enc']) && count($decoded) === 1)) {
                        $skipped++;

                        continue;
                    }

                    if (!$this->option('dry-run')) {
                        // Written through the service, the model accessor encrypts the value
                        UserHelper::runAsAdmin(fn () => ChannelsService::update($channel->uuid, ['credentials' => $decoded]));
                    }

                    $encrypted++;
                }
            });

        $this->info(($this->option('dry-run') ? 'Would encrypt: ' : 'Encrypted: ') . $encrypted . ', already encrypted or empty: ' . $skipped);

        return self::SUCCESS;
    }
}
