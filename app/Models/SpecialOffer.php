<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Category;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpecialOffer extends Model
{
    use HasFactory;
    protected $fillable = ['discount', 'startDate', 'endDate'];



    public function products()
    {
        return $this->belongsToMany(Product::class, 'special_offer_product', 'special_offer_id', 'product_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Categorie::class, 'special_offer_category', 'special_offer_id', 'category_id');
    }

    public function types()
    {
        return $this->belongsToMany(Type::class, 'special_offer_type', 'special_offer_id', 'type_id');
    }


}
