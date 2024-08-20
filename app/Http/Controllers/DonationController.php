<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index()
    {
        return response()->json(['donations' => Donation::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'target_amount' => 'required|numeric',
        ]);

        $donation = Donation::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
        ]);

        return response()->json($donation, 201);
    }

    public function updateStatus(Donation $donation)
    {
        if ($donation->collected_amount >= $donation->target_amount) {
            $donation->update(['status' => 'complete']);
        }

        return response()->json($donation);
    }
}
