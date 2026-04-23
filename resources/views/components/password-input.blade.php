@props([
    'name' => 'password', 
    'label' => 'Password', 
    'required' => true,
    'withConfirmation' => true
])

<div class="mb-3 position-relative password-component-container">
    <div class="form-floating position-relative">
        <input type="password" 
               name="{{ $name }}" 
               id="{{ $name }}" 
               class="form-control password-input-field @error($name) is-invalid @enderror" 
               placeholder="{{ $label }}" 
               {{ $required ? 'required' : '' }}>
        <label for="{{ $name }}" class="text-muted">{{ $label }}</label>
        
        <button type="button" class="btn btn-sm text-secondary position-absolute end-0 top-50 translate-middle-y me-2 fw-semibold js-toggle-password" tabindex="-1" style="z-index: 10;">
            Show
        </button>
        
        @error($name)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    
    <div class="password-strength-container mt-3" style="display: none;">
        <div class="d-flex align-items-center mb-3">
            <span class="text-secondary fw-bold me-2" style="font-size: 0.85rem;">Password strength</span>
            <div class="flex-grow-1 mx-2 bg-light d-flex" style="height: 4px; border-radius: 2px; overflow: hidden; gap: 2px;">
                <div class="password-strength-bar section-1" style="width: 33%; height: 100%; transition: background-color 0.3s ease; background-color: #e9ecef;"></div>
                <div class="password-strength-bar section-2" style="width: 33%; height: 100%; transition: background-color 0.3s ease; background-color: #e9ecef;"></div>
                <div class="password-strength-bar section-3" style="width: 33%; height: 100%; transition: background-color 0.3s ease; background-color: #e9ecef;"></div>
            </div>
            <span class="password-strength-text fw-bold text-muted" style="font-size: 0.85rem; width: 45px; text-align: right;">Weak</span>
        </div>
        
        <div class="password-requirements">
            <span class="d-block text-secondary fw-bold mb-2" style="font-size: 0.85rem;">Must contain at least</span>
            <ul class="list-unstyled mb-0 ms-1" style="font-size: 0.85rem;">
                <li class="req-length text-muted mb-2 d-flex align-items-center">
                    <i class="bi bi-circle-fill me-2" style="font-size: 0.4rem;"></i> 8 characters
                </li>
                <li class="req-lowercase text-muted mb-2 d-flex align-items-center">
                    <i class="bi bi-circle-fill me-2" style="font-size: 0.4rem;"></i> 1 lower case character
                </li>
                <li class="req-special text-muted d-flex align-items-center">
                    <i class="bi bi-circle-fill me-2" style="font-size: 0.4rem;"></i> 1 special character
                </li>
            </ul>
        </div>
    </div>
</div>

@if($withConfirmation)
<div class="mb-3 position-relative password-confirmation-container">
    <div class="form-floating position-relative">
        <input type="password" 
               name="{{ $name }}_confirmation" 
               id="{{ $name }}-confirm" 
               class="form-control password-confirm-field" 
               placeholder="Confirm {{ $label }}" 
               {{ $required ? 'required' : '' }}>
        <label for="{{ $name }}-confirm" class="text-muted">Confirm {{ $label }}</label>
        
        <button type="button" class="btn btn-sm text-secondary position-absolute end-0 top-50 translate-middle-y me-2 fw-semibold js-toggle-password-confirm" tabindex="-1" style="z-index: 10;">
            Show
        </button>
    </div>
</div>
@endif

