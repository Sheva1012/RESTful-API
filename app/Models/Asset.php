<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'status',
        'condition',
        'description',
        'purchase_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    /**
     * Relationship with Asset Loans
     */
    public function loans(): HasMany
    {
        return $this->hasMany(AssetLoan::class);
    }

    /**
     * Active loan (if currently borrowed)
     */
    public function activeLoan()
    {
        return $this->hasOne(AssetLoan::class)->where('status', 'active')->latestOfMany();
    }

    /**
     * Query scope to filter available assets
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Query scope to search by keyword
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('asset_code', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%")
              ->orWhere('condition', 'like', "%{$term}%");
        });
    }
}

