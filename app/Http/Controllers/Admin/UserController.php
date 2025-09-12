<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Solo mostrar clientes (usuarios con rol 'customer')
        $query->where('role', 'customer');

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por país
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        // Filtro por tipo de cliente (mayorista o regular)
        if ($request->has('is_wholesaler') && $request->is_wholesaler !== '') {
            $query->where('is_wholesaler', $request->boolean('is_wholesaler'));
        }

        // Ordenamiento
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $users = $query->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // Verificar que el usuario sea un cliente
        if ($user->role !== 'customer') {
            abort(404, 'Usuario no encontrado');
        }
        
        $user->load(['orders.orderItems.product']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Verificar que el usuario sea un cliente
        if ($user->role !== 'customer') {
            abort(404, 'Usuario no encontrado');
        }
        
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Verificar que el usuario sea un cliente
        if ($user->role !== 'customer') {
            abort(404, 'Usuario no encontrado');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'country']));

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // Verificar que el usuario sea un cliente
        if ($user->role !== 'customer') {
            abort(404, 'Usuario no encontrado');
        }
        
        // Verificar si el usuario tiene órdenes
        if ($user->orders()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No se puede eliminar el usuario porque tiene órdenes asociadas.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
