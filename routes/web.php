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
use Illuminate\Support\Facades\Session;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return view('welcome');
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
        Route::get('/product-subcategories/index',
                [ProductSubCategoryController::class, 'index'])
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





        //TEMP IMAGE CREATE
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
