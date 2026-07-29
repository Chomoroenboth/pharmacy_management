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
    .remove-btn {
        color: #dc2626; font-size: 12px; margin-top: 4px;
        background: none; border: none; cursor: pointer; padding: 0; text-decoration: underline;
    }
    .qty-controls { display: flex; align-items: center; gap: 8px; }
    .qty-btn {
        width: 28px; height: 28px; border: 1px solid #d1d5db; border-radius: 4px;
        background: #fff; cursor: pointer; font-size: 14px;
    }
    .qty-btn:hover { background: #f3f4f6; }
    .qty-btn:disabled { opacity: 0.5; cursor: not-allowed; }
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
    .btn-confirm:disabled { background: #9ca3af; cursor: not-allowed; }
    .empty-cart { text-align: center; padding: 40px; color: #6b7280; }
    .loading-text { color: #6b7280; padding: 20px 0; }
    .checkout-note { font-size: 12px; color: #9ca3af; margin-top: 8px; text-align: center; }
</style>

@vite(['resources/js/app.js'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemsContainer = document.getElementById('cartItemsContainer');
    const statusEl = document.getElementById('cartStatus');
    const subtotalEl = document.getElementById('subtotalDisplay');
    const totalEl = document.getElementById('totalDisplay');
    const confirmBtn = document.getElementById('confirmPurchaseBtn');
    const checkoutError = document.getElementById('checkoutError');

    async function loadCart() {
        statusEl.textContent = 'Loading your cart...';
        statusEl.style.display = 'block';
        itemsContainer.innerHTML = '';

        try {
            const response = await window.axios.get('/api/cart');
            const items = response.data.data;

            if (!items.length) {
                statusEl.innerHTML = 'Your cart is empty. <a href="{{ route('customer.otc-shop') }}">Browse medicines</a> to add items.';
                itemsContainer.classList.add('empty-cart');
                updateSummary(0);
                confirmBtn.disabled = true;
                return;
            }

            statusEl.style.display = 'none';
            itemsContainer.classList.remove('empty-cart');

            itemsContainer.innerHTML = items.map(function (item) {
                return `
                    <div class="cart-row" data-cart-id="${item.cart_id}">
                        <div>
                            <div class="item-name">${item.medicine_name}</div>
                            <button type="button" class="remove-btn" data-cart-id="${item.cart_id}">Remove</button>
                        </div>
                        <div class="qty-controls">
                            <button type="button" class="qty-btn qty-decrease" data-cart-id="${item.cart_id}" data-qty="${item.quantity}">-</button>
                            <span class="qty-display">${item.quantity}</span>
                            <button type="button" class="qty-btn qty-increase" data-cart-id="${item.cart_id}" data-qty="${item.quantity}">+</button>
                        </div>
                        <div class="item-price">$${Number(item.price).toFixed(2)}</div>
                        <div class="item-total">$${Number(item.subtotal).toFixed(2)}</div>
                    </div>
                `;
            }).join('');

            const total = items.reduce((sum, item) => sum + Number(item.subtotal), 0);
            updateSummary(total);
            confirmBtn.disabled = false;

        } catch (err) {
            if (err.response?.status === 401) {
                window.location.href = "{{ route('customer.login') }}";
                return;
            }
            statusEl.textContent = 'Failed to load your cart. Please try again.';
            console.error('Failed to load cart:', err);
        }
    }

    function updateSummary(total) {
        subtotalEl.textContent = '$' + total.toFixed(2);
        totalEl.textContent = '$' + total.toFixed(2);
    }

    async function updateQuantity(cartId, newQuantity) {
        if (newQuantity < 1) {
            return removeItem(cartId);
        }
        try {
            await window.axios.put('/api/cart/' + cartId, { quantity: newQuantity });
            loadCart();
        } catch (err) {
            console.error('Failed to update quantity:', err);
        }
    }

    async function removeItem(cartId) {
        try {
            await window.axios.delete('/api/cart/' + cartId);
            loadCart();
        } catch (err) {
            console.error('Failed to remove item:', err);
        }
    }

    itemsContainer.addEventListener('click', function (e) {
        const cartId = e.target.dataset.cartId;
        if (!cartId) return;

        if (e.target.matches('.qty-increase')) {
            const currentQty = parseInt(e.target.dataset.qty, 10);
            updateQuantity(cartId, currentQty + 1);
        } else if (e.target.matches('.qty-decrease')) {
            const currentQty = parseInt(e.target.dataset.qty, 10);
            updateQuantity(cartId, currentQty - 1);
        } else if (e.target.matches('.remove-btn')) {
            removeItem(cartId);
        }
    });
    confirmBtn.addEventListener('click', async function () {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

        checkoutError.style.display = 'none';
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';

        try {
            const checkoutResponse = await window.axios.post('/api/shop/checkout');
            const saleId = checkoutResponse.data.sale_id;

            await window.axios.post('/api/shop/sales/' + saleId + '/payment', {
                payment_method: selectedMethod,
            });

            window.location.href = "{{ route('customer.purchase.detail', '__SALE_ID__') }}".replace('__SALE_ID__', saleId);

        } catch (err) {
            if (err.response?.status === 401) {
                window.location.href = "{{ route('customer.login') }}";
                return;
            }
            checkoutError.textContent = err.response?.data?.message || 'Checkout failed. Please try again.';
            checkoutError.style.display = 'block';
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm Purchase';
            console.error('Checkout failed:', err);
        }
    });

    loadCart();
});
</script>
@endsection

@section('content')

    <div class="cart-layout">

        <div class="cart-items">
            <div class="cart-header">
                <div class="cart-title">Your Cart</div>
                <a href="{{ route('customer.otc-shop') }}" class="back-link">&larr; Back to Shop</a>
            </div>

            <p id="cartStatus" class="loading-text">Loading your cart...</p>
            <div id="cartItemsContainer"></div>
        </div>

        <div class="payment-card">
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
                <span id="subtotalDisplay">$0.00</span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span id="totalDisplay">$0.00</span>
            </div>

            <button type="button" id="confirmPurchaseBtn" class="btn-confirm" disabled>Confirm Purchase</button>
            <p id="checkoutError" class="checkout-note" style="color:#dc2626; display:none;"></p>
        </div>

    </div>

@stop
