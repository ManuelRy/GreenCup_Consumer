<?php

namespace App\Http\Controllers;

use App\Repository\FileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FileProxyController extends Controller
{
    public function __construct(private readonly FileRepository $files)
    {
    }

    public function show(Request $request, string $path)
    {
        if (str_contains($path, '..')) {
            abort(404);
        }

        $remoteUrl = $this->files->directGet($path);

        $response = Http::withoutVerifying()->get($remoteUrl);

        if ($response->failed()) {
            abort($response->status() ?: 404, 'Unable to fetch file.');
        }

        $headers = [
            'Content-Type' => $response->header('Content-Type') ?? 'application/octet-stream',
            'Cache-Control' => 'public, max-age=604800',
        ];

        if ($length = $response->header('Content-Length')) {
            $headers['Content-Length'] = $length;
        }

        return response($response->body(), 200, $headers);
    }
}
