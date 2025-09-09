@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-center">Checkout</h1>

    @if($cartItems->count() > 0)
        {{-- Error Messages --}}
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('cart.placeOrder') }}" method="POST" id="checkoutForm">
            @csrf
            <table class="w-full border-collapse border text-center mb-4">
                <thead>
                    <tr>
                        <th class="border p-2">Product</th>
                        <th class="border p-2">Price</th>
                        <th class="border p-2">Quantity</th>
                        <th class="border p-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                        <tr>
                            <td class="border p-2">{{ $item->product->name }}</td>
                            <td class="border p-2">RM {{ number_format($item->product->price, 2) }}</td>
                            <td class="border p-2">{{ $item->quantity }}</td>
                            <td class="border p-2">RM {{ number_format($item->product->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Shipping Option -->
            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Shipping Option</h2>
                <label class="mr-4">
                    <input type="radio" name="shipping" value="fast" onchange="updateTotal()">
                    Fast Delivery (RM 5.00)
                </label>
                <label>
                    <input type="radio" name="shipping" value="normal" onchange="updateTotal()">
                    Standard Delivery (RM 2.50)
                </label>
            </div>

            <!-- Shipping Address -->
            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Shipping Address</h2>
                <input type="text" name="address" class="border px-2 py-1 mb-2 w-full" placeholder="Enter full address">
                <input type="text" name="postal_code" class="border px-2 py-1 w-40" placeholder="Postal Code">
            </div>

            <!-- Promo Code -->
            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Promo Code</h2>
                <label class="mr-4">
                    <input type="radio" name="promocode_option" value="none" checked onchange="updateTotal()">
                    No Promo
                </label>
                <label class="mr-4">
                    <input type="radio" name="promocode_option" value="10percent" onchange="updateTotal()">
                    10% Discount
                </label>
                <input type="text" name="promocode" id="promocode" class="border px-2 py-1 ml-2" placeholder="Enter promo code"
                    oninput="updateTotal()">
            </div>

            <!-- Payment Option -->
            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Payment Option</h2>

                {{-- Credit Card --}}
                <label class="block mb-2">
                    <input type="radio" name="payment" value="credit_card" onclick="togglePaymentFields('credit_card')">
                    Credit Card
                </label>
                <div id="credit_card_fields" class="ml-6 mb-4">
                    <input type="text" name="name_on_card" class="border px-2 py-1 mb-2 w-64" placeholder="Name on Card"><br/>
                    <input type="text" name="card_number" class="border px-2 py-1 mb-2 w-64" placeholder="Card Number"><br/>
                    <input type="text" name="cvv" class="border px-2 py-1 mb-2 w-20" placeholder="CVV">
                </div>

                {{-- Online Banking --}}
                <label class="block mb-2">
                    <input type="radio" name="payment" value="online_banking" onclick="togglePaymentFields('online_banking')">
                    Online Banking
                </label>
                <div id="online_banking_fields" class="ml-6 hidden">
                    <select name="bank" class="border px-2 py-1 w-64 mb-2">
                        <option value="">-- Select Bank --</option>
                        <option value="maybank">Maybank</option>
                        <option value="cimb">CIMB</option>
                        <option value="rhb">RHB</option>
                    </select>
                    <br/>
                    <input type="text" name="account_name" class="border px-2 py-1 mb-2 w-64" placeholder="Account Holder Name"> <br/>
                    <input type="text" name="account_number" class="border px-2 py-1 w-64" placeholder="Account Number">
                </div>

            </div>

            <!-- Total and Place Order -->
            <div class="mt-4 flex justify-end items-center space-x-6">
                <h2 class="text-xl font-bold">Total: RM <span id="totalDisplay">{{ number_format($total, 2) }}</span></h2>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
                    Place Order
                </button>
            </div>
        </form>

        {{-- JS --}}
        <script>
            const subtotal = parseFloat("{{ $total }}");
            const fastShipping = 5.00;
            const normalShipping = 2.50;

            function updateTotal() {
                let shipping = document.querySelector('input[name="shipping"]:checked').value;
                let shippingCost = shipping === 'fast' ? fastShipping : normalShipping;

                let promoOption = document.querySelector('input[name="promocode_option"]:checked').value;
                let promoCode = document.getElementById('promocode').value.trim();
                let discount = 0;

                if (promoOption === '10percent' && promoCode === 'NewUser') {
                    discount = subtotal * 0.1;
                }

                let total = subtotal + shippingCost - discount;
                document.getElementById('totalDisplay').innerText = total.toFixed(2);
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
        </script>

    @else
        <p class="text-gray-500 text-center">Your cart is empty.</p>
    @endif
@endsection