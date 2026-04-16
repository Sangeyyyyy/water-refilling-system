<div id="water-loader-wrapper" class="water-loader-wrapper" style="{{ isset($show) && $show ? '' : 'display: none; opacity: 0; visibility: hidden;' }}">
    <div class="water-loader-container">
        <!-- SVG Water Gallon -->
        <svg class="water-gallon" viewBox="0 0 100 150" xmlns="http://www.w3.org/2000/svg">
            <!-- Bottle Cap/Top -->
            <rect x="35" y="0" width="30" height="15" rx="2" fill="currentColor" class="gallon-cap" />
            
            <!-- Bottle Neck/Outline -->
            <path d="M35 15 L35 25 Q35 35 20 35 L20 35 Q10 35 10 45 L10 140 Q10 150 20 150 L80 150 Q90 150 90 140 L90 45 Q90 35 80 35 L80 35 Q65 35 65 25 L65 15 Z" 
                  fill="none" stroke="currentColor" stroke-width="3" class="gallon-outline" />
            
            <!-- Water Level Mask to keep water inside gallon shape -->
            <defs>
                <clipPath id="gallon-mask">
                    <path d="M20 35 Q10 35 10 45 L10 140 Q10 150 20 150 L80 150 Q90 150 90 140 L90 45 Q90 35 80 35 L80 35 Q65 35 65 25 L65 15 L35 15 L35 25 Q35 35 20 35 Z" />
                </clipPath>
            </defs>

            <!-- Refilling Water -->
            <g clip-path="url(#gallon-mask)">
                <rect x="0" y="150" width="100" height="150" class="water-fill" />
                <!-- Subtle Wave Effect at the top of the water -->
                <path class="water-wave" d="M0 150 Q 25 145 50 150 T 100 150 V 300 H 0 Z" />
            </g>

            <!-- Banner Logo (Moved to front and resized) -->
            <image href="{{ asset('img/water-banner.png') }}" x="15" y="60" width="70" height="70" class="gallon-logo" />
        </svg>
        <div class="loader-text mt-3">Refilling...</div>
    </div>
</div>
