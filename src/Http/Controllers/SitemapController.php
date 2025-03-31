<?php

namespace VeiligLanceren\LaravelSeoSitemap\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SitemapController
{
    /**
     * Return the sitemap.xml file stored on disk.
     *
     * @return Response
     */
    public function index(): Response
    {
        $path = config('sitemap.file.path');
        $disk = config('sitemap.file.disk');

        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        $xml = Storage::disk($disk)->get($path);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}