<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventories = Inventory::orderBy('item_name')->get();
        
        // Calculate statistics
        $totalItems = $inventories->count();
        $activeItems = $inventories->where('item_status', 'active')->count();
        $lowStockItems = $inventories->filter(function($item) {
            return $item->item_quantity <= $item->minimum_stock;
        })->count();
        $outOfStockItems = $inventories->where('item_status', 'out_of_stock')->count();
        
        return view('admin.inventory.index', compact(
            'inventories', 
            'totalItems', 
            'activeItems', 
            'lowStockItems', 
            'outOfStockItems'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.inventory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:255|unique:inventory,item_name',
            'item_quantity' => 'required|integer|min:0',
            'item_status' => 'required|in:active,inactive,out_of_stock',
            'description' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'minimum_stock' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Inventory::create($request->all());

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item "' . $request->item_name . '" has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        return view('admin.inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        return view('admin.inventory.edit', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:255|unique:inventory,item_name,' . $inventory->id,
            'item_quantity' => 'required|integer|min:0',
            'item_status' => 'required|in:active,inactive,out_of_stock',
            'description' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'minimum_stock' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $inventory->update($request->all());

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item "' . $inventory->item_name . '" has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $itemName = $inventory->item_name;
        $inventory->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item "' . $itemName . '" has been deleted successfully!');
    }
}
