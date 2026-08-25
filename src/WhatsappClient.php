<?php

namespace Devaspid\WhatsappGateway;

use Devaspid\WhatsappGateway\Exceptions\AuthenticationException;
use Devaspid\WhatsappGateway\Exceptions\DeviceNotFoundException;
use Devaspid\WhatsappGateway\Exceptions\GatewayConnectionException;
use Devaspid\WhatsappGateway\Exceptions\RateLimitException;
use Devaspid\WhatsappGateway\Exceptions\ValidationException;
use Devaspid\WhatsappGateway\Exceptions\WhatsappGatewayException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class WhatsappClient
{
    protected PendingRequest $http;

    public function __construct(protected array $config)
    {
        $this->http = $this->buildHttpClient();
    }

    protected function buildHttpClient(): PendingRequest
    {
        $client = Http::baseUrl($this->config['base_url'])
            ->withHeaders([
                'X-Client-Id' => $this->config['client_id'],
                'X-Api-Key'   => $this->config['api_key'],
                'Accept'      => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout($this->config['timeout'] ?? 15);

        if (($this->config['retry']['times'] ?? 0) > 0) {
            $client = $client->retry(
                $this->config['retry']['times'],
                $this->config['retry']['sleep'] ?? 500,
                fn (\Exception $e) => $e instanceof ConnectionException,
            );
        }

        return $client;
    }

    public function get(string $endpoint, array $query = []): array
    {
        try {
            $response = $this->http->get($endpoint, $query);
        } catch (ConnectionException $e) {
            throw new GatewayConnectionException($e->getMessage());
        }

        return $this->handleResponse($response);
    }

    public function post(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->http->post($endpoint, $data);
        } catch (ConnectionException $e) {
            throw new GatewayConnectionException($e->getMessage());
        }

        return $this->handleResponse($response);
    }

    public function delete(string $endpoint): array
    {
        try {
            $response = $this->http->delete($endpoint);
        } catch (ConnectionException $e) {
            throw new GatewayConnectionException($e->getMessage());
        }

        return $this->handleResponse($response);
    }

    protected function handleResponse(\Illuminate\Http\Client\Response $response): array
    {
        $status = $response->status();
        $body   = $response->json() ?? [];

        return match (true) {
            $response->successful()  => $body,
            $status === 401          => throw new AuthenticationException(),
            $status === 404          => throw new DeviceNotFoundException($body['message'] ?? 'Resource tidak ditemukan'),
            $status === 422          => throw ValidationException::fromErrors($body['errors'] ?? ['message' => [$body['message'] ?? 'Validasi gagal']]),
            $status === 429          => throw new RateLimitException(),
            in_array($status, [502, 503]) => throw new GatewayConnectionException($body['message'] ?? 'WhatsApp engine error.'),
            default                  => throw WhatsappGatewayException::fromResponse(
                $status,
                $body['message'] ?? "HTTP error {$status}",
                $body['errors'] ?? null
            ),
        };
    }

    /**
     * Mengembalikan instance HTTP client mentah untuk kebutuhan kustom.
     */
    public function raw(): PendingRequest
    {
        return $this->http;
    }
}
