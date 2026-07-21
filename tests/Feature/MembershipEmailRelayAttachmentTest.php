<?php

namespace Tests\Feature;

use App\Services\MembershipEmailRelayService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MembershipEmailRelayAttachmentTest extends TestCase
{
    public function test_it_passes_normalized_base64_attachments_to_the_relay_api(): void
    {
        config([
            'services.email_relay.url' => 'https://relay.example.test/send',
            'services.email_relay.token' => 'secret-token',
        ]);
        Http::fake(['relay.example.test/*' => Http::response(['ok' => true])]);

        $result = app(MembershipEmailRelayService::class)->send([
            'to' => 'guest@example.com',
            'subject' => 'Voucher',
            'html_body' => '<p>Attached</p>',
            'attachments' => [[
                'filename' => '../voucher.pdf',
                'content_type' => 'application/pdf',
                'content_base64' => base64_encode('%PDF-sample'),
            ]],
        ]);

        $this->assertTrue($result['success']);
        Http::assertSent(fn($request): bool => $request->hasHeader('Authorization', 'Bearer secret-token')
            && $request['attachments'][0]['filename'] === 'voucher.pdf'
            && $request['attachments'][0]['content_type'] === 'application/pdf'
            && base64_decode($request['attachments'][0]['content_base64'], true) === '%PDF-sample');
    }
}
