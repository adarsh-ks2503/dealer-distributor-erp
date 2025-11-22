<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Order;
use App\Models\Dealer;
use App\Models\Distributor;
use App\Models\ItemBasicPrice;
use App\Models\State;
use App\Models\DistributorTeam;
use App\Models\BasicPriceHistory;
use App\Models\DealerOrderLimitRequest;
use App\Models\DistributorOrderLimitRequest;
use App\Models\City;
use App\Models\DealerContactPersonsDetail;
use App\Models\DistributorContactPersonsDetail;

use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\ItemSize;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\ItemSizesHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function __construct()
    {
        // Order Report
        $this->middleware('permission:OrderReport-Index', ['only' => ['order_report', 'get_order_report']]);

        // Dispatch Report
        $this->middleware('permission:DispatchReport-Index', ['only' => ['dispatch_report', 'get_dispatch_report']]);

        // Item Price Report
        $this->middleware('permission:ItemPriceReport-Index', ['only' => ['item_price_report', 'get_item_price_report']]);

        // Item Size Report
        $this->middleware('permission:ItemSizeReport-Index', ['only' => ['item_sizes_report', 'get_item_sizes_report']]);

        // Distributor Team Report
        $this->middleware('permission:DistributorTeamReport-Index', ['only' => ['distributor_team_report', 'get_distributor_team_report']]);

        // Distributor Report
        $this->middleware('permission:DistributorReport-Index', ['only' => ['distributors_report', 'get_distributors_report']]);

        // Dealer Report
        $this->middleware('permission:DealerReport-Index', ['only' => ['dealers_report', 'get_dealers_report']]);
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

    // Orders Report Starts Here
    public function order_report(Request $request, $id = null)
    {
        $selectedId = $id;
        $typeParam = $request->get('type');
        $types = $typeParam ? explode(',', $typeParam) : [];

        if ($request->has('from_dashboard') && $request->boolean('from_dashboard')) {
            $fy = $this->getCurrentFinancialYear();
            $fromValue = $fy['start'];
            $toValue = $fy['end'];
        } else {
            $now = Carbon::now();
            $fromValue = $now->copy()->startOfMonth()->format('Y-m-d');
            $toValue = $now->copy()->endOfMonth()->format('Y-m-d');
        }

        $itemNames = Item::with('sizes')->get();
        $warehouses = Warehouse::select('id')->get();
        $dealers = Dealer::all();
        $distributors = Distributor::all();
        $orders = Order::all();
        $basicPrice = ItemBasicPrice::all();

        return view('reports.orders', compact('itemNames', 'warehouses', 'selectedId', 'dealers', 'distributors', 'orders', 'basicPrice', 'types', 'fromValue', 'toValue'));
    }


    public function get_order_report(Request $request)
    {

        $query = Order::query();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('order_date', [$request->from_date, $request->to_date]);
        }
        if ($request->filled('dealer_name') && $request->dealer_name !== 'all') {
            $query->where('placed_by_dealer_id', $request->dealer_name);
        }

        if ($request->filled('distributer_name') && $request->distributer_name !== 'all') {
            $query->where('placed_by_distributor_id', $request->distributer_name);
        }

        if ($request->filled('order_number') && $request->order_number !== 'all') {
            $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
        }

        // if ($request->filled('order_number') && $request->order_number !== 'all') {
        //     $query->where('order_number', $request->order_number);
        //     Log::info('Filter Order Number:', [$request->order_number]);
        // }


        if ($request->filled('type') && $request->type !== 'all') {
            $types = explode(',', $request->type);
            $types = array_map('trim', $types);
            $query->whereIn('status', $types);
        }

        $items = $query->with(['dealer', 'distributor', 'allocations'])->get();

        return response()->json($items);
    }
    // Orders Report Ends Here

    // Dispatch Report Starts Here

    public function dispatch_report(Request $request, $id = null){
        $selectedId = $id;
        $type = $request->get('type');

        if ($request->has('from_dashboard') && $request->boolean('from_dashboard')) {
            $fy = $this->getCurrentFinancialYear(); // Assuming this method is available (copy from HomeController if needed)
            $fromValue = $fy['start'];
            $toValue = $fy['end'];
        } else {
            $now = Carbon::now();
            $fromValue = $now->copy()->startOfMonth()->format('Y-m-d');
            $toValue = $now->copy()->endOfMonth()->format('Y-m-d');
        }

        $dealers = Dealer::all();
        $distributors = Distributor::all();
        $warehouses = Warehouse::get();
        $orders = Order::all();
        $dispatchs = Dispatch::all();
        return view('reports.dispatch', compact('warehouses', 'dispatchs', 'selectedId', 'dealers', 'distributors', 'orders', 'type', 'fromValue', 'toValue'));
    }

    public function get_dispatch_report(Request $request)
    {
        $query = Dispatch::query();
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('dispatch_date', [$request->from_date, $request->to_date]);
        }
        if ($request->filled('dealer_name') && $request->dealer_name !== 'all') {
            $query->where('dealer_id', $request->dealer_name);
        }
        if ($request->filled('distributer_name') && $request->distributer_name !== 'all') {
            $query->where('distributor_id', $request->distributer_name);
        }
        if ($request->filled('dispatch_number') && $request->dispatch_number !== 'all') {
            $query->where('dispatch_number', 'LIKE', '%' . $request->dispatch_number . '%');
        }
        if ($request->filled('warehouse_id') && $request->warehouse_id !== 'all') {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('status', $request->type);
        }
        $items = $query->with(['dealer', 'distributor', 'dispatchItems', 'attachments', 'warehouse',])->get();
        return response()->json($items);
    }

    public function get_dispatch_items($id)
    {
        $items = DispatchItem::where('dispatch_id', $id)
            ->with(['order', 'size', 'item'])
            ->get();
        return response()->json($items);
    }
    // Dispatch Report Ends Here


    // Item Price Report Starts Here
    public function item_price_report($id = null)
    {
        $selectedId = $id;
        $itemBasicPrices = ItemBasicPrice::with('itemName', 'stateName')
            ->orderBy('approval_date', 'desc')
            ->get();
        $states = State::get();

        return view('reports.item_price', compact('selectedId', 'itemBasicPrices', 'states',));
    }

    // public function get_item_price_report(Request $request)
    // {
    //     $query = ItemBasicPrice::query();
    //     // dd($request->all());

    //     if ($request->filled('from_date') && $request->filled('to_date')) {
    //         $query->whereBetween('approval_date', [$request->from_date, $request->to_date]);
    //     }

    //     $items = $query->with(['itemName', 'stateName'])->get();
    //     $history = BasicPriceHistory::with(['itemName', 'stateName'])->get();

    //     Log::info('Dispatch count: ' . $items->count());
    //     // dd($items);
    //     return response()->json([
    //     'items' => $items,
    //     'history' => $history,
    // ]);
    // }

    // public function get_item_price_report(Request $request)
    // {
    //     // --- 1. Setup Queries for Both Models ---
    //     // Query for the current prices table
    //     $currentPricesQuery = ItemBasicPrice::query()->with(['itemName', 'stateName']);

    //     // Query for the history table
    //     $historyPricesQuery = BasicPriceHistory::query()->with(['itemName', 'stateName']);

    //     // --- 2. Apply Date Filters to BOTH Queries ---
    //     // This ensures the data from both tables is from the same period.
    //     if ($request->filled('from_date') && $request->filled('to_date')) {
    //         $dateRange = [$request->from_date, $request->to_date];

    //         $currentPricesQuery->whereBetween('created_at', $dateRange);

    //         // IMPORTANT: Change 'created_at' to the actual date column in your history table.
    //         $historyPricesQuery->whereBetween('created_at', $dateRange);
    //     }

    //     // State Filter
    //     if ($request->filled('state') && $request->state !== 'all') {
    //         // CORRECTED: Use the 'region' column for the current prices table
    //         $currentPricesQuery->where('region', $request->state);

    //         // CORRECTED: Use the 'state_id' column for the history table
    //         $historyPricesQuery->where('state_id', $request->state);
    //     }



    //     // Status Filter
    //     if ($request->filled('type') && $request->type !== 'all') {
    //         $status = $request->type; // e.g., 'pending'

    //         // --- MODIFICATION START ---
    //         // Convert the first letter to uppercase to match the database enum
    //         // 'pending' becomes 'Pending', 'old' becomes 'Old', etc.
    //         $formattedStatus = ucfirst($status);
    //         // --- MODIFICATION END ---

    //         if ($status === 'approved') {
    //             // 'Approved' exists in both tables
    //             $currentPricesQuery->where('status', 'Approved'); // Use the correctly cased value
    //             $historyPricesQuery->where('status', 'Approved');
    //         } elseif ($status === 'pending' || $status === 'old') {
    //             // 'Pending' and 'Old' only exist in the main price table
    //             $currentPricesQuery->where('status', $formattedStatus); // Use the formatted status
    //             $historyPricesQuery->whereRaw('1 = 0');
    //         } elseif ($status === 'rejected') {
    //             // 'Rejected' only exists in the history table
    //             $historyPricesQuery->where('status', 'Rejected');
    //             $currentPricesQuery->whereRaw('1 = 0');
    //         }
    //     }




    //     // --- 3. Execute the Queries ---
    //     $currentItems = $currentPricesQuery->get();
    //     Log::info('Total currentItems records for report: ' . $currentItems->count());
    //     $historyItems = $historyPricesQuery->get();
    //     Log::info('Total historyItems records for report: ' . $historyItems->count());

    //     // --- 4. Merge the Two Collections ---
    //     // This combines the results into a single list.
    //     $combinedData = $currentItems->merge($historyItems);

    //     // --- 5. (Optional but Recommended) Add a Type Identifier ---
    //     // This adds a new 'record_type' attribute to each item, which is very
    //     // useful for your frontend to identify the data source.
    //     $combinedData->each(function ($item) {
    //         if ($item instanceof ItemBasicPrice) {
    //             $item->record_type = 'current';
    //         } elseif ($item instanceof BasicPriceHistory) {
    //             $item->record_type = 'history';
    //         }
    //     });

    //     // Log the final count
    //     Log::info('Total combined records for report: ' . $combinedData->count());

    //     // --- 6. Return the Single, Combined List as JSON ---
    //     return response()->json([
    //         'report_data' => $combinedData,
    //     ]);
    // }
    public function get_item_price_report(Request $request)
    {
        // $currentPricesQuery = ItemBasicPrice::query()->with(['itemName', 'stateName']);
        // $historyPricesQuery = BasicPriceHistory::query()->with(['itemName', 'stateName']);

        // if ($request->filled('from_date') && $request->filled('to_date')) {
        //     // $dateRange = [$request->from_date, $request->to_date];

        //     // $currentPricesQuery->whereBetween('approval_date', $dateRange);

        //     // $historyPricesQuery->whereBetween('status_changed_at', $dateRange);

        //     $fromDate = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
        //     $toDate = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
        //     $dateRange = [$fromDate, $toDate];

        //     $currentPricesQuery->whereBetween('created_at', $dateRange);
        //     $historyPricesQuery->whereBetween('status_changed_at', $dateRange);
        // }

        // // State Filter
        // if ($request->filled('state') && $request->state !== 'all') {

        //     $currentPricesQuery->where('region', $request->state);
        //     $historyPricesQuery->where('state_id', $request->state);
        // }

        // if ($request->filled('type') && $request->type !== 'all') {
        //     $status = $request->type;
        //     $formattedStatus = ucfirst($status);

        //     if ($status === 'approved') {
        //         $currentPricesQuery->where('status', 'Approved');
        //         $historyPricesQuery->where('status', 'Approved');
        //     } elseif ($status === 'pending' || $status === 'old') {
        //         $currentPricesQuery->where('status', $formattedStatus);
        //         $historyPricesQuery->whereRaw('1 = 0');
        //     } elseif ($status === 'rejected') {
        //         $historyPricesQuery->where('status', 'Rejected');
        //         $currentPricesQuery->whereRaw('1 = 0');
        //     }
        // }

        // $currentItems = $currentPricesQuery->get();
        // $historyItems = $historyPricesQuery->get();

        // $combinedData = $currentItems->merge($historyItems);

        // $combinedData->each(function ($item) {
        //     if ($item instanceof ItemBasicPrice) {
        //         $item->record_type = 'current';
        //     } elseif ($item instanceof BasicPriceHistory) {
        //         $item->record_type = 'history';
        //     }
        // });

        // return response()->json([
        //     'report_data' => $combinedData,
        // ]);

        $query = ItemBasicPrice::query()->with(['itemName', 'stateName']);

        // Apply filters only to this single query
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        if ($request->filled('state') && $request->state !== 'all') {
            $query->where('region', $request->state);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $status = ucfirst($request->type); // 'approved' -> 'Approved'
            $query->where('status', $status);
        }

        // CHANGE 2: Fetch the data. No merging or history logic needed.
        $currentItems = $query->get();

        // CHANGE 3: Return the data directly. The 'report_data' wrapper is no longer necessary.
        return response()->json($currentItems);
    }


    public function get_item_price_history($id)
    {
        // 1. Pehle current price record ko find karein taaki humein item aur region ID mil sake
        $currentItem = ItemBasicPrice::findOrFail($id);

        // 2. Ab history table se uss item aur region ke saare records nikalein
        $history = BasicPriceHistory::with(['itemName', 'stateName'])
            ->where('item_id', $currentItem->item)
            ->where('state_id', $currentItem->region)
            ->orderBy('status_changed_at', 'desc') // Sabse naye changes pehle
            ->get();

        return response()->json($history);
    }
    // Item Price Report Ends Here

    // Distributor Team Report Starts Here
    public function distributor_team_report($id = null)
    {
        $selectedId = $id;
        $itemBasicPrices = ItemBasicPrice::with('itemName', 'stateName')
            ->where('status', 'Approved')
            ->orderBy('approval_date', 'desc')
            ->get();
        $teams = DistributorTeam::with(['distributor.state', 'dealers'])->get();
        $states = State::get();
        $distributors = Distributor::all();

        return view('reports.distributor_team', compact('selectedId', 'itemBasicPrices', 'states', 'distributors', 'teams'));
    }

    public function get_distributor_team_report(Request $request)
    {
        $query = DistributorTeam::query();

        // if ($request->filled('from_date') && $request->filled('to_date')) {
        //     $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        // }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay(); // This is the key

            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        if ($request->filled('distributer_name') && $request->distributer_name !== 'all') {
            $query->where('distributor_id', $request->distributer_name);
        }

        if ($request->has('distributor_code') && $request->distributor_code !== 'all') {
            $query->whereHas('distributor', function ($q) use ($request) {
                $q->where('code', 'LIKE', '%' . $request->distributor_code . '%');
            });
        }

        // if ($request->filled('state') && $request->state !== 'all') {
        //     $query->where('id', $request->state);
        //     // dd($request->state);
        // }
        if ($request->filled('state') && $request->state !== 'all') {
            // This tells Laravel: "Only get teams WHERE the related distributor
            // has a state_id that matches the request's state."
            $query->whereHas('distributor', function ($q) use ($request) {
                $q->where('state_id', $request->state);
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('status', $request->type);
        }

        $items = $query->with(['dealers', 'distributor.state'])->get();

        return response()->json($items);
    }

    public function get_distributor_team_dealers($id)
    {
        $team = DistributorTeam::findOrFail($id);

        $dealers = $team->dealers()->with(['city', 'state'])->get();

        return response()->json($dealers);
    }
    // Distributor Team Report Ends Here

    // Dealers Report Starts Here
    public function dealers_report($id = null)
    {
        $selectedId = $id;

        $itemNames = Item::with('sizes')->get();
        $warehouses = Warehouse::select('id')->get();
        $distributors = Distributor::all();
        $orders = Order::all();
        $basicPrice = ItemBasicPrice::all();

        $states = State::all();
        $city = City::all();


        $dealers = Dealer::with(['state', 'city', 'contactPersons'])
            ->orderBy('created_at', 'DESC')
            ->get();
        $requestCount = Dealer::where('status', 'Pending')->count();
        $olRequest = DealerOrderLimitRequest::where('status', 'pending')->count();

        return view('reports.dealers', compact('itemNames', 'warehouses', 'selectedId', 'dealers', 'distributors', 'orders', 'basicPrice', 'requestCount', 'olRequest', 'states', 'city',));
    }

    public function get_dealers_report(Request $request)
    {
        $query = Dealer::query();

        // 1. Date Filter (Your code was correct)
        // if ($request->filled('from_date') && $request->filled('to_date')) {
        //     $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        // }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay(); // This is the key

            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        if ($request->filled('dealer_name') && $request->dealer_name !== 'all') {
            $query->where('id', $request->dealer_name);
        }

        if ($request->filled('distributer_name') && $request->distributer_name !== 'all') {
            $query->where('distributor_id', $request->distributer_name);
        }

        if ($request->filled('state') && $request->state !== 'all') {
            $query->where('state_id', $request->state);
        }

        if ($request->filled('city') && $request->city !== 'all') {
            $query->where('city_id', $request->city);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('status', $request->type);
        }

        $query->orderBy('created_at', 'desc');

        $items = $query->with(['state', 'city', 'contactPersons', 'distributor'])->get();

        return response()->json($items);
    }

    public function get_dealer_order_limit_history($id)
    {
        $history = DealerOrderLimitRequest::where('dealer_id', $id)
            // ->where('status', 'Approved') // We only care about approved changes
            ->orderBy('updated_at', 'desc') // Show the most recent first
            ->get();

        return response()->json($history);
    }

    public function get_dealer_contact_persons($id)
    {
        // Find all contact persons where the dealer_id matches the provided id
        $contacts = DealerContactPersonsDetail::where('dealer_id', $id)->get();

        return response()->json($contacts);
    }


    public function distributors_report($id = null)
    {
        // dd($id);
        $selectedId = $id;

        $distributors = Distributor::all();

        $states = State::all();
        $city = City::all();

        return view('reports.distributors', compact('selectedId', 'distributors', 'states', 'city',));
    }

    public function get_distributors_report(Request $request)
    {
        $query = Distributor::query();

        // if ($request->filled('from_date') && $request->filled('to_date')) {
        //     $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        // }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay(); // This is the key

            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        if ($request->filled('distributer_name') && $request->distributer_name !== 'all') {
            $query->where('name', 'like', '%' . $request->distributer_name . '%');
        }

        if ($request->filled('state') && $request->state !== 'all') {
            $query->where('state_id', $request->state);
        }

        if ($request->filled('city') && $request->city !== 'all') {
            $query->where('city_id', $request->city);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('status', $request->type);
        }

        $query->orderBy('created_at', 'desc');

        $items = $query->with(['state', 'city', 'contactPersons'])->get();

        return response()->json($items);
    }




    public function get_distributor_order_limit_history($id)
    {
        $history = DistributorOrderLimitRequest::where('distributor_id', $id)
            // ->where('status', 'Approved')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($history);
    }

    public function get_distributor_contact_persons($id)
    {
        // Us distributor ID se jude saare contact persons ko find karein
        $contacts = DistributorContactPersonsDetail::where('distributor_id', $id)->get();

        return response()->json($contacts);
    }



    // Item Size report starts
    public function item_sizes_report($id = null)
    {
        $selectedId = $id;
        $itemSizes = ItemSize::with('itemName',)
            ->orderBy('approval_time', 'desc')
            ->get();



        // $currentHsnCodes = ItemSize::whereNotNull('hsn_code')->distinct()->pluck('hsn_code');
        $hsnCodes = ItemSize::whereNotNull('hsn_code')->distinct()->pluck('hsn_code');
        // Get unique codes from the history table
        // $historyHsnCodes = ItemSizesHistory::whereNotNull('hsn_code')->distinct()->pluck('hsn_code');

        // Merge both lists, get unique values, sort them, and remove any empty values
        // $hsnCodes = $currentHsnCodes->merge($historyHsnCodes)->unique()->sort()->filter();


        return view('reports.item_sizes', compact('selectedId', 'hsnCodes', 'itemSizes',));
    }

    public function get_item_sizes_report(Request $request)
    {
        // $currentSizesQuery = ItemSize::query()->with(['itemName',]);
        // $historySizesQuery = ItemSizesHistory::query()->with(['itemName',]);

        // if ($request->filled('from_date') && $request->filled('to_date')) {
        //     $dateRange = [$request->from_date, $request->to_date];

        //     $currentSizesQuery->whereBetween('created_at', $dateRange);

        //     $historySizesQuery->whereBetween('approval_time', $dateRange);
        // }

        // // State Filter
        // if ($request->filled('hsn_code') && $request->hsn_code !== 'all') {

        //     $currentSizesQuery->where('hsn_code', $request->hsn_code);
        //     $historySizesQuery->where('hsn_code', $request->hsn_code);
        // }

        // if ($request->filled('type') && $request->type !== 'all') {
        //     $status = $request->type;
        //     $formattedStatus = ucfirst($status);

        //     if ($status === 'active') {
        //         $currentSizesQuery->where('status', 'Active');
        //         $historySizesQuery->where('status', 'Approved');
        //     } elseif ($status === 'pending' || $status === 'old') {
        //         $currentSizesQuery->where('status', $formattedStatus);
        //         $historySizesQuery->whereRaw('1 = 0');
        //     } elseif ($status === 'rejected') {
        //         $historySizesQuery->where('status', 'Rejected');
        //         $currentSizesQuery->whereRaw('1 = 0');
        //     }
        // }

        // $currentItems = $currentSizesQuery->get();
        // $historyItems = $historySizesQuery->get();
        // // Log::info('Filter Order Number:', [$currentItems]);
        // // Log::info('Filter Order Number:', [$historyItems]);
        // Log::info('Filter Order Number:', [$currentItems->count()]);
        // Log::info('Filter Order Number:', [$historyItems->count()]);

        // $combinedData = $currentItems->merge($historyItems);

        // $combinedData->each(function ($item) {
        //     if ($item instanceof ItemSize) {
        //         $item->record_type = 'current';
        //     } elseif ($item instanceof ItemSizesHistory) {
        //         $item->record_type = 'history';
        //     }
        // });
        // // Log::info('Filter Order Number:', [$combinedData]);
        // // Log::info('Filter Order Number:', [$combinedData->count()]);
        // return response()->json([
        //     'report_data' => $combinedData,
        // ]);


        // CHANGE 1: We only need the query for the main ItemSize table.
        // $query = ItemSize::query()->with(['itemName'])->where('status', 'Active');
        $query = ItemSize::query()->with(['itemName']);

        // Apply filters only to this single query
        // if ($request->filled('from_date') && $request->filled('to_date')) {
        //     $dateRange = [$request->from_date, $request->to_date];
        //     $query->whereBetween('created_at', $dateRange);
        // }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            // Parse the start date to ensure it starts from 00:00:00
            $fromDate = Carbon::parse($request->from_date)->startOfDay();

            // Parse the end date and set its time to the very end of the day
            $toDate   = Carbon::parse($request->to_date)->endOfDay();

            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        if ($request->filled('hsn_code') && $request->hsn_code !== 'all') {
            $query->where('hsn_code', $request->hsn_code);
        }

        // if ($request->filled('type') && $request->type !== 'all') {
        //     $status = ucfirst($request->type);
        //     if ($status === 'Active') {
        //         $query->where('status', 'Active');
        //     } elseif ($status === 'Pending') {
        //         $query->where('status', $status);
        //     } elseif ($status === 'Inactive') {
        //         $query->where('status', $status);
        //     }
        //     // 'Rejected' and 'Approved' statuses from history are now ignored.
        // }
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('status', $request->type);
        }

        // CHANGE 2: Fetch the data. No merging or history logic needed.
        $currentItems = $query->get();

        // CHANGE 3: Return the data directly.
        return response()->json($currentItems);
    }

    public function get_item_size_history($id)
    {
        // 1. Find the current item size to get its details
        $currentSize = ItemSize::findOrFail($id);

        // 2. Use those details to find all matching records in the history table
        $history = ItemSizesHistory::with('itemName')
            ->where('item', $currentSize->item)
            ->where('size', $currentSize->size)
            ->orderBy('approval_time', 'desc') // Show most recent first
            ->get();

        return response()->json($history);
    }


    // public function get_item_sizes_report(Request $request)
    // {
    //     // Check if any filter is active
    //     $isFiltered = $request->filled('from_date') || $request->filled('type');

    //     if ($isFiltered) {
    //         // --- FILTERS ARE APPLIED: SHOW COMBINED DATA ---

    //         $currentItemsQuery = ItemSize::query()->with(['itemName']);
    //         $historyItemsQuery = ItemSizesHistory::query()->with(['itemName']);

    //         // Date Filter
    //         if ($request->filled('from_date') && $request->filled('to_date')) {
    //             $dateRange = [$request->from_date, $request->to_date];
    //             $currentItemsQuery->whereBetween('approval_time', $dateRange);
    //             $historyItemsQuery->whereBetween('approval_time', $dateRange);
    //         }

    //         if ($request->filled('hsn_code')) {
    //             $hsnCode = $request->hsn_code;
    //             $currentItemsQuery->where('hsn_code', $hsnCode);
    //             $historyItemsQuery->where('hsn_code', $hsnCode);
    //         }

    //         // Status Filter
    //         if ($request->filled('type') && $request->type !== 'all') {
    //             $status = $request->type; // e.g., 'active', 'pending'
    //             $formattedStatus = ucfirst($status); // e.g., 'Active', 'Pending'

    //             if ($status === 'active') {
    //                 // 'Active' is in the main table, 'Approved' is in history
    //                 $currentItemsQuery->where('status', 'Active');
    //                 $historyItemsQuery->where('status', 'Approved');
    //             } elseif ($status === 'pending' || $status === 'inactive') {
    //                 // 'Pending' and 'Inactive' only exist in the main table
    //                 $currentItemsQuery->where('status', $formattedStatus);
    //                 $historyItemsQuery->whereRaw('1 = 0'); // Force history to be empty
    //             } elseif ($status === 'rejected') {
    //                 // 'Rejected' only exists in the history table
    //                 $historyItemsQuery->where('status', 'Rejected');
    //                 $currentItemsQuery->whereRaw('1 = 0'); // Force current items to be empty
    //             }
    //         }

    //         $currentItems = $currentItemsQuery->get();
    //         $historyItems = $historyItemsQuery->get();
    //         $report_data = $currentItems->merge($historyItems);
    //     } else {
    //         // --- NO FILTERS (DEFAULT VIEW): SHOW CURRENT SIZES ONLY ---
    //         $report_data = ItemSize::query()
    //             ->with(['itemName'])
    //             ->whereIn('status', ['Active', 'Pending'])
    //             ->latest('updated_at')
    //             ->get();
    //     }

    //     // Identify the record type based on the correct models
    //     $report_data->each(function ($item) {
    //         if ($item instanceof ItemSize) {
    //             $item->record_type = 'current';
    //         } elseif ($item instanceof ItemSizesHistory) {
    //             $item->record_type = 'history';
    //         }
    //     });

    //     return response()->json([
    //         'report_data' => $report_data,
    //     ]);
    // }


    //     public function get_item_sizes_report(Request $request)
    // {
    //     // Check if any filter is active, NOW INCLUDING 'hsn_code'
    //     $isFiltered = $request->filled('from_date') || $request->filled('type') || $request->filled('hsn_code');

    //     if ($isFiltered) {
    //         // --- FILTERS ARE APPLIED: SHOW COMBINED DATA ---

    //         $currentItemsQuery = ItemSize::query()->with(['itemName']);
    //         $historyItemsQuery = ItemSizesHistory::query()->with(['itemName']);

    //         // Date Filter
    //         if ($request->filled('from_date') && $request->filled('to_date')) {
    //             $dateRange = [$request->from_date, $request->to_date];
    //             $currentItemsQuery->whereBetween('approval_time', $dateRange);
    //             $historyItemsQuery->whereBetween('approval_time', $dateRange);
    //         }

    //         // HSN Code Filter
    //         if ($request->filled('hsn_code')) {
    //             $hsnCode = $request->hsn_code;
    //             $currentItemsQuery->where('hsn_code', $hsnCode);
    //             $historyItemsQuery->where('hsn_code', $hsnCode);
    //         }

    //         // Status Filter
    //         if ($request->filled('type') && $request->type !== 'all') {
    //             $status = $request->type;
    //             $formattedStatus = ucfirst($status);

    //             if ($status === 'active') {
    //                 $currentItemsQuery->where('status', 'Active');
    //                 $historyItemsQuery->where('status', 'Approved');
    //             } elseif ($status === 'pending' || $status === 'inactive') {
    //                 $currentItemsQuery->where('status', $formattedStatus);
    //                 $historyItemsQuery->whereRaw('1 = 0');
    //             } elseif ($status === 'rejected') {
    //                 $historyItemsQuery->where('status', 'Rejected');
    //                 $currentItemsQuery->whereRaw('1 = 0');
    //             }
    //         }

    //         $currentItems = $currentItemsQuery->get();
    //         $historyItems = $historyItemsQuery->get();
    //         $report_data = $currentItems->merge($historyItems);

    //     } else {
    //         // --- NO FILTERS (DEFAULT VIEW): SHOW CURRENT SIZES ONLY ---
    //         $report_data = ItemSize::query()
    //             ->with(['itemName'])
    //             ->whereIn('status', ['Active', 'Pending'])
    //             ->latest('updated_at')
    //             ->get();
    //     }

    //     // Identify the record type
    //     $report_data->each(function ($item) {
    //         if ($item instanceof ItemSize) {
    //             $item->record_type = 'current';
    //         } elseif ($item instanceof ItemSizesHistory) {
    //             $item->record_type = 'history';
    //         }
    //     });

    //     return response()->json([
    //         'report_data' => $report_data,
    //     ]);
    // }
    // Item Price Report Ends Here

    public function index(Request $request)
    {
        try {
            $states = State::all();
            $cities = City::all();
            $distributors = Distributor::all();
            return view('reports.top_performers', compact('states', 'cities', 'distributors'));
        } catch (\Exception $e) {
            Log::error('Index Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to load report page'], 500);
        }
    }

    // public function getTopPerformers(Request $request)
    // {
    //     Log::info('Top Performers Request', $request->all());

    //     try {
    //         $timeSpan = $request->input('time_span', 'last_month');
    //         $stateId = $request->input('state', 'all');
    //         $cityId = $request->input('city', 'all');
    //         $status = $request->input('status', 'all');
    //         $fromDate = $request->input('from_date');
    //         $toDate = $request->input('to_date');

    //         // Calculate date range
    //         $endDate = now()->endOfDay();
    //         $startDate = now();

    //         switch ($timeSpan) {
    //             case 'last_week':
    //                 $startDate = now()->subWeek()->startOfDay();
    //                 break;
    //             case 'last_month':
    //                 $startDate = now()->subMonth()->startOfDay();
    //                 break;
    //             case 'last_quarter':
    //                 $startDate = now()->subQuarter()->startOfDay();
    //                 break;
    //             case 'last_year':
    //                 $startDate = now()->subYear()->startOfDay();
    //                 break;
    //             case 'ytd':
    //                 $startDate = now()->startOfYear();
    //                 break;
    //             case 'custom':
    //                 if (!$fromDate || !$toDate) {
    //                     return response()->json(['error' => 'Custom date range requires both from and to dates'], 400);
    //                 }
    //                 $startDate = Carbon::parse($fromDate)->startOfDay();
    //                 $endDate = Carbon::parse($toDate)->endOfDay();
    //                 break;
    //             default:
    //                 return response()->json(['error' => 'Invalid time span'], 400);
    //         }

    //         Log::info('Date Range', ['start' => $startDate->toDateTimeString(), 'end' => $endDate->toDateTimeString()]);

    //         // Check status values in dispatches
    //         $statusValues = DB::table('dispatches')->selectRaw('DISTINCT status')->pluck('status')->toArray();
    //         Log::info('Dispatch Status Values', ['statuses' => $statusValues]);
    //         $dispatchStatus = in_array('approved', $statusValues) ? 'approved' : (in_array('Approved', $statusValues) ? 'Approved' : 'approved');

    //         // Top Distributors Query
    //         $distributorsQuery = DB::table('distributors as d')
    //             ->select(
    //                 'd.id',
    //                 'd.name',
    //                 'd.code',
    //                 'd.order_limit',
    //                 'd.allowed_order_limit',
    //                 'd.individual_allowed_order_limit',
    //                 'd.mobile_no',
    //                 'd.status',
    //                 's.state as state_name',
    //                 'c.name as city_name',
    //                 DB::raw('COALESCE((SELECT COUNT(*) FROM distributor_contact_persons_details dcp WHERE dcp.distributor_id = d.id), 0) as contact_person_count'),
    //                 DB::raw("
    //                     COALESCE((
    //                         SELECT SUM(disp.total_amount)
    //                         FROM dispatches AS disp
    //                         WHERE disp.type = 'distributor'
    //                         AND disp.distributor_id = d.id
    //                         AND disp.dispatch_date BETWEEN ? AND ?
    //                         AND disp.status = ?
    //                     ), 0) +
    //                     COALESCE((
    //                         SELECT SUM(disp.total_amount)
    //                         FROM dispatches AS disp
    //                         INNER JOIN dealers AS dl ON dl.id = disp.dealer_id
    //                         WHERE disp.type = 'dealer'
    //                         AND dl.distributor_id = d.id
    //                         AND disp.dispatch_date BETWEEN ? AND ?
    //                         AND disp.status = ?
    //                     ), 0) AS total_amount
    //                 ")
    //             )
    //             ->leftJoin('states as s', 'd.state_id', '=', 's.id')
    //             ->leftJoin('cities as c', 'd.city_id', '=', 'c.id')
    //             ->setBindings([$startDate, $endDate, $dispatchStatus, $startDate, $endDate, $dispatchStatus]);

    //         // Top Dealers Query
    //         $dealersQuery = DB::table('dealers as dl')
    //             ->select(
    //                 'dl.id',
    //                 'dl.name',
    //                 'dl.code',
    //                 'dl.order_limit',
    //                 'dl.allowed_order_limit',
    //                 'dl.status',
    //                 's.state as state_name',
    //                 'c.name as city_name',
    //                 'dl.mobile_no',
    //                 DB::raw('COALESCE((SELECT COUNT(*) FROM dealer_contact_persons_details dcp WHERE dcp.dealer_id = dl.id), 0) as contact_person_count'),
    //                 DB::raw('COALESCE(SUM(disp.total_amount), 0) as total_amount')
    //             )
    //             ->leftJoin('dispatches as disp', function ($join) use ($startDate, $endDate, $dispatchStatus) {
    //                 $join->on('disp.dealer_id', '=', 'dl.id')
    //                      ->where('disp.type', '=', 'dealer')
    //                      ->whereBetween('disp.dispatch_date', [$startDate, $endDate])
    //                      ->where('disp.status', $dispatchStatus);
    //             })
    //             ->leftJoin('states as s', 'dl.state_id', '=', 's.id')
    //             ->leftJoin('cities as c', 'dl.city_id', '=', 'c.id')
    //             ->groupBy('dl.id', 'dl.name', 'dl.code', 'dl.order_limit', 'dl.allowed_order_limit', 'dl.status', 's.state', 'c.name', 'dl.mobile_no');

    //         // Apply filters
    //         if ($stateId !== 'all') {
    //             $distributorsQuery->where('d.state_id', $stateId);
    //             $dealersQuery->where('dl.state_id', $stateId);
    //         }
    //         if ($cityId !== 'all') {
    //             $distributorsQuery->where('d.city_id', $cityId);
    //             $dealersQuery->where('dl.city_id', $cityId);
    //         }
    //         if ($status !== 'all') {
    //             $distributorsQuery->where('d.status', ucfirst($status));
    //             $dealersQuery->where('dl.status', ucfirst($status));
    //         }

    //         // Execute queries
    //         try {
    //             $topDistributors = $distributorsQuery->orderByDesc('total_amount')->limit(10)->get();
    //             $topDealers = $dealersQuery->orderByDesc('total_amount')->limit(10)->get();
    //         } catch (\Exception $e) {
    //             Log::error('Query Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    //             return response()->json(['error' => 'Database query failed: ' . $e->getMessage()], 500);
    //         }

    //         Log::info('Query Results', [
    //             'distributors_count' => $topDistributors->count(),
    //             'dealers_count' => $topDealers->count(),
    //         ]);

    //         return response()->json([
    //             'distributors' => $topDistributors,
    //             'dealers' => $topDealers,
    //             'message' => ($topDistributors->isEmpty() && $topDealers->isEmpty()) ? 'No data found for the selected filters' : null,
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Top Performers Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    //         return response()->json(['error' => 'Server error occurred: ' . $e->getMessage()], 500);
    //     }
    // }


    // public function getTopPerformers(Request $request)
    // {
    //     Log::info('Top Performers Request', $request->all());

    //     try {
    //         // --- Filter Inputs ---
    //         $timeSpan = $request->input('time_span', 'last_month');
    //         $stateId = $request->input('state', 'all');
    //         $cityId = $request->input('city', 'all');
    //         $status = $request->input('status', 'all');
    //         $fromDate = $request->input('from_date');
    //         $toDate = $request->input('to_date');
    //         // 'dispatch' (amount) ya 'order' (amount) ke liye filter
    //         $criteriaType = $request->input('criteria_type', 'dispatch');

    //         // --- Date Range Calculation ---
    //         $endDate = now()->endOfDay();
    //         $startDate = now(); // Default, will be overwritten
    //         switch ($timeSpan) {
    //             case 'last_week':
    //                 $startDate = now()->subWeek()->startOfDay();
    //                 break;
    //             case 'last_month':
    //                 $startDate = now()->subMonth()->startOfDay();
    //                 break;
    //             case 'last_quarter':
    //                 $startDate = now()->subQuarter()->startOfDay();
    //                 break;
    //             case 'last_year':
    //                 $startDate = now()->subYear()->startOfDay();
    //                 break;
    //             case 'ytd':
    //                 $startDate = now()->startOfYear();
    //                 break;
    //             case 'custom':
    //                 if (!$fromDate || !$toDate) {
    //                     return response()->json(['error' => 'Custom date range requires both from and to dates'], 400);
    //                 }
    //                 $startDate = Carbon::parse($fromDate)->startOfDay();
    //                 $endDate = Carbon::parse($toDate)->endOfDay();
    //                 break;
    //             default:
    //                 return response()->json(['error' => 'Invalid time span'], 400);
    //         }
    //         Log::info('Date Range', ['start' => $startDate->toDateTimeString(), 'end' => $endDate->toDateTimeString()]);

    //         // Default status 'approved'
    //         $approvedStatus = 'approved';

    //         if ($criteriaType == 'order') {
    //             // ==========================================================
    //             // === NAYA LOGIC: JAB USER "BY ORDER AMOUNT" SELECT KARE ===
    //             // ==========================================================

    //             // Top Distributors Query (Orders ke basis par)
    //             // Distributors Query
    //             $distributorsQuery = DB::table('distributors as d')
    //                 ->select(
    //                     'd.id',
    //                     'd.name',
    //                     'd.code',
    //                     'd.status',
    //                     DB::raw("
    //         (
    //             COALESCE((
    //                 SELECT SUM(order_total) FROM (
    //                     SELECT SUM(oa.qty * oa.agreed_basic_price) + o.loading_charge + o.insurance_charge AS order_total
    //                     FROM order_allocations AS oa
    //                     JOIN orders AS o ON o.id = oa.order_id
    //                     WHERE oa.allocated_to_type = 'distributor' AND oa.allocated_to_id = d.id
    //                     AND o.order_date BETWEEN ? AND ?
    //                     GROUP BY o.id, o.loading_charge, o.insurance_charge
    //                 ) AS calculated_orders
    //             ), 0)
    //             +
    //             COALESCE((
    //                 SELECT SUM(order_total) FROM (
    //                     SELECT SUM(oa.qty * oa.agreed_basic_price) + o.loading_charge + o.insurance_charge AS order_total
    //                     FROM order_allocations AS oa
    //                     JOIN orders AS o ON o.id = oa.order_id
    //                     INNER JOIN dealers AS dl ON dl.id = oa.allocated_to_id
    //                     WHERE oa.allocated_to_type = 'dealer' AND dl.distributor_id = d.id
    //                     AND o.order_date BETWEEN ? AND ?
    //                     GROUP BY o.id, o.loading_charge, o.insurance_charge
    //                 ) AS calculated_orders_dealers
    //             ), 0)
    //         ) AS total_amount,
    //         (
    //             COALESCE((
    //                 SELECT SUM(oa.qty) FROM order_allocations AS oa JOIN orders AS o ON o.id = oa.order_id
    //                 WHERE oa.allocated_to_type = 'distributor' AND oa.allocated_to_id = d.id AND o.order_date BETWEEN ? AND ?
    //             ), 0)
    //             +
    //             COALESCE((
    //                 SELECT SUM(oa.qty) FROM order_allocations AS oa JOIN orders AS o ON o.id = oa.order_id INNER JOIN dealers AS dl ON dl.id = oa.allocated_to_id
    //                 WHERE oa.allocated_to_type = 'dealer' AND dl.distributor_id = d.id AND o.order_date BETWEEN ? AND ?
    //             ), 0)
    //         ) AS total_qty
    //     ")
    //                 )
    //                 ->setBindings([
    //                     // Bindings for Total Amount
    //                     $startDate,
    //                     $endDate,
    //                     $startDate,
    //                     $endDate,
    //                     // Bindings for Total Quantity
    //                     $startDate,
    //                     $endDate,
    //                     $startDate,
    //                     $endDate
    //                 ]);

    //             // Top Dealers Query (Orders ke basis par)
    //             // NOTE: Humne isko bhi subquery mein badal diya hai consistency ke liye.
    //             $dealersQuery = DB::table('dealers as dl')
    //                 ->select(
    //                     'dl.id',
    //                     'dl.name',
    //                     'dl.code',
    //                     'dl.status',
    //                     DB::raw("
    //         COALESCE((
    //             SELECT SUM(order_total) FROM (
    //                 SELECT SUM(oa.qty * oa.agreed_basic_price) + o.loading_charge + o.insurance_charge AS order_total
    //                 FROM order_allocations AS oa
    //                 JOIN orders AS o ON o.id = oa.order_id
    //                 WHERE oa.allocated_to_id = dl.id AND oa.allocated_to_type = 'dealer'
    //                 AND o.order_date BETWEEN ? AND ?
    //                 GROUP BY o.id, o.loading_charge, o.insurance_charge
    //             ) AS calculated_orders
    //         ), 0) AS total_amount,
    //         COALESCE((
    //             SELECT SUM(oa.qty)
    //             FROM order_allocations AS oa
    //             JOIN orders AS o ON o.id = oa.order_id
    //             WHERE oa.allocated_to_id = dl.id AND oa.allocated_to_type = 'dealer'
    //             AND o.order_date BETWEEN ? AND ?
    //         ), 0) AS total_qty
    //     ")
    //                 )
    //                 ->setBindings([
    //                     // Bindings for Total Amount
    //                     $startDate,
    //                     $endDate,
    //                     // Bindings for Total Quantity
    //                     $startDate,
    //                     $endDate
    //                 ]);
    //         } else {
    //             // ==============================================================
    //             // === AAPKA ORIGINAL LOGIC: JAB USER "BY DISPATCH AMOUNT" SELECT KARE ===
    //             // ==============================================================

    //             // Top Distributors Query (Aapka original code)
    //             $distributorsQuery = DB::table('distributors as d')
    //                 ->select(
    //                     'd.id',
    //                     'd.name',
    //                     'd.code',
    //                     'd.status',
    //                     DB::raw("
    //                     COALESCE((
    //                         SELECT SUM(disp.total_amount) FROM dispatches AS disp
    //                         WHERE disp.type = 'distributor' AND disp.distributor_id = d.id AND disp.dispatch_date BETWEEN ? AND ?
    //                     ), 0) +
    //                     COALESCE((
    //                         SELECT SUM(disp.total_amount) FROM dispatches AS disp
    //                         INNER JOIN dealers AS dl ON dl.id = disp.dealer_id
    //                         WHERE disp.type = 'dealer' AND dl.distributor_id = d.id AND disp.dispatch_date BETWEEN ? AND ?
    //                     ), 0) AS total_amount
    //                 ")
    //                 )
    //                 ->setBindings([$startDate, $endDate, $startDate, $endDate]);

    //             // Top Dealers Query (Aapka original code)
    //             $dealersQuery = DB::table('dealers as dl')
    //                 ->select('dl.id', 'dl.name', 'dl.code', 'dl.status', DB::raw('COALESCE(SUM(disp.total_amount), 0) as total_amount'))
    //                 ->leftJoin('dispatches as disp', function ($join) use ($startDate, $endDate, $approvedStatus) {
    //                     $join->on('disp.dealer_id', '=', 'dl.id')
    //                         ->where('disp.type', '=', 'dealer')
    //                         ->whereBetween('disp.dispatch_date', [$startDate, $endDate])
    //                         ->where('disp.status', $approvedStatus);
    //                 })
    //                 ->groupBy('dl.id', 'dl.name', 'dl.code', 'dl.status');
    //         }

    //         // --- Apply common filters ---
    //         if ($stateId !== 'all') {
    //             $distributorsQuery->where('d.state_id', $stateId);
    //             $dealersQuery->where('dl.state_id', $stateId);
    //         }
    //         if ($cityId !== 'all') {
    //             $distributorsQuery->where('d.city_id', $cityId);
    //             $dealersQuery->where('dl.city_id', $cityId);
    //         }
    //         if ($status !== 'all') {
    //             $distributorsQuery->where('d.status', ucfirst($status));
    //             $dealersQuery->where('dl.status', ucfirst($status));
    //         }

    //         // --- Execute queries ---
    //         $topDistributors = $distributorsQuery->orderByDesc('total_amount')->limit(10)->get();
    //         $topDealers = $dealersQuery->orderByDesc('total_amount')->limit(10)->get();

    //         return response()->json([
    //             'distributors' => $topDistributors,
    //             'dealers' => $topDealers,
    //             'message' => ($topDistributors->isEmpty() && $topDealers->isEmpty()) ? 'No data found for the selected filters' : null,
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Top Performers Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    //         return response()->json(['error' => 'Server error occurred: ' . $e->getMessage()], 500);
    //     }
    // }


    // public function getTopPerformers(Request $request)
    // {
    //     Log::info('Top Performers Request', $request->all());

    //     try {
    //         // --- Filter Inputs ---
    //         $timeSpan = $request->input('time_span', 'last_month');
    //         $stateId = $request->input('state', 'all');
    //         $cityId = $request->input('city', 'all');
    //         $status = $request->input('status', 'all');
    //         $fromDate = $request->input('from_date');
    //         $toDate = $request->input('to_date');
    //         $criteriaType = $request->input('criteria_type', 'dispatch');

    //         // --- Date Range Calculation ---
    //         $endDate = now()->endOfDay();
    //         $startDate = now();
    //         switch ($timeSpan) {
    //             case 'last_week':
    //                 $startDate = now()->subWeek()->startOfDay();
    //                 break;
    //             case 'last_month':
    //                 $startDate = now()->subMonth()->startOfDay();
    //                 break;
    //             case 'last_quarter':
    //                 $startDate = now()->subQuarter()->startOfDay();
    //                 break;
    //             case 'last_year':
    //                 $startDate = now()->subYear()->startOfDay();
    //                 break;
    //             case 'ytd':
    //                 $startDate = now()->startOfYear();
    //                 break;
    //             case 'custom':
    //                 if (!$fromDate || !$toDate) {
    //                     return response()->json(['error' => 'Custom date range requires both from and to dates'], 400);
    //                 }
    //                 $startDate = Carbon::parse($fromDate)->startOfDay();
    //                 $endDate = Carbon::parse($toDate)->endOfDay();
    //                 break;
    //             default:
    //                 return response()->json(['error' => 'Invalid time span'], 400);
    //         }
    //         Log::info('Date Range', ['start' => $startDate->toDateTimeString(), 'end' => $endDate->toDateTimeString()]);

    //         $approvedStatus = 'approved';

    //         if ($criteriaType == 'order') {
    //             // ==========================================================
    //             // === YEH UPDATED CODE HAI JO SERVER PAR BHI CHALEGA ===
    //             // ==========================================================

    //             $distributorsQuery = DB::table('distributors as d')
    //                 ->select([
    //                     'd.id',
    //                     'd.name',
    //                     'd.code',
    //                     'd.status',
    //                     DB::raw("( (COALESCE((SELECT SUM(oa.qty * oa.agreed_basic_price) FROM order_allocations oa JOIN orders o ON o.id = oa.order_id WHERE oa.allocated_to_type = 'distributor' AND oa.allocated_to_id = d.id AND o.order_date BETWEEN ? AND ?), 0) + COALESCE((SELECT SUM(oa.qty * oa.agreed_basic_price) FROM order_allocations oa JOIN orders o ON o.id = oa.order_id JOIN dealers dl ON dl.id = oa.allocated_to_id WHERE oa.allocated_to_type = 'dealer' AND dl.distributor_id = d.id AND o.order_date BETWEEN ? AND ?), 0)) + (COALESCE((SELECT SUM(o.loading_charge + o.insurance_charge) FROM orders o WHERE o.order_date BETWEEN ? AND ? AND o.id IN (SELECT DISTINCT oa.order_id FROM order_allocations oa WHERE oa.allocated_to_type='distributor' AND oa.allocated_to_id=d.id)), 0) + COALESCE((SELECT SUM(o.loading_charge + o.insurance_charge) FROM orders o WHERE o.order_date BETWEEN ? AND ? AND o.id IN (SELECT DISTINCT oa.order_id FROM order_allocations oa JOIN dealers dl ON dl.id = oa.allocated_to_id WHERE oa.allocated_to_type='dealer' AND dl.distributor_id=d.id)), 0)) ) AS total_amount"),
    //                     DB::raw("( COALESCE((SELECT SUM(qty) FROM order_allocations oa JOIN orders o ON o.id = oa.order_id WHERE oa.allocated_to_type = 'distributor' AND oa.allocated_to_id = d.id AND o.order_date BETWEEN ? AND ?), 0) + COALESCE((SELECT SUM(qty) FROM order_allocations oa JOIN orders o ON o.id = oa.order_id JOIN dealers dl ON dl.id = oa.allocated_to_id WHERE oa.allocated_to_type = 'dealer' AND dl.distributor_id = d.id AND o.order_date BETWEEN ? AND ?), 0) ) AS total_qty")
    //                 ])->setBindings([$startDate, $endDate, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate]);

    //             $dealersQuery = DB::table('dealers as dl')
    //                 ->select([
    //                     'dl.id',
    //                     'dl.name',
    //                     'dl.code',
    //                     'dl.status',
    //                     DB::raw("COALESCE((SELECT SUM(oa.qty * oa.agreed_basic_price) FROM order_allocations oa JOIN orders o ON o.id = oa.order_id WHERE oa.allocated_to_id = dl.id AND oa.allocated_to_type = 'dealer' AND o.order_date BETWEEN ? AND ?), 0) + COALESCE((SELECT SUM(o.loading_charge + o.insurance_charge) FROM orders o WHERE o.order_date BETWEEN ? AND ? AND o.id IN (SELECT DISTINCT oa.order_id FROM order_allocations oa WHERE oa.allocated_to_id = dl.id AND oa.allocated_to_type = 'dealer')), 0) AS total_amount"),
    //                     DB::raw("COALESCE((SELECT SUM(qty) FROM order_allocations oa JOIN orders o ON o.id = oa.order_id WHERE oa.allocated_to_id = dl.id AND oa.allocated_to_type = 'dealer' AND o.order_date BETWEEN ? AND ?), 0) AS total_qty")
    //                 ])->setBindings([$startDate, $endDate, $startDate, $endDate, $startDate, $endDate]);
    //         } else {
    //             // ===============================================
    //             // === FINAL DATABASE-COMPATIBLE DISPATCH LOGIC ===
    //             // ===============================================

    //             // Top Distributors Query
    //             $distributorsQuery = DB::table('distributors as d')
    //                 ->select([
    //                     'd.id',
    //                     'd.name',
    //                     'd.code',
    //                     'd.status',
    //                     // Total Amount
    //                     DB::raw("
    //             COALESCE((SELECT SUM(total_amount) FROM dispatches WHERE type = 'distributor' AND distributor_id = d.id AND dispatch_date BETWEEN ? AND ? AND status = ?), 0) +
    //             COALESCE((SELECT SUM(total_amount) FROM dispatches disp JOIN dealers dl ON dl.id = disp.dealer_id WHERE disp.type = 'dealer' AND dl.distributor_id = d.id AND disp.dispatch_date BETWEEN ? AND ? AND disp.status = ?), 0)
    //         AS total_amount"),
    //                     // Total Qty
    //                     DB::raw("
    //             COALESCE((SELECT SUM(di.dispatch_qty) FROM dispatch_items di JOIN dispatches disp ON disp.id = di.dispatch_id WHERE disp.type = 'distributor' AND disp.distributor_id = d.id AND disp.dispatch_date BETWEEN ? AND ? AND disp.status = ?), 0) +
    //             COALESCE((SELECT SUM(di.dispatch_qty) FROM dispatch_items di JOIN dispatches disp ON disp.id = di.dispatch_id JOIN dealers dl ON dl.id = disp.dealer_id WHERE disp.type = 'dealer' AND dl.distributor_id = d.id AND disp.dispatch_date BETWEEN ? AND ? AND disp.status = ?), 0)
    //         AS total_qty")
    //                 ])->setBindings([
    //                     // Bindings for Amount
    //                     $startDate,
    //                     $endDate,
    //                     $approvedStatus,
    //                     $startDate,
    //                     $endDate,
    //                     $approvedStatus,
    //                     // Bindings for Qty
    //                     $startDate,
    //                     $endDate,
    //                     $approvedStatus,
    //                     $startDate,
    //                     $endDate,
    //                     $approvedStatus
    //                 ]);

    //             // Top Dealers Query
    //             $dealersQuery = DB::table('dealers as dl')
    //                 ->select([
    //                     'dl.id',
    //                     'dl.name',
    //                     'dl.code',
    //                     'dl.status',
    //                     // Total Amount
    //                     DB::raw("
    //             COALESCE((SELECT SUM(total_amount) FROM dispatches WHERE dealer_id = dl.id AND type = 'dealer' AND dispatch_date BETWEEN ? AND ? AND status = ?), 0)
    //         AS total_amount"),
    //                     // Total Qty
    //                     DB::raw("
    //             COALESCE((SELECT SUM(di.dispatch_qty) FROM dispatch_items di JOIN dispatches disp ON disp.id = di.dispatch_id WHERE disp.dealer_id = dl.id AND disp.type = 'dealer' AND disp.dispatch_date BETWEEN ? AND ? AND disp.status = ?), 0)
    //         AS total_qty")
    //                 ])->setBindings([
    //                     // Bindings for Amount
    //                     $startDate,
    //                     $endDate,
    //                     $approvedStatus,
    //                     // Bindings for Qty
    //                     $startDate,
    //                     $endDate,
    //                     $approvedStatus
    //                 ]);
    //         }

    //         // --- Apply common filters ---
    //         if ($stateId !== 'all') {
    //             $distributorsQuery->where('d.state_id', $stateId);
    //             $dealersQuery->where('dl.state_id', $stateId);
    //         }
    //         if ($cityId !== 'all') {
    //             $distributorsQuery->where('d.city_id', $cityId);
    //             $dealersQuery->where('dl.city_id', $cityId);
    //         }
    //         if ($status !== 'all') {
    //             $distributorsQuery->where('d.status', ucfirst($status));
    //             $dealersQuery->where('dl.status', ucfirst($status));
    //         }

    //         // --- Execute queries ---
    //         $topDistributors = $distributorsQuery->orderByDesc('total_amount')->limit(10)->get();
    //         $topDealers = $dealersQuery->orderByDesc('total_amount')->limit(10)->get();

    //         return response()->json([
    //             'distributors' => $topDistributors,
    //             'dealers' => $topDealers,
    //             'message' => ($topDistributors->isEmpty() && $topDealers->isEmpty()) ? 'No data found for the selected filters' : null,
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Top Performers Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    //         return response()->json(['error' => 'Server error occurred: ' . $e->getMessage()], 500);
    //     }
    // }

    public function getTopPerformers(Request $request)
    {
        Log::info('Top Performers Request', $request->all());

        try {
            // --- Filters and Date Range ---
            $timeSpan = $request->input('time_span', 'last_month');
            $stateId = $request->input('state', 'all');
            $cityId = $request->input('city', 'all');
            $status = $request->input('status', 'all');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $criteriaType = $request->input('criteria_type', 'dispatch');
            $performer = $request->input('performer', 'all');

            $endDate = now()->endOfDay();
            $startDate = now()->subMonth()->startOfDay();
            switch ($timeSpan) {
                case 'last_week':
                    $startDate = now()->subWeek()->startOfDay();
                    break;
                case 'last_quarter':
                    $startDate = now()->subQuarter()->startOfDay();
                    break;
                case 'last_year':
                    $startDate = now()->subYear()->startOfDay();
                    break;
                case 'ytd':
                    $startDate = now()->startOfYear();
                    break;
                case 'custom':
                    if ($fromDate && $toDate) {
                        $startDate = Carbon::parse($fromDate)->startOfDay();
                        $endDate = Carbon::parse($toDate)->endOfDay();
                    }
                    break;
            }

            if ($criteriaType == 'order') {
    // ====================================================================
    // === ORDER LOGIC: Exclude PENDING and REJECTED status ===
    // ====================================================================
    $pendingStatus = 'pending';
    $rejectedStatus = 'rejected';

    $distributorsQuery = DB::table('distributors as d')
        ->select([
            'd.id',
            'd.name',
            'd.code',
            'd.status',
            DB::raw("(
                COALESCE((
                    SELECT SUM(oa.qty * (oa.agreed_basic_price + o.insurance_charge + o.loading_charge))
                    FROM order_allocations oa
                    JOIN orders o ON o.id = oa.order_id
                    WHERE o.type = 'distributor'
                    AND o.created_at BETWEEN ? AND ?
                    AND o.status NOT IN (?, ?)
                    AND (
                        (oa.allocated_to_type = 'distributor' AND oa.allocated_to_id = d.id)
                        OR (
                            oa.allocated_to_type = 'dealer'
                            AND oa.allocated_to_id IN (
                                SELECT id FROM dealers WHERE distributor_id = d.id
                            )
                        )
                    )
                ), 0)
            ) AS total_amount"),
            DB::raw("(
                COALESCE((
                    SELECT SUM(oa.qty)
                    FROM order_allocations oa
                    JOIN orders o ON o.id = oa.order_id
                    WHERE o.type = 'distributor'
                    AND o.created_at BETWEEN ? AND ?
                    AND o.status NOT IN (?, ?)
                    AND (
                        (oa.allocated_to_type = 'distributor' AND oa.allocated_to_id = d.id)
                        OR (
                            oa.allocated_to_type = 'dealer'
                            AND oa.allocated_to_id IN (
                                SELECT id FROM dealers WHERE distributor_id = d.id
                            )
                        )
                    )
                ), 0)
            ) AS total_qty")
        ])
        ->setBindings([
            $startDate, $endDate, $pendingStatus, $rejectedStatus,
            $startDate, $endDate, $pendingStatus, $rejectedStatus
        ]);

    $dealersQuery = DB::table('dealers as dl')
        ->select([
            'dl.id',
            'dl.name',
            'dl.code',
            'dl.status',
            DB::raw("(
                COALESCE((
                    SELECT SUM(oa.qty * (oa.agreed_basic_price + o.insurance_charge + o.loading_charge))
                    FROM order_allocations oa
                    JOIN orders o ON o.id = oa.order_id
                    WHERE oa.allocated_to_id = dl.id
                    AND oa.allocated_to_type = 'dealer'
                    AND o.created_at BETWEEN ? AND ?
                    AND o.status NOT IN (?, ?)
                    AND (
                        o.placed_by_dealer_id = dl.id
                        OR o.placed_by_distributor_id IN (
                            SELECT distributor_id
                            FROM distributor_team_dealers
                            WHERE dealer_id = dl.id
                        )
                    )
                ), 0)
            ) AS total_amount"),
            DB::raw("(
                COALESCE((
                    SELECT SUM(oa.qty)
                    FROM order_allocations oa
                    JOIN orders o ON o.id = oa.order_id
                    WHERE oa.allocated_to_id = dl.id
                    AND oa.allocated_to_type = 'dealer'
                    AND o.created_at BETWEEN ? AND ?
                    AND o.status NOT IN (?, ?)
                    AND (
                        o.placed_by_dealer_id = dl.id
                        OR o.placed_by_distributor_id IN (
                            SELECT distributor_id
                            FROM distributor_team_dealers
                            WHERE dealer_id = dl.id
                        )
                    )
                ), 0)
            ) AS total_qty")
        ])
        ->setBindings([
            $startDate, $endDate, $pendingStatus, $rejectedStatus,
            $startDate, $endDate, $pendingStatus, $rejectedStatus
        ]);
}
 else {
                // =================================================================
                // === DISPATCH LOGIC: Only show APPROVED dispatches ===
                // =================================================================
                $distributorsQuery = DB::table('distributors as d')
                    ->select([
                        'd.id',
                        'd.name',
                        'd.code',
                        'd.status',
                        // Total Amount
                        DB::raw("
                            COALESCE((
                                SELECT SUM(di.total_amount)
                                FROM dispatch_items di
                                JOIN dispatches disp ON disp.id = di.dispatch_id
                                JOIN orders o ON o.id = di.order_id
                                WHERE o.placed_by_distributor_id = d.id
                                AND disp.status = 'approved'
                                AND disp.dispatch_date BETWEEN ? AND ?
                            ), 0)
                            AS total_amount"),
                        // Total Qty
                        DB::raw("
                            COALESCE((
                                SELECT SUM(di.dispatch_qty)
                                FROM dispatch_items di
                                JOIN dispatches disp ON disp.id = di.dispatch_id
                                JOIN orders o ON o.id = di.order_id
                                WHERE o.placed_by_distributor_id = d.id
                                AND disp.status = 'approved'
                                AND disp.dispatch_date BETWEEN ? AND ?
                            ), 0)
                            AS total_qty")
                    ])->setBindings([
                        $startDate,
                        $endDate,
                        $startDate,
                        $endDate
                    ]);

                // ===================== DEALER =====================
    $dealersQuery = DB::table('dealers as dl')
        ->select([
            'dl.id',
            'dl.name',
            'dl.code',
            'dl.status',
            // ✅ Total Amount (Dealers only)
            DB::raw("
                COALESCE((
                    SELECT SUM(di.total_amount)
                    FROM dispatch_items di
                    JOIN dispatches disp ON disp.id = di.dispatch_id
                    JOIN order_allocations oa ON oa.id = di.allocation_id
                    JOIN orders o ON o.id = oa.order_id
                    WHERE oa.allocated_to_type = 'dealer'
                      AND oa.allocated_to_id = dl.id
                      AND disp.status = 'approved'
                      AND disp.dispatch_date BETWEEN ? AND ?
                ), 0) AS total_amount
            "),
            // ✅ Total Qty (Dealers only)
            DB::raw("
                COALESCE((
                    SELECT SUM(di.dispatch_qty)
                    FROM dispatch_items di
                    JOIN dispatches disp ON disp.id = di.dispatch_id
                    JOIN order_allocations oa ON oa.id = di.allocation_id
                    JOIN orders o ON o.id = oa.order_id
                    WHERE oa.allocated_to_type = 'dealer'
                      AND oa.allocated_to_id = dl.id
                      AND disp.status = 'approved'
                      AND disp.dispatch_date BETWEEN ? AND ?
                ), 0) AS total_qty
            ")
        ])
        ->setBindings([$startDate, $endDate, $startDate, $endDate]);
            }

            // --- Apply Filters ---
            if ($performer !== 'all') {
                [$type, $id] = explode('-', $performer);
                if ($type === 'dealer') $dealersQuery->where('dl.id', $id);
                elseif ($type === 'distributor') $distributorsQuery->where('d.id', $id);
            }
            if ($stateId !== 'all') {
                $distributorsQuery->where('d.state_id', $stateId);
                $dealersQuery->where('dl.state_id', $stateId);
            }
            if ($cityId !== 'all') {
                $distributorsQuery->where('d.city_id', $cityId);
                $dealersQuery->where('dl.city_id', $cityId);
            }
            if ($status !== 'all') {
                $distributorsQuery->where('d.status', ucfirst($status));
                $dealersQuery->where('dl.status', ucfirst($status));
            }

            $topDistributors = $distributorsQuery->orderByDesc('total_amount')->limit(10)->get();
            $topDealers = $dealersQuery->orderByDesc('total_amount')->limit(10)->get();

            return response()->json([
                'distributors' => $topDistributors,
                'dealers' => $topDealers
            ]);
        } catch (\Exception $e) {
            Log::error('Top Performers Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }

    public function getContactPersons($distributorId)
    {
        try {
            $contacts = DB::table('distributor_contact_persons_details')
                ->where('distributor_id', $distributorId)
                ->select('name', 'mobile_no', 'email')
                ->get();
            return response()->json($contacts);
        } catch (\Exception $e) {
            Log::error('Contact Persons Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to load contact persons: ' . $e->getMessage()], 500);
        }
    }




    public function getDealerOrders(Request $request, $dealerId)
    {
        try {
            // --- Get filters and calculate date range ---
            $criteriaType = $request->input('criteria_type', 'dispatch');
            $timeSpan = $request->input('time_span', 'last_month');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $endDate = now()->endOfDay();
            $startDate = now();

            switch ($timeSpan) {
                case 'last_week':
                    $startDate = now()->subWeek()->startOfDay();
                    break;
                case 'last_month':
                    $startDate = now()->subMonth()->startOfDay();
                    break;
                case 'last_quarter':
                    $startDate = now()->subQuarter()->startOfDay();
                    break;
                case 'last_year':
                    $startDate = now()->subYear()->startOfDay();
                    break;
                case 'ytd':
                    $startDate = now()->startOfYear();
                    break;
                case 'custom':
                    if ($fromDate && $toDate) {
                        $startDate = Carbon::parse($fromDate)->startOfDay();
                        $endDate = Carbon::parse($toDate)->endOfDay();
                    }
                    break;
            }

            $results = [];

            if ($criteriaType == 'order') {
    // === EFFICIENT WAY TO GET ORDERS AND ITEMS (UPDATED + EXCLUDE pending & rejected) ===
    $orders = DB::table('orders as o')
        ->select(
            'o.id',
            'o.order_number',
            'o.order_date',
            'o.loading_charge',
            'o.insurance_charge',
            'o.status',
            'o.placed_by_distributor_id'
        )
        ->join('order_allocations as oa', 'oa.order_id', '=', 'o.id')
        ->where('oa.allocated_to_type', 'dealer')
        ->where('oa.allocated_to_id', $dealerId)
        ->whereBetween('o.order_date', [$startDate, $endDate])
        ->whereNotIn('o.status', ['pending', 'rejected']) // ✅ exclude both
        ->where(function ($query) use ($dealerId) {
            $query->where('o.placed_by_dealer_id', $dealerId)
                  ->orWhereNotNull('o.placed_by_distributor_id');
        })
        ->groupBy(
            'o.id',
            'o.order_number',
            'o.order_date',
            'o.loading_charge',
            'o.insurance_charge',
            'o.status',
            'o.placed_by_distributor_id'
        )
        ->orderBy('o.order_date', 'desc')
        ->get();

    if ($orders->isNotEmpty()) {
        $orderIds = $orders->pluck('id');

        // ✅ Each allocation includes loading + insurance charges in its calculation
        $allItems = DB::table('order_allocations as oa')
            ->join('orders as o', 'o.id', '=', 'oa.order_id')
            ->select(
                'oa.order_id',
                'oa.id',
                'oa.qty',
                'oa.agreed_basic_price',
                DB::raw('(oa.qty * (oa.agreed_basic_price + o.loading_charge + o.insurance_charge)) as item_total')
            )
            ->whereIn('oa.order_id', $orderIds)
            ->get()
            ->groupBy('order_id');

        // Attach allocations to each order and calculate grand total
        foreach ($orders as $order) {
            $order->items = $allItems->get($order->id, collect());
            $order->grand_total = $order->items->sum('item_total');
        }
    }

    $results = $orders;
}
 else {
                // === DISPATCH LOGIC: CORRECT JOIN VIA order_id → order_allocations ===
                $dispatches = DB::table('dispatches as disp')
                    ->select(
                        'disp.id',
                        'disp.dispatch_number',
                        'disp.dispatch_date',
                        'disp.total_amount',
                        'disp.status'
                    )
                    ->join('dispatch_items as di', 'di.dispatch_id', '=', 'disp.id')
                    ->join('orders as o', 'o.id', '=', 'di.order_id')
                    ->join('order_allocations as oa', 'oa.order_id', '=', 'o.id') // Correct: via order_id
                    ->where('oa.allocated_to_type', 'dealer')
                    ->where('oa.allocated_to_id', $dealerId)
                    ->whereBetween('disp.dispatch_date', [$startDate, $endDate])
                    ->where('disp.status', 'approved')
                    ->groupBy('disp.id', 'disp.dispatch_number', 'disp.dispatch_date', 'disp.total_amount', 'disp.status')
                    ->orderBy('disp.dispatch_date', 'desc')
                    ->get();

                if ($dispatches->isNotEmpty()) {
                    $dispatchIds = $dispatches->pluck('id');

                    $allItems = DB::table('dispatch_items as di')
                        ->select(
                            'di.dispatch_id',
                            'di.id',
                            'di.item_name',
                            'di.dispatch_qty',
                            'di.final_price',
                            'di.total_amount as item_total'
                        )
                        ->whereIn('di.dispatch_id', $dispatchIds)
                        ->get()
                        ->groupBy('dispatch_id');

                    foreach ($dispatches as $dispatch) {
                        $dispatch->items = $allItems->get($dispatch->id, collect());
                    }
                }
                $results = $dispatches;
            }

            return response()->json([
                'success' => true,
                'orders' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Get Dealer Orders Error', ['error' => $e->getMessage(), 'dealer_id' => $dealerId, 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to fetch order details.'], 500);
        }
    }


    // ReportController.php mein is naye function ko add karein

    public function getPerformerDetails(Request $request, $type, $id)
    {
        try {
            // --- Filters aur Date Range ka code waisa hi hai ---
            $criteriaType = $request->input('criteria_type', 'dispatch');
            $timeSpan = $request->input('time_span', 'last_month');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $endDate = now()->endOfDay();
            $startDate = now();
            switch ($timeSpan) {
                case 'last_week':
                    $startDate = now()->subWeek()->startOfDay();
                    break;
                case 'last_month':
                    $startDate = now()->subMonth()->startOfDay();
                    break;
                case 'last_quarter':
                    $startDate = now()->subQuarter()->startOfDay();
                    break;
                case 'last_year':
                    $startDate = now()->subYear()->startOfDay();
                    break;
                case 'ytd':
                    $startDate = now()->startOfYear();
                    break;
                case 'custom':
                    if ($fromDate && $toDate) {
                        $startDate = Carbon::parse($fromDate)->startOfDay();
                        $endDate = Carbon::parse($toDate)->endOfDay();
                    }
                    break;
            }

            $records = collect();

            if ($criteriaType == 'order') {
                // ----- ORDER DETAILS FETCH KARNE KA LOGIC -----
                $query = DB::table('order_allocations as oa')
                    ->join('orders as o', 'o.id', '=', 'oa.order_id')
                    ->select('o.order_number as number', 'o.order_date as date', 'oa.qty', DB::raw('(oa.qty * oa.agreed_basic_price) as item_total'))
                    ->whereBetween('o.order_date', [$startDate, $endDate]);

                if ($type == 'dealer') {
                    $query->where('oa.allocated_to_type', 'dealer')->where('oa.allocated_to_id', $id);
                } elseif ($type == 'distributor') {
                    $dealerIds = DB::table('dealers')->where('distributor_id', $id)->pluck('id');
                    $query->where(function ($q) use ($id, $dealerIds) {
                        $q->where(function ($q2) use ($id) {
                            $q2->where('oa.allocated_to_type', 'distributor')->where('oa.allocated_to_id', $id);
                        })->orWhere(function ($q3) use ($dealerIds) {
                            $q3->where('oa.allocated_to_type', 'dealer')->whereIn('oa.allocated_to_id', $dealerIds);
                        });
                    });
                }
                $records = $query->orderBy('o.order_date', 'desc')->get();
            } else {
                // ----- DISPATCH DETAILS FETCH KARNE KA LOGIC -----
                $query = DB::table('dispatch_items as di')
                    ->join('dispatches as disp', 'disp.id', '=', 'di.dispatch_id')
                    ->join('order_allocations as oa', 'oa.id', '=', 'di.allocation_id')
                    ->select(
                        'disp.dispatch_number as number',
                        'disp.dispatch_date as date',
                        'di.item_name',
                        'di.dispatch_qty as qty',
                        'di.total_amount as item_total'
                    )
                    ->whereBetween('disp.dispatch_date', [$startDate, $endDate])
                    ->where('disp.status', 'approved');

                if ($type == 'dealer') {
                    $query->where('oa.allocated_to_type', 'dealer')
                        ->where('oa.allocated_to_id', $id);
                } elseif ($type == 'distributor') {
                    $dealerIds = DB::table('dealers')->where('distributor_id', $id)->pluck('id');
                    $query->where(function ($q) use ($id, $dealerIds) {
                        $q->where(function ($q2) use ($id) {
                            $q2->where('disp.type', 'distributor')
                                ->where('disp.distributor_id', $id)
                                ->where('oa.allocated_to_type', 'distributor')
                                ->where('oa.allocated_to_id', $id);
                        })->orWhere(function ($q3) use ($dealerIds) {
                            $q3->where('oa.allocated_to_type', 'dealer')
                                ->whereIn('oa.allocated_to_id', $dealerIds);
                        });
                    });
                }
                $records = $query->orderBy('disp.dispatch_date', 'desc')->get();
            }

            // HTML ko render karke response mein bhejein
            $html = view('partials.performer_details_table', ['records' => $records, 'criteria' => $criteriaType])->render();
            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            Log::error('Get Performer Details Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to fetch details.'], 500);
        }
    }
    public function getDistributorOrders(Request $request, $distributorId)
    {
        try {
            // --- Filters and Date Range ---
            $criteriaType = $request->input('criteria_type', 'dispatch');
            $timeSpan = $request->input('time_span', 'last_month');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $endDate = now()->endOfDay();
            $startDate = now()->subMonth()->startOfDay(); // Default

            switch ($timeSpan) {
                case 'last_week':
                    $startDate = now()->subWeek()->startOfDay();
                    break;
                case 'last_month':
                    $startDate = now()->subMonth()->startOfDay();
                    break;
                case 'last_quarter':
                    $startDate = now()->subQuarter()->startOfDay();
                    break;
                case 'last_year':
                    $startDate = now()->subYear()->startOfDay();
                    break;
                case 'ytd':
                    $startDate = now()->startOfYear();
                    break;
                case 'custom':
                    if ($fromDate && $toDate) {
                        $startDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
                        $endDate = \Carbon\Carbon::parse($toDate)->endOfDay();
                    }
                    break;
            }

            if ($criteriaType == 'order') {
    // === ORDER LOGIC (UPDATED: exclude pending & rejected for distributor modal) ===
    $orders = DB::table('orders as o')
        ->select('o.id', 'o.loading_charge', 'o.insurance_charge')
        ->join('order_allocations as oa', 'oa.order_id', '=', 'o.id')
        ->where(function ($query) use ($distributorId) {
            $dealerIds = DB::table('dealers')
                ->where('distributor_id', $distributorId)
                ->pluck('id');

            $query->where(fn($q) =>
                    $q->where('oa.allocated_to_type', 'distributor')
                      ->where('oa.allocated_to_id', $distributorId)
                )
                ->orWhere(fn($q) =>
                    $q->where('oa.allocated_to_type', 'dealer')
                      ->whereIn('oa.allocated_to_id', $dealerIds)
                );
        })
        ->whereBetween('o.order_date', [$startDate, $endDate])
        ->whereNotIn('o.status', ['pending', 'rejected']) // ✅ changed here
        ->where('o.type', '=', 'distributor')
        ->groupBy('o.id', 'o.loading_charge', 'o.insurance_charge')
        ->get();

    $orderIds = $orders->pluck('id');

    // ✅ Per-allocation total with charges included
    $allItems = DB::table('order_allocations as oa')
        ->join('orders as o', 'o.id', '=', 'oa.order_id')
        ->select(
            'oa.order_id',
            'oa.qty',
            DB::raw('(oa.qty * (oa.agreed_basic_price + o.loading_charge + o.insurance_charge)) as item_total')
        )
        ->whereIn('oa.order_id', $orderIds)
        ->get();

    $totalOrders = $orders->count();
    $totalQty = $allItems->sum('qty') ?? 0;
    $totalAmount = $allItems->sum('item_total'); // ✅ already includes charges
}
 else {
                // === DISPATCH LOGIC (UPDATED TO INCLUDE status = 'approved') ===
                $dispatches = DB::table('dispatches as disp')
                    ->select('disp.id', 'disp.total_amount')
                    ->where(function ($query) use ($distributorId) {
                        $dealerIds = DB::table('dealers')->where('distributor_id', $distributorId)->pluck('id');
                        $query->where(fn($q) => $q->where('disp.type', 'distributor')->where('disp.distributor_id', $distributorId))
                            ->orWhere(fn($q) => $q->where('disp.type', 'dealer')->whereIn('disp.dealer_id', $dealerIds));
                    })
                    ->where('disp.status', 'approved') // Added to filter only approved dispatches
                    ->whereExists(function ($query) use ($distributorId) {
                        $query->select(DB::raw(1))
                            ->from('dispatch_items as di')
                            ->join('orders as o', 'o.id', '=', 'di.order_id')
                            ->whereColumn('di.dispatch_id', 'disp.id')
                            ->where('o.placed_by_distributor_id', $distributorId);
                    })
                    ->whereBetween('disp.dispatch_date', [$startDate, $endDate])
                    ->get();

                $dispatchIds = $dispatches->pluck('id');

                // 'allItems' query for totalQty
                $allItems = DB::table('dispatch_items as di')
                    ->select('di.dispatch_id', 'di.dispatch_qty')
                    ->whereIn('di.dispatch_id', $dispatchIds)
                    ->get();

                $totalOrders = $dispatches->count();
                $totalQty = $allItems->sum('dispatch_qty') ?? 0;
                $totalAmount = $dispatches->sum('total_amount') ?? 0;
            }

            return response()->json([
                'success' => true,
                'totals' => [
                    'total_orders' => $totalOrders,
                    'total_qty' => $totalQty,
                    'total_amount' => $totalAmount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get Distributor Orders Error', ['error' => $e->getMessage(), 'distributor_id' => $distributorId]);
            return response()->json(['error' => 'Failed to fetch order details.'], 500);
        }
    }
    public function getDistributorTeams(Request $request, $distributorId)
    {
        try {
            // --- Filters and Date Range ---
            $criteriaType = $request->input('criteria_type', 'dispatch');
            $timeSpan = $request->input('time_span', 'last_month');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            Log::info('Get Distributor Teams Request', [
                'distributor_id' => $distributorId,
                'criteria_type' => $criteriaType,
                'time_span' => $timeSpan,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'all_inputs' => $request->all()
            ]);

            $endDate = now()->endOfDay();
            $startDate = now();

            switch ($timeSpan) {
                case 'last_week':
                    $startDate = now()->subWeek()->startOfDay();
                    break;
                case 'last_month':
                    $startDate = now()->subMonth()->startOfDay();
                    break;
                case 'last_quarter':
                    $startDate = now()->subQuarter()->startOfDay();
                    break;
                case 'last_year':
                    $startDate = now()->subYear()->startOfDay();
                    break;
                case 'ytd':
                    $startDate = now()->startOfYear();
                    break;
                case 'custom':
                    if ($fromDate && $toDate) {
                        $startDate = Carbon::parse($fromDate)->startOfDay();
                        $endDate = Carbon::parse($toDate)->endOfDay();
                    }
                    break;
            }

            // Fetch all teams with creation and update timestamps
            $teams = DB::table('distributor_teams as dt')
                ->select('dt.id', 'dt.no_of_dealers', 'dt.status', 'dt.total_order_limit', 'dt.ordered_quantity', 'dt.remarks', 'dt.created_at', 'dt.updated_at')
                ->where('dt.distributor_id', $distributorId)
                ->orderBy('dt.created_at', 'asc') // Sort ascending for correct next team logic
                ->get();

            $teamDealerMap = [];
            if ($teams->isNotEmpty()) {
                foreach ($teams as $team) {
                    $dealerAssignments = DB::table('distributor_team_dealers as dtd')
                        ->select('dtd.dealer_id')
                        ->where('dtd.distributor_team_id', $team->id)
                        ->get()
                        ->pluck('dealer_id')
                        ->toArray();
                    $teamDealerMap[$team->id] = $dealerAssignments;
                }
            }

            if ($teams->isNotEmpty()) {
                foreach ($teams as $index => $team) {
                    $teamStartDate = Carbon::parse($team->created_at);
                    $nextTeamStart = ($index < $teams->count() - 1) ? Carbon::parse($teams[$index + 1]->created_at) : $endDate;
                    $teamEndDate = ($team->status === 'Suspended' && $team->updated_at)
                        ? Carbon::parse($team->updated_at)
                        : $nextTeamStart;

                    $dealerIds = $teamDealerMap[$team->id];
                    $team->dealers = DB::table('dealers as d')
                        ->select('d.id', 'd.name', 'd.code')
                        ->whereIn('d.id', $dealerIds)
                        ->get();

                    // Calculate individual qty and total orders/dispatches for dealers
                    $dealerOrderData = [];
                    foreach ($dealerIds as $dealerId) {
                        if ($criteriaType === 'order') {
                            $orderData = DB::table('order_allocations as oa')
                                ->join('orders as o', 'o.id', '=', 'oa.order_id')
                                ->where('oa.allocated_to_type', 'dealer')
                                ->where('oa.allocated_to_id', $dealerId)
                                // ->where('o.status', '!=', 'pending')
                                ->whereNotIn('o.status', ['pending', 'rejected'])
                                ->where('o.type', 'distributor')
                                ->where('o.placed_by_distributor_id', $distributorId)
                                ->whereBetween('o.created_at', [$startDate, $endDate]) // Time span filter on created_at
                                ->where('o.created_at', '>=', $teamStartDate)
                                ->where('o.created_at', '<=', $teamEndDate)
                                ->select('oa.allocated_to_id', DB::raw('SUM(oa.qty) as individual_qty'), DB::raw('COUNT(DISTINCT o.id) as total_orders'))
                                ->groupBy('oa.allocated_to_id')
                                ->first();
                        } else {
                            $orderData = DB::table('dispatch_items as di')
                                ->join('dispatches as disp', 'disp.id', '=', 'di.dispatch_id')
                                ->join('order_allocations as oa', 'oa.id', '=', 'di.allocation_id')
                                ->join('orders as o', 'o.id', '=', 'di.order_id')
                                ->where('disp.type', 'distributor')
                                ->where('disp.distributor_id', $distributorId)
                                ->where('disp.status', 'approved')
                                ->where('oa.allocated_to_type', 'dealer')
                                ->where('oa.allocated_to_id', $dealerId)
                                ->where('o.placed_by_distributor_id', $distributorId)
                                ->whereBetween('o.created_at', [$startDate, $endDate]) // Time span filter on created_at
                                ->where('o.created_at', '>=', $teamStartDate)
                                ->where('o.created_at', '<=', $teamEndDate)
                                ->select('oa.allocated_to_id', DB::raw('SUM(di.dispatch_qty) as individual_qty'), DB::raw('COUNT(DISTINCT disp.id) as total_orders'))
                                ->groupBy('oa.allocated_to_id')
                                ->first();
                        }

                        $dealerOrderData[$dealerId] = $orderData ?: (object)['individual_qty' => 0, 'total_orders' => 0];
                        Log::info('Dealer Data', [
                            'team_id' => $team->id,
                            'dealer_id' => $dealerId,
                            'distributor_id' => $distributorId,
                            'criteria_type' => $criteriaType,
                            'data' => $dealerOrderData[$dealerId],
                            'team_period' => ['start' => $teamStartDate->toDateTimeString(), 'end' => $teamEndDate->toDateTimeString()]
                        ]);
                    }

                    // Assign dealer data
                    foreach ($team->dealers as $dealer) {
                        $data = $dealerOrderData[$dealer->id] ?? (object)['individual_qty' => 0, 'total_orders' => 0];
                        $dealer->individual_qty = $data->individual_qty ?? 0;
                        $dealer->total_orders = $data->total_orders ?? 0;
                    }

                    // Distributor quantity for this team's orders
                    if ($criteriaType === 'order') {
                        $distributorOrderData = DB::table('order_allocations as oa')
                            ->join('orders as o', 'o.id', '=', 'oa.order_id')
                            ->where('oa.allocated_to_type', 'distributor')
                            ->where('oa.allocated_to_id', $distributorId)
                            // ->where('o.status', '!=', 'pending')
                            ->whereNotIn('o.status', ['pending', 'rejected'])
                            ->where('o.type', 'distributor')
                            ->where('o.placed_by_distributor_id', $distributorId)
                            ->whereBetween('o.created_at', [$startDate, $endDate]) // Time span filter on created_at
                            ->where('o.created_at', '>=', $teamStartDate)
                            ->where('o.created_at', '<=', $teamEndDate)
                            ->select(DB::raw('SUM(oa.qty) as individual_qty'), DB::raw('COUNT(DISTINCT o.id) as total_orders'))
                            ->first();
                    } else {
                        $distributorOrderData = DB::table('dispatch_items as di')
                            ->join('dispatches as disp', 'disp.id', '=', 'di.dispatch_id')
                            ->join('order_allocations as oa', 'oa.id', '=', 'di.allocation_id')
                            ->join('orders as o', 'o.id', '=', 'di.order_id')
                            ->where('disp.type', 'distributor')
                            ->where('disp.distributor_id', $distributorId)
                            ->where('disp.status', 'approved')
                            ->where('oa.allocated_to_type', 'distributor')
                            ->where('oa.allocated_to_id', $distributorId)
                            ->where('o.placed_by_distributor_id', $distributorId)
                            ->whereBetween('o.created_at', [$startDate, $endDate]) // Time span filter on created_at
                            ->where('o.created_at', '>=', $teamStartDate)
                            ->where('o.created_at', '<=', $teamEndDate)
                            ->select(DB::raw('SUM(di.dispatch_qty) as individual_qty'), DB::raw('COUNT(DISTINCT disp.id) as total_orders'))
                            ->first();
                    }

                    $team->individual_qty = $distributorOrderData ? $distributorOrderData->individual_qty : 0;
                    $team->total_orders = $distributorOrderData ? $distributorOrderData->total_orders : 0;

                    Log::info('Team Data', [
                        'team_id' => $team->id,
                        'distributor_id' => $distributorId,
                        'criteria_type' => $criteriaType,
                        'distributor_qty' => $team->individual_qty,
                        'total_orders' => $team->total_orders,
                        'dealers' => $team->dealers->map(function ($dealer) {
                            return ['id' => $dealer->id, 'qty' => $dealer->individual_qty];
                        })->all(),
                        'team_period' => ['start' => $teamStartDate->toDateTimeString(), 'end' => $teamEndDate->toDateTimeString()]
                    ]);
                }
            }

            // Distributor's basic details and performance (total across all teams)
            $distributor = DB::table('distributors')->where('id', $distributorId)->first(['id', 'name', 'code']);
            if ($distributor) {
                if ($criteriaType === 'order') {
                    $distributorPerformance = DB::table('order_allocations as oa')
                        ->join('orders as o', 'o.id', '=', 'oa.order_id')
                        ->selectRaw('SUM(oa.qty) as individual_qty, COUNT(DISTINCT o.id) as total_orders')
                        ->where('oa.allocated_to_type', 'distributor')
                        ->where('oa.allocated_to_id', $distributorId)
                        // ->where('o.status', '!=', 'pending')
                        ->whereNotIn('o.status', ['pending', 'rejected'])
                        ->where('o.type', 'distributor')
                        ->where('o.placed_by_distributor_id', $distributorId)
                        ->whereBetween('o.created_at', [$startDate, $endDate]) // Time span filter on created_at
                        ->first();
                } else {
                    $distributorPerformance = DB::table('dispatch_items as di')
                        ->join('dispatches as disp', 'disp.id', '=', 'di.dispatch_id')
                        ->join('order_allocations as oa', 'oa.id', '=', 'di.allocation_id')
                        ->join('orders as o', 'o.id', '=', 'di.order_id')
                        ->selectRaw('SUM(di.dispatch_qty) as individual_qty, COUNT(DISTINCT disp.id) as total_orders')
                        ->where('disp.type', 'distributor')
                        ->where('disp.distributor_id', $distributorId)
                        ->where('disp.status', 'approved')
                        ->where('oa.allocated_to_type', 'distributor')
                        ->where('oa.allocated_to_id', $distributorId)
                        ->where('o.placed_by_distributor_id', $distributorId)
                        ->whereBetween('o.created_at', [$startDate, $endDate]) // Time span filter on created_at
                        ->groupBy('disp.distributor_id')
                        ->first();
                }

                $distributor->individual_qty = $distributorPerformance ? $distributorPerformance->individual_qty : 0;
                $distributor->total_orders = $distributorPerformance ? $distributorPerformance->total_orders : 0;
                Log::info('Distributor Performance', [
                    'distributor_id' => $distributorId,
                    'criteria_type' => $criteriaType,
                    'performance' => $distributorPerformance ? (array)$distributorPerformance : ['individual_qty' => 0, 'total_orders' => 0]
                ]);
            }

            Log::info('Distributor Teams Response', [
                'distributorId' => $distributorId,
                'criteria_type' => $criteriaType,
                'teams' => $teams->toArray(),
                'distributor' => $distributor,
                'start_date' => $startDate->toDateTimeString(),
                'end_date' => $endDate->toDateTimeString()
            ]);

            return response()->json([
                'success' => true,
                'teams' => $teams,
                'distributor' => $distributor
            ]);
        } catch (\Exception $e) {
            Log::error('Get Distributor Teams Error', [
                'error' => $e->getMessage(),
                'distributor_id' => $distributorId,
                'sql' => DB::getQueryLog(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch team details.'], 500);
        }
    }
}
