<?php

declare(strict_types=1);

namespace App\Http\Services;

use GuzzleHttp\Client;

final class ShipRocketApiService
{
    private string $apiTokenUrl;
    private string $email;
    private string $password;
    private ?string $token = null;
    private Client $client;

    public function __construct()
    {
         // Initialize the Guzzle client
         $this->client = new Client([
            'base_uri' => 'https://apiv2.shiprocket.in/v1/external/',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
        $this->email = config('shipRocket.email');
        $this->password = config('shipRocket.password');
        $this->apiTokenUrl = config('shipRocket.generate_token');
        $this->generateToken();
    }

    private function generateToken()
    {
        $data = [
            'email' => $this->email,
            'password' => $this->password
        ];

        try {
            $response = $this->client->post($this->apiTokenUrl, [
                'json' => $data
            ]);
            
            $this->token = json_decode($response->getBody()->getContents(), true)['token'];
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function createOrder(array $orderData): array
    {
        $url = 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc';

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                ],
                'json' => $orderData,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    public function calculateShippingCharge(string $pickupPincode, string $deliveryPincode, float $weight, bool $isCod = false): array
    {
        $url = 'https://apiv2.shiprocket.in/v1/external/courier/serviceability/';

        $payload = [
            'pickup_postcode' => $pickupPincode,
            'delivery_postcode' => $deliveryPincode,
            'cod' => $isCod,
            'weight' => $weight,
        ];

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                ],
                'json' => $payload,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
