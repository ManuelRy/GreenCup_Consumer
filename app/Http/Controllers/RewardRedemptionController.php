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
use Illuminate\Support\Facades\Log;

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
    public function index(Request $request)
    {
        $search = $request->get('search');
        $pointsRange = $request->get('points_range');
        $sortBy = $request->get('sort', 'newest');

        $sellers = $this->sRepo->list();

        // Get all valid rewards with seller information
        $allRewards = collect();

        foreach ($sellers as $seller) {
            $validRewards = $seller->rewards->filter->isValid();

            // Apply search filter for both reward name and shop name
            if ($search) {
                $validRewards = $validRewards->filter(function ($reward) use ($search, $seller) {
                    return stripos($reward->name, $search) !== false ||
                           stripos($seller->business_name, $search) !== false;
                });
            }

            // Apply points range filter
            if ($pointsRange) {
                $validRewards = $validRewards->filter(function ($reward) use ($pointsRange) {
                    if ($pointsRange === '0-100') {
                        return $reward->points_required >= 0 && $reward->points_required <= 100;
                    } elseif ($pointsRange === '101-500') {
                        return $reward->points_required >= 101 && $reward->points_required <= 500;
                    } elseif ($pointsRange === '501-1000') {
                        return $reward->points_required >= 501 && $reward->points_required <= 1000;
                    } elseif ($pointsRange === '1001+') {
                        return $reward->points_required >= 1001;
                    }
                    return true;
                });
            }

            // Add seller information to each reward
            foreach ($validRewards as $reward) {
                $reward->seller_info = $seller;
                $allRewards->push($reward);
            }
        }

        // Apply sorting
        switch ($sortBy) {
            case 'points-low':
                $allRewards = $allRewards->sortBy('points_required');
                break;
            case 'points-high':
                $allRewards = $allRewards->sortByDesc('points_required');
                break;
            case 'popular':
                $allRewards = $allRewards->sortByDesc('quantity_redeemed');
                break;
            case 'newest':
            default:
                $allRewards = $allRewards->sortByDesc('created_at');
                break;
        }

        // Group rewards back by seller but maintain sort order
        $groupedRewards = $allRewards->groupBy('seller_id');
        $sellers = $sellers->map(function ($seller) use ($groupedRewards) {
            if ($groupedRewards->has($seller->id)) {
                $seller->setRelation('rewards', $groupedRewards[$seller->id]->values());
            } else {
                $seller->setRelation('rewards', collect());
            }
            return $seller;
        })->filter(function ($seller) {
            return $seller->rewards->isNotEmpty();
        })->values(); // Reset the collection keys

        if ($request->ajax()) {
            // Debug: Log what we're sending
           Log::info('AJAX Response - Sellers count: ' . $sellers->count());
            foreach ($sellers as $seller) {
               Log::info('Seller: ' . $seller->business_name . ' - Rewards: ' . $seller->rewards->count());
                foreach ($seller->rewards as $reward) {
                   Log::info('  - Reward: ' . $reward->id . ' - ' . $reward->name);
                }
            }

            return response()->json([
                'html' => view('reward-redemption.partials.rewards-grid', compact('sellers'))->render()
            ]);
        }

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
