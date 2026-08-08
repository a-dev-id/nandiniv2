<?php

namespace App\Services\Affiliate\Click;

final readonly class BotDetectionResult
{
    public function __construct(
        public bool $isBot,
        public ?string $name = null,
    ) {}
}
