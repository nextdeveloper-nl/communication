<?php

namespace NextDeveloper\Communication\Services\SmsDelivery;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Twillio
{
    /**
     * Twilio API base URL
     */
    protected const API_BASE_URL = 'https://api.twilio.com/2010-04-01';

    /**
     * Send SMS using Twilio API
     *
     * @param string $sms The message content to send
     * @param string $to The recipient phone number (E.164 format)
     * @param string|null $from The sender phone number (defaults to config value)
     * @return array Response from Twilio API
     * @throws Exception If the message fails to send
     */
    public static function send(string $sms, string $to, ?string $from = null, ?string $accountSid = null, ?string $authToken = null)
    {
        // A channel can carry its own Twilio account; without one the platform account is used
        if (empty($accountSid) || empty($authToken)) {
            $accountSid = config('communication.services.twilio.sid');
            $authToken = config('communication.services.twilio.token');
        }

        $defaultFrom = config('communication.services.twilio.from');

        if (empty($accountSid) || empty($authToken)) {
            Log::error(__METHOD__ . ': Twilio credentials not configured');
            throw new \RuntimeException('Twilio credentials not configured. Please set TWILIO_SID and TWILIO_TOKEN in your environment.');
        }

        $fromNumber = $from ?? $defaultFrom;

        if (empty($fromNumber)) {
            Log::error(__METHOD__ . ': Twilio sender number not configured');
            throw new \RuntimeException('Twilio sender number not configured. Please set TWILIO_FROM in your environment.');
        }

        // Ensure phone numbers are in E.164 format
        $to = self::formatPhoneNumber($to);
        $fromNumber = self::formatPhoneNumber($fromNumber);

        $url = self::API_BASE_URL . "/Accounts/{$accountSid}/Messages.json";

        try {
            $response = Http::withBasicAuth($accountSid, $authToken)
                ->asForm()
                ->post($url, [
                    'To' => $to,
                    'From' => $fromNumber,
                    'Body' => $sms,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info(__METHOD__ . ': SMS sent successfully via Twilio', [
                    'sid' => $data['sid'] ?? null,
                    'to' => $to,
                    'status' => $data['status'] ?? null,
                ]);

                return $data;
            }

            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? 'Unknown error occurred';
            $errorCode = $errorData['code'] ?? $response->status();

            Log::error(__METHOD__ . ': Twilio API error', [
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
                'to' => $to,
            ]);

            throw new \RuntimeException("Twilio API error ({$errorCode}): {$errorMessage}");
        } catch (Exception $e) {
            Log::error(__METHOD__ . ': Failed to send SMS via Twilio', [
                'error' => $e->getMessage(),
                'to' => $to,
            ]);
            throw $e;
        }
    }

    /**
     * Format phone number to E.164 format
     *
     * @param string $phoneNumber
     * @return string
     */
    protected static function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters except the leading +
        $cleaned = preg_replace('/[^\d+]/', '', $phoneNumber);

        // Ensure it starts with +
        if (!str_starts_with($cleaned, '+')) {
            $cleaned = '+' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Asks Twilio for the account with the given (or the platform's) SID and token, without sending anything.
     *
     * @return array<string, mixed> The Twilio account resource
     *
     * @throws \RuntimeException With Twilio's own error text when the credentials are refused
     */
    public static function verifyAccount(?string $accountSid = null, ?string $authToken = null): array
    {
        if (empty($accountSid) || empty($authToken)) {
            $accountSid = config('communication.services.twilio.sid');
            $authToken = config('communication.services.twilio.token');
        }

        if (empty($accountSid) || empty($authToken)) {
            throw new \RuntimeException('Twilio credentials not configured (account SID and auth token).');
        }

        $response = Http::timeout(15)
            ->withBasicAuth($accountSid, $authToken)
            ->get(self::API_BASE_URL . '/Accounts/' . rawurlencode($accountSid) . '.json');

        if (!$response->successful()) {
            throw new \RuntimeException(sprintf(
                'Twilio API error (%s): %s',
                $response->json('code') ?? $response->status(),
                $response->json('message') ?? 'credentials refused'
            ));
        }

        if (($response->json('status') ?? 'active') !== 'active') {
            throw new \RuntimeException('Twilio account is ' . $response->json('status') . '.');
        }

        return (array) $response->json();
    }

    /**
     * Validate if Twilio is properly configured
     *
     * @return bool
     */
    public static function isConfigured(): bool
    {
        return !empty(config('communication.services.twilio.sid'))
            && !empty(config('communication.services.twilio.token'))
            && !empty(config('communication.services.twilio.from'));
    }

    /**
     * Get the status of a scent message
     *
     * @param string $messageSid The Twilio message SID
     * @return array
     * @throws Exception
     */
    public static function getMessageStatus(string $messageSid): array
    {
        $accountSid = config('communication.services.twilio.sid');
        $authToken = config('communication.services.twilio.token');

        if (empty($accountSid) || empty($authToken)) {
            throw new \RuntimeException('Twilio credentials not configured');
        }

        $url = self::API_BASE_URL . "/Accounts/{$accountSid}/Messages/{$messageSid}.json";

        $response = Http::withBasicAuth($accountSid, $authToken)->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \RuntimeException('Failed to get message status: ' . ($response->json()['message'] ?? 'Unknown error'));
    }
}
