<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemSize;
use Illuminate\Validation\Rule;

class ItemSizesController extends Controller
{
    // public function index()
    // {
    //     // $itemSizes = ItemSize::where('status', 'Active')->orderBy('size')->select('size', 'rate')->get();
    //     $itemSizes = ItemSize::where('status', 'Active')
    //                      ->orderBy('size') // Aapka order pehle se hai
    //                      ->select('size', 'rate')
    //                      ->paginate(10);
    //     return response()->json($itemSizes);
    // }

    // public function index(Request $request)
    // {
    //     // ===================================================================
    //     // --- Step 1: Pagination aur Search parameters ---
    //     // ===================================================================

    //     // 'per_page' optional hai. Default 10 rakha hai (aapke code ke hisaab se).
    //     $perPage = $request->input('per_page', 10);

    //     // --- Step 2: Query Banayein ---
    //     $query = ItemSize::where('status', 'Active');

    //     // --- Step 3: Optional Search Filter ---
    //     // Yeh 'size' column par search karega
    //     if ($request->filled('search')) {
    //         // Hum 'like' use kar rahe hain taaki '10' search karne par '10.5' bhi mil sake
    //         $query->where('size', 'like', '%' . $request->input('search') . '%');
    //     }

    //     // --- Step 4: Sorting aur Selecting ---
    //     $query->orderBy('size')
    //         ->select('size', 'rate');

    //     // --- Step 5: Paginate karein ---
    //     // 'page' parameter Laravel khud handle kar lega
    //     $paginator = $query->paginate($perPage);

    //     // ===================================================================
    //     // --- Step 6: Custom JSON Response (jaisa aapne pehle maanga tha) ---
    //     // ===================================================================
    //     return response()->json([
    //         'status'       => true,
    //         'data'         => $paginator->items(),
    //         'current_page' => $paginator->currentPage(),
    //         'per_page'     => (int) $paginator->perPage(),
    //         'total'        => $paginator->total(),
    //         'total_pages'  => $paginator->lastPage()
    //     ], 200);
    // }

    public function index(Request $request)
    {
        // ===================================================================
        // --- Step 1: Pagination aur Search parameters ---
        // ===================================================================

        // 'per_page' optional hai. Default 10 rakha hai (aapke code ke hisaab se).
        $perPage = $request->input('per_page', 10);

        // --- Step 2: Query Banayein ---
        $query = ItemSize::where('status', 'Active');

        // --- Step 3: Optional Search Filter ---
        // Yeh 'size' column par search karega
        if ($request->filled('search')) {
            // Hum 'like' use kar rahe hain taaki '10' search karne par '10.5' bhi mil sake
            $query->where('size', 'like', '%' . $request->input('search') . '%');
        }

        // --- Step 4: Sorting aur Selecting ---
        $query->orderBy('size')
              // --- YAHAN CHANGE KIYA GAYA HAI ---
              ->select('id', 'size', 'rate'); 
              // --- PEHLE YEH THA: ->select('size', 'rate'); ---

        // --- Step 5: Paginate karein ---
        // 'page' parameter Laravel khud handle kar lega
        $paginator = $query->paginate($perPage);

        // ===================================================================
        // --- Step 6: Custom JSON Response (jaisa aapne pehle maanga tha) ---
        // ===================================================================
        return response()->json([
            'status'     => true,
            'data'       => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'per_page'   => (int) $paginator->perPage(),
            'total'      => $paginator->total(),
            'total_pages'  => $paginator->lastPage()
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'hsn_code' => 'nullable|string|max:100',
            'size' => [
                'required',
                'numeric',
                Rule::unique('item_sizes')->where(fn($query) => $query->where('item', $request->item)),
            ],
            'rate' => 'required|numeric',
            'remarks' => 'nullable|string|max:255',
        ]);

        $validated['status'] = 'Active';

        $itemSize = ItemSize::create($validated);

        return response()->json([
            'message' => 'Item size added successfully.',
            'data' => $itemSize
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $itemSize = ItemSize::findOrFail($id);

        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'hsn_code' => 'nullable|string|max:100',
            'size' => [
                'required',
                'numeric',
                Rule::unique('item_sizes')->ignore($itemSize->id)->where(fn($query) => $query->where('item', $request->item)),
            ],
            'rate' => 'required|numeric',
            'remarks' => 'nullable|string|max:255',
        ]);

        $itemSize->update($validated);

        return response()->json([
            'message' => 'Item size updated successfully.',
            'data' => $itemSize
        ]);
    }

    public function show($id)
    {
        $itemSize = ItemSize::findOrFail($id);
        return response()->json($itemSize);
    }

    public function markInactive($id)
    {
        $itemSize = ItemSize::findOrFail($id);
        $itemSize->update(['status' => 'Inactive']);

        return response()->json(['message' => 'Item marked as inactive successfully.']);
    }

    public function inactiveItemSizes()
    {
        $itemSizes = ItemSize::where('status', 'Inactive')->orderBy('size')->get();
        return response()->json($itemSizes);
    }

    public function activateItemSize($id)
    {
        $itemSize = ItemSize::findOrFail($id);
        $itemSize->update(['status' => 'Active']);

        return response()->json(['message' => 'Item marked as Active successfully.']);
    }
}
