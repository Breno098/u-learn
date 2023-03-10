<?php

namespace App\Services\Admin;

use App\Models\Order;
use Carbon\Carbon;

class ChurnRateService
{
    /**
     * @param integer $month
     * @param integer|null|null $year
     * @return float
     */
    public function month(int $month, int|null $year = null): float
    {
        $date = Carbon::create($year ?? now()->format('Y'), $month, 1);

        $totalOrders = Order::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
        $cancelledOrders = Order::canceled()->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();

        if ($totalOrders == 0) {
            return (float) 0;
        }

        return round(($cancelledOrders / $totalOrders) * 100, 2);
    }
}
