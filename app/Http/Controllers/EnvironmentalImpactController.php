<?php

namespace App\Http\Controllers;

use App\Repository\ConsumerPointRepository;
use App\Repository\PendingTransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnvironmentalImpactController extends Controller
{
    private PendingTransactionRepository $pTRepo;
    private ConsumerPointRepository $cPRepo;
    public function __construct(ConsumerPointRepository $cPRepo, PendingTransactionRepository $pTRepo)
    {
        $this->pTRepo = $pTRepo;
        $this->cPRepo = $cPRepo;
    }

    public function index()
    {
        $consumer_id = Auth::id();
        $total_cups = $this->pTRepo->totalQuantityByConsumer($consumer_id);
        $store_visited = $this->cPRepo->countByConsumerId($consumer_id);
        return view('environmental-impact.index', compact(['total_cups', 'store_visited']));
    }
}
