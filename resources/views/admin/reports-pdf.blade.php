<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laundry Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #1f2937;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #6b7280;
            font-size: 14px;
        }
        
        .date-range {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .summary-card {
            border: 1px solid #e5e7eb;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9fafb;
        }
        
        .summary-card h3 {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .breakdown-item label {
            color: #6b7280;
        }
        
        .breakdown-item .value {
            font-weight: bold;
            color: #1f2937;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        table thead {
            background-color: #f3f4f6;
        }
        
        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            font-size: 12px;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-ready {
            background-color: #dbeafe;
            color: #0c4a6e;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .payment-fully-paid {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .payment-partially-paid {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .payment-unpaid {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Laundry Management Report</h1>
            <p>Business Analytics & Performance Summary</p>
        </div>
        
        <!-- Date Range -->
        <div class="date-range">
            Report Period: {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}
        </div>
        
        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Orders</h3>
                <div class="value">{{ $totalOrders }}</div>
            </div>
            <div class="summary-card">
                <h3>Total Revenue</h3>
                <div class="value">₱{{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="summary-card">
                <h3>Average Order Value</h3>
                <div class="value">₱{{ number_format($avgOrderValue, 2) }}</div>
            </div>
            <div class="summary-card">
                <h3>Completed Orders</h3>
                <div class="value">{{ $completedOrders }}</div>
            </div>
        </div>
        
        <!-- Payment Status Breakdown -->
        <div class="section">
            <div class="section-title">Payment Status Summary</div>
            <div class="breakdown-grid">
                <div>
                    <div class="breakdown-item">
                        <label>Fully Paid</label>
                        <span class="value">{{ $fullyPaid }} orders</span>
                    </div>
                    <div class="breakdown-item">
                        <label>Partially Paid</label>
                        <span class="value">{{ $partiallyPaid }} orders</span>
                    </div>
                    <div class="breakdown-item">
                        <label>Unpaid</label>
                        <span class="value">{{ $unpaid }} orders</span>
                    </div>
                </div>
                <div>
                    <div class="breakdown-item">
                        <label>Completion Rate</label>
                        <span class="value">{{ $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="breakdown-item">
                        <label>Payment Collection</label>
                        <span class="value">{{ $totalOrders > 0 ? round(($fullyPaid / $totalOrders) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Orders Table -->
        <div class="section">
            <div class="section-title">Order Details</div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                        <th>Amount Paid</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $order->customer->name ?? 'Unknown' }}</td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="status-badge 
                                @if($order->status === 'completed') status-completed
                                @elseif($order->status === 'ready') status-ready
                                @else status-pending
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                        <td>₱{{ number_format($order->amount_paid, 2) }}</td>
                        <td>
                            @php
                                $paymentStatus = 'Unpaid';
                                $paymentClass = 'payment-unpaid';
                                if ($order->amount_paid >= $order->total_amount) {
                                    $paymentStatus = 'Fully Paid';
                                    $paymentClass = 'payment-fully-paid';
                                } elseif ($order->amount_paid > 0) {
                                    $paymentStatus = 'Partially Paid';
                                    $paymentClass = 'payment-partially-paid';
                                }
                            @endphp
                            <span class="status-badge {{ $paymentClass }}">{{ $paymentStatus }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #6b7280;">No orders found for the selected period</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>This report was generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
            <p>Laundry Management System</p>
        </div>
    </div>
</body>
</html>
