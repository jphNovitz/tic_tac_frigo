<?php

namespace App\Http\Controllers\Admin\Fridge;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFridgeItemRequest;
use App\Models\FridgeItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FridgeItemController extends Controller
{
    public function index(): View
    {

        $fridgeItems = Auth::user()->fridgeItems()
            ->whereNull('consumed_at')
            ->orderBy('expires_at', 'asc')
            ->get();

        return view('admin.fridge.index', compact('fridgeItems'));
    }

    public function create(): View
    {
        return view('admin.fridge.create-form');
    }

    public function store(StoreFridgeItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;

        FridgeItem::create($data);

        return redirect()->route('dashboard')->with('status', 'fridge.item_added');
    }

    public function edit(FridgeItem $fridgeItem): View
    {
        if ($fridgeItem->user_id !== Auth::id()) {
            abort(404, 'Not Found');
        }

        return view('admin.fridge.edit-form', compact('fridgeItem'));
    }

    public function update(StoreFridgeItemRequest $request, FridgeItem $fridgeItem): RedirectResponse
    {
        if ($fridgeItem->user_id !== Auth::id()) {
            abort(404, 'Not Found');
        }

        $data = $request->validated();
        $fridgeItem->update($data);

        return redirect()->route('fridge-items.index')
            ->with('status', 'fridge.item_updated');
    }

    public function consume(FridgeItem $fridgeItem): RedirectResponse
    {
        if ($fridgeItem->user_id !== Auth::id()) {
            abort(404, 'Not Found');
        }

        $fridgeItem->update(['consumed_at' => now()]);

        return redirect()->route('fridge-items.index')->with('status', 'fridge.item_consumed');
    }

    public function increase(FridgeItem $fridgeItem): RedirectResponse
    {
        if ($fridgeItem->user_id !== Auth::id()) {
            abort(404, 'Not Found');
        }

        $fridgeItem->increment('unit_count', 1);

        return redirect()->route('fridge-items.index')->with('status', 'fridge.item_increased');
    }

    public function decrease(FridgeItem $fridgeItem): RedirectResponse
    {
        if ($fridgeItem->user_id !== Auth::id()) {
            abort(404, 'Not Found');
        }

        $decreased = FridgeItem::query()
            ->whereKey($fridgeItem->getKey())
            ->where('unit_count', '>', 1)
            ->decrement('unit_count');

        $status = $decreased === 1
            ? 'fridge.item_decreased'
            : 'fridge.item_cannot_decrease';

        return redirect()->route('fridge-items.index')->with('status', $status);
    }

    public function destroy(FridgeItem $fridgeItem): RedirectResponse
    {
        if ($fridgeItem->user_id !== Auth::id()) {
            abort(404, 'Not Found');
        }

        $fridgeItem->delete();

        return redirect()->route('fridge-items.index')->with('status', 'fridge.item_deleted');
    }
}
