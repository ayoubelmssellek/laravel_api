<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:0|max:5',
            'status' => 'nullable',
            'comment' => 'nullable|string|max:255',
        ]);


        $review = new Review();
        $review->user_id = auth()->id();
        $review->product_id = $validated['product_id'];
        $review->rating = $validated['rating'];
        $review->comment = $validated['comment'];
        $review->save();

            
        Notification::create([
            'type' => 'review',
            'user_id' => auth('sanctum')->check() ? auth('sanctum')->id() : null,
            'message' => auth('sanctum')->check() ? auth('sanctum')->user()->name : 'زائر',
        ]);

        return response()->json(['message' => 'Review added successfully'], 201);
    }

    public function index($productId)
    {
        // $reviews = Review::where('product_id', $productId)->with('user')->get();
        $reviews = DB::table('reviews')
        ->join('users', 'reviews.user_id', '=', 'users.id')
        ->join('products', 'reviews.product_id', '=', 'products.id')
        ->select('reviews.*', 'users.name as user_name', 'products.name as product_name', 'products.image_path as product_image')
        ->where('reviews.product_id', $productId)
        ->get();
    

        return response()->json($reviews);
    }
    
    public function GetAllreviews()
    {
        $reviews = DB::table('reviews')
        ->join('users', 'reviews.user_id', '=', 'users.id')
        ->join('products', 'reviews.product_id', '=', 'products.id')
        ->select('reviews.*', 'users.name as user_name', 'products.name as product_name', 'products.image_path as product_image')
        ->get();
    

        return response()->json($reviews);
    }
    public function ChangeReviewStatus(Request $request ,$id){
       
        $request->validate([
            'status' => 'required|string',
        ]);
    
        $review = Review::find($id);
        $review->status = $request->status;
        $review->save();
    
        return response()->json(['message' => 'Review status updated successfully'], 200);
    }

    public function Getreviewsbyid($id){
        $reviews = DB::table('reviews')
        ->join('users', 'reviews.user_id', '=', 'users.id')
        ->join('products', 'reviews.product_id', '=', 'products.id')
        ->select('reviews.*', 'users.name as user_name', 'products.name as product_name', 'products.image_path as product_image')
        ->where('reviews.product_id', $id)
        ->get();
    

        return response()->json($reviews);
    }


}