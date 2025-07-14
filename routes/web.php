<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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
    Route::resource('products', ProductController::class);
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

require __DIR__.'/auth.php';
