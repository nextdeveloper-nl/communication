<?php

namespace NextDeveloper\Communication\Services\Support;

/**
 * Verifies the Meta (Facebook) webhook handshake and per-request signature.
 * Shared by the Messenger and Lead Ads ingestion flows since both are delivered
 * through the same Page webhook subscription and the same App Secret.
 */
class FacebookSignatureVerifier
{
    /**
     * Confirms Meta's webhook verification handshake (GET request with
     * hub.mode / hub.verify_token / hub.challenge query params).
     *
     * @return string|null The challenge to echo back, or null if verification failed.
     */
    public static function verifyHandshake(?string $mode, ?string $verifyToken, ?string $challenge): ?string
    {
        if ($mode !== 'subscribe' || $challenge === null) {
            return null;
        }

        $expected = config('communication.services.facebook.verify_token');

        if (!$expected || !hash_equals((string) $expected, (string) $verifyToken)) {
            return null;
        }

        return $challenge;
    }

    /**
     * Verifies the X-Hub-Signature-256 header against the raw request body.
     * Must be checked against the exact raw bytes Meta signed — a re-encoded
     * JSON body will not match even when semantically identical.
     */
    public static function verifySignature(string $rawBody, ?string $signatureHeader): bool
    {
        if (!$signatureHeader || !str_starts_with($signatureHeader, 'sha256=')) {
            return false;
        }

        $appSecret = config('communication.services.facebook.app_secret');

        if (!$appSecret) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $appSecret);

        return hash_equals($expected, $signatureHeader);
    }
}
