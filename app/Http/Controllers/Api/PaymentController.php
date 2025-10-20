<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WompiService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $wompiService;
    protected $emailService;

    public function __construct(WompiService $wompiService, EmailService $emailService)
    {
        $this->wompiService = $wompiService;
        $this->emailService = $emailService;
    }

    /**
     * Crear un token de pago para tarjeta
     */
    public function createPaymentToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|string|size:16',
            'cvc' => 'required|string|between:3,4',
            'exp_month' => 'required|string|size:2',
            'exp_year' => 'required|string|size:2',
            'card_holder' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de tarjeta inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->wompiService->createPaymentToken($request->all());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al crear token de pago',
            'error' => $result['error'],
        ], 400);
    }

    /**
     * Procesar pago de una orden
     */
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_method_type' => 'required|in:CARD,PSE,NEQUI',
            'payment_token' => 'required_if:payment_method_type,CARD|string',
            'installments' => 'integer|min:1|max:36',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de pago inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $order = Order::with(['user', 'orderItems.product'])->findOrFail($request->order_id);

            // Verificar que la orden pertenece al usuario autenticado
            if ($order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para pagar esta orden',
                ], 403);
            }

            // Verificar que la orden no esté ya pagada
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta orden ya ha sido pagada',
                ], 400);
            }

            // Preparar datos para Wompi
            $paymentData = [
                'amount_in_cents' => $this->wompiService->convertToCents($order->total_amount),
                'currency' => 'COP',
                'customer_email' => $order->user->email,
                'customer_name' => $order->user->name,
                'customer_phone' => $order->user->phone ?? '',
                'payment_method_type' => $request->payment_method_type,
                'installments' => $request->installments ?? 1,
                'reference' => $this->wompiService->generateReference('ORDER_' . $order->id),
                'redirect_url' => config('app.frontend_url') . '/payment/result',
                'shipping_address' => $order->shipping_address,
            ];

            // Agregar token de pago si es tarjeta
            if ($request->payment_method_type === 'CARD' && $request->payment_token) {
                $paymentData['payment_token'] = $request->payment_token;
            }

            // Crear transacción en Wompi
            $result = $this->wompiService->createTransaction($paymentData);

            if ($result['success']) {
                $transaction = $result['data']['data'];

                // Actualizar orden con información de pago
                $oldStatus = $order->status;
                $order->update([
                    'payment_method' => $request->payment_method_type,
                    'payment_reference' => $transaction['id'],
                    'payment_status' => $transaction['status'] === 'APPROVED' ? 'paid' : 'pending',
                    'status' => $transaction['status'] === 'APPROVED' ? 'processing' : 'pending',
                ]);

                // Enviar email según el estado del pago
                if ($transaction['status'] === 'APPROVED') {
                    // Pago exitoso - enviar confirmación
                    try {
                        $this->emailService->sendOrderConfirmation($order);
                        Log::info("Order confirmation email sent for order {$order->id} with status: {$transaction['status']}");
                    } catch (\Exception $e) {
                        Log::error("Failed to send order confirmation email for order {$order->id}: " . $e->getMessage());
                    }
                    
                    // También enviar email de confirmación de pago
                    $this->emailService->sendPaymentConfirmation($order);
                } elseif ($transaction['status'] === 'DECLINED') {
                    // Pago rechazado - enviar email de pago fallido
                    try {
                        $this->emailService->sendPaymentFailed($order);
                        Log::info("Payment failed email sent for order {$order->id} with status: {$transaction['status']}");
                    } catch (\Exception $e) {
                        Log::error("Failed to send payment failed email for order {$order->id}: " . $e->getMessage());
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Pago procesado exitosamente',
                    'data' => [
                        'transaction_id' => $transaction['id'],
                        'status' => $transaction['status'],
                        'payment_url' => $transaction['payment_url'] ?? null,
                        'order' => $order->fresh(),
                    ],
                ]);
            }

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago',
                'error' => $result['error'],
            ], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Verificar estado de una transacción
     */
    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ID de transacción requerido',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->wompiService->getTransaction($request->transaction_id);

        if ($result['success']) {
            $transaction = $result['data']['data'];

            // Buscar orden por referencia de pago
            $order = Order::where('payment_reference', $request->transaction_id)->first();

            if ($order) {
                // Actualizar estado de la orden según el estado de la transacción
                $paymentStatus = $this->mapWompiStatusToOrderStatus($transaction['status']);
                $orderStatus = $paymentStatus === 'paid' ? 'processing' : 'pending';

                $order->update([
                    'payment_status' => $paymentStatus,
                    'status' => $orderStatus,
                ]);

                // Enviar email según el estado del pago
                if ($paymentStatus === 'paid') {
                    // Pago exitoso - enviar confirmación
                    try {
                        $this->emailService->sendOrderConfirmation($order);
                        Log::info("Order confirmation email sent for order {$order->id} with status: {$paymentStatus}");
                    } catch (\Exception $e) {
                        Log::error("Failed to send order confirmation email for order {$order->id}: " . $e->getMessage());
                    }
                } elseif ($paymentStatus === 'failed') {
                    // Pago rechazado - enviar email de pago fallido
                    try {
                        $this->emailService->sendPaymentFailed($order);
                        Log::info("Payment failed email sent for order {$order->id} with status: {$paymentStatus}");
                    } catch (\Exception $e) {
                        Log::error("Failed to send payment failed email for order {$order->id}: " . $e->getMessage());
                    }
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'transaction' => $transaction,
                        'order' => $order->fresh(),
                    ],
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Transacción no encontrada',
            'error' => $result['error'] ?? null,
        ], 404);
    }

    /**
     * Webhook para recibir notificaciones de Wompi
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('X-Signature');
        $payload = $request->getContent();

        // Verificar firma del webhook (temporalmente deshabilitado para testing)
        if ($signature && !$this->wompiService->verifyWebhookSignature($signature, $payload)) {
            Log::warning('Invalid Wompi webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();

        try {
            DB::beginTransaction();

            $transactionId = $data['data']['id'];
            $status = $data['data']['status'];

            // Buscar orden por referencia de pago
            $order = Order::where('payment_reference', $transactionId)->first();

            if ($order) {
                $paymentStatus = $this->mapWompiStatusToOrderStatus($status);
                $orderStatus = $paymentStatus === 'paid' ? 'processing' : 'pending';

                $order->update([
                    'payment_status' => $paymentStatus,
                    'status' => $orderStatus,
                    'wompi_transaction_id' => $transactionId,
                    'payment_reference' => $data['data']['reference'] ?? null,
                ]);

                // Crear o actualizar transacción de pago
                $this->createOrUpdatePaymentTransaction($order, $data, $status);

                // Enviar email de confirmación si el pago fue exitoso
                if ($paymentStatus === 'paid') {
                    try {
                        $this->emailService->sendOrderConfirmation($order);
                        $this->emailService->sendPaymentConfirmation($order);
                    } catch (\Exception $e) {
                        Log::error("Failed to send confirmation emails for order {$order->id}: " . $e->getMessage());
                    }
                }

                Log::info("Order {$order->id} payment status updated to {$paymentStatus}");
            }

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook processing error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Obtener métodos de pago disponibles
     */
    public function getPaymentMethods()
    {
        $result = $this->wompiService->getPaymentMethods();

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al obtener métodos de pago',
            'error' => $result['error'],
        ], 400);
    }

    /**
     * Obtener tipo de método de pago para Wompi
     */
    private function getPaymentMethodType($paymentMethodType)
    {
        switch ($paymentMethodType) {
            case 'card':
                return 'CARD';
            case 'nequi':
                return 'NEQUI';
            case 'bancolombia_transfer':
                return 'BANCOLOMBIA_TRANSFER';
            case 'pse':
                return 'PSE';
            default:
                return 'CARD';
        }
    }

    /**
     * Crear objeto de método de pago según el tipo
     */
    private function createPaymentMethodObject($paymentMethodType)
    {
        switch ($paymentMethodType) {
            case 'card':
                return [
                    'type' => 'CARD',
                    'installments' => 1,
                ];
            case 'nequi':
                return [
                    'type' => 'NEQUI',
                ];
            case 'bancolombia_transfer':
                return [
                    'type' => 'BANCOLOMBIA_TRANSFER',
                ];
            case 'pse':
                return [
                    'type' => 'PSE',
                ];
            default:
                return [
                    'type' => 'CARD',
                    'installments' => 1,
                ];
        }
    }

    /**
     * Convertir nombre de país a código ISO
     */
    private function getCountryCode($countryName)
    {
        $countryMap = [
            'Colombia' => 'CO',
            'México' => 'MX',
            'Estados Unidos' => 'US',
            'España' => 'ES',
            'Francia' => 'FR',
            'Alemania' => 'DE',
            'Italia' => 'IT',
            'Inglaterra' => 'GB',
            'Bélgica' => 'BE',
            'Países Bajos' => 'NL',
            'Holanda' => 'NL',
            'Japón' => 'JP',
            'Perú' => 'PE',
            'República Checa' => 'CZ',
            'Escocia' => 'GB',
            'Tailandia' => 'TH',
            'Brasil' => 'BR',
            'Argentina' => 'AR',
            'Chile' => 'CL',
            'Ecuador' => 'EC',
            'Venezuela' => 'VE',
            'Uruguay' => 'UY',
            'Paraguay' => 'PY',
            'Bolivia' => 'BO',
            'Panamá' => 'PA',
            'Costa Rica' => 'CR',
            'Guatemala' => 'GT',
            'Honduras' => 'HN',
            'El Salvador' => 'SV',
            'Nicaragua' => 'NI',
            'Cuba' => 'CU',
            'República Dominicana' => 'DO',
            'Puerto Rico' => 'PR',
            'Canadá' => 'CA',
        ];

        return $countryMap[$countryName] ?? 'CO'; // Default a Colombia
    }

    /**
     * Crear sesión de pago con Wompi
     */
    public function createWompiSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:COP',
            'customer_email' => 'nullable|email',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'redirect_url' => 'required|url',
            'payment_method' => 'nullable|string|in:card,nequi,bancolombia_transfer,pse',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de sesión inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $order = Order::with('user')->findOrFail($request->order_id);

            // Obtener datos del cliente de la orden o de la petición
            $customerEmail = $request->customer_email ?? $order->shipping_address['email'] ?? ($order->user ? $order->user->email : '');
            $customerName = $request->customer_name ?? $order->shipping_address['name'] ?? ($order->user ? $order->user->name : '');
            $customerPhone = $request->customer_phone ?? $order->shipping_address['phone'] ?? ($order->user ? $order->user->phone : '');

            // Validar que tenemos los datos mínimos
            if (empty($customerName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos del cliente incompletos. Se requiere nombre del cliente.',
                ], 400);
            }

            // Si no hay email, usar un email temporal basado en el order_id
            if (empty($customerEmail)) {
                $customerEmail = "order_{$order->id}@marketclub.temp";
            }

            // Mapear dirección al formato que espera Wompi
            $shippingAddress = $order->shipping_address;
            $wompiShippingAddress = [
                'address_line_1' => $shippingAddress['address'] ?? '',
                'address_line_2' => $shippingAddress['address_line_2'] ?? '',
                'city' => $shippingAddress['city'] ?? '',
                'region' => $shippingAddress['state'] ?? '',
                'country' => $this->getCountryCode($shippingAddress['country'] ?? ''),
                'phone_number' => $shippingAddress['phone'] ?? $customerPhone,
                'postal_code' => $shippingAddress['postal_code'] ?? '',
            ];

            // Determinar método de pago (por defecto: tarjeta)
            $paymentMethodType = $request->payment_method ?? 'card';
            
            // Crear token de pago según el método
            if ($paymentMethodType === 'card') {
                // Para tarjetas, necesitamos datos de la tarjeta (esto debería venir del frontend)
                // Por ahora, usamos un token temporal para testing
                $tokenResult = [
                    'success' => true,
                    'data' => [
                        'id' => 'tok_test_' . time() . '_' . rand(1000, 9999),
                    ],
                ];
            } else {
                // Para otros métodos (Nequi, PSE, etc.)
                $tokenData = [
                    'amount_in_cents' => $this->wompiService->convertToCents($request->amount),
                    'currency' => $request->currency,
                    'customer_email' => $customerEmail,
                    'payment_method' => $this->createPaymentMethodObject($paymentMethodType),
                ];

                $tokenResult = $this->wompiService->createPaymentTokenForMethod($tokenData);
            }
            
            if (!$tokenResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear token de pago',
                    'error' => $tokenResult['error'],
                ], 400);
            }

            // Crear objeto de método de pago con token
            $paymentMethod = [
                'type' => $this->getPaymentMethodType($paymentMethodType),
                'token' => $tokenResult['data']['id'],
                'installments' => 1, // Siempre 1 cuota por defecto
            ];

            // Para Nequi, agregar phone_number al método de pago
            if ($paymentMethodType === 'nequi') {
                // Validar que el teléfono tenga exactamente 10 dígitos
                $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone);
                if (strlen($cleanPhone) !== 10) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El número de teléfono debe tener exactamente 10 dígitos para Nequi.',
                    ], 400);
                }
                $paymentMethod['phone_number'] = $cleanPhone;
            }

            // Obtener acceptance token
            $acceptanceResult = $this->wompiService->getPaymentMethods();
            if (!$acceptanceResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener acceptance token',
                    'error' => $acceptanceResult['error'],
                ], 400);
            }

            // Preparar datos para Wompi
            $sessionData = [
                'amount_in_cents' => $this->wompiService->convertToCents($request->amount),
                'currency' => $request->currency,
                'customer_email' => $customerEmail,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'reference' => $this->wompiService->generateReference('ORDER_' . $order->id),
                'redirect_url' => $request->redirect_url,
                'shipping_address' => $wompiShippingAddress,
                'payment_method' => $paymentMethod,
                'acceptance_token' => $acceptanceResult['data']['acceptance_token'],
            ];

            // Crear sesión en Wompi
            $result = $this->wompiService->createTransaction($sessionData);

            if ($result['success']) {
                $transaction = $result['data']['data'];

                // Actualizar orden con información de pago
                $order->update([
                    'payment_reference' => $transaction['id'],
                    'payment_status' => 'pending',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Sesión de pago creada exitosamente',
                    'data' => [
                        'session_id' => $transaction['id'],
                        'payment_url' => $transaction['payment_url'] ?? null,
                        'order' => $order->fresh(),
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al crear sesión de pago',
                'error' => $result['error'],
            ], 400);

        } catch (\Exception $e) {
            Log::error('Wompi session creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Verificar estado del pago
     */
    public function checkPaymentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $order = Order::findOrFail($request->order_id);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->status,
                    'payment_reference' => $order->payment_reference,
                    'wompi_transaction_id' => $order->wompi_transaction_id,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking payment status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Crear datos para Widget de Wompi
     */
    public function createWompiWidget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'redirect_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $order = Order::findOrFail($request->order_id);

            // Obtener datos del cliente desde la orden
            $shippingAddress = $order->shipping_address;
            $customerName = $shippingAddress['name'] ?? 'Cliente';
            $customerEmail = $shippingAddress['email'] ?? 'cliente' . $order->id . '@marketclub.com';
            $customerPhone = $shippingAddress['phone'] ?? '3000000000';

            // Generar referencia única
            $reference = $this->wompiService->generateReference('ORDER_' . $order->id);

            // Generar firma de integridad para el Widget
            $signature = $this->generateWidgetSignature($reference, $order->total_amount);

            // Preparar datos para el Widget de Wompi (formato exacto según documentación)
            $widgetData = [
                'publicKey' => config('services.wompi.public_key'),
                'reference' => $reference,
                'amount' => $this->wompiService->convertToCents($order->total_amount),
                'currency' => 'COP',
                'integrity_signature' => $signature, // Wompi espera este campo específico
                'redirectUrl' => $request->redirect_url,
                'customerData' => [
                    'name' => $customerName,
                    'email' => $customerEmail,
                    'phoneNumber' => $customerPhone,
                    'phoneNumberPrefix' => '+57',
                ],
                'shippingAddress' => [
                    'addressLine1' => $shippingAddress['address'] ?? 'Dirección no especificada',
                    'city' => $shippingAddress['city'] ?? 'Ciudad no especificada',
                    'region' => $shippingAddress['state'] ?? 'Región no especificada',
                    'country' => 'CO',
                    'phoneNumber' => $customerPhone,
                ],
            ];

            // Actualizar la orden con la referencia de pago
            $order->update(['payment_reference' => $reference]);

            return response()->json([
                'success' => true,
                'message' => 'Datos del Widget generados exitosamente',
                'data' => $widgetData,
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating Wompi widget data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Verificar configuración de Wompi (solo para desarrollo)
     */
    public function checkWompiConfig()
    {
        if (config('app.env') === 'production') {
            return response()->json(['error' => 'Not available in production'], 403);
        }

        return response()->json([
            'success' => true,
            'config' => [
                'public_key' => config('services.wompi.public_key') ? '✅ Configurado' : '❌ Faltante',
                'private_key' => config('services.wompi.private_key') ? '✅ Configurado' : '❌ Faltante',
                'integrity_key' => config('services.wompi.integrity_key') ? '✅ Configurado' : '❌ Faltante',
                'production' => config('services.wompi.production') ? 'Producción' : 'Pruebas',
            ],
            'sample_signature' => $this->generateWidgetSignature('TEST_REF_123', 10000),
        ]);
    }

    /**
     * Generar firma de integridad para Widget de Wompi
     */
    public function getWidgetSignature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $order = Order::findOrFail($request->order_id);

            // Generar referencia única
            $reference = $this->wompiService->generateReference('ORDER_' . $order->id);

            // Generar firma de integridad
            $signature = $this->generateWidgetSignature($reference, $order->total_amount);

            return response()->json([
                'success' => true,
                'data' => [
                    'reference' => $reference,
                    'amount' => $this->wompiService->convertToCents($order->total_amount),
                    'currency' => 'COP',
                    'signature' => ['integrity' => $signature], // Formato correcto para Widget
                    'public_key' => config('services.wompi.public_key'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating widget signature: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Generar firma de integridad para suscripciones
     */
    public function getSubscriptionSignature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:subscription',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Verificar que el plan existe
            $plan = \App\Models\SubscriptionPlan::where('slug', $request->plan_id)->first();
            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan de suscripción no encontrado',
                ], 404);
            }

            // Generar referencia única para suscripción
            $reference = $this->wompiService->generateReference('SUBSCRIPTION_' . $request->plan_id . '_' . time());

            // Generar firma de integridad
            $signature = $this->generateWidgetSignature($reference, $request->amount);

            return response()->json([
                'success' => true,
                'data' => [
                    'reference' => $reference,
                    'amount' => $this->wompiService->convertToCents($request->amount),
                    'currency' => 'COP',
                    'signature' => ['integrity' => $signature], // Formato correcto para Widget
                    'public_key' => config('services.wompi.public_key'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating subscription signature: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Procesar pago de suscripción después de confirmación de Wompi
     */
    public function processSubscriptionPayment(Request $request)
    {
        Log::info('Processing subscription payment request', [
            'plan_id' => $request->plan_id,
            'transaction_id' => $request->transaction_id,
            'reference' => $request->reference,
            'amount' => $request->amount,
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id(),
        ]);

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|string|exists:subscription_plans,slug',
            'transaction_id' => 'required|string',
            'reference' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Log::error('Subscription payment validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user(); // El middleware ya garantiza que el usuario está autenticado
            Log::info('User authenticated for subscription', ['user_id' => $user->id, 'user_email' => $user->email]);
            $plan = \App\Models\SubscriptionPlan::where('slug', $request->plan_id)->firstOrFail();

            // Verificar si el usuario ya tiene una suscripción activa
            $activeSubscription = $user->activeSubscription;
            if ($activeSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya tienes una suscripción activa. Cancela tu suscripción actual antes de suscribirte a un nuevo plan.',
                    'current_subscription' => [
                        'plan_name' => $activeSubscription->subscriptionPlan->name,
                        'ends_at' => $activeSubscription->ends_at->format('Y-m-d'),
                        'days_remaining' => $activeSubscription->days_remaining,
                    ],
                ], 400);
            }

            // Crear nueva suscripción
            $startsAt = now();
            $endsAt = $startsAt->copy()->addMonth();

            $subscription = \App\Models\UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'price_paid' => $request->amount,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'next_billing_date' => $endsAt,
                'auto_renew' => true, // Habilitar renovación automática
                'metadata' => [
                    'transaction_id' => $request->transaction_id,
                    'reference' => $request->reference,
                    'subscribed_at' => now(),
                    'payment_method' => 'wompi',
                    'initial_payment' => true,
                ],
            ]);

            Log::info('Subscription created with auto-renewal enabled', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'plan' => $plan->name,
                'auto_renew' => true,
                'next_billing_date' => $endsAt->format('Y-m-d')
            ]);

            // Enviar email de confirmación
            try {
                $this->emailService->sendSubscriptionConfirmation($subscription);
                Log::info("Subscription created successfully for user {$user->id}, plan {$plan->name}");
            } catch (\Exception $e) {
                Log::error("Failed to send subscription confirmation email: " . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Suscripción creada exitosamente',
                'data' => [
                    'subscription' => [
                        'id' => $subscription->id,
                        'plan_name' => $plan->name,
                        'price_paid' => (float) $subscription->price_paid,
                        'starts_at' => $subscription->starts_at->format('Y-m-d'),
                        'ends_at' => $subscription->ends_at->format('Y-m-d'),
                        'next_billing_date' => $subscription->next_billing_date->format('Y-m-d'),
                        'days_remaining' => $subscription->days_remaining,
                        'status' => $subscription->status,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing subscription payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Crear transacción de pago manualmente (solo para testing)
     */
    public function createPaymentTransaction(Request $request)
    {
        if (config('app.env') === 'production') {
            return response()->json(['error' => 'Not available in production'], 403);
        }

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'wompi_transaction_id' => 'required|string',
            'reference' => 'required|string',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|in:APPROVED,PENDING,DECLINED,VOIDED',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $order = Order::findOrFail($request->order_id);
            
            $paymentTransaction = \App\Models\PaymentTransaction::create([
                'order_id' => $order->id,
                'wompi_transaction_id' => $request->wompi_transaction_id,
                'reference' => $request->reference,
                'payment_method' => $request->payment_method,
                'amount' => $request->amount,
                'currency' => 'COP',
                'status' => $request->status,
                'wompi_status' => $request->status,
                'wompi_response' => [
                    'id' => $request->wompi_transaction_id,
                    'status' => $request->status,
                    'amount_in_cents' => (int) ($request->amount * 100),
                    'currency' => 'COP',
                    'reference' => $request->reference,
                    'payment_method' => ['type' => $request->payment_method],
                ],
                'customer_data' => [
                    'email' => $order->user->email ?? $order->shipping_address['email'] ?? null,
                    'full_name' => $order->user->name ?? $order->shipping_address['name'] ?? null,
                    'phone_number' => $order->user->phone ?? $order->shipping_address['phone'] ?? null,
                ],
                'processed_at' => $request->status === 'APPROVED' ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transacción de pago creada exitosamente',
                'data' => $paymentTransaction,
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating payment transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Actualizar orden manualmente (solo para testing)
     */
    public function updateOrderStatus(Request $request)
    {
        if (config('app.env') === 'production') {
            return response()->json(['error' => 'Not available in production'], 403);
        }

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_status' => 'required|in:pending,paid,failed',
            'wompi_transaction_id' => 'nullable|string',
            'payment_reference' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $order = Order::findOrFail($request->order_id);
            
            $order->update([
                'payment_status' => $request->payment_status,
                'status' => $request->payment_status === 'paid' ? 'processing' : 'pending',
                'wompi_transaction_id' => $request->wompi_transaction_id,
                'payment_reference' => $request->payment_reference,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizada exitosamente',
                'data' => [
                    'order_id' => $order->id,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->status,
                    'wompi_transaction_id' => $order->wompi_transaction_id,
                    'payment_reference' => $order->payment_reference,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Crear o actualizar transacción de pago
     */
    private function createOrUpdatePaymentTransaction($order, $data, $status)
    {
        $transactionData = $data['data'];
        
        // Buscar transacción existente o crear nueva
        $paymentTransaction = \App\Models\PaymentTransaction::updateOrCreate(
            [
                'wompi_transaction_id' => $transactionData['id'],
            ],
            [
                'order_id' => $order->id,
                'reference' => $transactionData['reference'] ?? $order->payment_reference,
                'payment_method' => $this->mapWompiPaymentMethod($transactionData['payment_method'] ?? 'CARD'),
                'amount' => $transactionData['amount_in_cents'] / 100, // Convertir de centavos
                'currency' => $transactionData['currency'] ?? 'COP',
                'status' => $this->mapWompiStatusToTransactionStatus($status),
                'wompi_status' => $status,
                'wompi_response' => $data,
                'customer_data' => [
                    'email' => $order->user->email ?? $order->shipping_address['email'] ?? null,
                    'full_name' => $order->user->name ?? $order->shipping_address['name'] ?? null,
                    'phone_number' => $order->user->phone ?? $order->shipping_address['phone'] ?? null,
                ],
                'processed_at' => $status === 'APPROVED' ? now() : null,
            ]
        );

        return $paymentTransaction;
    }

    /**
     * Mapear método de pago de Wompi
     */
    private function mapWompiPaymentMethod($paymentMethod)
    {
        if (is_array($paymentMethod)) {
            return $paymentMethod['type'] ?? 'CARD';
        }
        
        return match (strtoupper($paymentMethod)) {
            'NEQUI' => 'NEQUI',
            'PSE' => 'PSE',
            'CARD', 'CREDIT_CARD' => 'CARD',
            default => 'CARD',
        };
    }

    /**
     * Mapear estado de Wompi a estado de transacción
     */
    private function mapWompiStatusToTransactionStatus($wompiStatus)
    {
        return match (strtoupper($wompiStatus)) {
            'APPROVED' => 'APPROVED',
            'PENDING' => 'PENDING',
            'DECLINED', 'REJECTED' => 'DECLINED',
            'VOIDED' => 'VOIDED',
            default => 'PENDING',
        };
    }

    /**
     * Crear datos para Checkout Web de Wompi
     */
    public function createWompiCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'redirect_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $order = Order::with('user')->findOrFail($request->order_id);

            // Obtener datos del cliente de la orden
            $customerEmail = $order->shipping_address['email'] ?? ($order->user ? $order->user->email : '');
            $customerName = $order->shipping_address['name'] ?? ($order->user ? $order->user->name : '');
            $customerPhone = $order->shipping_address['phone'] ?? ($order->user ? $order->user->phone : '');

            // Validar y corregir datos del cliente
            if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                $customerEmail = "cliente{$order->id}@marketclub.com";
            }

            if (empty($customerName)) {
                $customerName = "Cliente {$order->id}";
            }

            if (empty($customerPhone)) {
                $customerPhone = "3000000000";
            }

            // Limpiar y validar teléfono
            $customerPhone = preg_replace('/[^0-9]/', '', $customerPhone);
            if (strlen($customerPhone) !== 10) {
                $customerPhone = "3000000000";
            }

            // Generar referencia única
            $reference = $this->wompiService->generateReference('ORDER_' . $order->id);

            // Generar firma de integridad para el Widget
            $signature = $this->generateWidgetSignature($reference, $order->total_amount);

            // Preparar datos para el Widget de Wompi (formato exacto según documentación)
            $widgetData = [
                'publicKey' => config('services.wompi.public_key'),
                'reference' => $reference,
                'amount' => $this->wompiService->convertToCents($order->total_amount),
                'currency' => 'COP',
                'integrity_signature' => $signature, // Wompi espera este campo específico
                'redirectUrl' => $request->redirect_url,
                'customerData' => [
                    'name' => $customerName,
                    'email' => $customerEmail,
                    'phoneNumber' => $customerPhone,
                    'phoneNumberPrefix' => '+57',
                ],
                'shippingAddress' => [
                    'addressLine1' => $order->shipping_address['address'] ?? '',
                    'city' => $order->shipping_address['city'] ?? '',
                    'region' => $order->shipping_address['state'] ?? '',
                    'country' => $this->getCountryCode($order->shipping_address['country'] ?? ''),
                    'phoneNumber' => $customerPhone,
                ],
            ];

            // Actualizar orden con referencia de pago
            $order->update([
                'payment_reference' => $reference,
                'payment_status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Datos del Checkout Web generados exitosamente',
                'data' => $widgetData,
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating Wompi widget data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Generar firma de integridad para Widget de Wompi
     */
    private function generateWidgetSignature(string $reference, float $amount): string
    {
        $integrityKey = config('services.wompi.integrity_key');
        $amountInCents = $this->wompiService->convertToCents($amount);
        
        $signatureString = $reference . $amountInCents . 'COP' . $integrityKey;
        
        return hash('sha256', $signatureString);
    }

    /**
     * Mapear estados de Wompi a estados de orden
     */
    private function mapWompiStatusToOrderStatus(string $wompiStatus): string
    {
        return match ($wompiStatus) {
            'APPROVED' => 'paid',
            'DECLINED', 'VOIDED' => 'failed',
            'PENDING' => 'pending',
            default => 'pending',
        };
    }
}