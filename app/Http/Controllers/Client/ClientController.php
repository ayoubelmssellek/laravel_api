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
            DB::table('sales')
                ->select('user_id', DB::raw('MAX(CONVERT_TZ(created_at, "+00:00", "+01:00")) as last_sale_date'))
                ->groupBy('user_id'),
            'latest_sales',
            'users.id',
            '=',
            'latest_sales.user_id'
        )
        ->leftJoinSub(
            DB::table('sales')
                ->select('user_id', DB::raw('COUNT(*) as total_sales'))
                ->groupBy('user_id'),
            'sales_count',
            'users.id',
            '=',
            'sales_count.user_id'
        )
        ->select(
            'users.name', 
            'users.phone', 
            'latest_sales.last_sale_date',
            DB::raw('COALESCE(sales_count.total_sales, 0) as total_sales')
        )
        ->orderByDesc('latest_sales.last_sale_date')
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
