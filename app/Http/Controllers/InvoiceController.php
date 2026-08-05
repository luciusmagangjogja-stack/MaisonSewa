<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $service) {}

    public function index(Request $request)
    {
        $invoices = $this->service->list($request);
        return view('invoices.index', $invoices);
    }

    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        $invoice = $this->service->create($request->all());
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice berhasil dibuat!');
    }

    public function show(Rental $invoice)
    {
        $invoice->load(['customer', 'branch', 'items.product.category', 'createdBy', 'payments', 'activityLogs']);
        $payload = $this->service->buildPayload($invoice);
        return view('invoices.show', compact('invoice', 'payload'));
    }

    public function edit(Rental $invoice)
    {
        $invoice->load(['customer', 'branch', 'items.product.category', 'createdBy', 'payments']);
        $payload = $this->service->buildPayload($invoice);
        return view('invoices.edit', compact('invoice', 'payload'));
    }

    public function update(Request $request, Rental $invoice)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);
        $this->service->update($invoice, $validated);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice berhasil diperbarui!');
    }

    /**
     * Invoice is a legal document and CANNOT be deleted directly.
     * Use rentals.destroy (RentalController) for deletion.
     */
    public function destroy(Rental $invoice)
    {
        abort(403, 'Invoice tidak dapat dihapus. Gunakan rentals.destroy route.');
    }

    /**
     * Cancel invoice (legal cancel, not delete).
     * Only Super Admin can cancel.
     * Restores stock. Invoice remains in database.
     */
    public function cancel(Rental $invoice)
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403);
        }

        $this->service->cancel($invoice);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice berhasil dibatalkan!');
    }

    public function print(Rental $invoice)
    {
        $invoice->load(['customer', 'branch', 'items.product.category', 'payments', 'createdBy']);
        $payload = $this->service->buildPayload($invoice);
        return view('invoices.print', compact('invoice', 'payload'));
    }

    public function pdf(Rental $invoice)
    {
        return $this->service->downloadPdf($invoice);
    }

    public function qr(Rental $invoice)
    {
        return $this->service->qr($invoice);
    }

    public function whatsapp(Rental $invoice)
    {
        return $this->service->whatsapp($invoice);
    }

    public function receipt(Rental $invoice)
    {
        $invoice->load(['customer', 'branch', 'items.product.category', 'payments', 'createdBy']);
        $payload = $this->service->buildReceiptPayload($invoice);
        return view('invoices.receipt', compact('invoice', 'payload'));
    }
}
