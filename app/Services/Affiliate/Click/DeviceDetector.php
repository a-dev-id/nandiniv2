<?php

namespace App\Services\Affiliate\Click;

use Illuminate\Http\Request;

class DeviceDetector
{
    public function detect(Request $request, bool $isBot = false): string
    {
        if ($isBot) {
            return 'unknown';
        }

        $userAgent = mb_strtolower((string) $request->userAgent());
        $mobileHint = trim((string) $request->header('Sec-CH-UA-Mobile'));

        if (preg_match('/ipad|tablet|kindle|silk|playbook|android(?!.*mobile)/i', $userAgent)) {
            return 'tablet';
        }

        if ($mobileHint === '?1' || preg_match('/mobile|iphone|ipod|android|blackberry|opera mini|iemobile/i', $userAgent)) {
            return 'mobile';
        }

        if ($userAgent !== '' && preg_match('/mozilla|windows nt|macintosh|x11|linux|chrome|safari|firefox|edge|edg\//i', $userAgent)) {
            return 'desktop';
        }

        return 'unknown';
    }
}
