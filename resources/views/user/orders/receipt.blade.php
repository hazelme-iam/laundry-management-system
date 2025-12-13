<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
        }
        .receipt-header h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .receipt-header p {
            color: #6b7280;
            font-size: 14px;
        }
        .receipt-number {
            font-size: 18px;
            color: #3b82f6;
            font-weight: bold;
            margin-top: 10px;
        }
        .section {
            margin-bottom: 30px;
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
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-approved {
            background-color: #dbeafe;
            color: #0c4a6e;
        }
        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
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
            font-size: 13px;
            font-weight: bold;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #f9fafb;
            font-weight: bold;
            color: #1f2937;
        }
        .payment-section {
            background-color: #f0fdf4;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .payment-section .info-row {
            margin-bottom: 8px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        .print-button {
            text-align: center;
            margin-top: 20px;
        }
        .print-button button {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                padding: 0;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <h1>📋 Order Receipt</h1>
            <p>Thank you for your order!</p>
            <div class="receipt-number">Order #{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</div>
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
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">{{ $order->customer->address ?? 'N/A' }}</span>
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
                <span class="info-value">
                    <span class="status-badge status-{{ $order->status }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Weight:</span>
                <span class="info-value">{{ $order->weight ? $order->weight . ' kg' : 'To be measured at shop' }}</span>
            </div>
            @if($order->pickup_date)
            <div class="info-row">
                <span class="info-label">Pickup Date:</span>
                <span class="info-value">{{ $order->pickup_date->format('M d, Y') }}</span>
            </div>
            @endif
            @if($order->estimated_finish)
            <div class="info-row">
                <span class="info-label">Estimated Finish:</span>
                <span class="info-value">{{ $order->estimated_finish->format('M d, Y') }}</span>
            </div>
            @endif
        </div>

        <!-- Items -->
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
            <div class="info-row" style="font-size: 16px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                <span class="info-label">Total Amount:</span>
                <span class="info-value">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="payment-section">
            <div class="section-title" style="border: none; margin-bottom: 10px;">Payment Status (Cash Only)</div>
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
            @if($order->amount_paid > 0)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3);">
                <div class="info-row">
                    <span class="info-label">Cash Given:</span>
                    <span class="info-value">₱{{ number_format($order->amount_paid, 2) }}</span>
                </div>
                @php
                    $change = $order->amount_paid - $order->total_amount;
                @endphp
                @if($change > 0)
                <div class="info-row" style="font-weight: bold; font-size: 15px; color: #10b981;">
                    <span class="info-label">Change:</span>
                    <span class="info-value">₱{{ number_format($change, 2) }}</span>
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
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->payments->sortByDesc('payment_date') as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('M d, Y g:i A') }}</td>
                        <td class="text-right">₱{{ number_format($payment->amount, 2) }}</td>
                        <td class="text-right">₱{{ number_format($payment->cash_given ?? $payment->amount, 2) }}</td>
                        <td class="text-right">₱{{ number_format($payment->change ?? 0, 2) }}</td>
                        <td>{{ $payment->recordedBy->name ?? 'Unknown' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($order->payments->first()?->notes)
            <div style="margin-top: 15px; padding: 10px; background-color: #f3f4f6; border-radius: 6px;">
                <p style="font-size: 13px; color: #374151;"><strong>Notes:</strong></p>
                @foreach($order->payments as $payment)
                    @if($payment->notes)
                    <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">• {{ $payment->notes }}</p>
                    @endif
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <!-- Special Instructions -->
        @if($order->remarks)
        <div class="section">
            <div class="section-title">Special Instructions</div>
            <p style="color: #374151; font-size: 14px; line-height: 1.6;">{{ $order->remarks }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>This is an official receipt for your laundry order.</p>
            <p>Please keep this receipt for your records.</p>
            <p>Generated on {{ now()->format('M d, Y g:i A') }}</p>
        </div>

        <!-- Print Button -->
        <div class="print-button">
            <button onclick="window.print()">🖨️ Print Receipt</button>
        </div>
    </div>
</body>
</html>
