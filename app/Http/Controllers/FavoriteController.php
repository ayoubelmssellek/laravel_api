<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FavoriteController extends Controller
{
 
   public function toggle($productId)
   {
       $user = Auth::user(); 
        
       $favorite = Favorite::where('user_id', $user->id)
                           ->where('product_id', $productId)
                           ->first();

       if ($favorite) {
           $favorite->delete();
           return response()->json(['status' => 'removed']);
       } else {
           Favorite::create([
               'user_id' => $user->id,
               'product_id' => $productId,
           ]);
           return response()->json(['status' => 'added'],201);
       }
   }


   public function index()
   {
       
  $now = Carbon::now();

    $favorites = DB::table('favorites')
        ->join('products', 'favorites.product_id', '=', 'products.id')
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->join('types', 'types.id', '=', 'products.type_id')
        ->where('favorites.user_id', Auth::id())
        ->select(
            'products.*',
            'categories.name as category_name',
            'types.name as type_name',
            DB::raw('(SELECT COUNT(*) FROM reviews WHERE reviews.product_id = products.id) as reviews_count'),
            DB::raw('(SELECT COUNT(*) FROM favorites WHERE favorites.product_id = products.id) as favorites_count')
        )
        ->get();

    foreach ($favorites as $product) {
        $productOffers = DB::table('special_offers')
            ->join('special_offer_product', 'special_offers.id', '=', 'special_offer_product.special_offer_id')
            ->where('special_offer_product.product_id', $product->id)
            ->where('startDate', '<=', $now)
            ->where('endDate', '>=', $now)
            ->pluck('discount');

        $categoryOffers = DB::table('special_offers')
            ->join('special_offer_category', 'special_offers.id', '=', 'special_offer_category.special_offer_id')
            ->where('special_offer_category.category_id', $product->category_id)
            ->where('startDate', '<=', $now)
            ->where('endDate', '>=', $now)
            ->pluck('discount');

        $typeOffers = DB::table('special_offers')
            ->join('special_offer_type', 'special_offers.id', '=', 'special_offer_type.special_offer_id')
            ->where('special_offer_type.type_id', $product->type_id)
            ->where('startDate', '<=', $now)
            ->where('endDate', '>=', $now)
            ->pluck('discount');

        $allDiscounts = $productOffers
            ->merge($categoryOffers)
            ->merge($typeOffers);

        $product->discount = $allDiscounts->max() ?? 0;
    }

    return response()->json($favorites);

   }

}