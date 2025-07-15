@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>

    {{-- Metric Cards --}}
    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div style="border: 1px solid #ccc; padding: 20px;">
            <h3>Total Revenue</h3>
            <p>RM {{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div style="border: 1px solid #ccc; padding: 20px;">
            <h3>Total Sales</h3>
            <p>{{ $totalSales }}</p>
        </div>
        <div style="border: 1px solid #ccc; padding: 20px;">
            <h3>New Customers (This Month)</h3>
            <p>{{ $newCustomersThisMonth }}</p>
        </div>
    </div>

    {{-- Sales Trend Chart --}}
    <div>
        <h3>Sales Trend (Last 30 Days)</h3>
        <canvas id="salesChart"></canvas>
    </div>

    <div style="margin-top: 40px;">
        <h3>Customer Segmentation (RFM Analysis)</h3>
        <div id="segmentation-container">
            <p>Loading customer segments...</p>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const ctx = document.getElementById('salesChart');
        const salesLabels = @json($labels);
        const salesData = @json($data);

        new Chart(ctx, {
            type: 'line', // a line chart
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Daily Revenue (RM)',
                    data: salesData,
                    borderWidth: 2,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Use an async function to fetch the data
        async function fetchCustomerSegments() {
            try {
                const response = await fetch('/admin/customers/segmentation');

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const segments = await response.json();

                const container = document.getElementById('segmentation-container');

                // Create the table HTML
                let tableHtml = `<table border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Segment</th>
                            <th>Recency (Days)</th>
                            <th>Frequency</th>
                            <th>Monetary (RM)</th>
                        </tr>
                    </thead>
                    <tbody>`;

                segments.forEach(customer => {
                    tableHtml += `
                        <tr>
                            <td>${customer.name}</td>
                            <td>${customer.email}</td>
                            <td><strong>${customer.segment}</strong></td>
                            <td>${customer.recency}</td>
                            <td>${customer.frequency}</td>
                            <td>${customer.monetary}</td>
                        </tr>
                    `;
                });

                tableHtml += '</tbody></table>';
                container.innerHTML = tableHtml;

            } catch (error) {
                document.getElementById('segmentation-container').innerHTML = '<p style="color: red;">Failed to load customer segments.</p>';
                console.error('Error fetching segments:', error);
            }
        }

        // Call the function when the page loads
        document.addEventListener('DOMContentLoaded', fetchCustomerSegments);
    </script>
@endsection