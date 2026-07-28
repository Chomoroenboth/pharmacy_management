@extends('layouts.customer-app')

@section('title', 'Your Cart')

@section('page-css')
<style>
    .cart-layout { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }

    .cart-items { background: #fff; border-radius: 8px; box-shadow: 0px 1px 3px rgba(0,0,0,0.06); padding: 24px; }
    .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .cart-title { font-size: 22px; font-weight: 600; }
    .back-link { color: #10b981; font-size: 13px; text-decoration: none; }

    .cart-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 0; border-bottom: 1px solid #f3f4f6;
    }
    .cart-row:last-child { border-bottom: none; }
    .item-name { font-size: 15px; font-weight: 600; }
    .item-meta { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .remove-form button {
        color: #dc2626; font-size: 12px; margin-top: 4px;
        background: none; border: none; cursor: pointer; padding: 0; text-decoration: underline;
    }
    .qty-controls { display: flex; align-items: center; gap: 8px; }
    .qty-form { display: inline; }
    .qty-btn {
        width: 28px; height: 28px; border: 1px solid #d1d5db; border-radius: 4px;
        background: #fff; cursor: pointer; font-size: 14px;
    }
    .qty-btn:hover { background: #f3f4f6; }
    .item-price { width: 70px; text-align: right; font-size: 14px; color: #6b7280; }
    .item-total { width: 80px; text-align: right; font-weight: 600; }

    .payment-card { background: #fff; border-radius: 8px; box-shadow: 0px 1px 3px rgba(0,0,0,0.06); padding: 24px; }
    .payment-title { font-size: 15px; font-weight: 600; margin-bottom: 16px; }
    .payment-methods { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 24px; }
    .payment-option {
        border: 1px solid #d1d5db; border-radius: 6px; padding: 12px;
        text-align: center; font-size: 13px; cursor: pointer; display: block;
    }
    .payment-option input { display: none; }
    .payment-option input:checked + span {
        color: #047857; font-weight: 600;
    }
    .payment-option:has(input:checked) { border-color: #10b981; background: #ecfdf5; }

    .summary-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; color: #6b7280; }
    .summary-total { display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; margin: 16px 0; color: #191c1e; }
    .btn-confirm {
        width: 100%; padding: 14px; background: #10b981; color: #fff;
        border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer;
    }
    .btn-confirm:hover { background: #0ea371; }
    .empty-cart { text-align: center; padding: 40px; color: #6b7280; }
</style>
@endsection

@section('content')

    <div class="cart-layout">

        <div class="cart-items">
            <div class="cart-header">
                <div class="cart-title">Your Cart</div>
                <a href="{{ route('customer.otc-shop') }}" class="back-link">&larr; Back to Shop</a>
            </div>

            @forelse($cartItems as $item)
            <div class="cart-row">
                <div>
                    <div class="item-name">{{ $item->medicine_name }}</div>
                    <div class="item-meta">{{ $item->brand }}</div>
                    <form action="{{ route('customer.cart.remove', $item->medicine_id) }}" method="POST" class="remove-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Remove</button>
                    </form>
                </div>
                <div class="qty-controls">
                    <form action="{{ route('customer.cart.update', $item->medicine_id) }}" method="POST" class="qty-form">
                        @csrf
                        <input type="hidden" name="action" value="decrease">
                        <button type="submit" class="qty-btn">-</button>
                    </form>
                    <span>{{ $item->quantity }}</span>
                    <form action="{{ route('customer.cart.update', $item->medicine_id) }}" method="POST" class="qty-form">
                        @csrf
                        <input type="hidden" name="action" value="increase">
                        <button type="submit" class="qty-btn">+</button>
                    </form>
                </div>
                <div class="item-price">${{ number_format($item->price, 2) }}</div>
                <div class="item-total">${{ number_format($item->price * $item->quantity, 2) }}</div>
            </div>
            @empty
            <div class="empty-cart">
                Your cart is empty. <a href="{{ route('customer.otc-shop') }}">Browse medicines</a> to add items.
            </div>
            @endforelse
        </div>

        <div class="payment-card">
            <form action="{{ route('customer.cart.checkout') }}" method="POST">
                @csrf
                <div class="payment-title">Payment Method</div>
                <div class="payment-methods">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="cash" checked>
                        <span>Cash</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="credit_card">
                        <span>Credit Card</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="debit_card">
                        <span>Debit Card</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="transfer">
                        <span>Transfer</span>
                    </label>
                </div>

                <div class="payment-title">Order Summary</div>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>

                <button type="submit" class="btn-confirm" {{ $cartItems->isEmpty() ? 'disabled' : '' }}>Confirm Purchase</button>
            </form>
        </div>

    </div>

@stop