<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function payInvoice(Request $request, $invoiceId)
    {
        $request->validate([
            'payment_method' => 'required|in:va,invoice_link',
        ]);

        $user = Auth::user();
        $invoice = $user->invoices()->findOrFail($invoiceId);

        switch ($request->payment_method) {
            case 'va':
                $vaNumber = $this->generateVirtualAccount($invoice);
                $invoice->payment_method = 'va';
                $invoice->va_number = $vaNumber;
                break;
            case 'invoice_link':
                $invoice->payment_method = 'invoice_link';
                $invoice->payment_link = $this->generateInvoiceLink($invoice);
                break;
        }

        $invoice->save();

        return response()->json([
            'message' => 'Invoice payment initiated successfully.',
            'invoice' => $invoice,
        ]);
    }

    protected function generateVirtualAccount(Invoice $invoice)
    {
        return 'YOUR_VIRTUAL_ACCOUNT_NUMBER';
    }

    protected function generateInvoiceLink(Invoice $invoice)
    {
        return 'YOUR_INVOICE_LINK';
    }
}
