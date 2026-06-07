<!-- Alert Component -->
<div class="{{ $class }} border-l-4 border-{{ $borderColor }} rounded-md p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="{{ $icon }} text-{{ $textColor }}"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium {{ $textColor }}">{{ $message }}</p>
            @if (isset($description))
                <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
            @endif
        </div>
        <button type="button" 
                class="ml-4 text-sm font-medium text-{{ $buttonTextColor }}">
                    {{ $buttonText }}
                </button>
    </div>
</div>
