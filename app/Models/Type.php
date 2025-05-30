<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\SpecialOffer;
use App\Models\Sale;
class Type extends Model
{
    protected $fillable = [
        'name',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    public function specialOffers()
    {
        return $this->belongsToMany(
            SpecialOffer::class,
            'special_offer_type',
            'type_id',
            'special_offer_id'
        );
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }



    protected static function booted(){
        
    static::updated(function ($type) {
        if ($type->isDirty('status')) {
            Product::where('type_id', $type->id)->update([
                'status' => $type->status,
            ]);
        }
    });



}
}
