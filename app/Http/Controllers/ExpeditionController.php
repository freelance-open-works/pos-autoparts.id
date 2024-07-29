<?php

namespace App\Http\Controllers;

use App\Models\Expedition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ExpeditionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Expedition::query();

        if ($request->q) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        $query->orderBy('created_at', 'desc');

        return inertia('Expedition/Index', [
            'data' => $query->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        Expedition::create([
            'name' => $request->name,
            'address' => $request->address,
        ]);

        return redirect()->route('expeditions.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function update(Request $request, Expedition $expedition): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $expedition->fill([
            'name' => $request->name,
            'address' => $request->address,
        ]);

        $expedition->save();

        return redirect()->route('expeditions.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function destroy(Expedition $expedition): RedirectResponse
    {
        $expedition->delete();

        return redirect()->route('expeditions.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed deleted']);
    }
}
