@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">User Management</h2>
            <p class="text-muted">Manage system administrators, staff, managers, and clients.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ $tab === 'client' ? route('clients.create') : route('users.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="bi bi-person-plus me-2"></i>New {{ $tab === 'client' ? 'Client' : 'User' }}
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <ul class="nav nav-pills bg-white p-1 rounded-pill d-inline-flex border shadow-sm" style="height: 48px;">
            <li class="nav-item h-100">
                <a class="nav-link rounded-pill px-4 h-100 d-flex align-items-center {{ $tab === 'staff' ? 'active shadow-sm' : 'text-muted' }}" href="{{ route('users.index') }}">
                    <i class="bi bi-shield-lock me-2"></i>Internal Staff
                </a>
            </li>
            <li class="nav-item h-100">
                <a class="nav-link rounded-pill px-4 h-100 d-flex align-items-center {{ $tab === 'client' ? 'active shadow-sm' : 'text-muted' }}" href="{{ route('clients.index') }}">
                    <i class="bi bi-people me-2"></i>Clients
                </a>
            </li>
        </ul>

        <form action="{{ $tab === 'client' ? route('clients.index') : route('users.index') }}" method="GET" class="d-flex gap-2 align-items-center">
            @if($tab === 'staff')
                <select name="role_filter" class="form-select border-0 shadow-sm rounded-pill px-3 bg-white" onchange="this.form.submit()" style="height: 48px;">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role_filter') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="director" {{ request('role_filter') === 'director' ? 'selected' : '' }}>Director</option>
                    <option value="manager" {{ request('role_filter') === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="staff" {{ request('role_filter') === 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
            @elseif($tab === 'client')
                <select name="office_filter" class="form-select border-0 shadow-sm rounded-pill px-3 bg-white" onchange="this.form.submit()" style="height: 48px; max-width: 250px;">
                    <option value="">All Offices</option>
                    @if(isset($offices))
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ request('office_filter') == $office->id ? 'selected' : '' }}>
                                {{ $office->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            @endif
            
            <div class="d-flex align-items-center shadow-sm bg-white rounded-pill border p-1" style="height: 48px; min-width: 280px;">
                <span class="text-muted px-3 d-flex align-items-center h-100">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" class="form-control border-0 shadow-none bg-transparent h-100 px-0" placeholder="Search users..." value="{{ request('search') }}">
                <button class="btn btn-primary px-4 rounded-pill h-100 d-flex align-items-center justify-content-center" style="min-width: 100px;" type="submit">Search</button>
            </div>
        </form>
    </div>

    <div class="glass-card border-0 animate-fade-in shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-secondary small text-uppercase">
                        <th class="ps-4">Name</th>
                        <th>Email</th>
                        <th>Office</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill fs-5"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block">{{ $user->name }}</span>
                                    @if(auth()->id() === $user->id)
                                        <span class="badge bg-secondary rounded-pill tiny" style="font-size: 0.6rem;">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td class="small text-muted">
                            <div class="text-truncate" style="max-width: 250px;" title="{{ $user->office->name ?? 'N/A' }}">
                                {{ $user->office->name ?? 'N/A' }}
                            </div>
                        </td>
                        <td>
                            @php
                                $roleClass = match($user->role) {
                                    'admin' => 'bg-primary',
                                    'director' => 'bg-dark',
                                    'manager' => 'bg-success',
                                    'staff' => 'bg-info',
                                    'client' => 'bg-secondary',
                                    default => 'bg-secondary'
                                };
                                $roleLabel = match($user->role) {
                                    'admin' => 'Admin',
                                    'director' => 'Director',
                                    'manager' => 'Manager',
                                    'staff' => 'Staff',
                                    'client' => 'Client',
                                    default => $user->role
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $roleClass }} px-2 py-1 text-uppercase" style="font-size: 0.65rem;">
                                {{ $roleLabel }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route($tab === 'client' ? 'clients.show' : 'users.show', $user->id) }}" class="btn btn-sm btn-light border me-1" title="View User">
                                     <i class="bi bi-eye"></i>
                                 </a>
                                <a href="{{ route($tab === 'client' ? 'clients.edit' : 'users.edit', $user->id) }}" class="btn btn-sm btn-light border me-1" title="Edit User">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route($tab === 'client' ? 'clients.destroy' : 'users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                            No users found in this category.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
