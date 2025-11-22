<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Dispatch;
use App\Models\RollingProgramItem;
use App\Models\Sauda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Order;
use App\Models\Dealer;
use App\Models\Distributor;
use App\Models\OrderAllocation;
use App\Models\DispatchItem;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getCurrentFinancialYear()
    {
        $currentDate = Carbon::now();
        $year = $currentDate->year;
        if ($currentDate->month <= 3) {
            $fyStart = ($year - 1) . '-04-01';
            $fyEnd = $year . '-03-31';
        } else {
            $fyStart = $year . '-04-01';
            $fyEnd = ($year + 1) . '-03-31';
        }
        return ['start' => $fyStart, 'end' => $fyEnd];
    }

    private function calculateDashboardData($fromDate, $toDate)
    {
        // Order MT calculations
        $totalOrdersQtyMT = OrderAllocation::join('orders', 'order_allocations.order_id', '=', 'orders.id')
            ->whereBetween('orders.order_date', [$fromDate, $toDate])
            ->whereIn('order_allocations.status', ['approved', 'partially dispatched', 'completed', 'closed with condition'])
            ->sum('order_allocations.qty');

        $totalOrdersQtyNo = OrderAllocation::join('orders', 'order_allocations.order_id', '=', 'orders.id')
            ->whereBetween('orders.order_date', [$fromDate, $toDate])
            ->whereIn('order_allocations.status', ['approved', 'partially dispatched', 'completed', 'closed with condition'])
            ->count();

        $totalRemainingOrderQtyMT = OrderAllocation::join('orders', 'order_allocations.order_id', '=', 'orders.id')
            ->whereBetween('orders.order_date', [$fromDate, $toDate])
            ->whereIn('order_allocations.status', ['approved', 'partially dispatched'])
            ->select(DB::raw("SUM(CASE WHEN order_allocations.remaining_qty < 0 THEN 0 ELSE order_allocations.remaining_qty END) as total"))
            ->value('total');

        $totalRemainingOrderQtyNo = OrderAllocation::join('orders', 'order_allocations.order_id', '=', 'orders.id')
            ->whereBetween('orders.order_date', [$fromDate, $toDate])
            ->whereIn('order_allocations.status', ['approved', 'partially dispatched'])
            ->select(DB::raw("SUM(CASE WHEN order_allocations.remaining_qty < 0 THEN 0 ELSE order_allocations.remaining_qty END) as total"))
            ->count();

        $completedOrderQtyMT = OrderAllocation::join('orders', 'order_allocations.order_id', '=', 'orders.id')
            ->whereBetween('orders.order_date', [$fromDate, $toDate])
            ->whereIn('order_allocations.status', ['completed', 'closed with condition'])
            ->sum('order_allocations.dispatched_qty');

        $completedOrderQtyNo = OrderAllocation::join('orders', 'order_allocations.order_id', '=', 'orders.id')
            ->whereBetween('orders.order_date', [$fromDate, $toDate])
            ->whereIn('order_allocations.status', ['completed', 'closed with condition'])
            ->count();

        // Order Numbers
        $totalOrdersNo = Order::whereBetween('order_date', [$fromDate, $toDate])->count();

        $newOrdersApprovalsPendingNo = Order::whereBetween('order_date', [$fromDate, $toDate])
            ->where('status', 'pending')
            ->count();

        $approvedOrdersNo = Order::whereBetween('order_date', [$fromDate, $toDate])
            ->where('status', 'approved')
            ->count();

        $ordersCompletedNo = Order::whereBetween('order_date', [$fromDate, $toDate])
            ->whereIn('status', ['completed', 'closed with condition'])
            ->count();

        $partialDispatchedOrdersNo = Order::whereBetween('order_date', [$fromDate, $toDate])
            ->where('status', 'partial dispatch')
            ->count();

        $totalRemainingOrderNo = Order::whereBetween('order_date', [$fromDate, $toDate])
            ->whereNotIn('status', ['completed', 'closed with condition'])
            ->count();

        $totalRejectedOrderNo = Order::whereBetween('order_date', [$fromDate, $toDate])
            ->where('status', 'rejected')
            ->count();

        // Dispatch MT
        $totalDispatchQtyMT = round(DispatchItem::join('dispatches', 'dispatch_items.dispatch_id', '=', 'dispatches.id')
            ->whereBetween('dispatches.dispatch_date', [$fromDate, $toDate])
            ->sum('dispatch_items.dispatch_qty'));

        $totalDispatchQtyNo = DispatchItem::join('dispatches', 'dispatch_items.dispatch_id', '=', 'dispatches.id')
            ->whereBetween('dispatches.dispatch_date', [$fromDate, $toDate])
            ->count();

        $pendingDispatchQtyMT = round(DispatchItem::join('dispatches', 'dispatch_items.dispatch_id', '=', 'dispatches.id')
            ->whereBetween('dispatches.dispatch_date', [$fromDate, $toDate])
            ->where('dispatches.status', 'pending')
            ->sum('dispatch_items.dispatch_qty'));

        $pendingDispatchQtyNo = DispatchItem::join('dispatches', 'dispatch_items.dispatch_id', '=', 'dispatches.id')
            ->whereBetween('dispatches.dispatch_date', [$fromDate, $toDate])
            ->where('dispatches.status', 'pending')
            ->count();

        // Dispatch Numbers
        $totalDispatchesNo = Dispatch::whereBetween('dispatch_date', [$fromDate, $toDate])->count();

        $newDispatchApprovalsPendingNo = Dispatch::whereBetween('dispatch_date', [$fromDate, $toDate])
            ->where('status', 'pending')
            ->count();

        $totalApprovedDispatchNo = Dispatch::whereBetween('dispatch_date', [$fromDate, $toDate])
            ->where('status', 'approved')
            ->count();

        return compact(
            'totalOrdersQtyMT', 'totalRemainingOrderQtyMT', 'completedOrderQtyMT',
            'totalOrdersNo', 'newOrdersApprovalsPendingNo', 'approvedOrdersNo',
            'ordersCompletedNo', 'partialDispatchedOrdersNo', 'totalRemainingOrderNo',
            'totalRejectedOrderNo',
            'totalDispatchQtyMT', 'pendingDispatchQtyMT',
            'totalDispatchesNo', 'newDispatchApprovalsPendingNo', 'totalApprovedDispatchNo', 'totalOrdersQtyNo',
            'totalRemainingOrderQtyNo', 'completedOrderQtyNo', 'totalDispatchQtyNo', 'pendingDispatchQtyNo'
        );
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $fy = $this->getCurrentFinancialYear();
        $fyStart = $fy['start'];
        $fyEnd = $fy['end'];

        $data = $this->calculateDashboardData($fyStart, $fyEnd);

        // Static counts (no date filter)
        $dealerCount = Dealer::whereIn('status', ['Active','Inactive'])->count();
        $distributorCount = Distributor::count();
        $userCount = User::where('id', '!=', auth()->id())->count();

        $data['dealerCount'] = $dealerCount;
        $data['distributorCount'] = $distributorCount;
        $data['userCount'] = $userCount;

        return view('index', array_merge($data, compact('fyStart', 'fyEnd')));
    }

    public function getDashboardCounts(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $data = $this->calculateDashboardData($fromDate, $toDate);

        // Static counts
        $data['dealerCount'] = Dealer::whereIn('status', ['Active','Inactive'])->count();
        $data['distributorCount'] = Distributor::count();
        $data['userCount'] = User::where('id', '!=', auth()->id())->count();

        return response()->json($data);
    }

    public function get_city_list(Request $request)
    {
        $state_id = $request->state_id;
        $where = [
            'state' => $state_id
        ];
        $data = DB::table('city_state')->where($where)->get();
        // $data = City::where($where)->get();
        return json_encode($data);
    }
}
