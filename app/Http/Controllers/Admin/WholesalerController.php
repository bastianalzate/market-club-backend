<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class WholesalerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Solo mostrar usuarios que son mayoristas
        $query->where('is_wholesaler', true);

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtro por país
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        // Filtro por estado (usando is_active en lugar de status)
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'enabled');
        }

        // Ordenamiento
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $wholesalers = $query->paginate(10);

        return view('admin.wholesalers.index', compact('wholesalers'));
    }

    // Los métodos create y store no son necesarios ya que los mayoristas
    // se registran a través del sistema de usuarios normales con is_wholesaler = true

    public function show(User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        return view('admin.wholesalers.show', compact('wholesaler'));
    }

    public function edit(User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        return view('admin.wholesalers.edit', compact('wholesaler'));
    }

    public function update(Request $request, User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $wholesaler->id,
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $wholesaler->update($request->only(['name', 'email', 'phone', 'country', 'is_active']));

        return redirect()->route('admin.wholesalers.index')
            ->with('success', 'Mayorista actualizado exitosamente.');
    }

    public function approve(User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        $wholesaler->update([
            'is_active' => true,
        ]);

        return redirect()->route('admin.wholesalers.index')
            ->with('success', 'Mayorista habilitado exitosamente.');
    }

    public function toggleStatus(User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        $newStatus = !$wholesaler->is_active;
        
        $wholesaler->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'habilitado' : 'deshabilitado';
        
        return response()->json([
            'success' => true,
            'message' => "Mayorista {$statusText} exitosamente.",
            'status' => $newStatus ? 'enabled' : 'disabled',
            'status_text' => $statusText
        ]);
    }

    public function destroy(User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        // Verificar si el usuario tiene órdenes
        if ($wholesaler->orders()->count() > 0) {
            return redirect()->route('admin.wholesalers.index')
                ->with('error', 'No se puede eliminar el mayorista porque tiene órdenes asociadas.');
        }

        $wholesaler->delete();

        return redirect()->route('admin.wholesalers.index')
            ->with('success', 'Mayorista eliminado exitosamente.');
    }
}

