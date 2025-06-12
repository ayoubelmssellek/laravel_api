<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Product;
use App\Models\Categorie;
use Illuminate\Support\Str;
use App\Models\Type;


class Sale extends Model
{
    protected $fillable = [
        'sale_number',
        'product_id',
        'category_id',
        'type_id',
        'quantity',
        'user_id',
        'total_price',
        'sold_at',
    ];



    // علاقة البيع مع الطلب (Order)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // علاقة البيع مع المنتج (Product)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // علاقة البيع مع الفئة (Category)
    public function category()
    {
        return $this->belongsTo(Categorie::class);
    }

    // علاقة البيع مع النوع (Type)
    public function type()
    {
        return $this->belongsTo(Type::class);
    }


protected static function booted()
{
    static::creating(function ($sale) {
        do {
            $randomCode = 'SAL-' . now()->year . '-' . Str::upper(Str::random(6));
        } while (Sale::where('sale_number', $randomCode)->exists());

        $sale->sale_number = $randomCode;
    });
}

}