@once
@push('styles')
<style>
    .req-met {
        color: #198754 !important; /* success green */
    }
    .req-met i {
        color: #198754 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordFields = document.querySelectorAll('.password-input-field');
        
        passwordFields.forEach(field => {
            const container = field.closest('.password-component-container');
            const toggleBtn = container.querySelector('.js-toggle-password');
            const strengthContainer = container.querySelector('.password-strength-container');
            const bars = {
                1: container.querySelector('.section-1'),
                2: container.querySelector('.section-2'),
                3: container.querySelector('.section-3')
            };
            const textElement = container.querySelector('.password-strength-text');
            
            const reqLength = container.querySelector('.req-length');
            const reqLower = container.querySelector('.req-lowercase');
            const reqSpecial = container.querySelector('.req-special');

            // Handle Show/Hide toggle
            toggleBtn.addEventListener('click', function() {
                const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                field.setAttribute('type', type);
                this.textContent = type === 'password' ? 'Show' : 'Hide';
            });

            // Handle confirm show/hide if exists
            const nextConfirmContainer = container.nextElementSibling;
            if (nextConfirmContainer && nextConfirmContainer.classList.contains('password-confirmation-container')) {
                const confirmField = nextConfirmContainer.querySelector('.password-confirm-field');
                const toggleConfirmBtn = nextConfirmContainer.querySelector('.js-toggle-password-confirm');
                
                if (toggleConfirmBtn && confirmField) {
                    toggleConfirmBtn.addEventListener('click', function() {
                        const type = confirmField.getAttribute('type') === 'password' ? 'text' : 'password';
                        confirmField.setAttribute('type', type);
                        this.textContent = type === 'password' ? 'Show' : 'Hide';
                    });
                }
            }

            // Show strength container when input has value or is focused
            field.addEventListener('focus', function() {
                strengthContainer.style.display = 'block';
            });

            field.addEventListener('input', function() {
                const val = this.value;
                
                if (val.length === 0) {
                    // Reset all
                    [reqLength, reqLower, reqSpecial].forEach(el => el.classList.remove('req-met'));
                    resetBars(bars, textElement);
                    return;
                }
                
                strengthContainer.style.display = 'block';

                let score = 0;

                // Check length
                if (val.length >= 8) {
                    reqLength.classList.add('req-met');
                    score++;
                } else {
                    reqLength.classList.remove('req-met');
                }

                // Check lowercase
                if (/[a-z]/.test(val)) {
                    reqLower.classList.add('req-met');
                    score++;
                } else {
                    reqLower.classList.remove('req-met');
                }

                // Check special character
                if (/[^A-Za-z0-9]/.test(val)) {
                    reqSpecial.classList.add('req-met');
                    score++;
                } else {
                    reqSpecial.classList.remove('req-met');
                }

                updateStrengthUI(score, bars, textElement);
            });
            
            // Trigger initial input event on load if field already has value (e.g. browser autofill)
            if(field.value) {
                field.dispatchEvent(new Event('input'));
            }
        });

        function resetBars(bars, textElement) {
            bars[1].style.backgroundColor = '#e9ecef';
            bars[2].style.backgroundColor = '#e9ecef';
            bars[3].style.backgroundColor = '#e9ecef';
            textElement.textContent = 'Weak';
            textElement.style.color = '#6c757d';
        }

        function updateStrengthUI(score, bars, textElement) {
            resetBars(bars, textElement);
            
            if (score === 1) {
                bars[1].style.backgroundColor = '#dc3545'; // Danger Red
                textElement.textContent = 'Weak';
                textElement.style.color = '#dc3545';
            } else if (score === 2) {
                bars[1].style.backgroundColor = '#ffc107'; // Warning Yellow
                bars[2].style.backgroundColor = '#ffc107';
                textElement.textContent = 'Fair';
                textElement.style.color = '#ffc107';
            } else if (score >= 3) {
                bars[1].style.backgroundColor = '#bada55'; // Custom Green matching the image
                bars[2].style.backgroundColor = '#bada55';
                bars[3].style.backgroundColor = '#bada55';
                
                // Override for the specific pure green shown in the image #c2e000 or similar
                bars[1].style.backgroundColor = '#cce514'; 
                bars[2].style.backgroundColor = '#cce514';
                bars[3].style.backgroundColor = '#e9ecef'; // third bar is actually gray in the image when strong? 
                // Ah, wait! The image shows the first two bars filled with light greenish yellow, and third is gray!
                // Actually, let's keep it simple: 3 segments, 1 is red, 2 is yellow, 3 is green.
                bars[1].style.backgroundColor = '#bce615'; 
                bars[2].style.backgroundColor = '#bce615';
                bars[3].style.backgroundColor = '#bce615'; // 3 means all conditions met = strong
                
                textElement.textContent = 'Strong';
                textElement.style.color = '#7a960b'; // Darker text color matching image
            }
        }
    });
</script>
@endpush
@endonce
