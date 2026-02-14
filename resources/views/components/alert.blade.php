@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
    'icon' => true,
    'rounded' => 'md',
])

@php
// Define color classes
$bgClasses = [
    'success' => 'bg-green-50',
    'error' => 'bg-red-50',
    'warning' => 'bg-yellow-50',
    'info' => 'bg-blue-50',
];

$borderClasses = [
    'success' => 'border-green-200',
    'error' => 'border-red-200',
    'warning' => 'border-yellow-200',
    'info' => 'border-blue-200',
];

$textClasses = [
    'success' => 'text-green-800',
    'error' => 'text-red-800',
    'warning' => 'text-yellow-800',
    'info' => 'text-blue-800',
];

$iconClasses = [
    'success' => 'text-green-400',
    'error' => 'text-red-400',
    'warning' => 'text-yellow-400',
    'info' => 'text-blue-400',
];

// Define SVG icon paths
$icons = [
    'success' => 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
    'error' => 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z',
    'warning' => 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z',
    'info' => 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
];

// Set default type if invalid
$type = in_array($type, array_keys($bgClasses)) ? $type : 'info';

// Build base classes
$baseClasses = [
    $bgClasses[$type],
    $borderClasses[$type],
    $textClasses[$type],
    'border',
    'p-4',
    'rounded-' . $rounded,
];
@endphp

<div {{ $attributes->merge(['class' => implode(' ', $baseClasses)]) }} 
     role="alert"
     x-data="{ show: true }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95">
    
    <div class="flex">
        @if($icon && isset($icons[$type]))
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 {{ $iconClasses[$type] }}" 
                     xmlns="http://www.w3.org/2000/svg" 
                     viewBox="0 0 20 20" 
                     fill="currentColor">
                    <path fill-rule="evenodd" 
                          d="{{ $icons[$type] }}" 
                          clip-rule="evenodd" />
                </svg>
            </div>
        @endif
        
        <div class="{{ $icon ? 'ml-3' : '' }} flex-1">
            @if($title)
                <h3 class="font-medium">
                    {{ $title }}
                </h3>
            @endif
            
            <div class="{{ $title ? 'mt-2' : '' }} text-sm">
                {{ $slot }}
            </div>
        </div>
        
        @if($dismissible)
            <div class="ml-auto pl-3">
                <button type="button" 
                        @click="show = false"
                        class="inline-flex {{ $iconClasses[$type] }} hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-current rounded">
                    <span class="sr-only">Dismiss</span>
                    <svg class="h-5 w-5" 
                         xmlns="http://www.w3.org/2000/svg" 
                         viewBox="0 0 20 20" 
                         fill="currentColor">
                        <path fill-rule="evenodd" 
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" 
                              clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif
    </div>
</div>