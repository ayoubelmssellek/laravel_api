<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ItemOrder;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class Order extends Model
{
    protected $fillable = [
        'order_number',
        'name',
        'phonenumber',
        'delivery_type',
        'user_id',
        'total_order',
        'street',
        'housenumber',
        'status_order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function items()
    {
        return $this->hasMany(ItemOrder::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

 

}
