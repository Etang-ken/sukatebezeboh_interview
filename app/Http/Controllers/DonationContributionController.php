<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationContribution;
use Illuminate\Http\Request;

class DonationContributionController extends Controller
{
    public function store(Request $request, Donation $donation)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $contribution = DonationContribution::create([
            'donation_id' => $donation->id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
        ]);

        $donation->increment('collected_amount', $request->amount);

        return response()->json($contribution, 201);
    }
}
