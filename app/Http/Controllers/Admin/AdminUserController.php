<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Solo mostrar administradores (usuarios con rol 'admin' o 'super_admin')
        $query->whereIn('role', ['admin', 'super_admin']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $admins = $query->paginate(15);

        return view('admin.admin-users.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.admin-users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
            'is_active' => 'boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Administrador creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $adminUser)
    {
        // Verificar que el usuario sea un administrador
        if (!in_array($adminUser->role, ['admin', 'super_admin'])) {
            abort(404, 'Administrador no encontrado');
        }
        
        $adminUser->load(['orders.orderItems.product']);
        return view('admin.admin-users.show', compact('adminUser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $adminUser)
    {
        // Verificar que el usuario sea un administrador
        if (!in_array($adminUser->role, ['admin', 'super_admin'])) {
            abort(404, 'Administrador no encontrado');
        }
        
        return view('admin.admin-users.edit', compact('adminUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $adminUser)
    {
        // Verificar que el usuario sea un administrador
        if (!in_array($adminUser->role, ['admin', 'super_admin'])) {
            abort(404, 'Administrador no encontrado');
        }
        
        // Proteger el super admin principal
        $isMainSuperAdmin = $adminUser->email === 'admin@marketclub.com';
        
        $validationRules = [
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ];

        // Solo permitir cambiar email si no es el super admin principal
        if (!$isMainSuperAdmin) {
            $validationRules['email'] = [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($adminUser->id),
            ];
            $validationRules['role'] = 'required|in:admin,super_admin';
        }

        $request->validate($validationRules);

        $data = [
            'name' => $request->name,
            'is_active' => $request->boolean('is_active', true),
        ];

        // Solo permitir cambiar email y rol si no es el super admin principal
        if (!$isMainSuperAdmin) {
            $data['email'] = $request->email;
            $data['role'] = $request->role;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $adminUser->update($data);

        return redirect()->route('admin.admin-users.show', $adminUser)
            ->with('success', 'Administrador actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $adminUser)
    {
        // Verificar que el usuario sea un administrador
        if (!in_array($adminUser->role, ['admin', 'super_admin'])) {
            abort(404, 'Administrador no encontrado');
        }
        
        // No permitir eliminar el super admin principal
        if ($adminUser->email === 'admin@marketclub.com') {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el super administrador principal del sistema.');
        }

        // No permitir eliminar el último super admin
        if ($adminUser->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el último super administrador.');
        }

        $adminUser->delete();

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Administrador eliminado exitosamente.');
    }
}