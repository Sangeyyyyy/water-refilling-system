@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Order Placed Successfully!</h4>
                </div>

                <div class="card-body text-center">
                    <div class="my-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>
                    
                    <h3>Thank you, {{ $order->client_name }}!</h3>
                    <p class="lead">Your order for <strong>{{ $order->quantity }} container(s)</strong> has been received.</p>
                    
                    <div class="alert alert-light border">
                        <p class="mb-1"><strong>Order Reference ID:</strong> {{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                        <p class="mb-1"><strong>Total Amount:</strong> ₱{{ number_format($order->total_amount, 2) }}</p>
                        <p class="mb-1"><strong>Scheduled Delivery:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('l, F j, Y') }}</p>
                    </div>

                    <p class="text-muted">Please prepare the cash payment upon delivery.</p>

                    <a href="{{ route('orders.create') }}" class="btn btn-primary mt-3">Place Another Order</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
