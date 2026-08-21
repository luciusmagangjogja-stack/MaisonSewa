<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id', 'category_id', 'code', 'name', 'description',
        'size', 'color', 'brand', 'rental_price', 'deposit_price',
        'stock_total', 'stock_available', 'photo', 'qr_code',
        'condition', 'status', 'notes',
    ];

    protected $casts = [
        'rental_price' => 'decimal:2',
        'deposit_price' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'product_branch');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function rentalItems(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            // Jika URL external (http:// atau https://)
            if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
                return $this->photo;
            }
            return asset('storage/' . $this->photo);
        }
        // Default no product image
        return 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=400&h=500&fit=crop';
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        if ($this->qr_code) {
            return asset('storage/' . $this->qr_code);
        }
        return null;
    }

    public function isAvailable(): bool
    {
        return $this->stock_available > 0 && $this->status === 'available';
    }

    public function getRentalPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->rental_price, 0, ',', '.');
    }
}
