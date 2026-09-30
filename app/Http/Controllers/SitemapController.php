<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(Request $request, SitemapService $sitemap): Response
    {
        return response()
            ->view('sitemap', [
                'urls' => $sitemap->urls($request->getHost()),
            ], 200)
            ->header('Content-Type', 'application/xml');
    }
}
