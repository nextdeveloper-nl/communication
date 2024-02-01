<?php

namespace NextDeveloper\Communication\Services\Delivery;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Communication\Database\Models\Emails;

/**
 * Class UzmanPosta
 *
 * This class is responsible for sending emails via UzmanPosta.
 */
class UzmanPosta
{

    /**
     * @var Client The HTTP client for making requests.
     */
    protected $client;

    /**
     * @var array The headers for API requests.
     */
    protected $header;

    /**
     * UzmanPosta constructor.
     *
     * Initializes the UzmanPosta service with the required API credentials.
     */
    public function __construct()
    {
        $apiToken = config('communication.services.uzman_posta.api_token');
        $apiUrl = config('communication.services.uzman_posta.api_url');

        $this->header = [
            'Authorization' => $apiToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $this->client = new Client([
            'base_uri' => $apiUrl,
        ]);
    }

    /**
     *
     * Make an HTTP request to UzmanPosta API.
     *
     * @param string $endpoint The API endpoint.
     * @param string $method The HTTP method (GET, POST, etc.).
     * @param array $data The data to be sent in the request.
     * @param bool $query Whether to send data as query parameters or JSON payload.
     *
     * @return mixed|null The response from the API or null if an error occurs.
     * @throws GuzzleException
     */
    public function request(string $endpoint, string $method, array $data, bool $query = false)
    {
        try {
            $options = [
                'headers' => $this->header,
                $query ? 'query' : 'json' => $data,
            ];

            $response = $this->client->request($method, $endpoint, $options);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->getContents());
            }

            Log::error("[UzmanPosta] " . $response->getBody()->getContents());
        } catch (\Exception $e) {
            Log::error("[UzmanPosta] " . $e->getMessage());
        }

        return null;
    }

    /**
     * Send an email using UzmanPosta service.
     *
     * @param Emails $email The email model containing email details.
     *
     * @return void
     * @throws GuzzleException
     */
    public function send(Emails $email): void
    {
        $options = [
            'from'                  => config('communication.from.email'),
            'to'                    => $this->prepareTo($email->to),
            'reply_to'              => config('communication.from.reply_to'),
            'cc'                    => $email->cc ?? [],
            'bcc'                   => $email->bcc ?? [],
            'subject'               => $email->subject,
            'text'                  => $email->body,
            'headers'               => [
                'header'            => $email->heades ?? [],
            ],
            'attachments'           => $email->attachments ?? [],
            'inline_attachments'    => []
        ];

        $response = $this->request('messages', 'POST', $options);

        if ($response) {
            $uuid = $this->extractUuidFromObject($response);
            $emailEvent = $this->getMailStatus($uuid);

            if ($emailEvent) {
                $email->update([
                    'delivered_at'      => now(),
                    'delivery_results'   => $emailEvent
                ]);
            }
        }
    }

    /**
     * Extract the UUID from the UzmanPosta API response object.
     *
     * @param object $object The UzmanPosta API response object.
     *
     * @return string The extracted UUID.
     */
    protected function extractUuidFromObject($object): string
    {
        preg_match('/"id": "(.*?)"/', $object->id, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }

        Log::error("[UzmanPosta] ID not found in response: " . json_encode($object));

        return '';
    }

    /**
     * Get the mail status from UzmanPosta based on UUID.
     *
     * @param string $uuid The UUID of the email.
     *
     * @return mixed|null The mail status from the API or null if an error occurs.
     * @throws GuzzleException
     */
    protected function getMailStatus(string $uuid)
    {
        $options = ['uuid' => $uuid];
        $response = $this->request('reports/mail/events', 'GET', $options, true);

        return $response ??  null;
    }

    /**
     * Prepare the 'to' field by extracting and formatting email addresses.
     *
     * @param string $to The 'to' field containing email addresses.
     *
     * @return array The formatted array of email addresses.
     */
    protected function prepareTo($to): array
    {
        return array_filter(array_map('trim', explode(',', str_replace(['{', '}'], '', $to))), 'strlen');
    }

}
