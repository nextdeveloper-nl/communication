<?php

namespace NextDeveloper\Communication\Services\Support;

/**
 * Verifies the Meta webhook handshake and X-Hub-Signature-256 for the WhatsApp Business webhook.
 * One webhook URL serves every tenant's number; the signature is made with the Meta App secret, so a request is
 * authentic regardless of which tenant it is for. config communication.services.whatsapp.app_secret falls back to the
 * Facebook app secret since both normally belong to the same Meta App.
 */
class WhatsAppSignatureVerifier
{
    /**
     * @return string|null The challenge to echo back, or null if verification failed.
     */
    public static function verifyHandshake(?string $mode, ?string $verifyToken, ?string $challenge): ?string
    {
        if ($mode !== 'subscribe' || $challenge === null) {
            return null;
        }

        $expected = config('communication.services.whatsapp.verify_token');

        if (!$expected || !hash_equals((string) $expected, (string) $verifyToken)) {
            return null;
        }

        return $challenge;
    }

    /**
     * Must be checked against the exact raw bytes Meta signed.
     */
    public static function verifySignature(string $rawBody, ?string $signatureHeader): bool
    {
        if (!$signatureHeader || !str_starts_with($signatureHeader, 'sha256=')) {
            return false;
        }

        $appSecret = config('communication.services.whatsapp.app_secret')
            ?: config('communication.services.facebook.app_secret');

        if (!$appSecret) {
            return false;
        }

        return hash_equals('sha256=' . hash_hmac('sha256', $rawBody, $appSecret), $signatureHeader);
    }
}
