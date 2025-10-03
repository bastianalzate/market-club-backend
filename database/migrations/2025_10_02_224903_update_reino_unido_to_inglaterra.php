<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar en la tabla users
        DB::table('users')->where('country', 'Reino Unido')->update(['country' => 'Inglaterra']);
        
        // Actualizar en la tabla wholesalers
        DB::table('wholesalers')->where('country', 'Reino Unido')->update(['country' => 'Inglaterra']);
        
        // Actualizar en JSON fields de orders (shipping_address)
        $orders = DB::table('orders')->get();
        foreach ($orders as $order) {
            if ($order->shipping_address) {
                $address = json_decode($order->shipping_address, true);
                if (isset($address['country']) && $address['country'] === 'Reino Unido') {
                    $address['country'] = 'Inglaterra';
                    DB::table('orders')->where('id', $order->id)->update([
                        'shipping_address' => json_encode($address)
                    ]);
                }
            }
            
            if ($order->billing_address) {
                $address = json_decode($order->billing_address, true);
                if (isset($address['country']) && $address['country'] === 'Reino Unido') {
                    $address['country'] = 'Inglaterra';
                    DB::table('orders')->where('id', $order->id)->update([
                        'billing_address' => json_encode($address)
                    ]);
                }
            }
        }
        
        // Actualizar en JSON fields de payment_transactions (customer_data)
        $transactions = DB::table('payment_transactions')->get();
        foreach ($transactions as $transaction) {
            if ($transaction->customer_data) {
                $customerData = json_decode($transaction->customer_data, true);
                if (isset($customerData['country']) && $customerData['country'] === 'Reino Unido') {
                    $customerData['country'] = 'Inglaterra';
                    DB::table('payment_transactions')->where('id', $transaction->id)->update([
                        'customer_data' => json_encode($customerData)
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir en la tabla users
        DB::table('users')->where('country', 'Inglaterra')->update(['country' => 'Reino Unido']);
        
        // Revertir en la tabla wholesalers
        DB::table('wholesalers')->where('country', 'Inglaterra')->update(['country' => 'Reino Unido']);
        
        // Revertir en JSON fields de orders (shipping_address)
        $orders = DB::table('orders')->get();
        foreach ($orders as $order) {
            if ($order->shipping_address) {
                $address = json_decode($order->shipping_address, true);
                if (isset($address['country']) && $address['country'] === 'Inglaterra') {
                    $address['country'] = 'Reino Unido';
                    DB::table('orders')->where('id', $order->id)->update([
                        'shipping_address' => json_encode($address)
                    ]);
                }
            }
            
            if ($order->billing_address) {
                $address = json_decode($order->billing_address, true);
                if (isset($address['country']) && $address['country'] === 'Inglaterra') {
                    $address['country'] = 'Reino Unido';
                    DB::table('orders')->where('id', $order->id)->update([
                        'billing_address' => json_encode($address)
                    ]);
                }
            }
        }
        
        // Revertir en JSON fields de payment_transactions (customer_data)
        $transactions = DB::table('payment_transactions')->get();
        foreach ($transactions as $transaction) {
            if ($transaction->customer_data) {
                $customerData = json_decode($transaction->customer_data, true);
                if (isset($customerData['country']) && $customerData['country'] === 'Inglaterra') {
                    $customerData['country'] = 'Reino Unido';
                    DB::table('payment_transactions')->where('id', $transaction->id)->update([
                        'customer_data' => json_encode($customerData)
                    ]);
                }
            }
        }
    }
};
