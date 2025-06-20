<?php

namespace App\Services\MLTraining\DataBuilders;

use App\Models\User;

class UserChurnDataBuilder
{
    public function build(): array
    {
        $dataset = [];
        $users = User::with('orders')->cursor(); // use cursor for large data

        foreach ($users as $user) {
            $orders = $user->orders ?? collect();
            $totalOrders = $orders->count();
            $totalSpent = $orders->sum('total_amount');
            $firstOrderDate = $orders->min('created_at');
            $churned = $user->orders()
                            ->where('created_at', '>', now()->subMonths(6))
                            ->count() === 0 ? 1 : 0;

            $dataset[] = [
                'user_id' => $user->id,
                'email_domain' => substr(strrchr($user->email, "@"), 1),
                'total_orders' => $totalOrders,
                'total_spent' => $totalSpent,
                'first_order_date' => $firstOrderDate,
                'signup_date' => $user->created_at,
                'is_churned' => $churned,
            ];
        }

        return $dataset;
    }
}
