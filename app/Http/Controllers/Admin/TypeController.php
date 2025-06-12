<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Type;

class TypeController extends Controller
{
    public function index()
    {
        $types = Type::all();
        return response()->json($types);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);
        $type = Type::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);
        // Save the type to the database
        return response()->json([
            'message' => 'Type created successfully',
            'type' => $type,
        ], 201);


    }

    public function show($id)
    {
    }

    public function update(Request $request, $id)
    {
        // Code to update a specific type
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);
        // Logic to update the type
        $type = Type::findOrFail($id);
        $type->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);
        return response()->json([
            'message' => 'Type updated successfully',
            'type' => $type,
        ]);
    }
    public function updateTypeStatus(Request $request, $id)
    {
        // Code to update the status of a specific type
        $type = Type::findOrFail($id);
        $type->update([
            'status' => $request->status,
        ]);
        return response()->json([
            'message' => 'Type status updated successfully',
            'type' => $type,
        ]);
    }

    public function destroy($id)
    {
        // Code to delete a specific type
        $type = Type::findOrFail($id);
        $type->delete();
    }
}
