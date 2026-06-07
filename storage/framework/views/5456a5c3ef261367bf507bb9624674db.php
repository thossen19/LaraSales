<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', config('app.name', 'Sales ERP')); ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <?php echo $__env->make('components.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="flex-1 flex flex-col">
            <?php echo $__env->make('components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <main class="flex-1 p-6 overflow-auto">
                <?php if(session('success')): ?>
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                        <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove();">
                            <span class="text-green-500">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if(session('error')): ?>
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                        <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove();">
                            <span class="text-red-500">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/layouts/app.blade.php ENDPATH**/ ?>