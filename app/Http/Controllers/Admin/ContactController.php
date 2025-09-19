<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contact::with('resolvedBy');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estadísticas
        $stats = [
            'total' => Contact::count(),
            'new' => Contact::new()->count(),
            'in_progress' => Contact::inProgress()->count(),
            'resolved' => Contact::resolved()->count(),
            'today' => Contact::whereDate('created_at', today())->count(),
        ];

        return view('admin.contacts.index', compact('contacts', 'stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        $contact->load('resolvedBy');
        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        // Si se marca como resuelto, agregar información de resolución
        if ($request->status === 'resolved' && $contact->status !== 'resolved') {
            $data['resolved_at'] = now();
            $data['resolved_by'] = Auth::id();
        }

        // Si se cambia de resuelto a otro estado, limpiar información de resolución
        if ($request->status !== 'resolved' && $contact->status === 'resolved') {
            $data['resolved_at'] = null;
            $data['resolved_by'] = null;
        }

        $contact->update($data);

        return redirect()->route('admin.contacts.show', $contact)
                        ->with('success', 'Contacto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')
                        ->with('success', 'Contacto eliminado exitosamente.');
    }

    /**
     * Marcar múltiples contactos como resueltos
     */
    public function bulkResolve(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
        ]);

        Contact::whereIn('id', $request->contact_ids)
               ->where('status', '!=', 'resolved')
               ->update([
                   'status' => 'resolved',
                   'resolved_at' => now(),
                   'resolved_by' => Auth::id(),
               ]);

        return redirect()->route('admin.contacts.index')
                        ->with('success', 'Contactos marcados como resueltos.');
    }

    /**
     * Obtener estadísticas para el dashboard
     */
    public function stats()
    {
        return [
            'total' => Contact::count(),
            'new' => Contact::new()->count(),
            'in_progress' => Contact::inProgress()->count(),
            'resolved' => Contact::resolved()->count(),
            'today' => Contact::whereDate('created_at', today())->count(),
            'this_week' => Contact::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];
    }
}
