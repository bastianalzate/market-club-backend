<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Services\EmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestPurchaseEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:purchase-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar un email de prueba de compra exitosa';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("📧 Iniciando prueba de email de compra...");
        $this->info("Email destino: {$email}");
        $this->newLine();

        try {
            DB::beginTransaction();

            // 1. Buscar o crear usuario de prueba
            $this->info("1️⃣ Buscando/creando usuario de prueba...");
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => 'Usuario de Prueba',
                    'email' => $email,
                    'password' => bcrypt('password123'),
                    'phone' => '+573001234567',
                    'is_active' => true,
                ]);
                $this->info("✅ Usuario creado: {$user->name} ({$user->email})");
            } else {
                $this->info("✅ Usuario encontrado: {$user->name} ({$user->email})");
            }
            $this->newLine();

            // 2. Obtener productos para la orden
            $this->info("2️⃣ Obteniendo productos...");
            $products = Product::where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->take(2)
                ->get();

            if ($products->isEmpty()) {
                $this->error("❌ No hay productos disponibles para crear la orden de prueba");
                return 1;
            }

            foreach ($products as $product) {
                $this->line("  - {$product->name} (\${$product->price})");
            }
            $this->newLine();

            // 3. Crear orden de prueba
            $this->info("3️⃣ Creando orden de prueba...");
            
            $subtotal = 0;
            $orderItems = [];

            foreach ($products as $product) {
                $quantity = 1;
                $price = $product->sale_price ?? $product->price;
                $subtotal += $price * $quantity;
                
                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }

            $shipping = 10000; // Envío de $10,000
            $tax = $subtotal * 0.19; // IVA 19%
            $total = $subtotal + $shipping + $tax;

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'TEST-' . time(),
                'status' => 'processing',
                'payment_status' => 'paid',
                'payment_method' => 'CARD',
                'payment_reference' => 'TEST-REF-' . time(),
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'shipping_amount' => $shipping,
                'total_amount' => $total,
                'shipping_address' => [
                    'name' => $user->name,
                    'address' => 'Calle 123 #45-67, Apto 101',
                    'city' => 'Bogotá',
                    'state' => 'Cundinamarca',
                    'postal_code' => '110111',
                    'country' => 'Colombia',
                    'phone' => '+573001234567',
                ],
                'billing_address' => [
                    'name' => $user->name,
                    'address' => 'Calle 123 #45-67, Apto 101',
                    'city' => 'Bogotá',
                    'state' => 'Cundinamarca',
                    'postal_code' => '110111',
                    'country' => 'Colombia',
                    'phone' => '+573001234567',
                ],
                'notes' => 'Esta es una orden de prueba para verificar el envío de emails',
            ]);

            $this->info("✅ Orden creada: #{$order->order_number}");
            $this->line("  - Subtotal: \$" . number_format($subtotal, 0, ',', '.'));
            $this->line("  - IVA (19%): \$" . number_format($tax, 0, ',', '.'));
            $this->line("  - Envío: \$" . number_format($shipping, 0, ',', '.'));
            $this->line("  - Total: \$" . number_format($total, 0, ',', '.'));
            $this->newLine();

            // 4. Crear items de la orden
            $this->info("4️⃣ Agregando items a la orden...");
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);
                $this->line("  ✓ {$item['product']->name} x{$item['quantity']}");
            }
            $this->newLine();

            DB::commit();

            // 5. Enviar emails
            $this->info("5️⃣ Enviando emails de confirmación...");
            $this->newLine();

            // Recargar la orden con sus relaciones
            $order->load(['user', 'orderItems.product']);

            // Enviar email de confirmación de orden
            $this->line("📨 Enviando email de confirmación de orden...");
            $orderEmailSent = $this->emailService->sendOrderConfirmation($order);
            
            if ($orderEmailSent) {
                $this->info("✅ Email de confirmación de orden enviado exitosamente");
            } else {
                $this->error("❌ Error al enviar email de confirmación de orden");
            }
            $this->newLine();

            // Enviar email de confirmación de pago
            $this->line("📨 Enviando email de confirmación de pago...");
            $paymentEmailSent = $this->emailService->sendPaymentConfirmation($order);
            
            if ($paymentEmailSent) {
                $this->info("✅ Email de confirmación de pago enviado exitosamente");
            } else {
                $this->error("❌ Error al enviar email de confirmación de pago");
            }
            $this->newLine();

            // Resumen final
            $this->newLine();
            $this->info("═══════════════════════════════════════");
            $this->info("  RESUMEN DE LA PRUEBA");
            $this->info("═══════════════════════════════════════");
            $this->line("Email destino: {$email}");
            $this->line("Orden: #{$order->order_number}");
            $this->line("Total: \$" . number_format($order->total_amount, 0, ',', '.'));
            $this->line("Email de orden: " . ($orderEmailSent ? '✅ Enviado' : '❌ Fallido'));
            $this->line("Email de pago: " . ($paymentEmailSent ? '✅ Enviado' : '❌ Fallido'));
            $this->info("═══════════════════════════════════════");
            $this->newLine();

            if ($orderEmailSent && $paymentEmailSent) {
                $this->info("🎉 ¡Prueba completada exitosamente!");
                $this->info("Por favor revisa la bandeja de entrada de: {$email}");
                $this->warn("⚠️  Si no ves el email, revisa la carpeta de SPAM");
                return 0;
            } else {
                $this->warn("⚠️  Algunos emails no se enviaron. Revisa los logs:");
                $this->line("  tail -f storage/logs/laravel.log | grep -i 'email\\|brevo'");
                return 1;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error durante la prueba: " . $e->getMessage());
            $this->line("Traza del error:");
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}

