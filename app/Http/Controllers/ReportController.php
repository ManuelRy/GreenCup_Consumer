<?php

namespace App\Http\Controllers;

use App\Repository\FileRepository;
use App\Repository\ReportRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    private ReportRepository $rRepo;
    private FileRepository $fRepo;

    public function __construct(ReportRepository $rRepo, FileRepository $fRepo)
    {
        $this->rRepo = $rRepo;
        $this->fRepo = $fRepo;
    }

    public function create()
    {
        return view('report.index');
    }

    public function index()
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
                $file = $request->file('image');

                $response = $this->fRepo->upload($file);

                if ($response->successful()) {
                    $data = $response->json();

                    $this->rRepo->createEvidence([
                        'report_id' => $report->id,
                        'file_url'  => $this->fRepo->get($data['path'] ?? null),
                    ]);
                }
            }

            return redirect()->route('report.index')->with('success', 'Report created successfully!');
        } catch (\Throwable $th) {
            return back()->withErrors(['error' => 'Something went wrong while submitting your report. Please try again.'])->withInput();
        }
    }
}
