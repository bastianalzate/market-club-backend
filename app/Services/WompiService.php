<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WompiService
{
    private $baseUrl;
    private $publicKey;
    private $privateKey;
    private $isProduction;

    public function __construct()
    {
        $this->isProduction = config('services.wompi.production', false);
        $this->baseUrl = $this->isProduction 
            ? 'https://production.wompi.co/v1'
            : 'https://sandbox.wompi.co/v1';
        
        $this->publicKey = config('services.wompi.public_key');
        $this->privateKey = config('services.wompi.private_key');
    }

    /**
     * Crear una transacción de pago
     */
    public function createTransaction(array $data)
    {
        try {
            $payload = [
                'amount_in_cents' => $data['amount_in_cents'],
                'currency' => $data['currency'] ?? 'COP',
                'customer_email' => $data['customer_email'],
                'payment_method' => [
                    'type' => $data['payment_method_type'] ?? 'CARD',
                    'installments' => $data['installments'] ?? 1,
                ],
                'reference' => $data['reference'],
                'customer_data' => [
                    'email' => $data['customer_email'],
                    'full_name' => $data['customer_name'] ?? '',
                    'phone_number' => $data['customer_phone'] ?? '',
                ],
                'shipping_address' => $data['shipping_address'] ?? null,
                'redirect_url' => $data['redirect_url'] ?? null,
            ];

            // Si es pago con tarjeta, agregar token
            if ($data['payment_method_type'] === 'CARD' && isset($data['payment_token'])) {
                $payload['payment_method']['payment_token'] = $data['payment_token'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transactions', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
            ];

        } catch (Exception $e) {
            Log::error('Wompi transaction error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => ['message' => 'Error interno del servidor'],
            ];
        }
    }

    /**
     * Obtener información de una transacción
     */
    public function getTransaction(string $transactionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
            ])->get($this->baseUrl . '/transactions/' . $transactionId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
            ];

        } catch (Exception $e) {
            Log::error('Wompi get transaction error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => ['message' => 'Error interno del servidor'],
            ];
        }
    }

    /**
     * Crear un token de pago para tarjetas
     */
    public function createPaymentToken(array $cardData)
    {
        try {
            $payload = [
                'number' => $cardData['number'],
                'cvc' => $cardData['cvc'],
                'exp_month' => $cardData['exp_month'],
                'exp_year' => $cardData['exp_year'],
                'card_holder' => $cardData['card_holder'],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->publicKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/tokens/cards', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
            ];

        } catch (Exception $e) {
            Log::error('Wompi token creation error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => ['message' => 'Error interno del servidor'],
            ];
        }
    }

    /**
     * Verificar la firma de un webhook
     */
    public function verifyWebhookSignature(string $signature, string $payload)
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->privateKey);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Obtener métodos de pago disponibles
     */
    public function getPaymentMethods()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->publicKey,
            ])->get($this->baseUrl . '/merchants/' . $this->publicKey);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
            ];

        } catch (Exception $e) {
            Log::error('Wompi get payment methods error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => ['message' => 'Error interno del servidor'],
            ];
        }
    }

    /**
     * Convertir pesos a centavos
     */
    public function convertToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Convertir centavos a pesos
     */
    public function convertFromCents(int $cents): float
    {
        return $cents / 100;
    }

    /**
     * Generar referencia única para la transacción
     */
    public function generateReference(string $prefix = 'ORDER'): string
    {
        return $prefix . '_' . time() . '_' . rand(1000, 9999);
    }
}
