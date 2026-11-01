<?php

namespace NextDeveloper\Communication\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\Communication\Database\Models\Channels;

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
        $failed    = 0;

        Channels::withoutGlobalScopes()
            ->withTrashed()
            ->whereNotNull('credentials')
            ->chunkById(100, function ($channels) use (&$encrypted, &$skipped, &$failed) {
                foreach ($channels as $channel) {
                    $decoded = json_decode((string) $channel->getRawOriginal('credentials'), true);

                    // empty credentials or already {"_enc": ...}: nothing to do
                    if (empty($decoded) || (isset($decoded['_enc']) && count($decoded) === 1)) {
                        $skipped++;

                        continue;
                    }

                    if ($this->option('dry-run')) {
                        $encrypted++;

                        continue;
                    }

                    try {
                        // Migration of existing rows: written on the row that is already loaded (soft-deleted rows
                        // included, the service cannot find those); the model accessor encrypts the value.
                        $channel->credentials = $decoded;
                        $channel->saveQuietly();

                        // Only counted when the stored value really is the encrypted form now
                        if (!str_contains((string) $channel->fresh()->getRawOriginal('credentials'), '_enc')) {
                            throw new \RuntimeException('credentials still stored in plain text');
                        }

                        $encrypted++;
                    } catch (\Throwable $e) {
                        // one row must not stop the batch
                        $failed++;
                        $this->error('Channel ' . $channel->uuid . ': ' . $e->getMessage());
                    }
                }
            });

        $this->info(($this->option('dry-run') ? 'Would encrypt: ' : 'Encrypted: ') . $encrypted . ', already encrypted or empty: ' . $skipped . ', failed: ' . $failed);

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
