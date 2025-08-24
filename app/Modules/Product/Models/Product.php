<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


// extends Illuminate\Database\Eloquent\Model:
// so the  Product throught extends Model extends the Laravel all the method that is able to communicate with the database
// and also the method that is able to create a new product
// and also the method that is able to update a product
// and also the method that is able to delete a product
// and also the method that is able to show a product
// and also the method that is able to index a product
// and also the method that is able to store a product
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'marketing_description',
        'price',
        'discount_price',
        'quantity',
        'min_stock_level',
        'image_path',
        'internal_image_path',
        'customer_images',
        'category',
        'features',
        'is_visible',
        'status',
        'published_at',
        'published_by',
    ];

    protected $casts = [
        'customer_images' => 'array',
        'features' => 'array',
        'published_at' => 'datetime',
        'is_visible' => 'boolean',
    ];
    public function orders()
    {
        return $this->belongsToMany(\App\Modules\Order\Models\Order::class)->withPivot('quantity', 'price');
    }

    public function publishedBy()
    {
        return $this->belongsTo(\App\Modules\User\Models\User::class, 'published_by');
    }

    // Scopes for different product stages
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('is_visible', true);
    }

    public function scopeForCustomers($query)
    {
        return $query->published();
    }

    // Helper methods
    public function canBePublished()
    {
        return $this->status === 'approved' && !empty($this->marketing_description);
    }

    public function isPublished()
    {
        return $this->status === 'published' && $this->is_visible;
    }
}
