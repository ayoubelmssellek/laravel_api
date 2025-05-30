<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Review;
use App\Models\Order;


class AdminActionsController extends Controller
{
    public function GetAdminInfo(){
        $admin = User::where('role_id', 2)
        ->select('id','name', 'phone')
        ->first();
        return response()->json($admin);
    }

    public function GetAllManagers()
    {
        $managers = User::where('role_id', 3)
            ->select('id', 'name', 'phone')
            ->get();

        return response()->json($managers);
    }
    public function DeleteManager($id)
    {
        $manager = User::findOrFail($id);
        $manager->delete();
        return response()->json(['message' => 'Manager deleted successfully'], 200);
    }
   
    public function UpdateAdminAccount(Request $request , $id)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:255|unique:users,phone,'.$id,
        'password' => 'nullable|string|min:4|confirmed',
        
    ]);

    $user->name = $request->name;
    $user->phone = $request->phone;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

}

    public function Add_Manager(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users,phone',
            'password' => 'required|string|min:4|confirmed',
        ]);

        $manager = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role_id' => 3
        ]);

        return response()->json($manager,201);
    }




    public function badges_statistics(){
        $sales = [
            'count' => Sale::count(),
            'updated_at' => Sale::latest('updated_at')->value('updated_at')?->format('Y-m-d H:i:s'),
        ];
        $products = [
            'count' => Product::count(),
            'updated_at' => Product::latest('updated_at')->value('updated_at')?->format('Y-m-d H:i:s'),
        ];
        $Reviews = [
            'count' => Review::count(),
            'updated_at' => Review::latest('updated_at')->value('updated_at')?->format('Y-m-d H:i:s'),
        ];
        $orders = [
            'count' => Order::count(),
            'updated_at' => Order::latest('updated_at')->value('updated_at')?->format('Y-m-d H:i:s'),
        ];
        $users = [
            'count' => User::where('role_id', 1)->count(),
            'updated_at' => User::where('role_id', 1)->latest('updated_at')->value('updated_at')?->format('Y-m-d H:i:s'),
        ];
        return response()->json([
            'sales' => $sales,
            'products' => $products,
            'reviews' => $Reviews,
            'orders' => $orders,
            'users' => $users
        ]);
    }
}