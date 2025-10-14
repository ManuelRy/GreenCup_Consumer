<?php

namespace App\Repository;

use Illuminate\Support\Facades\Http;

class FileRepository
{
  public function host(): string
  {
    return 'https://188.166.186.208';
  }

  public function base(): string
  {
    return $this->host() . '/api/files';
  }

  public function get($path): string
  {
    return $this->base() . '/get/' . $path;
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
