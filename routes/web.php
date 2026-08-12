<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductVerificationController;
use App\Http\Controllers\Admin\UmkmVerificationController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController as OwnerDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner\MediaController;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\UmkmController;
use App\Http\Controllers\ProductController as PublicProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UmkmController as PublicUmkmController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/cari', SearchController::class)->name('public.search');
Route::get('/umkm', [PublicUmkmController::class, 'index'])->name('public.umkm.index');
Route::get('/umkm/{umkm:slug}', [PublicUmkmController::class, 'show'])->name('public.umkm.show');
Route::get('/produk', [PublicProductController::class, 'index'])->name('public.product.index');
Route::get('/produk/{product:slug}', [PublicProductController::class, 'show'])->name('public.product.show');
Route::get('/kategori/{category:slug}/umkm', [CategoryController::class, 'umkm'])->name('public.category.umkm');
Route::get('/kategori/{category:slug}/produk', [CategoryController::class, 'product'])->name('public.category.product');
Route::get('/tentang', [AboutController::class, 'index'])->name('public.about');
Route::get('/kontak', [ContactController::class, 'index'])->name('public.contact');

Route::get('/dashboard', OwnerDashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/umkm/create', [UmkmController::class, 'create'])->name('umkm.create');
        Route::post('/umkm', [UmkmController::class, 'store'])->name('umkm.store');
        Route::post('/umkm/{umkm}/submit', [UmkmController::class, 'submit'])->name('umkm.submit');
        Route::get('/umkm/{umkm}/edit', [UmkmController::class, 'edit'])->name('umkm.edit');
        Route::put('/umkm/{umkm}', [UmkmController::class, 'update'])->name('umkm.update');
        Route::get('/umkm/{umkm}/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/umkm/{umkm}/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/umkm/{umkm}/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/{product}/submit', [ProductController::class, 'submit'])->name('products.submit');
        Route::post('/umkm/{umkm}/media/{collection}', [MediaController::class, 'storeUmkmMedia'])->name('umkm.media.store');
        Route::post('/products/{product}/media/{collection}', [MediaController::class, 'storeProductMedia'])->name('products.media.store');
        Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    });

Route::middleware(['auth', 'role:administrator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/umkm/verification', [UmkmVerificationController::class, 'index'])->name('umkm.verification.index');
        Route::get('/umkm/verification/{verificationRequest}', [UmkmVerificationController::class, 'show'])->name('umkm.verification.show');
        Route::post('/umkm/verification/{verificationRequest}/approve', [UmkmVerificationController::class, 'approve'])->name('umkm.verification.approve');
        Route::post('/umkm/verification/{verificationRequest}/reject', [UmkmVerificationController::class, 'reject'])->name('umkm.verification.reject');
        Route::post('/umkm/verification/{verificationRequest}/needs-revision', [UmkmVerificationController::class, 'needsRevision'])->name('umkm.verification.needs-revision');
        Route::get('/products/verification', [ProductVerificationController::class, 'index'])->name('products.verification.index');
        Route::get('/products/verification/{verificationRequest}', [ProductVerificationController::class, 'show'])->name('products.verification.show');
        Route::post('/products/verification/{verificationRequest}/approve', [ProductVerificationController::class, 'approve'])->name('products.verification.approve');
        Route::post('/products/verification/{verificationRequest}/reject', [ProductVerificationController::class, 'reject'])->name('products.verification.reject');
        Route::post('/products/verification/{verificationRequest}/needs-revision', [ProductVerificationController::class, 'needsRevision'])->name('products.verification.needs-revision');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
