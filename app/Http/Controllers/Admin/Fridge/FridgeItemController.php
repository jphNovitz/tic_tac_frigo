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
}
