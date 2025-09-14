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
            'cvc' => 'required|string|size:3',
            'exp_month' => 'required|string|size:2',
            'exp_year' => 'required|string|size:4',
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

                // Enviar email de confirmación de pago si fue aprobado
                if ($transaction['status'] === 'APPROVED') {
                    $this->emailService->sendPaymentConfirmation($order);
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

        // Verificar firma del webhook
        if (!$this->wompiService->verifyWebhookSignature($signature, $payload)) {
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
                ]);

                // Aquí podrías agregar lógica adicional como:
                // - Enviar email de confirmación
                // - Actualizar inventario
                // - Generar factura
                // - etc.

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
     * Crear sesión de pago con Wompi
     */
    public function createWompiSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:COP',
            'customer_email' => 'required|email',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'redirect_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de sesión inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $order = Order::findOrFail($request->order_id);

            // Preparar datos para Wompi
            $sessionData = [
                'amount_in_cents' => $this->wompiService->convertToCents($request->amount),
                'currency' => $request->currency,
                'customer_email' => $request->customer_email,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone ?? '',
                'reference' => $this->wompiService->generateReference('ORDER_' . $order->id),
                'redirect_url' => $request->redirect_url,
                'shipping_address' => $order->shipping_address,
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