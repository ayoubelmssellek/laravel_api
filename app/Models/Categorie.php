<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use App\Models\Product;   
use App\Models\SpecialOffer;
use App\Models\Sale;
class Categorie extends Model
{
    protected $fillable = [
        'name',
        'image',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class,'category_id');
    }

    public function specialOffers()
    {
        return $this->belongsToMany(
            SpecialOffer::class,
            'special_offer_category',  // اسم جدول الpivot
            'category_id',             // المفتاح الخارجي لهذا الموديل في جدول الpivot
            'special_offer_id'         // المفتاح الخارجي للموديل المرتبط
        );
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }



    
}
