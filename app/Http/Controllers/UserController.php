<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'director', 'manager', 'staff']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_filter')) {
            $query->where('role', $request->role_filter);
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();
        $tab = 'staff';
        return view('admin.users.index', compact('users', 'tab'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $offices = Office::orderBy('name')->get();
        $tab = 'staff';
        return view('admin.users.create', compact('offices', 'tab'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            'role' => 'required|string|in:admin,director,manager,staff',
            'office_id' => 'required_if:role,director,manager,staff|nullable|exists:offices,id',
            'contact_number' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => "{$request->first_name} {$request->last_name}",
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'office_id' => $request->office_id,
            'contact_number' => $request->contact_number,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $offices = Office::orderBy('name')->get();
        $tab = 'staff';
        return view('admin.users.edit', compact('user', 'offices', 'tab'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            'role' => 'required|string|in:admin,director,manager,staff',
            'office_id' => 'required_if:role,director,manager,staff|nullable|exists:offices,id',
            'contact_number' => 'nullable|string|max:20',
        ]);

        $data = [
            'name' => "{$request->first_name} {$request->last_name}",
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'office_id' => $request->office_id,
            'contact_number' => $request->contact_number,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
