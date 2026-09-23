<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashRemittanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

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
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [
        AuthController::class,
        'logout'
    ])->name('logout');


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
    |
    | Finance has its own dashboard and finance-only workspace.
    |
    | Blade file:
    | resources/views/dashboard/finance.blade.php
    |
    | URL:
    | /finance/dashboard
    |
    */

    Route::middleware('role:Finance')->group(function () {

        Route::get('/finance/dashboard', [
            FinanceController::class,
            'index'
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

    });


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - VIEW
    |--------------------------------------------------------------------------
    |
    | Finance:
    | - Can view inventory
    |
    | Procurement:
    | - Can view inventory
    |
    | CEO/Admin:
    | - Can view inventory
    |
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
    |--------------------------------------------------------------------------
    |
    | Finance is intentionally excluded.
    |
    | CEO/Admin and Procurement can manage inventory.
    |
    */

    Route::middleware(
        'role:CEO/Admin,Procurement'
    )->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Create Inventory Item
        |--------------------------------------------------------------------------
        */

        Route::get('/inventory/create', [
            InventoryController::class,
            'create'
        ])->name('inventory.create');


        /*
        |--------------------------------------------------------------------------
        | Store Inventory Item
        |--------------------------------------------------------------------------
        */

        Route::post('/inventory', [
            InventoryController::class,
            'store'
        ])->name('inventory.store');


        /*
        |--------------------------------------------------------------------------
        | Edit Inventory Item
        |--------------------------------------------------------------------------
        */

        Route::get('/inventory/{inventoryItem}/edit', [
            InventoryController::class,
            'edit'
        ])->name('inventory.edit');


        /*
        |--------------------------------------------------------------------------
        | Update Inventory Item
        |--------------------------------------------------------------------------
        */

        Route::put('/inventory/{inventoryItem}', [
            InventoryController::class,
            'update'
        ])->name('inventory.update');


        /*
        |--------------------------------------------------------------------------
        | Stock Transactions
        |--------------------------------------------------------------------------
        */

        Route::get('/inventory/{inventoryItem}/stock', [
            InventoryController::class,
            'stockForm'
        ])->name('inventory.stock');


        /*
        |--------------------------------------------------------------------------
        | Supplier + Inventory Item Purchase Cost
        |--------------------------------------------------------------------------
        */

        Route::get('/inventory/supplier-item-cost', [
            InventoryController::class,
            'supplierItemCost'
        ])->name('inventory.supplier-item-cost');


        /*
        |--------------------------------------------------------------------------
        | Multi-Item Stock Receipt
        |--------------------------------------------------------------------------
        */

        Route::post('/inventory/stock-receipt', [
            InventoryController::class,
            'stockReceiptStore'
        ])->name('inventory.stock-receipt.store');


        /*
        |--------------------------------------------------------------------------
        | Legacy / Single-Item Stock In
        |--------------------------------------------------------------------------
        */

        Route::post('/inventory/{inventoryItem}/stock-in', [
            InventoryController::class,
            'stockIn'
        ])->name('inventory.stock-in');


        /*
        |--------------------------------------------------------------------------
        | Manual Stock Out
        |--------------------------------------------------------------------------
        */

        Route::post('/inventory/{inventoryItem}/stock-out', [
            InventoryController::class,
            'stockOut'
        ])->name('inventory.stock-out');


        /*
        |--------------------------------------------------------------------------
        | Physical Count Adjustment
        |--------------------------------------------------------------------------
        */

        Route::post('/inventory/{inventoryItem}/adjust', [
            InventoryController::class,
            'adjust'
        ])->name('inventory.adjust');

    });


    /*
    |--------------------------------------------------------------------------
    | PURCHASE PRICE LOOKUP
    |--------------------------------------------------------------------------
    |
    | Used by purchase/inventory management.
    |
    | Finance does NOT need direct access to this endpoint.
    |
    | IMPORTANT:
    | This route stays BEFORE /purchases/{purchase}.
    |
    */

    Route::middleware(
        'role:CEO/Admin,Procurement'
    )->group(function () {

        Route::get('/purchases/latest-cost', [
            InventoryController::class,
            'latestPurchaseCost'
        ])->name('purchases.latest-cost');

    });


    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    |
    | Products are restricted to CEO/Admin.
    |
    | Finance does NOT have Product access.
    | Procurement does NOT have Product access.
    |
    */

    Route::middleware(
        'role:CEO/Admin'
    )->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Product List
        |--------------------------------------------------------------------------
        */

        Route::get('/products', [
            ProductController::class,
            'index'
        ])->name('products.index');


        /*
        |--------------------------------------------------------------------------
        | Create Product
        |--------------------------------------------------------------------------
        */

        Route::get('/products/create', [
            ProductController::class,
            'create'
        ])->name('products.create');


        /*
        |--------------------------------------------------------------------------
        | Store Product
        |--------------------------------------------------------------------------
        */

        Route::post('/products', [
            ProductController::class,
            'store'
        ])->name('products.store');


        /*
        |--------------------------------------------------------------------------
        | Edit Product
        |--------------------------------------------------------------------------
        */

        Route::get('/products/{product}/edit', [
            ProductController::class,
            'edit'
        ])->name('products.edit');


        /*
        |--------------------------------------------------------------------------
        | Update Product
        |--------------------------------------------------------------------------
        */

        Route::put('/products/{product}', [
            ProductController::class,
            'update'
        ])->name('products.update');

    });


    /*
    |--------------------------------------------------------------------------
    | RECIPES
    |--------------------------------------------------------------------------
    |
    | CEO/Admin only.
    |
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
    |--------------------------------------------------------------------------
    |
    | Finance is VIEW ONLY.
    |
    | CEO/Admin and Procurement can manage suppliers.
    |
    */

    Route::middleware(
        'role:CEO/Admin,Procurement'
    )->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Create Supplier
        |--------------------------------------------------------------------------
        */

        Route::get('/suppliers/create', [
            SupplierController::class,
            'create'
        ])->name('suppliers.create');


        /*
        |--------------------------------------------------------------------------
        | Store Supplier
        |--------------------------------------------------------------------------
        */

        Route::post('/suppliers', [
            SupplierController::class,
            'store'
        ])->name('suppliers.store');


        /*
        |--------------------------------------------------------------------------
        | Edit Supplier
        |--------------------------------------------------------------------------
        */

        Route::get('/suppliers/{supplier}/edit', [
            SupplierController::class,
            'edit'
        ])->name('suppliers.edit');


        /*
        |--------------------------------------------------------------------------
        | Update Supplier
        |--------------------------------------------------------------------------
        */

        Route::put('/suppliers/{supplier}', [
            SupplierController::class,
            'update'
        ])->name('suppliers.update');


        /*
        |--------------------------------------------------------------------------
        | Delete Supplier
        |--------------------------------------------------------------------------
        */

        Route::delete('/suppliers/{supplier}', [
            SupplierController::class,
            'destroy'
        ])->name('suppliers.destroy');


        /*
        |--------------------------------------------------------------------------
        | Activate Supplier
        |--------------------------------------------------------------------------
        */

        Route::patch('/suppliers/{supplier}/activate', [
            SupplierController::class,
            'activate'
        ])->name('suppliers.activate');


        /*
        |--------------------------------------------------------------------------
        | Deactivate Supplier
        |--------------------------------------------------------------------------
        */

        Route::patch('/suppliers/{supplier}/deactivate', [
            SupplierController::class,
            'deactivate'
        ])->name('suppliers.deactivate');


        /*
        |--------------------------------------------------------------------------
        | Put Supplier On Hold
        |--------------------------------------------------------------------------
        */

        Route::patch('/suppliers/{supplier}/hold', [
            SupplierController::class,
            'hold'
        ])->name('suppliers.hold');


        /*
        |--------------------------------------------------------------------------
        | Blacklist Supplier
        |--------------------------------------------------------------------------
        */

        Route::patch('/suppliers/{supplier}/blacklist', [
            SupplierController::class,
            'blacklist'
        ])->name('suppliers.blacklist');

    });


    /*
    |--------------------------------------------------------------------------
    | SUPPLIERS - VIEW
    |--------------------------------------------------------------------------
    |
    | Finance can view suppliers.
    |
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
    |--------------------------------------------------------------------------
    |
    | Finance is VIEW ONLY.
    |
    | CEO/Admin and Procurement manage purchases.
    |
    */

    Route::middleware(
        'role:CEO/Admin,Procurement'
    )->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Create Purchase
        |--------------------------------------------------------------------------
        */

        Route::get('/purchases/create', [
            PurchaseController::class,
            'create'
        ])->name('purchases.create');


        /*
        |--------------------------------------------------------------------------
        | Store Purchase
        |--------------------------------------------------------------------------
        */

        Route::post('/purchases', [
            PurchaseController::class,
            'store'
        ])->name('purchases.store');


        /*
        |--------------------------------------------------------------------------
        | Edit Purchase
        |--------------------------------------------------------------------------
        */

        Route::get('/purchases/{purchase}/edit', [
            PurchaseController::class,
            'edit'
        ])->name('purchases.edit');


        /*
        |--------------------------------------------------------------------------
        | Update Purchase
        |--------------------------------------------------------------------------
        */

        Route::put('/purchases/{purchase}', [
            PurchaseController::class,
            'update'
        ])->name('purchases.update');


        /*
        |--------------------------------------------------------------------------
        | Submit Purchase
        |--------------------------------------------------------------------------
        */

        Route::post('/purchases/{purchase}/submit', [
            PurchaseController::class,
            'submit'
        ])->name('purchases.submit');


        /*
        |--------------------------------------------------------------------------
        | Approve Purchase
        |--------------------------------------------------------------------------
        */

        Route::post('/purchases/{purchase}/approve', [
            PurchaseController::class,
            'approve'
        ])->name('purchases.approve');


        /*
        |--------------------------------------------------------------------------
        | Reject Purchase
        |--------------------------------------------------------------------------
        */

        Route::post('/purchases/{purchase}/reject', [
            PurchaseController::class,
            'reject'
        ])->name('purchases.reject');


        /*
        |--------------------------------------------------------------------------
        | Mark Purchase as Ordered
        |--------------------------------------------------------------------------
        */

        Route::post('/purchases/{purchase}/order', [
            PurchaseController::class,
            'order'
        ])->name('purchases.order');


        /*
        |--------------------------------------------------------------------------
        | Receive Purchase
        |--------------------------------------------------------------------------
        */

        Route::post('/purchases/{purchase}/receive', [
            PurchaseController::class,
            'receive'
        ])->name('purchases.receive');


        /*
        |--------------------------------------------------------------------------
        | Cancel Purchase
        |--------------------------------------------------------------------------
        */

        Route::post('/purchases/{purchase}/cancel', [
            PurchaseController::class,
            'cancel'
        ])->name('purchases.cancel');

    });


    /*
    |--------------------------------------------------------------------------
    | PURCHASES - VIEW
    |--------------------------------------------------------------------------
    |
    | Finance can view purchases.
    |
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
    |--------------------------------------------------------------------------
    |
    | Finance is VIEW ONLY.
    |
    | Only CEO/Admin can create or cancel sales.
    |
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
    |--------------------------------------------------------------------------
    |
    | Finance can view sales.
    |
    | Procurement is intentionally excluded.
    |
    */

    Route::middleware(
        'role:CEO/Admin,Finance'
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


    /*
    |--------------------------------------------------------------------------
    | EXPENSES - MANAGEMENT
    |--------------------------------------------------------------------------
    |
    | Finance can fully manage expenses.
    |
    | CEO/Admin can also manage expenses.
    |
    */

    Route::middleware(
        'role:CEO/Admin,Finance'
    )->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Create Expense
        |--------------------------------------------------------------------------
        */

        Route::get('/expenses/create', [
            ExpenseController::class,
            'create'
        ])->name('expenses.create');


        /*
        |--------------------------------------------------------------------------
        | Store Expense
        |--------------------------------------------------------------------------
        */

        Route::post('/expenses', [
            ExpenseController::class,
            'store'
        ])->name('expenses.store');


        /*
        |--------------------------------------------------------------------------
        | Edit Expense
        |--------------------------------------------------------------------------
        */

        Route::get('/expenses/{expense}/edit', [
            ExpenseController::class,
            'edit'
        ])->name('expenses.edit');


        /*
        |--------------------------------------------------------------------------
        | Update Expense
        |--------------------------------------------------------------------------
        */

        Route::put('/expenses/{expense}', [
            ExpenseController::class,
            'update'
        ])->name('expenses.update');


        /*
        |--------------------------------------------------------------------------
        | Delete Expense
        |--------------------------------------------------------------------------
        */

        Route::delete('/expenses/{expense}', [
            ExpenseController::class,
            'destroy'
        ])->name('expenses.destroy');

    });


    /*
    |--------------------------------------------------------------------------
    | EXPENSES - VIEW
    |--------------------------------------------------------------------------
    |
    | Finance and CEO/Admin can view expenses.
    |
    */

    Route::middleware(
        'role:CEO/Admin,Finance'
    )->group(function () {

        Route::get('/expenses', [
            ExpenseController::class,
            'index'
        ])->name('expenses.index');


        Route::get('/expenses/{expense}', [
            ExpenseController::class,
            'show'
        ])->name('expenses.show');

    });


    /*
    |--------------------------------------------------------------------------
    | CASH REMITTANCE - FINANCE ONLY
    |--------------------------------------------------------------------------
    |
    | Finance can:
    |
    | - View remittance records
    | - Create a remittance
    | - View a remittance
    | - Edit a remittance
    | - Delete a remittance
    |
    */

    Route::middleware('role:Finance')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Cash Remittance List
        |--------------------------------------------------------------------------
        */

        Route::get('/cash-remittances', [
            CashRemittanceController::class,
            'index'
        ])->name('cash-remittances.index');


        /*
        |--------------------------------------------------------------------------
        | Create Cash Remittance
        |--------------------------------------------------------------------------
        */

        Route::get('/cash-remittances/create', [
            CashRemittanceController::class,
            'create'
        ])->name('cash-remittances.create');


        /*
        |--------------------------------------------------------------------------
        | Store Cash Remittance
        |--------------------------------------------------------------------------
        */

        Route::post('/cash-remittances', [
            CashRemittanceController::class,
            'store'
        ])->name('cash-remittances.store');


        /*
        |--------------------------------------------------------------------------
        | View Cash Remittance
        |--------------------------------------------------------------------------
        */

        Route::get('/cash-remittances/{cashRemittance}', [
            CashRemittanceController::class,
            'show'
        ])->name('cash-remittances.show');


        /*
        |--------------------------------------------------------------------------
        | Edit Cash Remittance
        |--------------------------------------------------------------------------
        */

        Route::get('/cash-remittances/{cashRemittance}/edit', [
            CashRemittanceController::class,
            'edit'
        ])->name('cash-remittances.edit');


        /*
        |--------------------------------------------------------------------------
        | Update Cash Remittance
        |--------------------------------------------------------------------------
        */

        Route::put('/cash-remittances/{cashRemittance}', [
            CashRemittanceController::class,
            'update'
        ])->name('cash-remittances.update');


        /*
        |--------------------------------------------------------------------------
        | Delete Cash Remittance
        |--------------------------------------------------------------------------
        */

        Route::delete('/cash-remittances/{cashRemittance}', [
            CashRemittanceController::class,
            'destroy'
        ])->name('cash-remittances.destroy');

    });


    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    |
    | Finance has access to Reports.
    |
    | Procurement also has access, but the ReportController must restrict
    | the actual report content for Procurement to purchasing/inventory
    | reports only.
    |
    */

    Route::middleware(
        'role:CEO/Admin,Finance,Procurement'
    )->group(function () {

        Route::get('/reports', [
            ReportController::class,
            'index'
        ])->name('reports.index');


        Route::get('/reports/pdf', [
            ReportController::class,
            'pdf'
        ])->name('reports.pdf');


        Route::get('/reports/excel', [
            ReportController::class,
            'excel'
        ])->name('reports.excel');

    });

});