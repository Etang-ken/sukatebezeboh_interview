<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/donations",
     *     summary="Retrieve a list of all donations",
     *     @OA\Response(
     *         response=200,
     *         description="List of donations",
     *         @OA\JsonContent(
     *             @OA\Property(property="donations", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(['donations' => Donation::all()]);
    }

    /**
     * @OA\Post(
     *     path="/api/donations",
     *     summary="Create a new donation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "target_amount"},
     *             @OA\Property(property="title", type="string", example="Help the Needy"),
     *             @OA\Property(property="description", type="string", example="Donate to help the less fortunate."),
     *             @OA\Property(property="target_amount", type="number", format="float", example="5000.00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Donation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="donation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:0',
        ]);

        $donation = Donation::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
        ]);

        return response()->json($donation, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/donations/{donation_id}",
     *     summary="Retrieve details of a specific donation",
     *     @OA\Parameter(
     *         name="donation_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the donation to retrieve"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Donation details",
     *         @OA\JsonContent(
     *             @OA\Property(property="donation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Donation not found"
     *     )
     * )
     */
    public function show($donation_id)
    {
        return response()->json(Donation::findOrFail($donation_id));
    }

    /**
     * @OA\Patch(
     *     path="/api/donations/{donation_id}/status",
     *     summary="Update the status of a donation if the target amount is reached",
     *     @OA\Parameter(
     *         name="donation_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the donation to update"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Donation status updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="donation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Donation not found"
     *     )
     * )
     */
    public function updateStatus(Donation $donation)
    {
        if ($donation->collected_amount >= $donation->target_amount) {
            $donation->update(['status' => 'complete']);
        }

        return response()->json($donation);
    }
}
