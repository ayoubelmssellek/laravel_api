<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categorie;
use Illuminate\Support\Facades\Storage;

class CategorieController extends Controller
{
    public function index()
    {
        // get all categories with products
        $categories = Categorie::withCount('products')->get(); 
        return response()->json($categories);
    }

    public function show($id)
    {
        // Logic to retrieve and return a specific category by ID
    }

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('CategoriesImages', 'public');
    }

    $category = Categorie::create([
        'name' => $request->name,
        'image' => $imagePath, // نحتافظ غير بالمسار النسبي
        'status' => 'available',
    ]);

    return response()->json([
        'message' => 'Category created successfully',
        'category' => $category,
    ], 201);
}


public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'status' => 'required|string|max:255',
    ]);

    $category = Categorie::findOrFail($id);

    // حفظ المسار القديم باش نحذف الصورة من بعد
    $oldImage = $category->image;

    $newImagePath = $oldImage;

    if ($request->hasFile('image')) {
        // حذف الصورة القديمة إلا كانت كاينة
        if ($oldImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        // تخزين الصورة الجديدة
        $newImagePath = $request->file('image')->store('CategoriesImages', 'public');
    }

    $category->update([
        'name' => $request->name,
        'image' => $newImagePath,
        'status' => $request->status,
    ]);

    return response()->json([
        'message' => 'Category updated successfully',
        'category' => $category,
    ]);
}


    public function destroy($id)
    {
        $category = Categorie::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }


   
}
