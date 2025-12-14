<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1f2937;
            margin: 0 0 5px 0;
            font-size: 28px;
        }
        .header p {
            color: #6b7280;
            margin: 0;
            font-size: 14px;
        }
        .receipt-number {
            color: #3b82f6;
            font-weight: bold;
            font-size: 18px;
            margin-top: 10px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .info-label {
            color: #6b7280;
            font-weight: 500;
        }
        .info-value {
            color: #1f2937;
            font-weight: 500;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            font-size: 13px;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .text-right {
            text-align: right;
        }
        .payment-section {
            background-color: #f0f9ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 25px;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 30px;
        }
        .success-badge {
            display: inline-block;
            background-color: #dcfce7;
            color: #166534;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Receipt</h1>
            <p>Thank you for your order!</p>
            <div class="receipt-number">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
        </div>

        <!-- Customer Information -->
        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $order->customer->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $order->customer->phone }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $order->customer->email ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Order Details -->
        <div class="section">
            <div class="section-title">Order Details</div>
            <div class="info-row">
                <span class="info-label">Order Date:</span>
                <span class="info-value">{{ $order->created_at->format('M d, Y g:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value"><span class="success-badge">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></span>
            </div>
            <div class="info-row">
                <span class="info-label">Weight:</span>
                <span class="info-value">
                    @if($order->confirmed_weight)
                        {{ $order->confirmed_weight }} kg (Confirmed)
                    @elseif($order->weight)
                        {{ $order->weight }} kg
                    @else
                        To be measured at shop
                    @endif
                </span>
            </div>
            @if($order->estimated_finish)
            <div class="info-row">
                <span class="info-label">Estimated Finish:</span>
                <span class="info-value">{{ $order->estimated_finish->format('M d, Y') }}</span>
            </div>
            @endif
        </div>

        <!-- Items & Add-ons -->
        <div class="section">
            <div class="section-title">Items & Add-ons</div>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Laundry Service (Base)</td>
                        <td class="text-right">1</td>
                        <td class="text-right">₱150.00</td>
                        <td class="text-right">₱150.00</td>
                    </tr>
                    @if($order->add_ons && count($order->add_ons) > 0)
                        <tr style="background-color: #f9fafb; font-weight: bold;">
                            <td colspan="4">Add-ons</td>
                        </tr>
                        @foreach($order->add_ons as $key => $value)
                            @php
                                $addOn = is_int($key) ? $value : $key;
                                $qty = is_int($key) ? 1 : (int) $value;
                                $price = $addOn === 'detergent' ? 16 : 14;
                                $subtotal = $qty * $price;
                            @endphp
                            <tr>
                                <td>
                                    @if($addOn === 'detergent')
                                        🧼 Detergent
                                    @elseif($addOn === 'fabric_conditioner')
                                        ✨ Fabric Conditioner
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $addOn)) }}
                                    @endif
                                </td>
                                <td class="text-right">{{ $qty }}</td>
                                <td class="text-right">₱{{ number_format($price, 2) }}</td>
                                <td class="text-right">₱{{ number_format($subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pricing Summary -->
        <div class="section">
            <div class="section-title">Pricing Summary</div>
            <div class="info-row">
                <span class="info-label">Subtotal:</span>
                <span class="info-value">₱{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->discount > 0)
            <div class="info-row">
                <span class="info-label">Discount:</span>
                <span class="info-value">-₱{{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            <div class="info-row" style="font-size: 16px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-weight: bold;">
                <span class="info-label">Total Amount:</span>
                <span class="info-value">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="payment-section">
            <div class="section-title" style="border: none; margin-bottom: 10px;">Payment Status</div>
            <div class="info-row">
                <span class="info-label">Amount Due:</span>
                <span class="info-value">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Amount Paid:</span>
                <span class="info-value">₱{{ number_format($order->amount_paid, 2) }}</span>
            </div>
            <div class="info-row" style="font-weight: bold; font-size: 15px;">
                <span class="info-label">Balance:</span>
                <span class="info-value">₱{{ number_format($order->total_amount - $order->amount_paid, 2) }}</span>
            </div>
            @if($order->payments && $order->payments->count() > 0)
                @php
                    $totalCashGiven = $order->payments->sum('cash_given');
                    $totalChange = $order->payments->sum('change');
                @endphp
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.1);">
                <div class="info-row">
                    <span class="info-label">Total Cash Given:</span>
                    <span class="info-value">₱{{ number_format($totalCashGiven, 2) }}</span>
                </div>
                @if($totalChange > 0)
                <div class="info-row" style="font-weight: bold; font-size: 15px; color: #10b981;">
                    <span class="info-label">Total Change:</span>
                    <span class="info-value">₱{{ number_format($totalChange, 2) }}</span>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Payment History -->
        @if($order->payments && $order->payments->count() > 0)
        <div class="section">
            <div class="section-title">Payment History</div>
            <table>
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th class="text-right">Amount Paid</th>
                        <th class="text-right">Cash Given</th>
                        <th class="text-right">Change</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->payments->sortByDesc('payment_date') as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('M d, Y g:i A') }}</td>
                        <td class="text-right">₱{{ number_format($payment->amount, 2) }}</td>
                        <td class="text-right">₱{{ number_format($payment->cash_given ?? $payment->amount, 2) }}</td>
                        <td class="text-right">₱{{ number_format($payment->change ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="footer">
            <p>Thank you for choosing our laundry service!</p>
            <p>If you have any questions, please contact us.</p>
        </div>
    </div>
</body>
</html>
