<?php

namespace App\Services\Voucher;

class VoucherTermsFormatter
{
    /**
     * @return array<int, array{title: string, html: string}>
     */
    public function sections(?string $html): array
    {
        $html = trim((string) $html);

        if ($html === '') {
            return $this->defaultSections();
        }

        $headingPattern = '~<h[1-6]\b[^>]*>\s*(?:<[^>]+>\s*)*(Usage Terms|Payment Terms)\s*(?:</[^>]+>\s*)*</h[1-6]\s*>~i';
        $parts = preg_split($headingPattern, $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if (! is_array($parts)) {
            return [['title' => 'Terms and Conditions', 'html' => $html]];
        }

        $sections = [];
        $currentTitle = null;

        foreach ($parts as $part) {
            $normalized = strtolower(trim(strip_tags($part)));

            if (in_array($normalized, ['usage terms', 'payment terms'], true)) {
                $currentTitle = $normalized === 'usage terms' ? 'Usage Terms' : 'Payment Terms';
                $sections[$currentTitle] ??= '';

                continue;
            }

            if ($currentTitle !== null && trim($part) !== '') {
                $sections[$currentTitle] .= $part;
            }
        }

        if ($sections === []) {
            return [['title' => 'Terms and Conditions', 'html' => $html]];
        }

        return collect($sections)
            ->filter(fn (string $content): bool => trim($content) !== '')
            ->map(fn (string $content, string $title): array => [
                'title' => $title,
                'html' => $content,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{title: string, html: string}>
     */
    private function defaultSections(): array
    {
        return [
            [
                'title' => 'Usage Terms',
                'html' => '<ul><li>The voucher is valid for 12 months from the date of purchase.</li><li>Advance reservation is required. To redeem your voucher, please contact our Reservations Team via email at <a href="mailto:reservation@nandinibali.com">reservation@nandinibali.com</a> or WhatsApp at <a href="https://wa.me/6281236871170">+62 812 3687 1170</a>.</li><li>The voucher is non-refundable, non-transferable, and cannot be exchanged for cash, either in whole or in part.</li><li>The voucher cannot be used in conjunction with any other promotions, discounts, special offers, or packages unless otherwise stated.</li><li>Blackout dates may apply.</li></ul>',
            ],
            [
                'title' => 'Payment Terms',
                'html' => '<ul><li>Please note that all payments are non-refundable once successfully completed.</li></ul>',
            ],
        ];
    }
}
