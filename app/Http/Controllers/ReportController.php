<?php

namespace App\Http\Controllers;

use App\Repository\ReportRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    private ReportRepository $rRepo;

    public function __construct(ReportRepository $rRepo)
    {
        $this->rRepo = $rRepo;
    }
    public function index()
    {
        return view('report.index');
    }

    public function store(Request $request)
    {
        try {
            //code...
            $request->validate([
                'title' => 'required|string|max:100',
                'priority' => 'required|string', // adjust values to match Report::$priorities
                'tag' => 'required|string|max:50',
                'description' => 'required|string|max:1000',
                'image' => 'nullable|image|max:5120',
            ]);

            $report = $this->rRepo->create([
                'title'        => $request->title,
                'priority'     => $request->priority,
                'tag'           => $request->tag,
                'description'  => $request->description,
                'reporter_id'  => Auth::id(),
            ]);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('report_evidences', 'public');
                $this->rRepo->createEvidence([
                    'report_id' => $report->id,
                    'file_url'  => Storage::url($path),
                ]);
            }

            return redirect()->route('report.index')->with('success', 'Report created successfully!');
        } catch (\Throwable $th) {
            dd($th  );
            abort(500, 'Something went wrong');
        }
    }
}
