<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Credit Note #<?php echo e($creditNote->credit_note_number); ?></title>
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
        <h1>Credit Note</h1>
        <p>Credit Note #: <?php echo e($creditNote->credit_note_number); ?></p>
        <p>Date: <?php echo e($creditNote->credit_date ? date('d/m/Y', strtotime($creditNote->credit_date)) : ''); ?></p>
        <p>Status: <?php echo e(ucfirst($creditNote->status ?? 'approved')); ?></p>
        <?php if($creditNote->reason): ?>
        <p>Reason: <?php echo e($creditNote->reason); ?></p>
        <?php endif; ?>
    </div>

    <table class="details">
        <tr>
            <td class="label">Customer:</td>
            <td><?php echo e($creditNote->customer->name ?? 'N/A'); ?></td>
            <td class="label">Branch:</td>
            <td><?php echo e($creditNote->branch->branch_name ?? 'N/A'); ?></td>
        </tr>
        <?php if($creditNote->originalInvoice): ?>
        <tr>
            <td class="label">Original Invoice:</td>
            <td colspan="3"><?php echo e($creditNote->originalInvoice->order_number); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($creditNote->memo): ?>
        <tr>
            <td class="label">Memo:</td>
            <td colspan="3"><?php echo e($creditNote->memo); ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>Item Code</th>
                <th>Description</th>
                <th class="right">Quantity</th>
                <th class="right">Unit Price</th>
                <th class="right">Discount</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; $subtotal = 0; ?>
            <?php $__currentLoopData = $creditNote->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $subtotal += $item->line_total; ?>
                <tr>
                    <td><?php echo e($i++); ?></td>
                    <td><?php echo e($item->item_code); ?></td>
                    <td><?php echo e($item->description); ?></td>
                    <td class="right"><?php echo e(number_format($item->quantity, 2)); ?></td>
                    <td class="right"><?php echo e(number_format($item->unit_price, 2)); ?></td>
                    <td class="right"><?php echo e(number_format($item->discount_percentage, 2)); ?>%</td>
                    <td class="right"><?php echo e(number_format($item->line_total, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Sub-total:</td>
            <td class="value"><?php echo e(number_format($subtotal, 2)); ?></td>
        </tr>
        <?php if($creditNote->discount_amount > 0): ?>
        <tr>
            <td class="label">Discount:</td>
            <td class="value">(<?php echo e(number_format($creditNote->discount_amount, 2)); ?>)</td>
        </tr>
        <?php endif; ?>
        <?php if($creditNote->tax_amount > 0): ?>
        <tr>
            <td class="label">Tax:</td>
            <td class="value"><?php echo e(number_format($creditNote->tax_amount, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <tr class="grand">
            <td class="label">Total Credit:</td>
            <td class="value"><?php echo e(number_format($creditNote->total_amount, 2)); ?></td>
        </tr>
    </table>

    <div class="footer">
        Credit Note #<?php echo e($creditNote->credit_note_number); ?> &mdash; Generated by FA-SAAS
    </div>
</body>
</html>
<?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/credit-notes/print.blade.php ENDPATH**/ ?>