<?php
namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $invoices = $user->invoices()->paginate(10);

        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->invoice_number = 'INV-' . time(); // Generate Invoice Number
        $invoice->due_date = $request->due_date;
        $invoice->total = 0; // Initialize total to 0

        $invoice->save();

        $total = 0;
        foreach ($request->items as $item) {
            $invoiceItem = new InvoiceItem();
            $invoiceItem->invoice_id = $invoice->id;
            $invoiceItem->item_name = $item['item_name'];
            $invoiceItem->qty = $item['qty'];
            $invoiceItem->price = $item['price'];
            $invoiceItem->save();

            $total += $item['qty'] * $item['price'];
        }

        $invoice->total = $total;
        $invoice->save();

        return response()->json($invoice, 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $invoice = $user->invoices()->with('items')->findOrFail($id);

        return response()->json($invoice);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $invoice = $user->invoices()->findOrFail($id);

        $invoice->due_date = $request->due_date;
        $invoice->total = 0; // Reset total

        $invoice->items()->delete(); // Delete old items

        $total = 0;
        foreach ($request->items as $item) {
            $invoiceItem = new InvoiceItem();
            $invoiceItem->invoice_id = $invoice->id;
            $invoiceItem->item_name = $item['item_name'];
            $invoiceItem->qty = $item['qty'];
            $invoiceItem->price = $item['price'];
            $invoiceItem->save();

            $total += $item['qty'] * $item['price'];
        }

        $invoice->total = $total;
        $invoice->save();

        return response()->json($invoice);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $invoice = $user->invoices()->findOrFail($id);

        $invoice->items()->delete(); // Delete related items
        $invoice->delete();

        return response()->json(null, 204);
    }
}
