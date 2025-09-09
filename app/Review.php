<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'reviewer_name',
        'rating',
        'title',
        'content',
        'is_approved'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(\App\Modules\Product\Models\Product::class);
    }
}
