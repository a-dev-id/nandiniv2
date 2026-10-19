import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

const source = readFileSync(new URL('../../resources/js/app.js', import.meta.url), 'utf8');
const modalCode = source.slice(source.indexOf('Alpine.data("inquiryModal"'), source.indexOf('Alpine.data("redemptionModal"'));

function harness(response, dining = true) {
    let modal;
    let resets = 0;
    let captchaResets = 0;
    let countdown = 0;
    const window = { location: { href: 'https://dining.example.test/?email=secret', pathname: '/' }, dataLayer: [], addEventListener() {} };
    const document = { querySelector: () => null, querySelectorAll: () => [], addEventListener() {}, title: 'Arbitrary private dining text', documentElement: { classList: { add() {}, remove() {} } }, body: { classList: { add() {}, remove() {} } } };
    vm.runInNewContext(modalCode, {
        Alpine: { data: (_name, factory) => { modal = factory(); } },
        window,
        document,
        FormData: class {},
        fetch: async () => { if (response instanceof Error) throw response; return response; },
        resetRecaptcha: () => captchaResets++,
    });
    modal.clearCloseCountdown = () => {};
    modal.startCloseCountdown = (seconds) => { countdown = seconds; };
    const form = { action: '/inquiries', hasAttribute: () => dining, reset: () => resets++ };
    return { modal, window, document, submit: () => modal.submit({ target: form }), counts: () => ({ resets, captchaResets, countdown }) };
}

const confirmed = (category = 'private_dining') => ({ ok: true, json: async () => ({ success: true, inquiry_id: 42, lead_type: 'inquiry', inquiry_category: category, message: 'Saved', email: 'private@example.test', note: 'Secret' }) });

test('confirmed Dining success emits exactly one safe event and preserves confirmation behavior', async () => {
    const h = harness(confirmed());
    await h.submit();
    await h.submit(); // Replayed successful response cannot duplicate its event.
    assert.equal(h.window.dataLayer.length, 1);
    assert.equal(JSON.stringify(h.window.dataLayer[0]), JSON.stringify({ event: 'inquiry_success', lead_type: 'inquiry', inquiry_category: 'private_dining', inquiry_source: 'dining', experience: 'general', page_path: '/' }));
    assert.equal(h.modal.message, 'Saved');
    assert.equal(h.modal.isSubmitting, false);
    assert.deepEqual(h.counts(), { resets: 2, captchaResets: 2, countdown: 5 });
});

test('HTTP failures, invalid success, parsing failure and network errors never emit', async () => {
    for (const response of [
        { ok: false, status: 422, json: async () => ({ errors: { email: ['Invalid'] } }) },
        { ok: false, status: 500, json: async () => ({ success: true }) },
        { ok: true, json: async () => ({ success: false }) },
        { ok: true, json: async () => { throw new Error('Malformed JSON'); } },
        new Error('Network failure'),
    ]) {
        const h = harness(response);
        await h.submit();
        assert.equal(h.window.dataLayer.length, 0);
        assert.ok(h.modal.error);
        assert.equal(h.counts().resets, 0);
        assert.equal(h.modal.isSubmitting, false);
    }
});

test('all shared inquiry forms are supported but unrelated forms cannot emit', async () => {
    const h = harness(confirmed('dining'));
    await h.submit();
    assert.equal(h.window.dataLayer[0].inquiry_category, 'dining');
    const other = harness(confirmed(), false);
    await other.submit();
    assert.equal(other.window.dataLayer.length, 0);
});

test('category comes from structured trigger data, not arbitrary title text', () => {
    const h = harness(confirmed());
    const trigger = { dataset: { inquiryCategory: 'private_dining' }, closest: () => ({ querySelector: () => null }) };
    h.modal.setInquiryItem(trigger);
    assert.equal(h.modal.inquiryCategory, 'private_dining');
    h.modal.setInquiryItem(null);
    assert.equal(h.modal.inquiryCategory, 'general');
});

test('all categories emit one popup open per actual opening and one shared success', async () => {
    const sources = { private_dining: 'dining', dining: 'dining', spa: 'spa', experience: 'experiences', romantic_experience: 'experiences', wedding: 'wedding', general: 'website' };
    for (const [category, source] of Object.entries(sources)) {
        const h = harness(confirmed(category));
        h.modal.open(null, { inquiryCategory: category, experience: 'controlled_experience' });
        h.modal.open(null, { inquiryCategory: category });
        assert.equal(h.window.dataLayer.length, 1);
        assert.equal(h.window.dataLayer[0].event, 'inquiry_popup_open');
        assert.equal(h.window.dataLayer[0].inquiry_source, source);
        assert.equal(h.window.dataLayer[0].experience, 'controlled_experience');
        await h.submit();
        assert.equal(h.window.dataLayer.length, 2);
        assert.equal(h.window.dataLayer[1].event, 'inquiry_success');
        assert.equal(h.window.dataLayer[1].inquiry_category, category);
        assert.ok(!JSON.stringify(h.window.dataLayer).includes('secret'));
        assert.ok(!JSON.stringify(h.window.dataLayer).includes('dining_inquiry_success'));
        h.modal.close();
        assert.equal(h.window.dataLayer.length, 2);
        h.modal.open(null, { inquiryCategory: category });
        assert.equal(h.window.dataLayer.length, 3);
    }
});

test('concurrent submit is guarded and analytics failures do not break confirmation', async () => {
    const h = harness(confirmed());
    h.modal.isSubmitting = true;
    await h.submit();
    assert.equal(h.window.dataLayer.length, 0);
    h.modal.isSubmitting = false;
    h.window.dataLayer = { push: () => { throw new Error('Blocked analytics'); } };
    await h.submit();
    assert.equal(h.modal.message, 'Saved');
    assert.equal(h.counts().countdown, 5);
});

test('legacy popup links are marked and intercepted while genuine WhatsApp is untouched', () => {
    const h = harness(confirmed());
    let click;
    const makeLink = (text) => {
        const attrs = new Set();
        return { textContent: text, dataset: {}, closest: () => null, hasAttribute: (key) => attrs.has(key), setAttribute: (key) => attrs.add(key) };
    };
    const inquiry = makeLink('Send Inquiry');
    const whatsapp = makeLink('WhatsApp our team');
    h.document.querySelectorAll = () => [inquiry, whatsapp];
    h.document.addEventListener = (_type, callback) => { click = callback; };
    h.modal.$el = { dataset: { inquiryCategory: 'wedding' } };
    h.modal.init();
    assert.ok(inquiry.hasAttribute('data-inquiry-button'));
    assert.equal(whatsapp.hasAttribute('data-inquiry-button'), false);
    let prevented = 0;
    click({ target: { closest: () => whatsapp }, preventDefault: () => prevented++ });
    assert.equal(prevented, 0);
    assert.equal(h.window.dataLayer.length, 0);
    click({ target: { closest: () => inquiry }, preventDefault: () => prevented++ });
    assert.equal(prevented, 1);
    assert.equal(h.window.dataLayer[0].inquiry_category, 'wedding');
    assert.equal(h.window.dataLayer[0].event, 'inquiry_popup_open');
});
