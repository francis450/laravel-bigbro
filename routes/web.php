<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('api/{invoice}', [InvoiceController::class, 'apiShow'])->name('api.invoice');
    Route::get('/create', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('/', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
});

Route::prefix('clients')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('client.index');
    Route::get('/create', [ClientController::class, 'create'])->name('client.create');
    Route::post('/', [ClientController::class, 'store'])->name('client.store');
    Route::get('/{client}', [ClientController::class, 'show'])->name('client.show');
    Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('client.edit');
    Route::put('/{client}', [ClientController::class, 'update'])->name('client.update');
    Route::delete('/{client}', [ClientController::class, 'destroy'])->name('client.destroy');
});

Route::prefix('items')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('items.index');
    Route::get('/create', [ItemController::class, 'create'])->name('item.create');
    Route::post('/', [ItemController::class, 'store'])->name('item.store');
    Route::get('/{item}', [ItemController::class, 'show'])->name('item.show');
    Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('item.edit');
    Route::put('/{item}', [ItemController::class, 'update'])->name('item.update');
    Route::delete('/{item}', [ItemController::class, 'destroy'])->name('item.destroy');
});

Route::prefix('inventoryItems')->group(function () {
    Route::get('/', [InventoryItemController::class, 'index'])->name('inventoryItems.index');
    Route::get('/create', [InventoryItemController::class, 'create'])->name('inventoryItem.create');
    Route::post('/', [InventoryItemController::class, 'store'])->name('inventoryItem.store');
    Route::get('/{inventoryItem}', [InventoryItemController::class, 'show'])->name('inventoryItem.show');
    Route::get('/{inventoryItem}/edit', [InventoryItemController::class, 'edit'])->name('inventoryItem.edit');
    Route::put('/{inventoryItem}', [InventoryItemController::class, 'update'])->name('inventoryItem.update');
    Route::delete('/{inventoryItem}', [InventoryItemController::class, 'destroy'])->name('inventoryItem.destroy');
});