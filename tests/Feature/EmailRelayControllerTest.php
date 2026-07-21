<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailRelayControllerTest extends TestCase
{
    public function test_authenticated_relay_request_sends_pdf_attachment(): void
    {
        config(['services.email_relay.token' => 'relay-secret']);
        config([
            'mail.default' => 'array',
            'mail.mailers.array' => ['transport' => 'array'],
        ]);
        app('mail.manager')->forgetMailers();

        $this->withToken('relay-secret')->postJson('/api/email-relay/send', [
            'to' => 'guest@example.com',
            'cc' => [],
            'bcc' => ['archive@example.com'],
            'subject' => 'Your voucher',
            'html_body' => '<p>Your voucher is attached.</p>',
            'reply_to' => 'reservations@example.com',
            'attachments' => [[
                'filename' => 'voucher.pdf',
                'content_type' => 'application/pdf',
                'content_base64' => base64_encode('%PDF-sample'),
            ]],
        ])->assertOk()->assertJson([
            'ok' => true,
            'attachments' => 1,
        ]);

        $messages = Mail::mailer()->getSymfonyTransport()->messages();
        $email = $messages[0]->getOriginalMessage();
        $attachments = $email->getAttachments();

        $this->assertCount(1, $messages);
        $this->assertSame('guest@example.com', $email->getTo()[0]->getAddress());
        $this->assertSame('archive@example.com', $email->getBcc()[0]->getAddress());
        $this->assertCount(1, $attachments);
        $this->assertSame('voucher.pdf', $attachments[0]->getFilename());
    }

    public function test_relay_rejects_an_invalid_token(): void
    {
        config(['services.email_relay.token' => 'relay-secret']);

        $this->withToken('wrong-token')->postJson('/api/email-relay/send', [
            'to' => 'guest@example.com',
        ])->assertForbidden();
    }
}
