<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpecialOffer;
use Illuminate\Support\Facades\DB;


class SpecialOfferController extends Controller
{
        public function index()
        {
            SpecialOffer::where('endDate', '<=', now())->delete();

            $offers = SpecialOffer::with(['products', 'categories', 'types'])
                ->where('endDate', '>', now())
                ->get();

            $offers = $offers->map(function ($offer) {
                $data = [
                    'id' => $offer->id,
                    'title' => $offer->title,
                    'discount' => $offer->discount,
                    'startDate' => $offer->startDate,
                    'endDate' => $offer->endDate,
                    'description' => $offer->description,
                    'image' => $offer->image_path,
                    'type' => null,
                    'target' => null,
                ];

                // نحدد نوع العرض بناءً على العلاقة المرتبطة
                if ($offer->products->isNotEmpty()) {
                    $product = $offer->products->first(); // نأخذ أول منتج
                    $data['type'] = 'product';
                    $data['target'] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'type' => $product->type->name ?? null,
                        'category' => $product->category->name ?? null,
                        'price' => $product->price,
                        'image' => $product->image_path,
                    ];
                } elseif ($offer->categories->isNotEmpty()) {
                    $category = $offer->categories->first();
                    $data['type'] = 'category';
                    $data['target'] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'image' => $category->image,
                    ];
                } elseif ($offer->types->isNotEmpty()) {
                    $type = $offer->types->first();
                    $data['type'] = 'type';
                    $data['target'] = [
                        'id' => $type->id,
                        'name' => $type->name,
                    ];
                }

                return $data;
            });

            return response()->json($offers);
        }



        public function store(Request $request)
        {
            $request->validate([
                'discount' => 'required|numeric|min:1|max:100',
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate',
            ]);

            $count = 0;
            if ($request->has('product_id')) $count++;
            if ($request->has('category_id')) $count++;
            if ($request->has('type_id')) $count++;

            if ($count === 0) {
                return response()->json(['error' => 'you must chose one product or type or category at less'], 422);
            }

            if ($count > 1) {
                return response()->json(['error' => 'the offre has be rolated in product or type or category'], 422);
            }

            $offer = SpecialOffer::create($request->only(['discount', 'startDate', 'endDate']));

            if ($request->has('product_id')) {
                $offer->products()->attach($request->product_id);
            } elseif ($request->has('category_id')) {
                $offer->categories()->attach($request->category_id);
            } elseif ($request->has('type_id')) {
                $offer->types()->attach($request->type_id);
            }

            return response()->json(['message' => 'offre created successfully', 'offer' => $offer], 201);
        }

        public function destroy($id)
        {
            DB::transaction(function () use ($id) {
                $specialOffre = SpecialOffer::findOrFail($id);

                $specialOffre->products()->detach();
                $specialOffre->categories()->detach();
                $specialOffre->types()->detach();

                $specialOffre->delete();
            });

            return response()->json(['message' => 'offre deleted successfully'], 200);
        }



}
