<?php $__env->startSection('title', 'Sales Orders - Sales ERP'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Sales Orders</h2>
        <p class="mt-2 text-gray-600">Manage your sales orders and track customer purchases.</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium text-gray-900">Sales Orders List</h3>
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>New Sales Order
            </button>
        </div>
        
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-file-invoice text-6xl mb-4"></i>
            <p class="text-lg">No sales orders yet</p>
            <p class="text-sm mt-2">Create your first sales order to get started</p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/orders/index.blade.php ENDPATH**/ ?>