<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\ConsumerPoint;
use App\Repository\ConsumerPointRepository;
use App\Repository\RewardRepository;
use App\Repository\SellerRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RewardRedemptionController extends Controller
{
    private RewardRepository $rRepo;
    private ConsumerPointRepository $cPRepo;
    private SellerRepository $sRepo;

    public function __construct(RewardRepository $rRepo, ConsumerPointRepository $cPRepo, SellerRepository $sRepo)
    {
        $this->rRepo = $rRepo;
        $this->cPRepo = $cPRepo;
        $this->sRepo = $sRepo;
    }
    public function myRewards()
    {
        $redemptions = $this->rRepo->history(Auth::id());
        return view('reward-redemption.my', compact('redemptions'));
    }
    public function index()
    {
        $sellers = $this->sRepo->list();
        return view('reward-redemption.index', compact('sellers'));
    }

    public function redeem($id)
    {
        try {
            DB::beginTransaction();
            // get the reward
            $reward = $this->rRepo->get($id);
            if (!$reward || !$reward->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not found'
                ], 404);
            }
            $consumer_id = Auth::id();
            $seller_id = $reward->seller_id;
            $reward_points = $reward->points_per_unit;
            // check if the reward point is enough to redeem
            $cp = $this->cPRepo->getByConsumerAndSeller($consumer_id, $seller_id);
            if ($cp->coins < $reward_points) {
                if (!$reward) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot redeem'
                    ], 404);
                }
            }
            // deduct the coins from consumer
            $this->cPRepo->redeem($consumer_id, $seller_id, $reward_points);
            // add the quantity redeemed to the reward model
            $this->rRepo->redeem($reward->id, 1);
            // create a new redeem history
            $this->rRepo->createHistory($consumer_id, $reward->id);
            // add the deducted coins to seller
            // $this->sRepo->addPoints($seller_id, $reward_points);
            DB::commit();
            return response()->json([
                'success' => true,
                'receipt' => "Redeem successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error checking reward'
            ], 500);
        }
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
