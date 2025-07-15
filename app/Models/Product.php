<?php

namespace App\Models;

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
        'description',
        'price',
        'quantity',
        'image_path',
        'is_visible',
    ];
    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price');
    }
}
