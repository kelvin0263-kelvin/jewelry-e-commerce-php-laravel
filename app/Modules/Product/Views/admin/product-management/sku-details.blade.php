@extends('layouts.admin')

@section('title', 'Product Management')

@section('content')
    <!-- Header -->
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-gem mr-2"></i>SKU Details - {{ $inventory->name }}
            </h1>
            <p class="text-gray-600 mt-1 text-sm">Manage SKU variations for this inventory item</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.product-management.index') }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4 auto-dismiss" data-dismiss-delay="5000">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4 auto-dismiss" data-dismiss-delay="5000">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('inventory_changes'))
        <div class="mb-4 rounded-md bg-blue-50 p-4 auto-dismiss" data-dismiss-delay="5000">
            <div class="flex">
                <div class="ml-3">
                    <h6 class="text-sm font-medium text-blue-800 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>Inventory Changes Detected
                    </h6>
                    <p class="mt-1 text-sm text-blue-700">
                        <strong>SKU:</strong> {{ session('inventory_changes.sku') }} - 
                        <strong>Product:</strong> {{ session('inventory_changes.name') }}
                    </p>
                    <p class="mt-1 text-sm text-blue-700">
                        <strong>Updated:</strong> {{ session('inventory_changes.updated_at') }}
                    </p>
                    <p class="mt-1 text-sm text-blue-700">
                        <strong>Changes:</strong> {{ implode(', ', session('inventory_changes.changes')) }}
                    </p>
                </div>
            </div>
        </div>
    @endif



    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h6 class="text-lg font-medium text-gray-800 flex items-center">
                <i class="fas fa-list mr-2 text-blue-500"></i>Products List
            </h6>
        </div>
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
            <span class="text-sm text-gray-600 flex items-center">
                <i class="fas fa-box mr-1"></i>{{ $products->total() }} products
            </span>
        </div>
        <div class="product-module-table" style="overflow: hidden !important; position: relative;">
            <div class="table-scroll-container" style="overflow-x: auto; overflow-y: hidden; scrollbar-width: none; -ms-overflow-style: none; -webkit-scrollbar: none; width: 100%;">
                <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">ID (SKU)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discounted Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Features</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Media</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 w-24">
                                {{ $product->sku }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                RM{{ number_format($product->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($product->selling_price)
                                    RM{{ number_format($product->selling_price, 2) }}
                                @else
                                    None
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($product->discount_price)
                                    RM{{ number_format($product->discount_price, 2) }}
                                @else
                                    None
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($product->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    @foreach($product->features ?? [] as $feature)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $product->description ?: 'None' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-image mr-1"></i>
                                    {{ count($product->customer_images ?? []) }} img
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->product_record && $product->product_record->marketing_description && $product->product_record->marketing_description !== 'None')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Complete
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Incomplete
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex flex-col space-y-1">
                                    <!-- Create Information button - always show -->
                                    @if($product->product_record)
                                        <a href="{{ route('admin.product-management.enhance', $product->product_record) }}" 
                                           class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-cyan-500 hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 w-24 justify-center">
                                            <i class="fas fa-plus-circle mr-1"></i>Create Info
                                        </a>
                                    @else
                                        <a href="{{ route('admin.product-management.create-info', $product->id) }}" 
                                           class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-cyan-500 hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 w-24 justify-center">
                                            <i class="fas fa-plus-circle mr-1"></i>Create Info
                                        </a>
                                    @endif
                                    
                                    <!-- Edit button - always show -->
                                    @if($product->product_record)
                                        <a href="{{ route('admin.product-management.edit', $product->product_record) }}" 
                                           class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-24 justify-center">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                    @else
                                        <a href="{{ route('admin.product-management.create', $product->id) }}" 
                                           class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-24 justify-center">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-box-open text-gray-400 mb-2 text-3xl"></i>
                                <h5 class="text-gray-600 text-lg font-medium mb-2">No Products Found</h5>
                                <p class="text-gray-500 text-sm">No inventory variations are available at the moment</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    @if($products->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @endif

    </div>

    
    <!-- Custom Styles -->
    <style>
         body a,
body a:link,
body a:visited,
body a:hover,
body a:active {
    text-decoration: none;
    color: black
}

        /* Enhanced Page Header */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 0;
            margin-bottom: 0.75rem;
            border-radius: 0 0 8px 8px;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.1) 100%);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .page-header > * {
            position: relative;
            z-index: 1;
        }
        
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table thead th {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table tbody td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            font-size: 0.75rem;
        }
        
        .table tbody td small {
            font-size: 0.75rem;
        }
        
        
        .d-flex.gap-1 .btn {
            font-size: 0.65rem;
            width: 80px;
            min-width: 80px;
            max-width: 80px;
            white-space: nowrap;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 0.25rem 0.5rem;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .d-flex.gap-1 .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .hover-row:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: scale(1.01);
            transition: all 0.3s ease;
        }
        
        
        
        
        
        
        
        .reviews-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            display: inline-block;
            letter-spacing: 0.5px;
        }
        
        .reviews-btn:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .reviews-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
        }
        
        /* Enhanced Pagination */
        .pagination-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1.5rem 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #dee2e6;
        }
        
        .pagination .page-link {
            border: 1px solid #dee2e6;
            color: #495057;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.6rem 1rem;
            margin: 0 0.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .pagination .page-link:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .pagination .page-item.disabled .page-link {
            background: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
            cursor: not-allowed;
        }
        
        .pagination .page-item.disabled .page-link:hover {
            background: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
            transform: none;
            box-shadow: none;
        }
        
        /* Custom Back Button - Completely isolated styling with maximum specificity */
        body .d-flex.gap-1 .custom-back-btn,
        .custom-back-btn {
            background: white !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            color: #2d3748 !important;
            font-weight: 500 !important;
            padding: 0.4rem 0.8rem !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            font-size: 0.75rem !important;
            line-height: 1.2 !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            margin: 0 !important;
            width: auto !important;
            height: auto !important;
            min-width: auto !important;
            max-width: none !important;
        }
        
        body .d-flex.gap-1 .custom-back-btn:hover,
        .custom-back-btn:hover {
            background: #f7fafc !important;
            border-color: rgba(0, 0, 0, 0.12) !important;
            color: #2d3748 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
            text-decoration: none !important;
        }
        
        .custom-back-btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }
        
        .custom-back-btn:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1) !important;
        }
        
        .custom-back-btn i {
            font-size: 0.7rem !important;
            margin-right: 0.3rem !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts after specified delay
            const autoDismissAlerts = document.querySelectorAll('.auto-dismiss');
            
            autoDismissAlerts.forEach(function(alert) {
                const delay = parseInt(alert.getAttribute('data-dismiss-delay')) || 5000;
                
                setTimeout(function() {
                    if (alert && alert.parentNode) {
                        // Add fade out effect
                        alert.classList.add('fade');
                        alert.classList.remove('show');
                        
                        // Remove from DOM after fade animation
                        setTimeout(function() {
                            if (alert && alert.parentNode) {
                                alert.parentNode.removeChild(alert);
                            }
                        }, 150); // Wait for fade animation to complete
                    }
                }, delay);
            });
        });
        
        // 强制隐藏滚动条 - 使用更激进的方法
        document.addEventListener('DOMContentLoaded', function() {
            const tables = document.querySelectorAll('.product-module-table');
            const scrollContainers = document.querySelectorAll('.table-scroll-container');
            
            // 处理主表格容器
            tables.forEach(function(table) {
                table.style.overflow = 'hidden';
                table.style.position = 'relative';
            });
            
            // 处理滚动容器
            scrollContainers.forEach(function(container) {
                container.style.scrollbarWidth = 'none';
                container.style.msOverflowStyle = 'none';
                container.style.overflowX = 'auto';
                container.style.overflowY = 'hidden';
                
                // 强制应用样式
                container.setAttribute('style', container.getAttribute('style') + '; scrollbar-width: none !important; -ms-overflow-style: none !important;');
            });
            
            // 添加全局CSS来强制隐藏滚动条
            const style = document.createElement('style');
            style.textContent = `
                .table-scroll-container::-webkit-scrollbar {
                    display: none !important;
                    width: 0 !important;
                    height: 0 !important;
                    background: transparent !important;
                }
                .table-scroll-container::-webkit-scrollbar:horizontal {
                    display: none !important;
                    height: 0 !important;
                    width: 0 !important;
                }
                .table-scroll-container::-webkit-scrollbar-track {
                    display: none !important;
                    width: 0 !important;
                    height: 0 !important;
                }
                .table-scroll-container::-webkit-scrollbar-thumb {
                    display: none !important;
                    width: 0 !important;
                    height: 0 !important;
                }
                .table-scroll-container::-webkit-scrollbar-corner {
                    display: none !important;
                }
                .table-scroll-container::-webkit-scrollbar-button {
                    display: none !important;
                    width: 0 !important;
                    height: 0 !important;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
@endsection

@push('styles')
<style>
    /* Product Module - Hide floating horizontal scrollbars only */
    .product-module-table {
        -ms-overflow-style: none !important;  /* Internet Explorer 10+ */
        scrollbar-width: none !important;  /* Firefox */
        overflow-x: auto !important;
    }
    
    /* Hide floating horizontal scrollbars in webkit browsers */
    .product-module-table::-webkit-scrollbar { 
        display: none !important;
        width: 0 !important;
        height: 0 !important;
        background: transparent !important;
    }
    
    .product-module-table::-webkit-scrollbar:horizontal {
        display: none !important;
        height: 0 !important;
    }
    
    .product-module-table::-webkit-scrollbar-track {
        display: none !important;
        background: transparent !important;
    }
    
    .product-module-table::-webkit-scrollbar-thumb {
        display: none !important;
        background: transparent !important;
    }
    
    .product-module-table::-webkit-scrollbar-corner {
        display: none !important;
        background: transparent !important;
    }
    
    /* Hide floating horizontal scrollbar buttons */
    .product-module-table::-webkit-scrollbar-button {
        display: none !important;
    }
    
    .product-module-table::-webkit-scrollbar-button:horizontal {
        display: none !important;
    }
    
    .product-module-table::-webkit-scrollbar-track-piece {
        display: none !important;
    }
    
    .product-module-table::-webkit-scrollbar-track-piece:horizontal {
        display: none !important;
    }
    
    /* Additional overrides for floating horizontal scrollbars */
    .product-module-table {
        scrollbar-gutter: stable !important;
        scrollbar-color: transparent transparent !important;
    }
    
    /* 完全隐藏滚动条 - 使用嵌套div方法 */
    .product-module-table {
        overflow: hidden !important;
        position: relative !important;
    }
    
    .table-scroll-container {
        overflow-x: auto !important;
        overflow-y: hidden !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
        width: 100% !important;
        height: 100% !important;
    }
    
    /* 隐藏所有webkit滚动条 */
    .table-scroll-container::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
        background: transparent !important;
    }
    
    .table-scroll-container::-webkit-scrollbar:horizontal {
        display: none !important;
        height: 0 !important;
        width: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-track {
        display: none !important;
        background: transparent !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-thumb {
        display: none !important;
        background: transparent !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-corner {
        display: none !important;
        background: transparent !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-button {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-button:horizontal {
        display: none !important;
        height: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-track-piece {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-track-piece:horizontal {
        display: none !important;
        height: 0 !important;
    }
    
    /* 强制隐藏所有滚动条元素 */
    .table-scroll-container *::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container *::-webkit-scrollbar-track {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container *::-webkit-scrollbar-thumb {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    /* 确保没有滚动条空间被保留 */
    .table-scroll-container {
        scrollbar-gutter: stable !important;
        scrollbar-color: transparent transparent !important;
    }
    
    /* 覆盖任何现有的滚动条样式 */
    .table-scroll-container[style*="scrollbar"] {
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }
    
    /* 额外的webkit滚动条隐藏 */
    .table-scroll-container {
        -webkit-scrollbar-width: none !important;
        -webkit-scrollbar-height: none !important;
    }
</style>
@endpush

