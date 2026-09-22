<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProcurementReportController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Root Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;

        return match ($role) {
            'CEO/Admin'   => redirect()->route('admin.dashboard'),
            'Finance'     => redirect()->route('finance.dashboard'),
            'Procurement' => redirect()->route('procurement.dashboard'),
            default       => abort(403, 'Your role [' . ($role ?? 'NULL') . '] does not have access to any dashboard.'),
        };
    }

    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/register', [
        AuthController::class,
        'showRegistrationForm'
    ])->name('register');

    Route::post('/register', [
        AuthController::class,
        'register'
    ]);

    Route::get('/login', [
        AuthController::class,
        'showLogin'
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login'
    ])->name('login.authenticate');

});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Logout & Settings
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [
        AuthController::class,
        'logout'
    ])->name('logout');

    Route::get('/settings', [SettingsController::class, 'edit'])
        ->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])
        ->name('settings.update');


    /*
    |--------------------------------------------------------------------------
    | CEO / ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:CEO/Admin')->group(function () {

        Route::get('/admin/dashboard', [
            DashboardController::class,
            'admin'
        ])->name('admin.dashboard');

    });


    /*
    |--------------------------------------------------------------------------
    | FINANCE DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Finance')->group(function () {

        Route::get('/finance/dashboard', [
            DashboardController::class,
            'finance'
        ])->name('finance.dashboard');

    });


    /*
    |--------------------------------------------------------------------------
    | PROCUREMENT DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Procurement')->group(function () {

        Route::get('/procurement/dashboard', [
            DashboardController::class,
            'procurement'
        ])->name('procurement.dashboard');

        Route::get('/procurement/reports', [
            ProcurementReportController::class,
            'index',
        ])->name('procurement.reports');

    });


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - VIEW
    |
    | CEO/Admin   = Full access
    | Finance     = View only
    | Procurement = Manage
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin,Finance,Procurement'
    )->group(function () {

        Route::get('/inventory', [
            InventoryController::class,
            'index'
        ])->name('inventory.index');

    });


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - MANAGEMENT
    |
    | CEO/Admin   = Full access
    | Procurement = Manage
    |
    | Finance is intentionally excluded.
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin,Procurement'
    )->group(function () {

        Route::get('/inventory/create', [
            InventoryController::class,
            'create'
        ])->name('inventory.create');

        Route::post('/inventory', [
            InventoryController::class,
            'store'
        ])->name('inventory.store');

        Route::get('/inventory/{inventoryItem}/edit', [
            InventoryController::class,
            'edit'
        ])->name('inventory.edit');

        Route::put('/inventory/{inventoryItem}', [
            InventoryController::class,
            'update'
        ])->name('inventory.update');

        Route::get('/inventory/{inventoryItem}/stock', [
            InventoryController::class,
            'stockForm'
        ])->name('inventory.stock');

        Route::post('/inventory/{inventoryItem}/stock-in', [
            InventoryController::class,
            'stockIn'
        ])->name('inventory.stock-in');

        Route::post('/inventory/{inventoryItem}/stock-out', [
            InventoryController::class,
            'stockOut'
        ])->name('inventory.stock-out');

        Route::post('/inventory/{inventoryItem}/adjust', [
            InventoryController::class,
            'adjust'
        ])->name('inventory.adjust');

    });


    /*
    |--------------------------------------------------------------------------
    | PRODUCTS - VIEW
    |
    | CEO/Admin   = Full access
    | Finance     = View only
    | Procurement = View only
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin,Finance,Procurement'
    )->group(function () {

        Route::get('/products', [
            ProductController::class,
            'index'
        ])->name('products.index');

    });


    /*
    |--------------------------------------------------------------------------
    | PRODUCTS - MANAGEMENT
    |
    | CEO/Admin = Add and Edit
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin'
    )->group(function () {

        Route::get('/products/create', [
            ProductController::class,
            'create'
        ])->name('products.create');

        Route::post('/products', [
            ProductController::class,
            'store'
        ])->name('products.store');

        Route::get('/products/{product}/edit', [
            ProductController::class,
            'edit'
        ])->name('products.edit');

        Route::put('/products/{product}', [
            ProductController::class,
            'update'
        ])->name('products.update');

    });


    /*
    |--------------------------------------------------------------------------
    | RECIPES
    |
    | CEO/Admin only
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin'
    )->group(function () {

        Route::get(
            '/products/{product}/recipe',
            [
                RecipeController::class,
                'edit'
            ]
        )->name('recipes.edit');

        Route::post(
            '/products/{product}/recipe/items',
            [
                RecipeController::class,
                'addItem'
            ]
        )->name('recipes.items.store');

        Route::put(
            '/products/{product}/recipe/items/{recipeItem}',
            [
                RecipeController::class,
                'updateItem'
            ]
        )->name('recipes.items.update');

        Route::delete(
            '/products/{product}/recipe/items/{recipeItem}',
            [
                RecipeController::class,
                'removeItem'
            ]
        )->name('recipes.items.destroy');

        Route::put(
            '/products/{product}/recipe/instructions',
            [
                RecipeController::class,
                'updateInstructions'
            ]
        )->name('recipes.instructions.update');

    });


    /*
    |--------------------------------------------------------------------------
    | SUPPLIERS - MANAGEMENT
    |
    | CEO/Admin   = Full access
    | Procurement = Manage
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin,Procurement'
    )->group(function () {

        Route::get('/suppliers/create', [
            SupplierController::class,
            'create'
        ])->name('suppliers.create');

        Route::post('/suppliers', [
            SupplierController::class,
            'store'
        ])->name('suppliers.store');

        Route::get('/suppliers/{supplier}/edit', [
            SupplierController::class,
            'edit'
        ])->name('suppliers.edit');

        Route::put('/suppliers/{supplier}', [
            SupplierController::class,
            'update'
        ])->name('suppliers.update');

        Route::delete('/suppliers/{supplier}', [
            SupplierController::class,
            'destroy'
        ])->name('suppliers.destroy');

        Route::patch('/suppliers/{supplier}/activate', [
            SupplierController::class,
            'activate'
        ])->name('suppliers.activate');

    });


    /*
    |--------------------------------------------------------------------------
    | SUPPLIERS - VIEW
    |
    | CEO/Admin   = View
    | Finance     = View only
    | Procurement = View
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin,Finance,Procurement'
    )->group(function () {

        Route::get('/suppliers', [
            SupplierController::class,
            'index'
        ])->name('suppliers.index');

        Route::get('/suppliers/{supplier}', [
            SupplierController::class,
            'show'
        ])->name('suppliers.show');

    });


    /*
    |--------------------------------------------------------------------------
    | PURCHASES - MANAGEMENT
    |
    | CEO/Admin   = Create, edit, approve, reject, order, cancel
    | Procurement = Create, edit, submit, order, cancel
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin,Procurement'
    )->group(function () {

        Route::get('/purchases/create', [
            PurchaseController::class,
            'create'
        ])->name('purchases.create');

        Route::post('/purchases', [
            PurchaseController::class,
            'store'
        ])->name('purchases.store');

        Route::get('/purchases/{purchase}/edit', [
            PurchaseController::class,
            'edit'
        ])->name('purchases.edit');

        Route::put('/purchases/{purchase}', [
            PurchaseController::class,
            'update'
        ])->name('purchases.update');

        Route::post('/purchases/{purchase}/submit', [
            PurchaseController::class,
            'submit'
        ])->name('purchases.submit');

        Route::post('/purchases/{purchase}/approve', [
            PurchaseController::class,
            'approve'
        ])->name('purchases.approve');

        Route::post('/purchases/{purchase}/reject', [
            PurchaseController::class,
            'reject'
        ])->name('purchases.reject');

        Route::post('/purchases/{purchase}/order', [
            PurchaseController::class,
            'order'
        ])->name('purchases.order');

        Route::post('/purchases/{purchase}/cancel', [
            PurchaseController::class,
            'cancel'
        ])->name('purchases.cancel');

    });


    /*
    |--------------------------------------------------------------------------
    | PURCHASES - VIEW
    |
    | CEO/Admin   = View
    | Finance     = View only
    | Procurement = View
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin,Finance,Procurement'
    )->group(function () {

        Route::get('/purchases', [
            PurchaseController::class,
            'index'
        ])->name('purchases.index');

        Route::get('/purchases/{purchase}', [
            PurchaseController::class,
            'show'
        ])->name('purchases.show');

    });


    /*
    |--------------------------------------------------------------------------
    | SALES - MANAGEMENT
    |
    | CEO/Admin = Create, complete, and cancel sales
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin'
    )->group(function () {

        Route::get('/sales/create', [
            SalesController::class,
            'create'
        ])->name('sales.create');

        Route::post('/sales', [
            SalesController::class,
            'store'
        ])->name('sales.store');

        Route::post('/sales/{sale}/cancel', [
            SalesController::class,
            'cancel'
        ])->name('sales.cancel');

    });


    /*
    |--------------------------------------------------------------------------
    | SALES - VIEW
    |
    | CEO/Admin   = View
    | Finance     = View
    | Procurement = View
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:CEO/Admin,Finance,Procurement'
    )->group(function () {

        Route::get('/sales', [
            SalesController::class,
            'index'
        ])->name('sales.index');

        Route::get('/sales/{sale}', [
            SalesController::class,
            'show'
        ])->name('sales.show');

    });

});