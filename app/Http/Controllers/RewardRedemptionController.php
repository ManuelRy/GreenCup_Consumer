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
            // Include active rewards that are either valid now OR coming soon (but not expired)
            $validRewards = $seller->rewards->filter(function($reward) {
                return $reward->is_active
                    && !$reward->isExpired()
                    && $reward->remaining_stock > 0;
            });

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

    public function redeem(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $quantity = $validated['quantity'];

            DB::beginTransaction();
            // get the reward
            $reward = $this->rRepo->get($id);
            if (!$reward || !$reward->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reward is not available or has expired'
                ], 404);
            }

            // Check if reward has started
            if (!$reward->hasStarted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This reward has not started yet. It will be available ' . $reward->time_until_start
                ], 400);
            }

            // Check if reward can accommodate the requested quantity
            if (!$reward->canRedeemQuantity($quantity)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Only ' . $reward->remaining_stock . ' items available'
                ], 400);
            }

            $consumer_id = Auth::id();
            $seller_id = $reward->seller_id;
            $total_points = $reward->points_required * $quantity;

            // check if the consumer has enough points
            $cp = $this->cPRepo->getByConsumerAndSeller($consumer_id, $seller_id);
            if ($cp->coins < $total_points) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient points. You need ' . number_format($total_points) . ' points but only have ' . number_format($cp->coins)
                ], 400);
            }

            // deduct the coins from consumer
            $this->cPRepo->redeem($consumer_id, $seller_id, $total_points, $reward);
            // add the quantity redeemed to the reward model
            $this->rRepo->redeem($reward->id, $quantity);
            // create a new redeem history
            $this->rRepo->createHistory($consumer_id, $reward->id, $quantity);
            // NOTE: Points will be added to seller only when they approve the redemption
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Successfully redeemed {$quantity} item(s) for {$total_points} points",
                'quantity' => $quantity,
                'points_spent' => $total_points,
                'remaining_balance' => $cp->coins - $total_points,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reward redemption error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing redemption: ' . $e->getMessage()
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
