<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Bonus;
use App\Models\Admin\Supplier;
use App\Models\Admin\Brand;
use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin\Status;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderFilesMail;
use Illuminate\Support\Facades\Mail;

class ReceivingController extends Controller
{

    public function dashboard(Request $request)
    {
      
        return view('layouts.admin.receivings.dashboard');
    }

    public function index()
    {
        $orders = Order::with(['items.supplier'])
            ->whereHas('status', fn ($q) => $q->where('code', 'active'))
            ->orderByDesc('created_at')
            ->paginate(paginationPerPage());

        return view('layouts.admin.receivings.index', compact('orders'));
    }

}
