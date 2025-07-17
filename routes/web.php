<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Api\CustomerController as ApiCustomerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProductController as AdminProductController; // 引入后台控制器并使用别名

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// need to login first(auth) and then check if the user is admin (is_admin)
// ->prefix('admin') mean add a prefix to the route example /producsts become /admin/products
// ->name('admin.'): 这个方法用于为这个路由组内的所有命名路由添加名称前缀 so when use Route::resource which create products.index it will become admin.products.index
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // 例如: admin.products.index, admin.products.create
    // Route::resource mean also create somethings like route::get('products',[ProductController::class,'index']) but follow the 7 restful routes which have index,create,store,show,edit,update,destroy
    // which map to each method in the product controller 
    Route::resource('products', AdminProductController::class);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('customers/segmentation', [ApiCustomerController::class, 'segmentation'])->name('api.customers.segmentation');

    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{user}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('customers/{user}/edit', [CustomerController::class, 'edit'])->name('customers.edit'); // Add this line
    Route::put('customers/{user}', [CustomerController::class, 'update'])->name('customers.update'); // Add this line

    Route::get('reports/product-performance', [ReportController::class, 'productPerformance'])->name('reports.product_performance');

});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Public-facing Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');



require __DIR__ . '/auth.php';
