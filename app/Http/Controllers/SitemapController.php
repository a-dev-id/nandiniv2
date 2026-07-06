<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(SitemapService $sitemap): Response
    {
        return response()
            ->view('sitemap', [
                'urls' => $sitemap->urls(),
            ], 200)
            ->header('Content-Type', 'application/xml');
    }
}
