<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\ConsumerPoint;
use Illuminate\Support\Facades\Auth;

class RewardRedemptionController extends Controller
{
    public function myRewards()
    {
        // Example: Fetch user's redeemed rewards (replace with real logic)
        $consumer = Auth::guard('consumer')->user();
        // You should have a model/table for redemptions, here is a placeholder:
        $redemptions = [];
        // If you have a Redemption model, use:
        // $redemptions = Redemption::where('consumer_id', $consumer->id)->latest()->get();
        return view('reward-redemption.my', compact('redemptions'));
    }
    public function index()
    {
        // Get all sellers with rewards (assuming rewards are items for now)
        $sellers = Seller::with(['qrCodes.item'])->get();

        // Get the authenticated consumer
        $consumer = Auth::guard('consumer')->user();

        // Get all wallet balances for this consumer (per shop)
        $wallets = ConsumerPoint::where('consumer_id', $consumer->id)->get()->keyBy('seller_id');

        // Prepare data: group rewards (items) by shop
        $shops = [];
        foreach ($sellers as $seller) {
            // Get all items for this seller via QR codes (if any)
            $items = $seller->qrCodes->pluck('item')->filter();
            if ($items->isEmpty()) continue;

            $shops[] = [
                'seller' => $seller,
                'wallet' => $wallets[$seller->id]->coins ?? 0,
                'rewards' => $items->unique('id')->values(),
            ];
        }

        return view('reward-redemption.index', compact('shops'));
    }

    public function redeem($reward)
    {
        // TODO: Implement logic to show redeem page for a reward
        return view('reward-redemption.redeem');
    }

    public function process(Request $request, $reward)
    {
        // TODO: Implement logic to process reward redemption
        return redirect()->route('reward.index');
    }

    public function history()
    {
        // TODO: Implement logic to show redemption history
        return view('reward-redemption.history');
    }

    public function show($redemption)
    {
        // TODO: Implement logic to show a specific redemption
        return view('reward-redemption.show');
    }

    public function search(Request $request)
    {
        // TODO: Implement logic to search rewards
        return response()->json([]);
    }

    public function filter(Request $request)
    {
        // TODO: Implement logic to filter rewards
        return response()->json([]);
    }

    public function checkAvailability($reward)
    {
        // TODO: Implement logic to check reward availability
        return response()->json(['available' => true]);
    }
}
