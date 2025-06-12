<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Categorie;
use App\Models\Type;
use App\Models\ItemOrder;
use App\Models\Review;
use App\Models\SpecialOffer;
use App\Models\Favorite;
use App\Models\Sale;
class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'date_add_product',
        'disponible',
        'image_path',
        'category_id',
        'type_id',
        'status'
    ];



    public function category()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function specialOffers()
    {
        return $this->belongsToMany(
            SpecialOffer::class,
            'special_offer_product',
            'product_id',
            'special_offer_id'
        );
    }



    public function orderItems()
    {
        return $this->hasMany(ItemOrder::class);
    }


    public function sales()
    {
        return $this->hasMany(Sale::class);
    }




}




