<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_code',
        'asset_id',
        'borrower_id',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'purpose',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date:Y-m-d',
        'expected_return_date' => 'date:Y-m-d',
        'actual_return_date' => 'date:Y-m-d',
    ];

    /**
     * Relationship with Asset
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relationship with Borrower
     */
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    /**
     * Query scope for active loans
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Query scope for returned loans
     */
    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    /**
     * Query scope for overdue loans
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
                     ->where('expected_return_date', '<', now()->toDateString());
    }
}

