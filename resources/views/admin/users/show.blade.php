@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">User Profile</h2>
            <p class="text-muted">Viewing account details for {{ $user->name }}.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ $tab === 'client' ? route('clients.index') : route('users.index') }}" class="btn btn-light border shadow-sm rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route($tab === 'client' ? 'clients.edit' : 'users.edit', $user->id) }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="bi bi-pencil me-2"></i>Edit Account
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="glass-card border-0 shadow-sm p-4 text-center animate-fade-in">
                <div class="position-relative d-inline-block mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-4 text-primary d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                        <i class="bi bi-person-fill" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $user->name }}</h3>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                
                @php
                    $roleClass = match($user->role) {
                        'admin' => 'bg-dark',
                        'superadmin' => 'bg-black',
                        'unit_admin' => 'bg-primary',
                        'manager' => 'bg-success',
                        'staff' => 'bg-info',
                        'client' => 'bg-secondary',
                        default => 'bg-secondary'
                    };
                    $roleLabel = match($user->role) {
                        'admin' => 'Admin',
                        'unit_admin' => 'Unit Admin',
                        'manager' => 'Manager',
                        'staff' => 'Staff',
                        'client' => 'Client',
                        default => $user->role
                    };
                @endphp
                <span class="badge rounded-pill {{ $roleClass }} px-3 py-2 text-uppercase mb-4" style="font-size: 0.75rem;">
                    {{ $roleLabel }}
                </span>

                <div class="border-top pt-4">
                    <div class="row text-start g-3">
                        <div class="col-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">Office/Unit</label>
                            <p class="mb-0 text-dark fw-semibold">{{ $user->office->name ?? 'None Assigned' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">Contact Number</label>
                            <p class="mb-0 text-dark fw-semibold">{{ $user->contact_number ?? 'Not Provided' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">Account Created</label>
                            <p class="mb-0 text-dark fw-semibold">{{ $user->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity/Stats Card -->
        <div class="col-lg-8">
            <div class="glass-card border-0 shadow-sm p-4 animate-fade-in" style="animation-delay: 0.1s;">
                <h4 class="fw-bold text-dark mb-4">Recent Activity</h4>
                
                @if($tab === 'client')
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-4 border text-center">
                                    <div class="text-primary fs-3 mb-1">
                                        <i class="bi bi-cart-fill"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0">{{ $user->orders->count() }}</h5>
                                    <p class="small text-muted mb-0">Total Orders</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-4 border text-center">
                                    <div class="text-success fs-3 mb-1">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0">{{ $user->orders->where('status', 'delivered')->count() }}</h5>
                                    <p class="small text-muted mb-0">Delivered</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-4 border text-center">
                                    <div class="text-warning fs-3 mb-1">
                                        <i class="bi bi-clock-fill"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0">{{ $user->orders->whereIn('status', ['pending', 'processing'])->count() }}</h5>
                                    <p class="small text-muted mb-0">On-going</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle shadow-sm rounded-4 overflow-hidden border">
                        <thead class="bg-light">
                            <tr class="small text-uppercase text-muted">
                                <th class="ps-3">Activity Type</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($tab === 'client')
                                @forelse($user->orders()->latest()->take(5)->get() as $order)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border-primary border-opacity-25 border rounded-pill px-3">Order</span>
                                        </td>
                                        <td>Placed order #{{ $order->id }} ({{ $order->quantity }} gallons)</td>
                                        <td class="text-muted small">{{ $order->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">No recent activity found.</td>
                                    </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td class="ps-3"><span class="badge bg-info bg-opacity-10 text-info border-info border-opacity-25 border rounded-pill px-3">System</span></td>
                                    <td>Account information updated</td>
                                    <td class="text-muted small">{{ $user->updated_at->diffForHumans() }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-3"><span class="badge bg-info bg-opacity-10 text-info border-info border-opacity-25 border rounded-pill px-3">System</span></td>
                                    <td>User account created</td>
                                    <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
