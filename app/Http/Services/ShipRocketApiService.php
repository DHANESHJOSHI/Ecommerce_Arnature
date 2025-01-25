<?php

declare(strict_types=1);

namespace App\Http\Services;

use GuzzleHttp\Client;

final class ShippingApiService
{
    private string $apiBaseUrl;
    private string $publicKey;
    private string $privateKey;
    private ?string $token = null;
    private Client $client;

    // public function __construct()
    // {
    //     // Initialize the Guzzle client
    //     $this->client = new Client([
    //         'base_uri' => 'https://shipping-api.com/app/api/v1/',
    //         'headers' => [
    //             'Content-Type' => 'application/json',
    //         ],
    //     ]);        
    // }

    public function __construct()
    {
        $this->apiBaseUrl = config('services.shipping_api.base_url');
        $this->client = new Client([
            'base_uri' => $this->apiBaseUrl,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }


    private function login(): void
    {
        try {
            $response = $this->client->post('/login', [
              'headers' => [
                    'public-key' => config('services.shipping_api.public_key'),
                    'private-key' => config('services.shipping_api.private_key'),
                ],
            ]);

            $this->token = json_decode($response->getBody()->getContents(), true)['token'];
        } catch (\Exception $e) {
            throw new \RuntimeException('Login failed: ' . $e->getMessage());
        }
    }

    public function checkPincodeServiceability(string $pickupPincode, string $deliveryPincode): array
    {
        $this->ensureAuthenticated();

        try {
            $response = $this->client->post('/pincode-serviceability', [
                'headers' => $this->getAuthHeaders(),
                'json' => [
                    'pickup_pincode' => $pickupPincode,
                    'delivery_pincode' => $deliveryPincode,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function calculateRate(array $rateData): array
    {
        $this->ensureAuthenticated();

        try {
            $response = $this->client->post('/rate-calculator', [
                'headers' => $this->getAuthHeaders(),
                'json' => $rateData,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function ensureAuthenticated(): void
    {
        if (!$this->token) {
            $this->login();
        }
    }

    private function getAuthHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }
}
