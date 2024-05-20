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

        $response = response()->json($invoices);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');

        return $response;
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
        // Handle preflight requests
        if ($request->isMethod('options')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, DELETE, PUT')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
        }

        // Define validation rules
        $rules = [
            'client.id' => 'required|integer|exists:clients,id',
            'client.name' => 'string|nullable',
            'selectedItems.*.item_id' => 'required|integer|exists:inventory_items,id',
            'selectedItems.*.item_name' => 'required|string',
            'selectedItems.*.quantity' => 'required|integer|min:1',
            'selectedItems.*.price' => 'required|numeric|min:0',
            'selectedItems.*.taxable' => 'boolean',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, DELETE, PUT')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
        }

        DB::beginTransaction();
        try {
            // Calculate the total invoice amount
            $invoice_total = 0;
            foreach ($request->selectedItems as $item) {
                $invoice_total += $item['price'] * $item['quantity'];
            }

            // Create a new invoice
            $invoice = new Invoice();
            $client = Client::findOrFail($request->client['id']);
            $invoice->client_id = $client->id;
            $invoice->user_id = Auth::id();
            $invoice->total = $invoice_total;
            $invoice->invoice_number = $request->invoice_number;

            $invoice->save();

            // Save invoice items
            foreach ($request->selectedItems as $item) {
                $inventory_item = InventoryItem::findOrFail($item['item_id']);

                $invoice_item = new Item();
                $invoice_item->inventory_item_id = $inventory_item->id;
                $invoice_item->invoice_id = $invoice->id;
                $invoice_item->quantity = $item['quantity'];
                $invoice_item->price = $item['price'];

                if (array_key_exists('taxable', $item) && $item['taxable']) {
                    $invoice_item->total_tax = 0.16 * $item['price'] * $item['quantity'];
                } else {
                    $invoice_item->total_tax = 0;
                }

                $invoice_item->save();
            }

            DB::commit();

            return response()->json($invoice, 201)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, DELETE, PUT')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while processing your request.'], 500)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, DELETE, PUT')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
        }
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
