<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Admin\Order;

class OpenOrderComposer
{
    public function compose(View $view)
    {
        $openOrder = Order::with('orderItems')
            ->whereHas('status', fn($q) => $q->where('code', 'pending'))
            ->first();

        $view->with('openOrder', $openOrder);
    }
}
