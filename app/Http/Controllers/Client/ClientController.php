<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
  public function GetClientsData(Request $request)
{
    $ClinetData = DB::table('users')
        ->where('role_id', 1)
        ->leftJoinSub(
            DB::table('orders')
                ->select('user_id', DB::raw('MAX(CONVERT_TZ(created_at, "+00:00", "+01:00")) as last_order_date'))
                ->groupBy('user_id'),
            'latest_orders',
            'users.id',
            '=',
            'latest_orders.user_id'
        )
        ->leftJoinSub(
            DB::table('orders')
                ->select('user_id', DB::raw('COUNT(*) as total_orders'))
                ->groupBy('user_id'),
            'orders_count',
            'users.id',
            '=',
            'orders_count.user_id'
        )
        ->select(
            'users.name', 
            'users.phone', 
            'latest_orders.last_order_date',
            DB::raw('COALESCE(orders_count.total_orders, 0) as total_orders')
        )
        ->orderByDesc('latest_orders.last_order_date')
        ->get();

    return response()->json($ClinetData);
}

public function GetSubsciptionStatic(Request $request)
{
    $subscriptionData = DB::table('users')
        ->select(
            DB::raw('COUNT(*) as total_users'),
            DB::raw('SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as daily_subscriptions'),
            DB::raw('SUM(CASE WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) THEN 1 ELSE 0 END) as weekly_subscriptions'),
            DB::raw('SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as monthly_subscriptions')
        )
        ->where('role_id', 1)
        ->first();

    return response()->json($subscriptionData);
}

}
