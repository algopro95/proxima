<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceItemController extends Controller
{
    public function index($invoiceId)
    {
        $user = Auth::user();
        $invoice = $user->invoices()->findOrFail($invoiceId);
        $items = $invoice->items;

        return response()->json($items);
    }

    public function store(Request $request, $invoiceId)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $invoice = $user->invoices()->findOrFail($invoiceId);

        $item = new InvoiceItem();
        $item->invoice_id = $invoice->id;
        $item->item_name = $request->item_name;
        $item->qty = $request->qty;
        $item->price = $request->price;
        $item->save();

        // Update the invoice total
        $invoice->total += $item->qty * $item->price;
        $invoice->save();

        return response()->json($item, 201);
    }

    public function show($invoiceId, $itemId)
    {
        $user = Auth::user();
        $invoice = $user->invoices()->findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);

        return response()->json($item);
    }

    public function update(Request $request, $invoiceId, $itemId)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $invoice = $user->invoices()->findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);

        // Update the total before updating the item
        $invoice->total -= $item->qty * $item->price;

        $item->item_name = $request->item_name;
        $item->qty = $request->qty;
        $item->price = $request->price;
        $item->save();

        // Update the invoice total
        $invoice->total += $item->qty * $item->price;
        $invoice->save();

        return response()->json($item);
    }

    public function destroy($invoiceId, $itemId)
    {
        $user = Auth::user();
        $invoice = $user->invoices()->findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);

        // Update the total before deleting the item
        $invoice->total -= $item->qty * $item->price;
        $invoice->save();

        $item->delete();

        return response()->json(null, 204);
    }
}
