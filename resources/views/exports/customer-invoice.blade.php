{{-- resources/views/exports/customer-invoice.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Orders - {{ $user->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; }
        .order { margin-bottom: 30px; page-break-inside: avoid; }
        .order-header { background: #8B4513; color: white; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .total { font-weight: bold; text-align: right; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>My Order History</h1>
        <p>{{ $user->name }} | {{ $user->email }}</p>
        <p>Generated: {{ date('F j, Y g:i A') }}</p>
    </div>
    
    @foreach($orders as $order)
    <div class="order">
        <div class="order-header">
            <strong>Order #{{ $order->order_number }}</strong> | 
            Date: {{ $order->created_at->format('F j, Y') }} | 
            Status: {{ ucfirst($order->status) }}
        </div>
        
        <table>
            <thead>
                <tr><th>Book</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->book->title }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₱{{ number_format($item->price, 2) }}</td>
                    <td>₱{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="total">
            Total: ₱{{ number_format($order->total_amount, 2) }}
        </div>
    </div>
    @endforeach
</body>
</html>