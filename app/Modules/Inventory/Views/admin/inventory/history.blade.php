@extends('layouts.admin')


@section('title', 'Inventory History')


@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">📜 Inventory Update History</h1>


    <!-- History Container -->
    <div id="history-container" class="space-y-6 text-center">
        <p class="text-gray-500">Loading history...</p>
        <button id="terminate-chat-btn" onclick="terminateChat()">End Chat</button>  


    <div id="history-container1" class="space-y-6 text-center">
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', async function () {
    const container = document.getElementById('history-container1');
    container.innerHTML = `<p class="text-gray-500">Loading history...</p>`;
});

// Make terminateChat an async function
async function terminateChat() {
    const container = document.getElementById('history-container');

    try {
        // Fetch JSON from API
        const response = await fetch("/api/inventory/history");
        let inventories = await response.json();


        console.log("1111")
        // Clear loader
        container.innerHTML = "";

        if (!inventories.length) {
            container.innerHTML = `<p class="text-gray-500">No edited inventory found.</p>`;
            return;
        }

        inventories.forEach(item => {
            const updatedAt = new Date(item.updated_at).toLocaleString();

            const card = document.createElement('div');
            card.className = "relative pl-8 border-l-4 border-blue-500 bg-white shadow rounded-xl p-5 transition hover:shadow-lg";

            // Timeline dot
            const dot = document.createElement('div');
            dot.className = "absolute left-0 top-5 transform -translate-x-1/2 bg-blue-500 rounded-full w-4 h-4";
            card.appendChild(dot);

            // Header
            const header = document.createElement('div');
            header.className = "flex justify-between items-center";
            header.innerHTML = `
                <h2 class="text-lg font-semibold text-gray-800">${item.name}</h2>
                <span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-700">Edited</span>
            `;
            card.appendChild(header);

            // Description
            const desc = document.createElement('p');
            desc.className = "text-sm text-gray-600 mt-1";
            desc.innerHTML = `<strong>Type:</strong> ${item.type} | <strong>Updated:</strong> ${updatedAt}`;
            card.appendChild(desc);

            // Variations
            if (item.variations && item.variations.length > 0) {
                const variationsWrapper = document.createElement('div');
                variationsWrapper.className = "mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4";

                item.variations.forEach(variation => {
                    const varCard = document.createElement('div');
                    varCard.className = "bg-gray-50 p-3 rounded-lg border hover:shadow transition";

                    const img = variation.image_path
                        ? `<img src="/storage/${variation.image_path}" class="w-full h-24 rounded-lg border object-cover mb-2">`
                        : `<img src="/images/no-image.png" class="w-full h-24 rounded-lg border object-cover mb-2">`;

                    varCard.innerHTML = `
                        ${img}
                        <p class="text-sm font-semibold text-gray-800">${variation.sku}</p>
                        <p class="text-xs text-gray-500">
                            Color: ${variation.color || '-'} |
                            Size: ${variation.size || '-'} |
                            Stock: ${variation.stock}
                        </p>
                        <p class="text-sm font-bold text-blue-600 mt-1">RM ${parseFloat(variation.price).toFixed(2)}</p>
                    `;
                    variationsWrapper.appendChild(varCard);
                });

                card.appendChild(variationsWrapper);
            }

            container.appendChild(card);
        });

    } catch (error) {
        console.error(error);
        container.innerHTML = `<p class="text-red-500">Failed to load history.</p>`;
    }
}
</script>



@endsection
