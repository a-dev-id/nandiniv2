<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $contents = implode("\n", [
            'User-agent: *',
            'Disallow:',
            '',
            'Sitemap: '.$request->getSchemeAndHttpHost().'/sitemap.xml',
            '',
        ]);

        return response($contents, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
