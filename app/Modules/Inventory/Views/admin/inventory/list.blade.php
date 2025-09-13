{{-- resources/views/inventory/admin/inventory/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Inventory List')

@section('content')

<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <!-- Page Title on the left -->
        <h1 class="text-2xl font-bold text-gray-800">📦 Inventory List</h1>

        <!-- Back Button on the right -->
        <button type="button" onclick="history.back()"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg shadow hover:bg-gray-400 transition">
            ← Back
        </button>
    </div>

    <div id="inventory-container" class="overflow-x-auto text-gray-700">
        <p class="text-center">Loading inventories...</p>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('inventory-container');

    const externalApi = "http://127.0.0.1:8000/api/inventory"; // external first
    const internalApi = "/api/inventory"; // fallback

    function renderTable(data) {
        if (data.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500">No inventories found.</p>';
            return;
        }

        let html = `
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b">ID</th>
                        <th class="px-4 py-2 border-b">Name</th>
                        <th class="px-4 py-2 border-b">Type</th>
                        <th class="px-4 py-2 border-b">Quantity</th>
                        <th class="px-4 py-2 border-b">Variations</th>
                    </tr>
                </thead>
                <tbody>
        `;

        data.forEach(inv => {
            let variationsHtml = '<span class="text-gray-400">None</span>';
            if (inv.variations && inv.variations.length > 0) {
                variationsHtml = '<ul class="list-disc pl-5">';
                inv.variations.forEach(v => {
                    variationsHtml += `<li>SKU: ${v.sku}, Stock: ${v.stock}, Price: ${Number(v.price).toFixed(2)}</li>`;
                });
                variationsHtml += '</ul>';
            }

            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border-b">${inv.id}</td>
                    <td class="px-4 py-2 border-b">${inv.name}</td>
                    <td class="px-4 py-2 border-b">${inv.type}</td>
                    <td class="px-4 py-2 border-b">${inv.quantity}</td>
                    <td class="px-4 py-2 border-b">${variationsHtml}</td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        container.innerHTML = html;
    }

    // Try external first, fallback to internal if external fails
    fetch(externalApi)
        .then(res => {
            if (!res.ok) throw new Error("External API failed");
            return res.json();
        })
        .then(response => {
            renderTable(response.data || []);
        })
        .catch(err => {
    container.innerHTML = `
        <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 border border-yellow-300">
            ⚠️ External API failed, falling back to internal: ${err.message}
        </div>
    `;

    console.warn("External failed, trying internal API:", err.message);

    fetch(internalApi)
        .then(res => {
            if (!res.ok) throw new Error("Internal API also failed");
            return res.json();
        })
        .then(response => {
            renderTable(response.data || []);
        })
        .catch(err2 => {
            container.innerHTML = `
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-300">
                    ❌ Error loading inventories: ${err2.message}
                </div>
            `;
            console.error(err2);
        });
});

});
</script>

@endsection
