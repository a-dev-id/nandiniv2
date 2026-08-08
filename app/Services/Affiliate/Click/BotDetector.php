<?php

namespace App\Services\Affiliate\Click;

class BotDetector
{
    /** @var array<string, array<int, string>> */
    private const PATTERNS = [
        'facebook' => ['facebookexternalhit', 'facebot', 'meta-externalagent', 'meta-externalfetcher'],
        'instagram' => ['instagram'],
        'whatsapp' => ['whatsapp'],
        'telegram' => ['telegrambot'],
        'x' => ['twitterbot'],
        'linkedin' => ['linkedinbot'],
        'googlebot' => ['googlebot', 'google-inspectiontool'],
        'bingbot' => ['bingbot', 'bingpreview'],
        'slack' => ['slackbot', 'slack-imgproxy'],
        'discord' => ['discordbot'],
    ];

    public function detect(?string $userAgent): BotDetectionResult
    {
        $normalized = mb_strtolower(trim((string) $userAgent));

        foreach (self::PATTERNS as $name => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($normalized, $pattern)) {
                    return new BotDetectionResult(true, $name);
                }
            }
        }

        if ($normalized !== '' && preg_match('/(?:bot|crawler|spider|headlesschrome|phantomjs|pingdom|uptime|monitoring|curl\/|wget\/)/i', $normalized)) {
            return new BotDetectionResult(true, 'other');
        }

        return new BotDetectionResult(false);
    }
}
