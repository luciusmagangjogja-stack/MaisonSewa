<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;

class InvoicePublicController extends Controller
{
    public function show(Rental $rental)
    {
        $rental->loadMissing(['customer', 'branch', 'items.product.category', 'createdBy']);
        return view('invoices.public', compact('rental'));
    }
}
