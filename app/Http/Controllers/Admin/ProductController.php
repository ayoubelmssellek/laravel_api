<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SpecialOffer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProductController extends Controller 
{


public function index()
{

    SpecialOffer::where('endDate', '<=', now())->delete();

    $products = Product::with([
        'category.specialOffers' => function($query) {
            $query->where('endDate', '>', now());
        },
        'type.specialOffers' => function($query) {
            $query->where('endDate', '>', now());
        },
        'specialOffers' => function($query) {
            $query->where('endDate', '>', now());
        },
        'reviews',
        'favorites',
    ])->get();

    foreach ($products as $product) {
        $allOffers = collect();

        if ($product->specialOffers) {
            $allOffers = $allOffers->merge($product->specialOffers);
        }
        if ($product->category && $product->category->specialOffers) {
            $allOffers = $allOffers->merge($product->category->specialOffers);
        }
        if ($product->type && $product->type->specialOffers) {
            $allOffers = $allOffers->merge($product->type->specialOffers);
        }

        $bestOffer = $allOffers->sortByDesc('discount')->first();

        $product->discount = $bestOffer ? $bestOffer->discount : 0;
        $product->reviews_count = $product->reviews->count();
        $product->favorites_count = $product->favorites->count();
    }

    return response()->json($products);
}






    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'status' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'disponible' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'type_id' => 'required|exists:types,id',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ProductsImages', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'date_add_product' => now(),
            'disponible' => $request->disponible,
            'image_path' => $imagePath,
            'category_id' => $request->category_id,
            'type_id' => $request->type_id,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'status' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'disponible' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'type_id' => 'required|exists:types,id',
        ]);

        $oldImage = $product->image_path;

        if ($request->hasFile('image')) {
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $imagePath = $request->file('image')->store('ProductsImages', 'public');
            $product->image_path = $imagePath;
        }

        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->status = $request->status;
        $product->disponible = $request->disponible;
        $product->category_id = $request->category_id;
        $product->type_id = $request->type_id;

        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    public function UpdateProductStatus(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'status' => 'required|string|max:255',
        ]);


        $product->status = $request->status;
        
        $product->save();

        return response()->json([
            'message' => 'Product status updated successfully',
            'product' => $product,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
