<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::all();

        return response()->json($invoices);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return Inertia::render(
        //     'Invoice/Create'
        // );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'client.id' => 'required|integer',
            'client.name' => 'string',
            'selectedItems.*.item_name' => 'required|string',
            'selectedItems.*.quantity' => 'required',
            'selectedItems.*.price' => 'required|numeric',
            'selectedItems.*.taxable' => 'boolean',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $invoice_total = 0;
        foreach ($request->selectedItems as $item) {
            $invoice_total += $item['price'] * $item['quantity'];
        }
        $invoice = new Invoice();
        $client = Client::find($request->client['id']);
        $invoice->client_id = $client->id;
        $invoice->user_id = Auth::id();
        $invoice->total = $invoice_total;
        $invoice->invoice_number = $request->invoice_number;

        $invoice->save();

        // save invoice items
        foreach ($request->selectedItems as $item) {
            $inventory_item = InventoryItem::find($item['item_id']);
            $inventory_item_id = $inventory_item->id;
            $invoice_id = $invoice->id;
            $quantity = $item['quantity'];
            $price = $item['price'];


            $invoice_item = new Item();
            // check if there is a property named taxable in the item object          
            if (array_key_exists('taxable', $item)) {

                $invoice_item->total_tax = 0.16 * $price * $quantity;
            } else {
                $invoice_item->total_tax = 0;
            }

            $invoice_item->inventory_item_id = $inventory_item_id;
            $invoice_item->invoice_id = $invoice_id;
            $invoice_item->quantity = $quantity;
            $invoice_item->price = $price;

            $invoice_item->save();
        }


        // client details


        return response()->json($invoice, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        // $invoiceItems = $invoice->items;
        // get invoice items belonging to the invoice
        // $invoiceItems = $invoice->items;
        $items = Item::where('invoice_id', $invoice->id)->get();
        $client = Client::find($invoice->client_id);

        // return blade template document.blade.php in the views folder
        // return view('document', [
        //     'invoiceItems' => $items,
        //     'client' => $client
        // ]);

        // return response()->json($items);
        return response()->file(public_path('template.html'));
    }

    public function apiShow(Invoice $invoice)
    {
        $items = Item::where('invoice_id', $invoice->id)->get();

        foreach ($items as $item) {
            // Retrieve the inventory item related to this item
            $inventory_item = InventoryItem::find($item->inventory_item_id);

            // Assign the name of the inventory item to the item
            $item->name = $inventory_item->name;
        }

        // Retrieve the client associated with the invoice
        $client = Client::find($invoice->client_id);

        return response()->json([
            'type' => 'INVOICE',
            'data' => $items,
            'client' => $client,
            'invoice_number' => $invoice->invoice_number,
            'invoice_date' => $invoice->created_at
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
