<!-- Pagination Component -->
<div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 rounded-md shadow-sm">
    <div class="flex items-center">
        <button 
            @click="previousPage()" 
            :disabled="!$prevPageUrl"
            class="relative inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium text-gray-700 bg-white border border border-gray-300 hover:bg-gray-50 disabled:opacity-50 cursor-not-allowed">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>
        <span class="text-sm text-gray-700">
            Page {{ $currentPage }} of {{ $lastPage }}
        </div>
        <div class="flex items-center">
            <button 
                @click="nextPage()" 
                :disabled="!$nextPageUrl"
                class="relative inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium text-gray-700 bg-white border border border-gray-300 hover:bg-gray-50 disabled:opacity-50 cursor-not-allowed">
                    <i class="fas fa-chevron-right"></i>
                </button>
        </div>
    </div>
</div>
