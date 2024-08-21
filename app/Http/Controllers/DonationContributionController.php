<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationContribution;
use Illuminate\Http\Request;

class DonationContributionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/donations/{donation}/contributions",
     *     summary="Create a new contribution for a specific donation",
     *     tags={"Contributions"},
     *     @OA\Parameter(
     *         name="donation",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the donation to contribute to"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", format="float", example="50.00", description="Amount of the contribution")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contribution created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="donation_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example="50.00"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The amount field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - donation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Donation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - user not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
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
