<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->quotation_number }}</title>
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
        <h1>Sales Quotation</h1>
        <p>Quotation #: {{ $quotation->quotation_number }}</p>
        <p>Date: {{ $quotation->quotation_date ? date('d/m/Y', strtotime($quotation->quotation_date)) : '' }}</p>
        <p>Valid Until: {{ $quotation->expiry_date ? date('d/m/Y', strtotime($quotation->expiry_date)) : '' }}</p>
        <p>Status: {{ ucfirst($quotation->status ?? 'draft') }}</p>
    </div>

    <table class="details">
        <tr>
            <td class="label">Customer:</td>
            <td>{{ $quotation->customer->name ?? 'N/A' }}</td>
            <td class="label">Reference:</td>
            <td>{{ $quotation->reference ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Sales Person:</td>
            <td>{{ $quotation->salesPerson->name ?? 'N/A' }}</td>
            <td class="label">Sales Type:</td>
            <td>{{ $quotation->salesType->type_name ?? 'N/A' }}</td>
        </tr>
        @if($quotation->deliver_to)
        <tr>
            <td class="label">Deliver To:</td>
            <td colspan="3">{{ $quotation->deliver_to }}</td>
        </tr>
        @endif
        @if($quotation->delivery_address)
        <tr>
            <td class="label">Address:</td>
            <td colspan="3">{{ $quotation->delivery_address }}</td>
        </tr>
        @endif
        @if($quotation->phone)
        <tr>
            <td class="label">Phone:</td>
            <td colspan="3">{{ $quotation->phone }}</td>
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
            @foreach($quotation->lineItems as $item)
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
        @if($quotation->discount_amount > 0)
        <tr>
            <td class="label">Discount:</td>
            <td class="value">({{ number_format($quotation->discount_amount, 2) }})</td>
        </tr>
        @endif
        @if($quotation->freight_cost > 0)
        <tr>
            <td class="label">Shipping:</td>
            <td class="value">{{ number_format($quotation->freight_cost, 2) }}</td>
        </tr>
        @endif
        @if($quotation->tax_amount > 0)
        <tr>
            <td class="label">Tax:</td>
            <td class="value">{{ number_format($quotation->tax_amount, 2) }}</td>
        </tr>
        @endif
        <tr class="grand">
            <td class="label">Amount Total:</td>
            <td class="value">{{ number_format($quotation->total_amount, 2) }}</td>
        </tr>
    </table>

    @if($quotation->customer_notes)
    <div style="margin-top: 30px;">
        <strong>Notes:</strong>
        <p style="margin-top: 5px;">{{ $quotation->customer_notes }}</p>
    </div>
    @endif

    <div class="footer">
        Quotation #{{ $quotation->quotation_number }} &mdash; Generated by FA-SAAS
    </div>
</body>
</html>
