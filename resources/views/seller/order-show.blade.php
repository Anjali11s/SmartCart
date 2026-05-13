@extends('layouts.master')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('seller.orders') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Order Details -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
            <h2 class="text-lg font-bold mb-4">Order Items</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left">Product</th>
                            <th class="p-3 text-center">Quantity</th>
                            <th class="p-3 text-right">Price</th>
                            <th class="p-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr class="border-b">
                            <td class="p-3">
                                {{ $item->product->name }}
                                @if($item->product->seller_id == Auth::id())
                                    <span class="text-green-600 text-xs ml-1">(Your Product)</span>
                                @endif
                             </td>
                            <td class="p-3 text-center">{{ $item->quantity }}</td>
                            <td class="p-3 text-right">₹{{ number_format($item->price, 2) }}</td>
                            <td class="p-3 text-right font-semibold">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                         </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="3" class="p-3 text-right font-bold">Total:</td>
                            <td class="p-3 text-right font-bold text-indigo-600">₹{{ number_format($order->total_amount, 2) }}</td>
                         </tr>
                    </tfoot>
                 </table>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-bold mb-4">Shipping Address</h2>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p><strong>{{ $order->shippingAddress?->full_name ?? 'N/A' }}</strong></p>
                <p>{{ $order->shippingAddress?->full_address ?? $order->shipping_address_text }}</p>
                <p>Phone: {{ $order->shipping_phone }}</p>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div>
        <!-- Status Update Section - Only for eligible orders -->
        @php
            $canUpdateStatus = in_array($order->order_status, [
                'pending', 'confirmed', 'processing', 'shipped', 'out_for_delivery'
            ]);
        @endphp
        
        @if($canUpdateStatus)
        <div class="bg-white rounded-xl p-6 shadow-sm sticky top-4">
            <h2 class="text-lg font-bold mb-4">Update Status</h2>
            
            <form action="{{ route('seller.orders.update-status', $order) }}" method="POST">
                @csrf
                <select name="status" class="w-full p-2 border rounded-lg mb-4" required>
                    <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $order->order_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="out_for_delivery" {{ $order->order_status == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Tracking Number (Optional)</label>
                    <input type="text" name="tracking_number" class="w-full p-2 border rounded-lg" value="{{ $order->tracking_number }}">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Courier Name (Optional)</label>
                    <input type="text" name="courier_name" class="w-full p-2 border rounded-lg" value="{{ $order->courier_name }}">
                </div>
                
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                    Update Status
                </button>
            </form>
        </div>
        @else
        <!-- Order Completion Info -->
        <div class="bg-white rounded-xl p-6 shadow-sm sticky top-4">
            <div class="text-center">
                @if($order->order_status == 'delivered')
                    <div class="bg-green-100 rounded-lg p-4 mb-4">
                        <i class="fas fa-check-circle text-green-600 text-3xl mb-2"></i>
                        <h3 class="font-bold text-green-800">Order Delivered</h3>
                        @if($order->delivered_at && $order->delivered_at->format('H:i:s') != '00:00:00')
                            <p class="text-sm text-green-700 mt-1">Delivered on: {{ $order->delivered_at->format('d M Y, h:i A') }}</p>
                        @elseif($order->delivered_at)
                            <p class="text-sm text-green-700 mt-1">Delivered on: {{ $order->delivered_at->format('d M Y') }}</p>
                        @endif
                        <p class="text-xs text-green-600 mt-2">No further status updates allowed</p>
                    </div>
                @elseif($order->order_status == 'cancelled')
                    <div class="bg-red-100 rounded-lg p-4 mb-4">
                        <i class="fas fa-times-circle text-red-600 text-3xl mb-2"></i>
                        <h3 class="font-bold text-red-800">Order Cancelled</h3>
                        <p class="text-sm text-red-700 mt-1">This order has been cancelled</p>
                        @if($order->cancellation_reason)
                            <p class="text-xs text-red-600 mt-2">Reason: {{ $order->cancellation_reason }}</p>
                        @endif
                    </div>
                @elseif($order->order_status == 'returned')
                    <div class="bg-blue-100 rounded-lg p-4 mb-4">
                        <i class="fas fa-undo-alt text-blue-600 text-3xl mb-2"></i>
                        <h3 class="font-bold text-blue-800">Order Returned & Refunded</h3>
                        <p class="text-sm text-blue-700 mt-1">This order has been returned and refunded</p>
                        @if($order->return_reason)
                            <p class="text-xs text-blue-600 mt-2">Return reason: {{ $order->return_reason }}</p>
                        @endif
                        @if($order->refund_processed_at)
                            <p class="text-xs text-blue-600 mt-1">Refund processed on: {{ $order->refund_processed_at->format('d M Y, h:i A') }}</p>
                        @endif
                    </div>
                @elseif($order->order_status == 'return_approved')
                    <div class="bg-blue-100 rounded-lg p-4 mb-4">
                        <i class="fas fa-check-circle text-blue-600 text-3xl mb-2"></i>
                        <h3 class="font-bold text-blue-800">Return Approved</h3>
                        <p class="text-sm text-blue-700 mt-1">Return request has been approved</p>
                        <p class="text-xs text-blue-600 mt-2">Refund is being processed</p>
                    </div>
                @elseif($order->order_status == 'return_rejected')
                    <div class="bg-red-100 rounded-lg p-4 mb-4">
                        <i class="fas fa-times-circle text-red-600 text-3xl mb-2"></i>
                        <h3 class="font-bold text-red-800">Return Rejected</h3>
                        <p class="text-sm text-red-700 mt-1">Return request has been rejected</p>
                        @if($order->cancellation_reason)
                            <p class="text-xs text-red-600 mt-2">Reason: {{ $order->cancellation_reason }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Return Request Processing Section -->
        @if($order->order_status == 'return_requested')
        <div class="mt-4 p-4 bg-orange-50 rounded-lg border border-orange-200">
            <h3 class="font-bold text-orange-800 mb-3"><i class="fas fa-exchange-alt"></i> Return Request</h3>
            <p class="text-sm text-orange-700 mb-2"><strong>Reason:</strong> {{ $order->return_reason }}</p>
            <p class="text-sm text-orange-700 mb-3"><strong>Requested on:</strong> {{ $order->return_requested_at->format('d M Y, h:i A') }}</p>
            <div class="flex gap-3">
                <form action="{{ route('seller.orders.approve-return', $order) }}" method="POST" onsubmit="return confirm('Approve this return request?')">
                    @csrf
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                        <i class="fas fa-check"></i> Approve Return
                    </button>
                </form>
                <button onclick="showRejectModal()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-times"></i> Reject Return
                </button>
            </div>
            
            <!-- Reject Modal -->
            <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-bold mb-4">Reject Return Request</h3>
                    <form action="{{ route('seller.orders.reject-return', $order) }}" method="POST">
                        @csrf
                        <textarea name="rejection_reason" class="w-full p-2 border rounded-lg mb-4" rows="3" placeholder="Reason for rejection" required></textarea>
                        <div class="flex gap-3">
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">Confirm Reject</button>
                            <button type="button" onclick="hideRejectModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        
        @if($order->order_status == 'return_approved' && $order->refund_status == 'processing')
        <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="font-bold text-blue-800 mb-2">Return Approved</h3>
            <p class="text-sm text-blue-700 mb-2">Refund pending. Process refund to complete return.</p>
            <form action="{{ route('seller.orders.process-refund', $order) }}" method="POST" onsubmit="return confirm('Process refund for this order?')">
                @csrf
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-credit-card"></i> Process Refund
                </button>
            </form>
        </div>
        @endif
        
        <!-- Order Information -->
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600">Order placed on: {{ $order->created_at->format('d M Y, h:i A') }}</p>
            @if($order->payment_method == 'COD')
                <p class="text-sm text-gray-600">Payment: Cash on Delivery</p>
            @else
                <p class="text-sm text-gray-600">Payment: Online ({{ ucfirst($order->payment_status) }})</p>
            @endif
            @if($order->tracking_number)
                <p class="text-sm text-gray-600">Tracking: {{ $order->tracking_number }}</p>
            @endif
            @if($order->courier_name)
                <p class="text-sm text-gray-600">Courier: {{ $order->courier_name }}</p>
            @endif
            @if($order->delivered_at)
                @if($order->delivered_at->format('H:i:s') != '00:00:00')
                    <p class="text-sm text-green-600">Delivered on: {{ $order->delivered_at->format('d M Y, h:i A') }}</p>
                @else
                    <p class="text-sm text-green-600">Delivered on: {{ $order->delivered_at->format('d M Y') }}</p>
                @endif
            @endif
            @if($order->seller_earnings > 0 && $order->order_status != 'returned' && $order->order_status != 'cancelled')
                <p class="text-sm text-green-600 mt-2">Your Earnings: ₹{{ number_format($order->seller_earnings, 2) }}</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function hideRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endpush
@endsection