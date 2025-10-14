<?php

namespace App\Traits;

use App\Repository\FileRepository;
use Illuminate\Support\Str;

trait NormalizesRemoteUrl
{
    /**
     * Normalize stored remote file references so they can be rendered safely over HTTPS.
     */
    protected function normalizeRemoteUrl(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        /** @var FileRepository $fileRepo */
        $fileRepo = app(FileRepository::class);

        if (Str::startsWith($value, '//')) {
            $value = 'https:' . $value;
        }

        if (!Str::startsWith($value, 'http')) {
            return $fileRepo->get(ltrim($value, '/'));
        }

        $host = parse_url($value, PHP_URL_HOST);

        if ($host && $host === $fileRepo->remoteHost()) {
            $relative = $fileRepo->extractRelativePathFromUrl($value);

            if ($relative) {
                return $fileRepo->get($relative);
            }
        }

        if (Str::startsWith($value, 'http://')) {
            $value = Str::replaceFirst('http://', 'https://', $value);
        }

        return $value;
    }
}
