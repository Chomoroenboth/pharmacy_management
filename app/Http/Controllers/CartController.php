<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Shared fake medicine catalog used across shop/search/cart until real DB is connected
    private function catalog()
    {
        return collect([
            1 => (object) ['medicine_id' => 1, 'medicine_name' => 'Ibuprofen Extra Strength', 'brand' => 'PharmaCorp Inc.', 'category' => 'Pain Relief', 'price' => 12.99, 'stock_quantity' => 145],
            2 => (object) ['medicine_id' => 2, 'medicine_name' => 'DayTime Cold & Flu Relief', 'brand' => 'HealthPlus', 'category' => 'Cold & Flu', 'price' => 15.49, 'stock_quantity' => 60],
            3 => (object) ['medicine_id' => 3, 'medicine_name' => 'Vitamin C 1000mg Tabs', 'brand' => 'NatureLife', 'category' => 'Vitamins & Supplements', 'price' => 18.00, 'stock_quantity' => 200],
            4 => (object) ['medicine_id' => 4, 'medicine_name' => 'Allergy Relief Cetirizine', 'brand' => 'BreatheEasy', 'category' => 'Allergy', 'price' => 22.50, 'stock_quantity' => 30],
            5 => (object) ['medicine_id' => 5, 'medicine_name' => 'Antacid Chewable Tablets', 'brand' => 'GastricCare', 'category' => 'Digestive Health', 'price' => 8.75, 'stock_quantity' => 90],
            6 => (object) ['medicine_id' => 6, 'medicine_name' => 'Antibiotic Ointment', 'brand' => 'HealQuick', 'category' => 'First Aid', 'price' => 6.20, 'stock_quantity' => 15],
            7 => (object) ['medicine_id' => 7, 'medicine_name' => 'Advil Liqui-Gels', 'brand' => 'Ibuprofen 200mg', 'category' => 'Pain Relief', 'price' => 8.99, 'stock_quantity' => 145],
            8 => (object) ['medicine_id' => 8, 'medicine_name' => 'Motrin IB', 'brand' => 'Ibuprofen 200mg', 'category' => 'Pain Relief', 'price' => 14.49, 'stock_quantity' => 12],
            9 => (object) ['medicine_id' => 9, 'medicine_name' => 'Generic Ibuprofen', 'brand' => 'Ibuprofen 200mg', 'category' => 'Pain Relief', 'price' => 19.99, 'stock_quantity' => 320],
            10 => (object) ['medicine_id' => 10, 'medicine_name' => "Children's Advil", 'brand' => 'Ibuprofen 100mg/5mL', 'category' => 'Pain Relief', 'price' => 7.49, 'stock_quantity' => 45],
        ]);
    }

    public function otcShop(Request $request)
    {
        $categories = ['Pain Relief', 'Vitamins & Supplements', 'Cold & Flu', 'Digestive Health', 'First Aid', 'Allergy'];
        $activeCategory = $request->input('category', 'All Medicines');

        $medicines = $this->catalog()->filter(function ($medicine) use ($activeCategory) {
            return $activeCategory === 'All Medicines' || $medicine->category === $activeCategory;
        })->values();

        return view('customer.otc-shop', compact('categories', 'medicines', 'activeCategory'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');

        $results = $this->catalog()->filter(function ($medicine) use ($query) {
            return $query === '' || stripos($medicine->medicine_name, $query) !== false
                || stripos($medicine->brand, $query) !== false;
        })->values();

        return view('customer.search-results', compact('query', 'results'));
    }

    public function addToCart(Request $request)
    {
        $medicineId = (int) $request->input('medicine_id');
        $cart = Session::get('cart', []);

        $cart[$medicineId] = ($cart[$medicineId] ?? 0) + 1;
        Session::put('cart', $cart);

        return back()->with('message', 'Added to cart!');
    }

    public function updateQuantity(Request $request, $id)
    {
        $cart = Session::get('cart', []);
        $action = $request->input('action');

        if (isset($cart[$id])) {
            if ($action === 'increase') {
                $cart[$id]++;
            } elseif ($action === 'decrease') {
                $cart[$id]--;
                if ($cart[$id] <= 0) {
                    unset($cart[$id]);
                }
            }
        }

        Session::put('cart', $cart);
        return back();
    }

    public function removeFromCart($id)
    {
        $cart = Session::get('cart', []);
        unset($cart[$id]);
        Session::put('cart', $cart);

        return back()->with('message', 'Item removed from cart.');
    }

    public function viewCart()
    {
        $cart = Session::get('cart', []);
        $catalog = $this->catalog();

        $cartItems = collect($cart)->map(function ($quantity, $medicineId) use ($catalog) {
            $medicine = $catalog->get($medicineId);
            if (!$medicine) return null;

            return (object) [
                'medicine_id' => $medicine->medicine_id,
                'medicine_name' => $medicine->medicine_name,
                'brand' => $medicine->brand,
                'price' => $medicine->price,
                'quantity' => $quantity,
            ];
        })->filter()->values();

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        return view('customer.cart', compact('cartItems', 'subtotal'));
    }

    public function checkout(Request $request)
    {
        $cart = Session::get('cart', []);
        $catalog = $this->catalog();

        if (empty($cart)) {
            return redirect()->route('customer.cart')->with('message', 'Your cart is empty.');
        }

        $items = collect($cart)->map(function ($quantity, $medicineId) use ($catalog) {
            $medicine = $catalog->get($medicineId);
            if (!$medicine) return null;

            return (object) [
                'medicine_name' => $medicine->medicine_name,
                'quantity' => $quantity,
                'unit_price' => $medicine->price,
                'subtotal' => $medicine->price * $quantity,
            ];
        })->filter()->values();

        $total = $items->sum('subtotal');
        $paymentMethod = $request->input('payment_method', 'cash');

        $purchase = (object) [
            'display_id' => 'SALE' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'date' => now()->format('M d, Y'),
            'type' => 'Over the Counter (OTC)',
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'doctor_name' => null,
            'total' => $total,
            'items' => $items,
        ];

        Session::put('last_purchase', $purchase);
        Session::forget('cart');

        return redirect()->route('customer.purchase.detail', 'latest');
    }

    public function purchaseDetail($id)
    {
        return view('customer.purchase-detail', ['saleId' => $id]);
    }
}
