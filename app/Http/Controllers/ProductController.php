<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class ProductController extends Controller
{
  public function index(Request $request)
  {
      $user = Auth::user();
      $query = Product::with(['category', 'branches'])
          ->when(!$user->isSuperAdmin(), function($q) use ($user) {
              $q->whereHas('branches', fn($bq) => $bq->where('branches.id', $user->branch_id));
          })
          ->when($request->category, fn($q) => $q->where('category_id', $request->category))
          ->when($request->status, fn($q) => $q->where('status', $request->status))
          ->when($request->size, fn($q) => $q->where('size', $request->size))
          ->when($request->branch_id, fn($q) => $q->whereHas('branches', fn($bq) => $bq->where('branches.id', $request->branch_id)))
          ->when(
              $request->search,
              fn($q) => $q->where(function ($q2) use ($request) {
                  $q2->where('name', 'like', "%{$request->search}%")
                      ->orWhere('code', 'like', "%{$request->search}%")
                      ->orWhere('color', 'like', "%{$request->search}%");
              }),
          )
          ->latest();

      $products   = $query->paginate(16)->withQueryString();
      $categories = Category::where('is_active', true)->get();
      $branches = Branch::where('is_active', true)->get();
      $stats = [
         'total' => (clone $query)->count(),
         'available' => (clone $query)->where('status', 'available')->count(),
         'maintenance' => (clone $query)->where('status', 'maintenance')->count(),
         'rented_units' => (clone $query)->where('status', 'rented')->sum(\DB::raw('stock_total - stock_available')),
     ];

     return view('products.index', compact('products', 'categories', 'stats'));
 }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        return view('products.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
    $data = $request->validate([
        'category_id'   => 'required|exists:categories,id',
        'name'          => 'required|string|max:150',
        'description'   => 'nullable|string',
        'size'          => 'nullable|string|max:20',
        'color'         => 'nullable|string|max:50',
        'brand'         => 'nullable|string|max:100',
        'rental_price'  => 'required|numeric|min:0',
        'deposit_price' => 'nullable|numeric|min:0',
        'stock_total'   => 'required|integer|min:1',
        'condition'     => 'required|in:excellent,good,fair,poor',
        'status'        => 'required|in:available,maintenance,inactive',
        'notes'         => 'nullable|string',
        'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'branch_ids'    => 'nullable|array',
        'branch_ids.*'  => 'exists:branches,id',
    ]);

    $user = Auth::user();

    // Tentukan branch_id legacy untuk kompatibilitas (prefix kode, dll)
    if ($user->isSuperAdmin()) {
        $branchIds = $request->filled('branch_ids') ? $request->input('branch_ids') : [$user->branch_id ?? 1];
        $legacyBranchId = $branchIds[0];
    } else {
        $branchIds = [$user->branch_id];
        $legacyBranchId = $user->branch_id;
    }

    $data['branch_id']       = $legacyBranchId;
    $data['stock_available'] = $data['stock_total'];
    $data['code']            = $this->generateCode($legacyBranchId);

    if ($request->hasFile('image')) {
        $data['photo'] = $request->file('image')->store('products', 'public');
    }

    unset($data['image'], $data['branch_ids']);

    $product = Product::create($data);
    $product->branches()->sync($branchIds);
    $this->generateQrCode($product);

    return redirect()->route('products.show', $product)
        ->with('success', 'Produk berhasil ditambahkan!');
}

    public function update(Request $request, Product $product)
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'size' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'brand' => 'nullable|string|max:100',
            'rental_price' => 'required|numeric|min:0',
            'deposit_price' => 'nullable|numeric|min:0',
            'stock_total' => 'required|integer|min:0',
            'stock_available' => 'required|integer|min:0',
            'condition' => 'required|in:excellent,good,fair,poor',
            'status' => 'sometimes|in:available,maintenance,inactive',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        if ($request->hasFile('image')) {
            if ($product->photo && Storage::disk('public')->exists($product->photo)) {
                Storage::disk('public')->delete($product->photo);
            }
            $data['photo'] = $request->file('image')->store('products', 'public');
        } else {
            unset($data['photo']);
        }

        unset($data['image']);

        $product->update($data);

        if ($request->filled('branch_ids')) {
            $product->branches()->sync($request->input('branch_ids'));
        }

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil diperbarui!');
    }

    public function show(Product $product)
    {
        $user = Auth::user();
        if (!$user || (!$user->isSuperAdmin() && !$user->isAdminToko() && !$user->isSales())) {
            abort(403, 'Unauthorized action.');
        }

        $product->load(['category', 'branch', 'rentalItems' => fn($q) => $q->with('rental.customer')->latest()->limit(10)]);
        return view('products.show', compact('product'));
    }

    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock_available' => ['required', 'integer', 'min:0', 'lte:' . $product->stock_total],
            'stock_note' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStock = $product->stock_available;
        $product->update([
            'stock_available' => $validated['stock_available'],
        ]);

        $newStock = $validated['stock_available'];
        $note = $validated['stock_note'] ?? null;

        ActivityLog::create([
            'user_id' => Auth::id(),
            'branch_id' => Auth::user()->branch_id,
            'action' => 'update_product_stock',
            'model_type' => Product::class,
            'model_id' => $product->id,
            'description' => Auth::user()->name . ' mengubah stok produk ' . $product->name . ' dari ' . $oldStock . ' menjadi ' . $newStock . ($note ? ' (' . $note . ')' : ''),
            'old_values' => ['stock_available' => $oldStock],
            'new_values' => ['stock_available' => $newStock, 'stock_note' => $note],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Stok berhasil diperbarui dari ' . $oldStock . ' menjadi ' . $newStock . '!');
    }

    public function edit(Product $product)
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $categories = Category::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        $selectedBranches = $product->branches->pluck('id')->toArray();
        return view('products.edit', compact('product', 'categories', 'branches', 'selectedBranches'));
    }

    public function regenerateQr(Product $product)
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $this->generateQrCode($product);
        return back()->with('success', 'QR Code berhasil digenerate ulang!');
    }

    private function generateCode(int $branchId): string
    {
        $branchId = $branchId ?? 1; // fallback jika null
        $prefix = 'PRD' . str_pad($branchId, 2, '0', STR_PAD_LEFT);
        $last = Product::where('code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();
        $seq = $last ? (int) substr($last->code, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function destroy(Product $product)
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        // Cek apakah produk masih dipakai pada rental aktif/belum kembali.
        $hasActiveRental = $product->rentalItems()
            ->whereHas('rental', function ($q) {
                $q->whereIn('rental_status', ['waiting', 'active', 'overdue']);
            })
            ->exists();

        if ($hasActiveRental) {
            return back()->with('error', 'Produk tidak dapat dihapus karena masih digunakan pada transaksi sewa yang belum kembali.');
        }

        $product->delete(); // soft delete (Product sudah pakai SoftDeletes)

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function generateQrCode(Product $product): void
    {
        $qrData = route('products.show', $product);
        $path = 'qrcodes/products/' . $product->code . '.svg';
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $svg = QrCode::format('svg')->size(200)->generate($qrData);
        file_put_contents($fullPath, $svg);
        $product->update(['qr_code' => $path]);
    }
}
