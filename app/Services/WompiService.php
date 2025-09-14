<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WompiService
{
    private $publicKey;
    private $privateKey;
    private $baseUrl;
    private $isProduction;

    public function __construct()
    {
        $this->publicKey = config('services.wompi.public_key');
        $this->privateKey = config('services.wompi.private_key');
        $this->isProduction = config('services.wompi.production', false);
        $this->baseUrl = $this->isProduction 
            ? 'https://production.wompi.co/v1' 
            : 'https://sandbox.wompi.co/v1';
    }

    /**
     * Crear token de pago para tarjeta
     */
    public function createPaymentToken(array $cardData): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->publicKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/tokens/cards', [
                'number' => $cardData['number'],
                'cvc' => $cardData['cvc'],
                'exp_month' => $cardData['exp_month'],
                'exp_year' => $cardData['exp_year'],
                'card_holder' => $cardData['card_holder'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data['data'],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error desconocido',
            ];

        } catch (\Exception $e) {
            Log::error('Wompi token creation error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error interno del servidor',
            ];
        }
    }

    /**
     * Crear token de pago para otros métodos (Nequi, PSE, etc.)
     */
    public function createPaymentTokenForMethod(array $paymentData): array
    {
        try {
            // Para métodos que no requieren token (Nequi, PSE, etc.)
            // Simulamos un token temporal con formato válido
            $methodType = $paymentData['payment_method']['type'];
            $tokenId = 'tok_' . strtolower($methodType) . '_' . time() . '_' . rand(1000, 9999);
            
            return [
                'success' => true,
                'data' => [
                    'id' => $tokenId,
                    'type' => $methodType,
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Wompi token creation error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error interno del servidor',
            ];
        }
    }

    /**
     * Crear transacción de pago
     */
    public function createTransaction(array $paymentData): array
    {
        try {
            // Generar firma de integridad ANTES de agregar signature
            $signature = $this->generateSignature($paymentData);
            
            // Agregar signature a los datos
            $paymentData['signature'] = $signature;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transactions', $paymentData);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error desconocido',
            ];

        } catch (\Exception $e) {
            Log::error('Wompi transaction creation error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error interno del servidor',
            ];
        }
    }

    /**
     * Obtener información de una transacción
     */
    public function getTransaction(string $transactionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->publicKey,
            ])->get($this->baseUrl . '/transactions/' . $transactionId);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Transacción no encontrada',
            ];

        } catch (\Exception $e) {
            Log::error('Wompi transaction get error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error interno del servidor',
            ];
        }
    }

    /**
     * Obtener métodos de pago disponibles
     */
    public function getPaymentMethods(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->publicKey,
            ])->get($this->baseUrl . '/merchants/' . $this->publicKey);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => [
                        'acceptance_token' => $data['data']['presigned_acceptance']['acceptance_token'],
                    ],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error al obtener métodos de pago',
            ];

        } catch (\Exception $e) {
            Log::error('Wompi payment methods error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error interno del servidor',
            ];
        }
    }

    /**
     * Verificar firma del webhook
     */
    public function verifyWebhookSignature(string $signature, string $payload): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->privateKey);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Convertir pesos a centavos
     */
    public function convertToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Generar referencia única
     */
    public function generateReference(string $prefix): string
    {
        return $prefix . '_' . time() . '_' . rand(1000, 9999);
    }

    /**
     * Generar firma de integridad para Wompi
     */
    private function generateSignature(array $paymentData): string
    {
        // Crear string de datos para la firma (sin signature)
        $signatureData = [
            $paymentData['reference'],
            $paymentData['amount_in_cents'],
            $paymentData['currency'],
            $this->privateKey,
        ];

        $signatureString = implode('', $signatureData);
        
        // Generar hash SHA256
        return hash('sha256', $signatureString);
    }
}