@props([
    'modalId' => 'confirmationModal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmColor' => 'blue', // blue, red, green, etc.
    'confirmAction' => '',
    'method' => 'POST',
    'formId' => '',
    'showFooter' => true,
    'showIcon' => true,
    'icon' => null, // Custom SVG icon
    'size' => 'md', // sm, md, lg, xl
    'closeOnBackdropClick' => true,
])

@php
    // Size classes
    $sizeClasses = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
    ][$size] ?? 'sm:max-w-md';

    // Color classes
    $colorClassesArray = [
        'blue' => [
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-600',
            'button' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            'ring' => 'ring-blue-500',
        ],
        'red' => [
            'bg' => 'bg-red-100',
            'text' => 'text-red-600',
            'button' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
            'ring' => 'ring-red-500',
        ],
        'green' => [
            'bg' => 'bg-green-100',
            'text' => 'text-green-600',
            'button' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
            'ring' => 'ring-green-500',
        ],
        'yellow' => [
            'bg' => 'bg-yellow-100',
            'text' => 'text-yellow-600',
            'button' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
            'ring' => 'ring-yellow-500',
        ],
        'amber' => [
            'bg' => 'bg-amber-100',
            'text' => 'text-amber-600',
            'button' => 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500',
            'ring' => 'ring-amber-500',
        ],
        'indigo' => [
            'bg' => 'bg-indigo-100',
            'text' => 'text-indigo-600',
            'button' => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
            'ring' => 'ring-indigo-500',
        ],
        'purple' => [
            'bg' => 'bg-purple-100',
            'text' => 'text-purple-600',
            'button' => 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500',
            'ring' => 'ring-purple-500',
        ],
    ];
    
    $colorClasses = $colorClassesArray[$confirmColor] ?? $colorClassesArray['blue'];

    // Default icon based on color
    $defaultIcon = $confirmColor === 'red' 
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
@endphp

<div id="{{ $modalId }}" 
     class="fixed inset-0 z-50 overflow-y-auto hidden"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true"
     x-data="{ open: false }"
     x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     @keydown.escape.window="if(open) closeModal('{{ $modalId }}')">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm"
         x-show="open"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @if($closeOnBackdropClick) @click="closeModal('{{ $modalId }}')" @endif>
    </div>

    <!-- Modal Container - Centered -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Modal Panel -->
        <div class="relative inline-block align-middle bg-white rounded-2xl shadow-2xl transform transition-all w-full {{ $sizeClasses }} overflow-hidden"
             @click.stop
             x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Close Button (Top-right) -->
            <button type="button"
                    onclick="closeModal('{{ $modalId }}')"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 rounded-full p-1.5 hover:bg-gray-100 transition-colors"
                    aria-label="Close">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Content -->
            <div class="px-8 pt-10 pb-8">
                @if($showIcon)
                    <!-- Icon Container -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full {{ $colorClasses['bg'] }} mb-6">
                        @if($icon)
                            {!! $icon !!}
                        @else
                            <svg class="h-8 w-8 {{ $colorClasses['text'] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {!! $defaultIcon !!}
                            </svg>
                        @endif
                    </div>
                @endif

                <!-- Title -->
                <h3 class="text-2xl font-semibold text-gray-900 mb-3 text-center" id="modal-title">
                    {{ $title }}
                </h3>

                <!-- Message -->
                <div class="mt-4">
                    <p class="text-gray-600 text-center leading-relaxed">
                        {{ $message }}
                    </p>
                </div>
            </div>

            <!-- Footer Buttons -->
            @if($showFooter)
                <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200 sm:flex sm:flex-row-reverse sm:gap-3">
                    @if($confirmAction)
                        <form action="{{ $confirmAction }}" method="POST" id="{{ $formId ?: 'modalForm' }}" class="inline w-full sm:w-auto">
                            @csrf
                            @if($method !== 'POST')
                                @method($method)
                            @endif
                            <button type="submit" 
                                    class="w-full sm:w-auto px-6 py-3 rounded-lg font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] {{ $colorClasses['button'] }} text-white">
                                {{ $confirmText }}
                            </button>
                        </form>
                    @else
                        <button type="button" 
                                onclick="document.getElementById('{{ $formId ?: 'modalForm' }}').submit();"
                                class="w-full sm:w-auto px-6 py-3 rounded-lg font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] {{ $colorClasses['button'] }} text-white">
                            {{ $confirmText }}
                        </button>
                    @endif
                    
                    <button type="button" 
                            onclick="closeModal('{{ $modalId }}')"
                            class="mt-3 w-full sm:w-auto px-6 py-3 rounded-lg font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 shadow-sm transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] sm:mt-0">
                        {{ $cancelText }}
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal._open = true;
        
        // Trigger Alpine.js show
        if (modal._x_dataStack) {
            modal._x_dataStack[0].open = true;
        }
        
        // Prevent body scroll
        document.body.classList.add('overflow-hidden');
        
        // Focus trap
        modal.setAttribute('aria-hidden', 'false');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal._open = false;
        
        // Trigger Alpine.js hide
        if (modal._x_dataStack) {
            modal._x_dataStack[0].open = false;
        }
        
        // Restore body scroll
        document.body.classList.remove('overflow-hidden');
        
        // Remove focus trap
        modal.setAttribute('aria-hidden', 'true');
    }
}

// Initialize modals on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    // Close all modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id$="Modal"]').forEach(function(modal) {
                if (!modal.classList.contains('hidden') && modal._open) {
                    closeModal(modal.id);
                }
            });
        }
    });
    
    // Initialize Alpine.js data for each modal
    document.querySelectorAll('[id$="Modal"]').forEach(function(modal) {
        modal._open = false;
    });
});

// Utility function to open modal with custom content
function openModalWithContent(config) {
    const modalId = config.modalId || 'dynamicModal';
    
    // Create or update modal
    let modal = document.getElementById(modalId);
    if (!modal) {
        modal = document.createElement('div');
        modal.id = modalId;
        document.body.appendChild(modal);
    }
    
    // Set modal content
    modal.innerHTML = `
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm"></div>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden">
                    <div class="px-8 pt-10 pb-8">
                        ${config.icon || ''}
                        <h3 class="text-2xl font-semibold text-gray-900 mb-3 text-center">${config.title}</h3>
                        <p class="text-gray-600 text-center leading-relaxed">${config.message}</p>
                    </div>
                    <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
                        <button onclick="closeModal('${modalId}')" class="px-6 py-3 rounded-lg font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                            ${config.cancelText || 'Cancel'}
                        </button>
                        <button onclick="${config.confirmAction || `closeModal('${modalId}')`}" class="px-6 py-3 rounded-lg font-medium bg-blue-600 text-white hover:bg-blue-700">
                            ${config.confirmText || 'Confirm'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}
</script>