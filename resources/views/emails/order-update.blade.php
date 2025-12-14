<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Update</title>
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
        .order-number {
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
        .message-box {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .message-box p {
            margin: 0;
            color: #1f2937;
            line-height: 1.6;
            font-size: 14px;
        }
        .status-badge {
            display: inline-block;
            background-color: #dbeafe;
            color: #0c4a6e;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 30px;
        }
        .cta-button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Update</h1>
            <p>We have an update about your order</p>
            <div class="order-number">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
        </div>

        <!-- Customer Greeting -->
        <div class="section">
            <p style="color: #1f2937; font-size: 14px; line-height: 1.6;">
                Hi {{ $user->name }},
            </p>
        </div>

        <!-- Message -->
        <div class="message-box">
            <p>{{ $customMessage }}</p>
        </div>

        <!-- Order Status -->
        <div class="section">
            <div class="section-title">Current Order Status</div>
            <div class="info-row">
                <span class="info-label">Order ID:</span>
                <span class="info-value">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value"><span class="status-badge">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></span>
            </div>
            <div class="info-row">
                <span class="info-label">Order Date:</span>
                <span class="info-value">{{ $order->created_at->format('M d, Y g:i A') }}</span>
            </div>
            @if($order->estimated_finish)
            <div class="info-row">
                <span class="info-label">Estimated Finish:</span>
                <span class="info-value">{{ $order->estimated_finish->format('M d, Y') }}</span>
            </div>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="section">
            <div class="section-title">Order Summary</div>
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
            <div class="info-row">
                <span class="info-label">Total Amount:</span>
                <span class="info-value">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Amount Paid:</span>
                <span class="info-value">₱{{ number_format($order->amount_paid, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Balance:</span>
                <span class="info-value">₱{{ number_format($order->total_amount - $order->amount_paid, 2) }}</span>
            </div>
        </div>

        <!-- Call to Action -->
        <div style="text-align: center; margin: 30px 0;">
            <p style="color: #6b7280; font-size: 14px;">
                If you have any questions about your order, please don't hesitate to contact us.
            </p>
        </div>

        <div class="footer">
            <p>Thank you for choosing our laundry service!</p>
            <p>Best regards,<br>The Laundry Team</p>
        </div>
    </div>
</body>
</html>
