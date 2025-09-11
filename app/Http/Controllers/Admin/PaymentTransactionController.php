<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentTransaction::with(['order.user']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('wompi_transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('order.user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.payment-transactions.index', compact('transactions'));
    }

    public function show(PaymentTransaction $paymentTransaction)
    {
        $paymentTransaction->load(['order.user', 'order.orderItems.product']);
        
        return view('admin.payment-transactions.show', compact('paymentTransaction'));
    }

    public function destroy(PaymentTransaction $paymentTransaction)
    {
        $paymentTransaction->delete();
        
        return redirect()->route('admin.payment-transactions.index')
            ->with('success', 'Transacción eliminada exitosamente.');
    }
}