@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">

            <!-- Page Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-10 text-center">💳 Checkout</h1>

            @if($cartItems->count() > 0)

                {{-- Error Messages --}}
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded-xl mb-6 shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('cart.placeOrder') }}" method="POST" id="checkoutForm" class="space-y-8">
                    @csrf

                    <!-- Order Summary -->
                    <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">🛍️ Order Summary</h2>
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr
                                    class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white uppercase text-xs tracking-wider">
                                    <th class="p-3 rounded-tl-lg">Product</th>
                                    <th class="p-3">Price</th>
                                    <th class="p-3">Quantity</th>
                                    <th class="p-3 rounded-tr-lg">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($cartItems as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 font-medium text-gray-900">{{ $item->product->name }}</td>
                                        @php
                                            $unitPrice = $item->product->discount_price ?? $item->product->selling_price;
                                        @endphp
                                        <td class="p-3 text-gray-700">RM {{ number_format($unitPrice, 2) }}</td>
                                        <td class="p-3 text-gray-700">{{ $item->quantity }}</td>
                                        <td class="p-3 font-semibold text-green-600">
                                            RM {{ number_format($unitPrice * $item->quantity, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Shipping Option -->
                    <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">🚚 Shipping Option</h2>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-3">
                                <input type="radio" name="shipping" value="fast" onchange="updateTotal()"
                                    class="text-indigo-600">
                                <span>Fast Delivery <span class="text-gray-500">(RM 5.00)</span></span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="radio" name="shipping" value="normal" onchange="updateTotal()"
                                    class="text-indigo-600">
                                <span>Standard Delivery <span class="text-gray-500">(RM 2.50)</span></span>
                            </label>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">🏠 Shipping Address</h2>
                        <input type="text" name="address"
                            class="border rounded-lg px-3 py-2 mb-3 w-full focus:ring-2 focus:ring-indigo-400"
                            placeholder="Enter full address">
                        <input type="text" name="postal_code"
                            class="border rounded-lg px-3 py-2 w-40 focus:ring-2 focus:ring-indigo-400"
                            placeholder="Postal Code">
                    </div>

                    <!-- Promo Code -->
                    <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">🎟️ Promo Code</h2>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-3">
                                <input type="radio" name="promocode_option" value="none" checked onchange="updateTotal()"
                                    class="text-indigo-600">
                                <span>No Promo</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="radio" name="promocode_option" value="10percent" onchange="updateTotal()"
                                    class="text-indigo-600">
                                <span>10% Discount</span>
                            </label>
                            <input type="text" name="promocode" id="promocode"
                                class="border rounded-lg px-3 py-2 mt-2 w-64 focus:ring-2 focus:ring-indigo-400"
                                placeholder="Enter promo code" oninput="updateTotal()">
                        </div>
                    </div>

                    <!-- Payment Option -->
                    <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">💰 Payment Option</h2>

                        <!-- Credit Card -->
                        <label class="flex items-center space-x-3 mb-2">
                            <input type="radio" name="payment" value="credit_card" onclick="togglePaymentFields('credit_card')"
                                class="text-indigo-600">
                            <span>Credit Card</span>
                        </label>
                        <div id="credit_card_fields" class="ml-6 hidden mb-4 space-y-2">
                            <input type="text" name="card_number"
                                class="border rounded-lg px-3 py-2 w-64 focus:ring-2 focus:ring-indigo-400"
                                placeholder="Card Number">
                            <input type="text" name="name_on_card"
                                class="border rounded-lg px-3 py-2 w-64 focus:ring-2 focus:ring-indigo-400"
                                placeholder="Name on Card">
                            <input type="text" name="expiry_date"
                                class="border rounded-lg px-3 py-2 w-32 focus:ring-2 focus:ring-indigo-400"
                                placeholder="Expiry Date">
                            <input type="password" name="cvv" maxlength="3" pattern="\d{3}" inputmode="numeric"
                                class="border rounded-lg px-3 py-2 w-20 focus:ring-2 focus:ring-indigo-400" placeholder="CVV">
                        </div>

                        <!-- Online Banking -->
                        <label class="flex items-center space-x-3 mb-2">
                            <input type="radio" name="payment" value="online_banking"
                                onclick="togglePaymentFields('online_banking')" class="text-indigo-600">
                            <span>Online Banking</span>
                        </label>
                        <div id="online_banking_fields" class="ml-6 hidden space-y-2">
                            <select name="bank_name"
                                class="border rounded-lg px-3 py-2 w-64 focus:ring-2 focus:ring-indigo-400">
                                <option value="">-- Select Bank --</option>
                                <option value="maybank">Maybank</option>
                                <option value="cimb">CIMB</option>
                                <option value="rhb">RHB</option>
                            </select>
                            <br />
                            <input type="text" name="account_name"
                                class="border rounded-lg px-3 py-2 w-64 focus:ring-2 focus:ring-indigo-400"
                                placeholder="Account Holder Name">
                            <br />
                            <input type="text" name="account_no"
                                class="border rounded-lg px-3 py-2 w-64 focus:ring-2 focus:ring-indigo-400"
                                placeholder="Account Number">
                        </div>
                    </div>

                    <!-- Total + Place Order -->
                    <div class="flex justify-between items-center bg-white shadow-lg rounded-2xl p-6 border border-gray-200">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">
                                Total: RM <span id="totalDisplay">{{ number_format($total, 2) }}</span>
                            </h2>
                            <p id="originalPrice" class="text-sm text-red-500 line-through hidden">
                                Original Price: RM <span id="originalPriceValue" class="line-through"></span>
                            </p>
                        </div>
                        <button type="submit" onclick="return confirmOrder()"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl shadow-md transition">
                            ✅ Place Order
                        </button>
                    </div>
                </form>

                {{-- JS --}}
                <script>
                    const subtotal = parseFloat("{{ $total }}");
                    const fastShipping = 5.00;
                    const normalShipping = 2.50;

                    function updateTotal() {
                        let shipping = document.querySelector('input[name="shipping"]:checked');
                        let shippingCost = 0;
                        if (shipping) {
                            shippingCost = shipping.value === 'fast' ? fastShipping : normalShipping;
                        }

                        let promoOption = document.querySelector('input[name="promocode_option"]:checked').value;
                        let promoCode = document.getElementById('promocode').value.trim();
                        let discount = 0;

                        let originalTotal = subtotal + shippingCost;
                        let finalTotal = originalTotal;

                        if (promoOption === '10percent' && promoCode === 'NewUser') {
                            discount = subtotal * 0.1;
                            finalTotal = originalTotal - discount;

                            // Show original price (with strikethrough)
                            document.getElementById('originalPrice').classList.remove('hidden');
                            document.getElementById('originalPriceValue').innerText = originalTotal.toFixed(2);
                        } else {
                            // Hide original price if no discount
                            document.getElementById('originalPrice').classList.add('hidden');
                        }

                        document.getElementById('totalDisplay').innerText = finalTotal.toFixed(2);
                    }



                    function togglePaymentFields(type) {
                        const creditFields = document.getElementById('credit_card_fields');
                        const bankFields = document.getElementById('online_banking_fields');

                        if (type === 'credit_card') {
                            creditFields.classList.remove('hidden');
                            bankFields.classList.add('hidden');
                        } else {
                            creditFields.classList.add('hidden');
                            bankFields.classList.remove('hidden');
                        }
                    }

                    function confirmOrder() {
                        return confirm("Are you sure you want to place order now?");
                    }

                </script>

            @else
                <!-- Empty Checkout -->
                <div class="text-center bg-white shadow-lg rounded-2xl p-12">
                    <p class="text-gray-500 text-lg">Your bag is empty 🛍️</p>
                    <a href="{{ url('/products') }}"
                        class="inline-block mt-6 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl shadow-md transition">
                        Browse Products
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection