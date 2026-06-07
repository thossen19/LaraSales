<!-- Stats Card Component -->
<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0 {{ $color }} rounded-md p-3">
                <i class="{{ $icon }}"></i>
            </div>
            <div class="ml-4">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">{{ $title }}</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ $value }}</dd>
                </dl>
            </div>
        </div>
        <div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="{{ $trendIconClass }}">
                        <i class="fas {{ $trendIcon }}"></i>
                    </span>
                    <span class="font-medium {{ $trendText }}">{{ $trendValue }}</span>
                    <span class="text-gray-500">from last month</span>
                </div>
            </div>
        </div>
    </div>
</div>
