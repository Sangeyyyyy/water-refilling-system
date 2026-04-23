<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the clients.
     */
    public function index(Request $request)
    {
        $query = Client::with('office');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('office_filter')) {
            $query->where('office_id', $request->office_filter);
        }

        $users = $query->orderBy('first_name')->orderBy('last_name')->paginate(10)->withQueryString();
        $offices = \App\Models\Office::orderBy('name')->get();
        $tab = 'client';
        return view('admin.users.index', compact('users', 'tab', 'offices'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        $offices = Office::orderBy('name')->get();
        $tab = 'client';
        return view('admin.users.create', compact('offices', 'tab'));
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[^A-Za-z0-9]).+$/',
            'office_id' => 'nullable|exists:offices,id',
            'contact_number' => 'required|string|max:20',
        ]);

        Client::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'office_id' => $request->office_id,
            'contact_number' => $request->contact_number,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified client.
     */
    public function show($id)
    {
        $user = Client::findOrFail($id);
        $tab = 'client';
        return view('admin.users.show', compact('user', 'tab'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit($id)
    {
        $user = Client::findOrFail($id);
        $offices = Office::orderBy('name')->get();
        $tab = 'client';
        return view('admin.users.edit', compact('user', 'offices', 'tab'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Client::findOrFail($id);
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('clients')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[^A-Za-z0-9]).+$/',
            'office_id' => 'nullable|exists:offices,id',
            'contact_number' => 'required|string|max:20',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'office_id' => $request->office_id,
            'contact_number' => $request->contact_number,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy($id)
    {
        $user = Client::findOrFail($id);
        $user->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
