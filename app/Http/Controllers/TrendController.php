<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Courier;

class TrendController extends Controller
{
    public function last30DaysTrends(Request $request)
    {
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $courierId = $request->query('courier_id');

        $query = DB::table('delivery_results')
            ->selectRaw('DATE(delivered_at) as date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(success) as success_count')
            ->selectRaw('SUM(rto) as rto_count')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, delivered_at)) as avg_delivery_days')
            ->whereBetween('delivered_at', [$startDate, $endDate]);

        if ($courierId) {
            $query->where('courier_id', $courierId);
        } else {
            $query->addSelect('courier_id');
        }

        $groupBy = $courierId ? DB::raw('DATE(delivered_at)') : [DB::raw('DATE(delivered_at)'), 'courier_id'];

        $trends = $query
            ->groupBy($groupBy)
            ->orderBy('date')
            ->get();

        $trends = $trends->map(function ($row) use ($courierId) {
            $row->success_rate = $row->total ? $row->success_count / $row->total : 0;
            $row->rto_rate = $row->total ? $row->rto_count / $row->total : 0;
            if ($courierId) {
                $row->courier = Courier::find($courierId);
            } else {
                $row->courier_id = $row->courier_id;
            }
            return $row;
        });

        return response()->json($trends);
    }
}
