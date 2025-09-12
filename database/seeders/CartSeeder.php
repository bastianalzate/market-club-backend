<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios y productos
        $users = User::where('role', 'customer')->get();
        $products = Product::where('is_active', true)->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('No hay usuarios o productos para crear carritos.');
            return;
        }

        // Crear carritos para algunos usuarios
        foreach ($users->take(3) as $user) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'subtotal' => 0,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'total_amount' => 0,
            ]);

            // Agregar productos aleatorios al carrito
            $cartProducts = $products->random(rand(1, 4));
            $subtotal = 0;

            foreach ($cartProducts as $product) {
                $quantity = rand(1, 3);
                $unitPrice = $product->current_price;
                $totalPrice = $quantity * $unitPrice;
                $subtotal += $totalPrice;

                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'product_snapshot' => $product->toArray(),
                ]);
            }

            // Calcular totales
            $taxAmount = $subtotal * 0.19; // 19% IVA
            $shippingAmount = $subtotal >= 100000 ? 0 : 10000; // Envío gratis sobre $100,000
            $totalAmount = $subtotal + $taxAmount + $shippingAmount;

            $cart->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
            ]);
        }

        // Crear wishlists para algunos usuarios
        foreach ($users->take(5) as $user) {
            $wishlistProducts = $products->random(rand(2, 6));
            
            foreach ($wishlistProducts as $product) {
                Wishlist::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
            }
        }

        $this->command->info('Carritos y wishlists creados exitosamente.');
    }
}