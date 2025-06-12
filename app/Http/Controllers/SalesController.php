<?php


namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function index()
    {
        $sales = DB::table('sales')
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->join('categories', 'sales.category_id', '=', 'categories.id')
            ->join('types', 'sales.type_id', '=', 'types.id')
            ->select(
                'sales.id',
                'sales.sale_number',
                'sales.quantity',
                'sales.total_price',
                'sales.sold_at',

                // منجدبين من العلاقات
                'products.name as product_name',
                'products.price as product_price',
                'categories.name as category_name',
                'types.name as type_name'
            )
            ->get();
        return response()->json($sales);
    }

    public function topCategories(Request $request)
   {
    $days = (int) $request->query('days', 1); 

    $now = Carbon::now('Africa/Casablanca')->endOfDay(); // نهاية اليوم الحالي
    $startDate = Carbon::now('Africa/Casablanca')->subDays($days - 1)->startOfDay(); // بداية أول يوم

    $query = Sale::select('category_id', DB::raw('SUM(total_price) as total_sales'))
        ->with('category:id,name')
        ->whereBetween('sold_at', [$startDate, $now])
        ->groupBy('category_id')
        ->orderByDesc('total_sales')
        ->take(7)
        ->get();

    return response()->json($query);
   }

    public function topTypes(Request $request)
    {
        $days = (int) $request->query('days', 1); 

        $now = Carbon::now('Africa/Casablanca')->endOfDay(); // ← نهاية اليوم الحالي (23:59:59)
        $startDate = Carbon::now('Africa/Casablanca')->subDays($days - 1)->startOfDay(); // ← بداية أول يوم

        $query = Sale::select('type_id', DB::raw('SUM(total_price) as total_sales'))
            ->with('type:id,name')
            ->whereBetween('sold_at', [$startDate, $now])
            ->groupBy('type_id')
            ->orderByDesc('total_sales')
            ->take(7)
            ->get();

        return response()->json($query);
    }

    public function salesByProduct()
    {
        $sales = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_price) as total_sales'))
            ->groupBy('product_id')
            ->orderByDesc('total_sales')
            ->with('product:name,id')
            ->get();

        return response()->json($sales);
    }

    public function filter_by_times(Request $request)
    {
        $range = $request->input('range', 'all'); 

        if ($range === 'all') {
            $sales = Sale::with('product:name,id', 'category:name,id', 'type:name,id')->get();
        } else {
            switch ($range) {
                case 'month':
                    $days = 30;
                    break;
                case 'week':
                    $days = 7;
                    break;
                case 'day':
                    $days = 1;
                    break;
                default:
                    $days = 1;
                    break;
            }
            $startDate = Carbon::now('Africa/Casablanca')->subDays($days);
            $endDate = Carbon::now('Africa/Casablanca');

            $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
                ->with('product:name,id', 'category:name,id', 'type:name,id')
                ->get();
        }

        return response()->json($sales);
    }



    public function sales_statistic()
    {
        $now = Carbon::now('UTC')->addHours(2); 

        $totalSales = Sale::count();

        $salesLast30Days = Sale::where('sold_at', '>=', $now->copy()->subDays(30))->count();

        $salesLast7Days = Sale::where('sold_at', '>=', $now->copy()->subDays(7))->count();

        $salesLast24Hours = Sale::where('sold_at', '>=', $now->copy()->subHours(24))->count();
        
        return response()->json([
            'total_sales' => $totalSales,
            'sales_last_30_days' => $salesLast30Days,
            'sales_last_7_days' => $salesLast7Days,
            'sales_last_24_hours' => $salesLast24Hours,
        ]);
    }

}
