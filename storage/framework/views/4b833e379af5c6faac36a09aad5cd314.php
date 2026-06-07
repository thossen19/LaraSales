<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order #<?php echo e($po->order_number); ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 24px; margin: 0; color: #000; }
        .header p { margin: 2px 0; font-size: 11px; color: #666; }
        .details { width: 100%; margin-bottom: 20px; }
        .details td { vertical-align: top; padding: 4px 8px; font-size: 11px; }
        .details .label { font-weight: bold; color: #555; width: 140px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th { background: #333; color: #fff; padding: 8px; font-size: 10px; text-align: left; text-transform: uppercase; }
        table.items td { padding: 6px 8px; border-bottom: 1px solid #ddd; font-size: 11px; }
        table.items .right { text-align: right; }
        table.items .center { text-align: center; }
        table.items tr:nth-child(even) { background: #f9f9f9; }
        .totals { width: 300px; margin-left: auto; }
        .totals td { padding: 4px 8px; font-size: 11px; }
        .totals .label { text-align: right; font-weight: bold; }
        .totals .value { text-align: right; width: 100px; }
        .totals .grand { font-size: 14px; font-weight: bold; border-top: 2px solid #333; }
        .footer { position: fixed; bottom: 20px; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Purchase Order</h1>
        <p>Order #: <?php echo e($po->order_number); ?></p>
        <p>Date: <?php echo e($po->order_date ? date('d/m/Y', strtotime($po->order_date)) : ''); ?></p>
        <p>Status: <?php echo e(ucfirst($po->status)); ?></p>
    </div>

    <table class="details">
        <tr>
            <td class="label">Supplier:</td>
            <td><?php echo e($po->supplier->name ?? 'N/A'); ?></td>
            <td class="label">Supplier's Ref:</td>
            <td><?php echo e($po->supp_ref ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label">Delivery Address:</td>
            <td><?php echo e($po->delivery_address ?? ''); ?></td>
            <td class="label">Location:</td>
            <td><?php echo e($po->location ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <td class="label">Currency:</td>
            <td><?php echo e($po->curr_code ?? ($po->supplier->curr_code ?? 'USD')); ?></td>
            <td class="label">Payment Terms:</td>
            <td><?php echo e($po->payment_terms ?? ''); ?></td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>Item Code</th>
                <th>Description</th>
                <th class="right">Quantity</th>
                <th class="center">Unit</th>
                <th class="right">Unit Price</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; $total = 0; ?>
            <?php $__currentLoopData = $po->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $line_total = $item->quantity * $item->unit_price; $total += $line_total; ?>
                <tr>
                    <td><?php echo e($i++); ?></td>
                    <td><?php echo e($item->item->code ?? ''); ?></td>
                    <td><?php echo e($item->description); ?></td>
                    <td class="right"><?php echo e($item->quantity); ?></td>
                    <td class="center"><?php echo e($item->item->unit_of_measure ?? 'each'); ?></td>
                    <td class="right"><?php echo e(number_format($item->unit_price, 2)); ?></td>
                    <td class="right"><?php echo e(number_format($line_total, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Sub-total:</td>
            <td class="value"><?php echo e(number_format($total, 2)); ?></td>
        </tr>
        <tr>
            <td class="label">Tax:</td>
            <td class="value"><?php echo e(number_format($po->tax_amount, 2)); ?></td>
        </tr>
        <tr class="grand">
            <td class="label">Amount Total:</td>
            <td class="value"><?php echo e(number_format($total + $po->tax_amount, 2)); ?></td>
        </tr>
    </table>

    <?php if($po->notes): ?>
    <div style="margin-top: 30px;">
        <strong>Notes:</strong>
        <p style="margin-top: 5px;"><?php echo e($po->notes); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        Purchase Order #<?php echo e($po->order_number); ?> &mdash; Generated by Sales ERP
    </div>
</body>
</html>
<?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/purchases/orders/print.blade.php ENDPATH**/ ?>