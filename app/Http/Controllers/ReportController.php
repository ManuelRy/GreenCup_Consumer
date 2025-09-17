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

    public function list()
    {
        $reports = $this->rRepo->getByReporterId(Auth::id());
        return view('report.list', compact('reports'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:100',
                'priority' => 'required|string', // adjust values to match Report::$priorities
                'tag' => 'required|string|max:50',
                'description' => 'required|string|max:1000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB = 5120KB
            ], [
                'image.max' => 'The image size must not exceed 5MB.',
                'image.image' => 'The uploaded file must be a valid image.',
                'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
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

            return redirect()->route('report.list')->with('success', 'Report created successfully!');
        } catch (\Throwable $th) {
            return back()->withErrors(['error' => 'Something went wrong while submitting your report. Please try again.'])->withInput();
        }
    }
}
