<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Order #{{ $order->order_number }}</title>
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
        table.items .left { text-align: left; }
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
        <h1>Sales Order</h1>
        <p>Order #: {{ $order->order_number }}</p>
        <p>Date: {{ $order->order_date ? date('d/m/Y', strtotime($order->order_date)) : '' }}</p>
        <p>Delivery Date: {{ $order->delivery_date ? date('d/m/Y', strtotime($order->delivery_date)) : '' }}</p>
        <p>Status: {{ ucfirst($order->status ?? 'pending') }}</p>
    </div>

    <table class="details">
        <tr>
            <td class="label">Customer:</td>
            <td>{{ $order->customer->name ?? 'N/A' }}</td>
            <td class="label">Branch:</td>
            <td>{{ $order->customerBranch->branch_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Sales Person:</td>
            <td>{{ $order->salesPerson->name ?? 'N/A' }}</td>
            <td class="label">Sales Type:</td>
            <td>{{ $order->salesType->type_name ?? 'N/A' }}</td>
        </tr>
        @if($order->delivery_address)
        <tr>
            <td class="label">Delivery Address:</td>
            <td colspan="3">{{ $order->delivery_address }}</td>
        </tr>
        @endif
        @if($order->internal_notes)
        <tr>
            <td class="label">Customer Ref:</td>
            <td colspan="3">{{ $order->internal_notes }}</td>
        </tr>
        @endif
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
            @php $i = 1; $subtotal = 0; @endphp
            @foreach($order->lineItems as $item)
                @php $line_total = $item->line_total; $subtotal += $line_total; @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="right">{{ number_format($item->quantity, 2) }}</td>
                    <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="right">{{ number_format($item->discount_percentage, 2) }}%</td>
                    <td class="right">{{ number_format($line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Sub-total:</td>
            <td class="value">{{ number_format($subtotal, 2) }}</td>
        </tr>
        @if($order->discount_amount > 0)
        <tr>
            <td class="label">Discount:</td>
            <td class="value">({{ number_format($order->discount_amount, 2) }})</td>
        </tr>
        @endif
        @if($order->tax_amount > 0)
        <tr>
            <td class="label">Tax:</td>
            <td class="value">{{ number_format($order->tax_amount, 2) }}</td>
        </tr>
        @endif
        <tr class="grand">
            <td class="label">Amount Total:</td>
            <td class="value">{{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    @if($order->customer_notes)
    <div style="margin-top: 30px;">
        <strong>Notes:</strong>
        <p style="margin-top: 5px;">{{ $order->customer_notes }}</p>
    </div>
    @endif

    <div class="footer">
        Sales Order #{{ $order->order_number }} &mdash; Generated by FA-SAAS
    </div>
</body>
</html>
