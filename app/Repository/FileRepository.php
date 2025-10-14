<?php

namespace App\Repository;

use Illuminate\Support\Facades\Http;

class FileRepository
{
    public function host(): string
    {
        return rtrim(config('services.files.host', 'http://188.166.186.208'), '/');
    }

    public function base(): string
    {
        return $this->host() . '/api/files';
    }

    public function directGet(string $path): string
    {
        return rtrim($this->base(), '/') . '/get/' . ltrim($path, '/');
    }

    public function get(?string $path): string
    {
        $path = ltrim((string) $path, '/');

        if ($path === '') {
            return '';
        }

        if ($this->shouldProxy() && app()->bound('router') && app('router')->has('files.proxy')) {
            return route('files.proxy', ['path' => $path]);
        }

        return $this->directGet($path);
    }

    public function extractRelativePathFromUrl(string $url): ?string
    {
        $parts = parse_url($url);

        if (!$parts || !isset($parts['path'])) {
            return null;
        }

        $path = ltrim($parts['path'], '/');
        $prefix = 'api/files/get/';

        if (str_starts_with($path, $prefix)) {
            return substr($path, strlen($prefix));
        }

        return $path;
    }

    public function remoteHost(): ?string
    {
        return parse_url($this->host(), PHP_URL_HOST);
    }

    protected function shouldProxy(): bool
    {
        return (bool) config('services.files.use_proxy', true);
    }

    public function upload($folder, $file)
    {
        return Http::attach(
            'file',
            fopen($file->getRealPath(), 'r'),
            $file->getClientOriginalName()
        )->post($this->base() . '/upload', [
            'folder' =>$folder
        ]);
    }
}
