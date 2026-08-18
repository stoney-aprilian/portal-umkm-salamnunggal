<?php

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\OwnerVerificationController;
use App\Http\Controllers\Admin\ProductVerificationController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UmkmVerificationController;
use App\Http\Controllers\Admin\UmkmsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController as OwnerDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner\MediaController;
use App\Http\Controllers\Owner\AccountVerificationController;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\ProductRevisionController;
use App\Http\Controllers\Owner\UmkmController;
use App\Http\Controllers\Owner\UmkmRevisionController;
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
        Route::get('/umkm/{umkm}/revisions/create', [UmkmRevisionController::class, 'create'])->name('umkm.revisions.create');
        Route::post('/umkm/{umkm}/revisions', [UmkmRevisionController::class, 'store'])->name('umkm.revisions.store');
        Route::get('/umkm/revisions/{revision}/edit', [UmkmRevisionController::class, 'edit'])->name('umkm.revisions.edit');
        Route::put('/umkm/revisions/{revision}', [UmkmRevisionController::class, 'update'])->name('umkm.revisions.update');
        Route::post('/umkm/revisions/{revision}/submit', [UmkmRevisionController::class, 'submit'])->name('umkm.revisions.submit');
        Route::post('/umkm/revisions/{revision}/media/{collection}', [MediaController::class, 'storeUmkmRevisionMedia'])->name('umkm.revisions.media.store');
        Route::get('/products/{product}/revisions/create', [ProductRevisionController::class, 'create'])->name('products.revisions.create');
        Route::post('/products/{product}/revisions', [ProductRevisionController::class, 'store'])->name('products.revisions.store');
        Route::get('/products/revisions/{revision}/edit', [ProductRevisionController::class, 'edit'])->name('products.revisions.edit');
        Route::put('/products/revisions/{revision}', [ProductRevisionController::class, 'update'])->name('products.revisions.update');
        Route::post('/products/revisions/{revision}/submit', [ProductRevisionController::class, 'submit'])->name('products.revisions.submit');
        Route::post('/products/revisions/{revision}/media/{collection}', [MediaController::class, 'storeProductRevisionMedia'])->name('products.revisions.media.store');
        Route::get('/umkm/create', [UmkmController::class, 'create'])->name('umkm.create');
        Route::post('/umkm', [UmkmController::class, 'store'])->name('umkm.store');
        Route::post('/umkm/{umkm}/submit', [UmkmController::class, 'submit'])->name('umkm.submit');
        Route::get('/umkm/{umkm}/edit', [UmkmController::class, 'edit'])->name('umkm.edit');
        Route::put('/umkm/{umkm}', [UmkmController::class, 'update'])->name('umkm.update');
        Route::get('/umkm/{umkm}/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/umkm/{umkm}/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/umkm/{umkm}/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/{product}/submit', [ProductController::class, 'submit'])->name('products.submit');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/umkm/{umkm}/media/{collection}', [MediaController::class, 'storeUmkmMedia'])->name('umkm.media.store');
        Route::post('/products/{product}/media/{collection}', [MediaController::class, 'storeProductMedia'])->name('products.media.store');
        Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    });

Route::middleware(['auth', 'role:administrator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/owner-verification', [OwnerVerificationController::class, 'index'])->name('owner-verification.index');
        Route::get('/owner-verification/{verificationRequest}', [OwnerVerificationController::class, 'show'])->name('owner-verification.show');
        Route::post('/owner-verification/{verificationRequest}/approve', [OwnerVerificationController::class, 'approve'])->name('owner-verification.approve');
        Route::post('/owner-verification/{verificationRequest}/reject', [OwnerVerificationController::class, 'reject'])->name('owner-verification.reject');
        Route::post('/owner-verification/{verificationRequest}/needs-revision', [OwnerVerificationController::class, 'needsRevision'])->name('owner-verification.needs-revision');
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
        Route::get('/umkms', [UmkmsController::class, 'index'])->name('umkms.index');
        Route::get('/umkms/create', [UmkmsController::class, 'create'])->name('umkms.create');
        Route::post('/umkms', [UmkmsController::class, 'store'])->name('umkms.store');
        Route::get('/umkms/{umkm}', [UmkmsController::class, 'show'])->name('umkms.show');
        Route::get('/umkms/{umkm}/edit', [UmkmsController::class, 'edit'])->name('umkms.edit');
        Route::put('/umkms/{umkm}', [UmkmsController::class, 'update'])->name('umkms.update');
        Route::delete('/umkms/{umkm}', [UmkmsController::class, 'destroy'])->name('umkms.destroy');
        Route::post('/umkms/{umkm}/media/{collection}', [AdminMediaController::class, 'store'])->name('umkms.media.store')->whereIn('collection', ['logo', 'banner', 'gallery']);
        Route::delete('/media/{media}', [AdminMediaController::class, 'destroy'])->name('media.destroy');
        Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
        Route::get('/products/{product}', [ProductsController::class, 'show'])->name('products.show');
        Route::get('/products/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductsController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductsController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/media/{collection}', [AdminMediaController::class, 'storeProduct'])->name('products.media.store')->whereIn('collection', ['product']);
        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('/users', [UsersController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UsersController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/suspend', [UsersController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/activate', [UsersController::class, 'activate'])->name('users.activate');
        Route::post('/users/{user}/reset-password', [UsersController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/categories', [CategoriesController::class, 'index'])->name('categories.index');
        Route::get('/categories/create/{type}', [CategoriesController::class, 'create'])->name('categories.create')->whereIn('type', ['umkm', 'product']);
        Route::post('/categories/{type}', [CategoriesController::class, 'store'])->name('categories.store')->whereIn('type', ['umkm', 'product']);
        Route::get('/categories/{category}/edit', [CategoriesController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoriesController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoriesController::class, 'destroy'])->name('categories.destroy');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });

Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/account/verification', [AccountVerificationController::class, 'notice'])->name('account.verification.notice');
    Route::get('/account/verification/edit', [AccountVerificationController::class, 'edit'])->name('account.verification.edit');
    Route::put('/account/verification', [AccountVerificationController::class, 'update'])->name('account.verification.update');
    Route::post('/account/verification/submit', [AccountVerificationController::class, 'submit'])->name('account.verification.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
