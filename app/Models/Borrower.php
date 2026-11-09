<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrower extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_code',
        'name',
        'department',
        'phone',
        'email',
    ];

    /**
     * Relationship with Asset Loans
     */
    public function loans(): HasMany
    {
        return $this->hasMany(AssetLoan::class);
    }

    /**
     * Active loans currently held by this borrower
     */
    public function activeLoans(): HasMany
    {
        return $this->hasMany(AssetLoan::class)->where('status', 'active');
    }

    /**
     * Query scope to search borrower
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('borrower_code', 'like', "%{$term}%")
              ->orWhere('department', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
    }
}

