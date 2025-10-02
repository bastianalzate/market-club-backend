<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wholesaler;
use Illuminate\Http\Request;

class WholesalerController extends Controller
{
    public function index(Request $request)
    {
        $query = Wholesaler::query();

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por país
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por tipo de negocio
        if ($request->filled('business_type')) {
            $query->where('business_type', $request->business_type);
        }

        // Ordenamiento
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $wholesalers = $query->with('approver')->paginate(10);

        return view('admin.wholesalers.index', compact('wholesalers'));
    }

    public function create()
    {
        return view('admin.wholesalers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|unique:wholesalers,email',
            'phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'business_type' => 'required|in:restaurant,bar,retail_store,distributor,other',
            'business_description' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Wholesaler::create($request->all());

        return redirect()->route('admin.wholesalers.index')
            ->with('success', 'Mayorista creado exitosamente.');
    }

    public function show(Wholesaler $wholesaler)
    {
        $wholesaler->load('approver');
        return view('admin.wholesalers.show', compact('wholesaler'));
    }

    public function edit(Wholesaler $wholesaler)
    {
        return view('admin.wholesalers.edit', compact('wholesaler'));
    }

    public function update(Request $request, Wholesaler $wholesaler)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|unique:wholesalers,email,' . $wholesaler->id,
            'phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'business_type' => 'required|in:restaurant,bar,retail_store,distributor,other',
            'business_description' => 'nullable|string',
            'status' => 'required|in:enabled,disabled',
            'notes' => 'nullable|string',
        ]);

        $wholesaler->update($request->all());

        return redirect()->route('admin.wholesalers.index')
            ->with('success', 'Mayorista actualizado exitosamente.');
    }

    public function approve(Wholesaler $wholesaler)
    {
        $wholesaler->update([
            'status' => 'enabled',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('admin.wholesalers.index')
            ->with('success', 'Mayorista habilitado exitosamente.');
    }

    public function toggleStatus(Wholesaler $wholesaler)
    {
        $newStatus = $wholesaler->status === 'enabled' ? 'disabled' : 'enabled';
        
        $wholesaler->update(['status' => $newStatus]);

        $statusText = $newStatus === 'enabled' ? 'habilitado' : 'deshabilitado';
        
        return response()->json([
            'success' => true,
            'message' => "Mayorista {$statusText} exitosamente.",
            'status' => $newStatus,
            'status_text' => $statusText
        ]);
    }

    public function destroy(Wholesaler $wholesaler)
    {
        $wholesaler->delete();

        return redirect()->route('admin.wholesalers.index')
            ->with('success', 'Mayorista eliminado exitosamente.');
    }
}

