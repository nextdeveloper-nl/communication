<?php

namespace NextDeveloper\Communication\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class AIService
{

    protected Client $client;

    /**
     * @throws \Exception
     */
    public function __construct()
    {

        $aiUrl = config('communication.services.ai.api_url');
        $token = config('communication.services.ai.token');

        if (!$aiUrl || !$token) {
            throw new \Exception('AI service is not configured');
        }

        $this->client = new Client([
            'base_uri' => $aiUrl,
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);
    }


    public function createSession($userId = null)
    {
        try {
            $response = $this->client->post('sessions', [
                'json' => [
                    'title'         => 'Telegram Session',
                    'description'   => 'Session for Telegram',
                    'iam_user_id'   => $userId,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error("[Communication::AIService] Error creating session: " . $e->getMessage());
            return null;
        }


    }

    /**
     * @throws GuzzleException
     */
    public function sendMessage($sessionId, $message)
    {

        $response = $this->client->post("conversations/$sessionId", [
            'json' => [
                'message' => $message,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
