<!-- Breadcrumb Component -->
<nav class="flex" aria-label="breadcrumb">
    <ol class="flex items-center space-x-2">
        @foreach ($breadcrumbs as $key => $breadcrumb)
            <li class="flex items-center">
                @if ($key === $current)
                    <li class="flex items-center">
                        <span class="text-gray-500">{{ $key }}</span>
                    </li>
                    @else
                    <li class="flex items-center">
                        <a href="{{ $url }}" class="text-gray-500 hover:text-gray-700 hover:text-blue-600">{{ $key }}</a>
                    </li>
                @endif
            </li>
        </ol>
    </ol>
</nav>
