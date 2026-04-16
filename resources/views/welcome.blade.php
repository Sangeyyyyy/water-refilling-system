@extends('layouts.app')

@section('content')
<div class="hero-carousel">
    <!-- Image Slideshow -->
    <div id="heroSlideshow" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('img/banner.png') }}" class="d-block w-100" alt="Banner">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/carousel2.png') }}" class="d-block w-100" alt="Water 2">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/carousel3.png') }}" class="d-block w-100" alt="Water 3">
            </div>
        </div>
        <!-- Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroSlideshow" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroSlideshow" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroSlideshow" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
    </div>

</div>

<div id="services" class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 text-center hover-lift">
                <div class="feature-icon mb-3">
                    <i class="bi bi-clock-history fs-1 text-primary"></i>
                </div>
                <h3>Fast Delivery</h3>
                <p class="text-muted">Swift and reliable delivery schedule every {{ $deliveryDaysStr ?? 'Tuesday and Friday' }}, ensuring you never run out of water.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 text-center hover-lift">
                <div class="feature-icon mb-3">
                    <i class="bi bi-droplet fs-1 text-primary"></i>
                </div>
                <h3>Purest Quality</h3>
                <p class="text-muted">Multi-stage filtration process that meets the highest standards of safety and purity for your health.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 text-center hover-lift">
                <div class="feature-icon mb-3">
                    <i class="bi bi-clipboard-check fs-1 text-primary"></i>
                </div>
                <h3>Simple Ordering</h3>
                <p class="text-muted">Our smart guest checkout remembers your preferences, making re-ordering as simple as one click.</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white py-5 border-top border-bottom">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-accent">Vision, Mission & Core Values</h2>
            <div class="mx-auto bg-accent rounded-pill mb-3" style="width: 60px; height: 4px;"></div>
        </div>
        <div class="row g-4 justify-content-center">
            <!-- Vision -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center hover-lift">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-eye fs-1 text-accent"></i>
                    </div>
                    <h3 class="fw-bold text-accent">Vision</h3>
                    <p class="text-muted">An Institution Leading in Agri-Fisheries and Socio-Cultural Development in the ASEAN Region</p>
                </div>
            </div>
            <!-- Mission -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center hover-lift">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-bullseye fs-1 text-accent"></i>
                    </div>
                    <h3 class="fw-bold text-accent">Mission</h3>
                    <p class="text-muted"><span class="text-dnsc-green">DNSC</span> shall produce future-ready workforce, create innovative solutions and technologies, empower communities, and uphold good governance towards sustainable development.</p>
                </div>
            </div>
            <!-- Core Values -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center hover-lift">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-flag fs-1 text-accent"></i>
                    </div>
                    <h3 class="fw-bold text-accent">Core Values</h3>
                    <ul class="list-unstyled text-muted mb-0 small text-start d-inline-block">
                        <li class="mb-1"><i class="bi bi-check2-circle text-accent me-2"></i>Stewardship</li>
                        <li class="mb-1"><i class="bi bi-check2-circle text-accent me-2"></i>Adaptability and Excellence</li>
                        <li class="mb-1"><i class="bi bi-check2-circle text-accent me-2"></i>Integrity and Innovativeness</li>
                        <li><i class="bi bi-check2-circle text-accent me-2"></i>Love of God and Country</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-5 bg-white border-top">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="h1 fw-bold text-accent mb-4">Exclusive Institutional Rate</h2>
                <p class="lead text-muted mb-4">We provide high-quality, multi-filtered drinking water at special rates for <span class="text-dnsc-green">DNSC</span> offices and units.</p>
                
                <div class="row g-4 mb-5">
                    <div class="col-sm-6">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="benefit-text">
                                <h6 class="mb-0 fw-bold">PPMP Integrated</h6>
                                <small class="opacity-75">Automatic deduction</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="benefit-text">
                                <h6 class="mb-0 fw-bold">Certified Pure</h6>
                                <small class="opacity-75">Institutional standards</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div class="benefit-text">
                                <h6 class="mb-0 fw-bold">Direct Delivery</h6>
                                <small class="opacity-75">To your office/unit</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="benefit-text">
                                <h6 class="mb-0 fw-bold">Smart Scheduling</h6>
                                <small class="opacity-75">Scheduled Delivery</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5 offset-lg-1 col-12 mt-4 mt-lg-0">
                <div class="institutional-card p-4 p-md-5 shadow-lg text-center animate-fade-in">
                    <span class="badge bg-white text-accent rounded-pill px-3 py-2 mb-4 fw-bold">BEST VALUE</span>
                    <div class="pricing-value">₱{{ isset($unitPrice) ? number_format($unitPrice, 0) : '25' }}</div>
                    <div class="pricing-unit mb-4">Per 5-Gallon Container</div>
                    <p class="mb-5 opacity-75">Get fresh, clean water delivered straight to your office with zero hassle.</p>
                    @if(Auth::guard('web')->check() || Auth::guard('client')->check())
                    <a href="{{ route('orders.create') }}" class="btn btn-pricing btn-lg w-100 rounded-pill">
                        Order Refill Now <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                    @else
                    <div class="alert alert-info border-0 shadow-sm rounded-4 py-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Please <a href="{{ route('login') }}" class="fw-bold text-accent">Login</a> to place an order.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-light py-5">
    <div class="container text-center">
        <h2 class="mb-4">Internal Access Only</h2>
        <p class="text-muted mb-0">This portal is specifically for <span class="text-dnsc-green">DNSC</span> offices, units, and personnel. Ensure your PPMP is updated for seamless transactions.</p>
    </div>
</div>
@endsection
