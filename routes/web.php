<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\shop\ShopController;
use Illuminate\Support\Facades\Session;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;



/// TO SHOW CLIENT FRONT PAGE
Route::get('/home', function () {
    return view('welcome');
});

Route::get('/', [FrontController::class, 'index'])->name('client.view');

Route::get(
    '/shop/{category_slug?}/{sub_category_slug?}',
    [ShopController::class, 'index']
)->name('client.shop');

Route::get('/product/{slug}', [ShopController::class, 'product'])
    ->name('client.product');

//CART CONTROLLER
Route::get('cart/index', [CartController::class, 'index'])->name('client.cart');
Route::post('cart/add', [CartController::class, 'add'])
    ->name('client.addToCart');
Route::post('cart/update', [CartController::class, 'update'])
    ->name('client.updateCart');
Route::post('cart/delete', [CartController::class, 'destory'])
    ->name('client.deleteCart');
Route::get('cart/checkout', [CartController::class, 'checkout'])
    ->name('client.checkout');

//AUTH CONTROLLER
Route::group(['prefix'=> 'client'], function(){

    Route::group(['middleware' => 'guest'], function(){

        Route::get('/register', [AuthController::class, 'register'])
            ->name('client.register');
        Route::post('/processRegister', [AuthController::class, 'processRegister'])
            ->name('client.processRegister');
        Route::get('/login', [AuthController::class, 'login'])
            ->name('client.login');
        Route::post('/processLogin', [AuthController::class, 'processLogin'])
            ->name('client.processLogin');
    });

    //AUTHENTICATED ROUTES
     Route::group(['middleware' => 'auth'], function(){

        Route::get('/profile', [AuthController::class, 'profile'])
            ->name('client.profile');
        Route::get('/logout', [AuthController::class, 'logout'])
            ->name('client.logout');
     });
});





Route::group(['prefix' => 'admin'], function () {

    Route::group(['middleware' => 'admin.guest'], function () {

        Route::get('/login', [AdminLoginController::class, 'index'])
            ->name('admin.login');

        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])
            ->name('admin.authenticate');
    });

    Route::group(['middleware' => 'admin.auth'], function () {

        // Dashboard routes
        Route::get('/dashboard', [HomeController::class, 'index'])
            ->name('admin.dashboard');

        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');


        // Categories routes
        Route::get('/categories/index', [CategoryController::class, 'index'])
            ->name('categories.index');

        Route::get('/categories/create', [CategoryController::class, 'create'])
            ->name('categories.create');

        Route::post('/categories/store', [CategoryController::class, 'store'])
            ->name('categories.store');

        Route::get('/categories/edit/{id}', [CategoryController::class, 'edit'])
            ->name('categories.edit');

        Route::put('/categories/update/{id}', [
            CategoryController::class, 'update'
        ])->name('categories.update');

        Route::delete('/categories/delete/{id}', [
            CategoryController::class, 'destory'
        ])->name('categories.delete');

        // Sub Category  routes
        Route::get('/sub-categories/index', [
            SubCategoryController::class, 'index'
        ])->name('sub-categories.index');

        Route::get('/sub-categories/create', [
            SubCategoryController::class, 'create'
        ])->name('sub-categories.create');

        Route::post('/sub-categories/store', [SubCategoryController::class, 'store'])
            ->name('sub-categories.store');

        Route::get('/sub-categories/edit/{id}', [SubCategoryController::class, 'edit'])
            ->name('sub-categories.edit');

        Route::put('/sub-categories/update/{id}', [
            SubCategoryController::class, 'update'
        ])->name('sub-categories.update');

        Route::delete('/sub-categories/delete/{id}', [
            SubCategoryController::class, 'destory'
        ])->name('sub-categories.delete');


        //BRANDS ROUTES
        Route::get('/brand/index', [BrandController::class, 'index'])
            ->name('brand.index');

        Route::get('/brand/create', [BrandController::class, 'create'])
            ->name('brand.create');

        Route::post('/brand/store', [BrandController::class, 'store'])
            ->name('brand.store');

        Route::get('/brand/edit/{id}', [BrandController::class, 'edit'])
            ->name('brand.edit');

        Route::put('/brand/update/{id}', [BrandController::class, 'update'])
            ->name('brand.update');

        Route::delete('/brand/delete/{id}', [BrandController::class, 'destory'])
            ->name('brand.delete');

        //PRODUCT ROUTES, PRODUCT-SUBCATEGORY
        Route::get(
            '/product-subcategories/index',
            [ProductSubCategoryController::class, 'index']
        )
            ->name('product-subcategories.index');

        Route::get('/products/index', [ProductController::class, 'index'])
            ->name('products.index');

        Route::get('/products/create', [ProductController::class, 'create'])
            ->name('products.create');

        Route::post('/products/store', [ProductController::class, 'store'])
            ->name('products.store');

        Route::get('/products/edit/{id}', [ProductController::class, 'edit'])
            ->name('products.edit');

        Route::put('/products/update/{id}', [ProductController::class, 'update'])
            ->name('products.update');

        Route::delete('/products/delete/{id}', [ProductController::class, 'destory'])
            ->name('products.delete');

        Route::get('/products/get', [ProductController::class, 'get'])
            ->name('products.get');






        //TEMP IMAGE CREATE
        Route::post('image/update', [ProductImageController::class, 'update'])
            ->name('product-images.update');

        Route::delete('image/delete', [ProductImageController::class, 'destory'])
            ->name('product-images.delete');

        Route::post('categories/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');

        Route::get('/categories/getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug =  Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug,
            ]);
        })->name('categories.getSlug');
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
