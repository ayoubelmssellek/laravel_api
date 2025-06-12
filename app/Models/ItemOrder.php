<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Product;
use App\Models\Order;
class ItemOrder extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'total_price',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
};