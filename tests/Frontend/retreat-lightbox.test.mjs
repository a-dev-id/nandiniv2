import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/js/retreat-lightbox.js', import.meta.url), 'utf8');

function fixture(supportsDialog = true) {
    const element = (data = {}) => ({
        ...data,
        events: {},
        addEventListener(name, callback) { this.events[name] = callback; },
        removeAttribute(name) { delete this[name]; },
        focus() { this.focused = true; },
    });
    const links = [0, 1, 2].map((id) => element({ href: `/original-${id}.webp`, dataset: { alt: `Photo ${id}` } }));
    const controls = Object.fromEntries(['image', 'caption', 'counter', 'close', 'previous', 'next'].map((name) => [`[data-lightbox-${name}]`, element()]));
    const dialog = element({
        querySelector: (selector) => controls[selector],
        showModal: supportsDialog ? function () { this.open = true; } : undefined,
        close() { this.open = false; this.events.close(); },
    });
    const story = { querySelectorAll: () => links, querySelector: () => dialog };
    runInNewContext(source, { document: { querySelectorAll: () => [story] } });
    const click = (index, overrides = {}) => {
        const event = { preventDefault() { this.prevented = true; }, ...overrides };
        links[index].events.click?.(event);
        return event;
    };
    return { links, controls, dialog, click };
}

test('opening is deferred until selection; arrows wrap; closing releases image and restores focus', () => {
    const { controls, dialog, links, click } = fixture();
    const image = controls['[data-lightbox-image]'];
    assert.equal(image.src, undefined);
    assert.equal(click(0).prevented, true);
    assert.equal(dialog.open, true);
    assert.equal(image.src, '/original-0.webp');
    dialog.events.keydown({ key: 'ArrowLeft', preventDefault() {} });
    assert.equal(image.src, '/original-2.webp');
    assert.equal(controls['[data-lightbox-counter]'].textContent, '3 / 3');
    controls['[data-lightbox-next]'].events.click();
    assert.equal(image.alt, 'Photo 0');
    controls['[data-lightbox-previous]'].events.click();
    assert.equal(image.alt, 'Photo 2');
    controls['[data-lightbox-close]'].events.click();
    assert.equal(image.src, undefined);
    assert.equal(links[0].focused, true);
});

test('modified links retain native behavior and mobile swipes navigate', () => {
    const { controls, dialog, click } = fixture();
    assert.equal(click(1, { ctrlKey: true }).prevented, undefined);
    assert.equal(dialog.open, undefined);
    click(1);
    const image = controls['[data-lightbox-image]'];
    image.events.touchstart({ changedTouches: [{ clientX: 250 }] });
    image.events.touchend({ changedTouches: [{ clientX: 100 }] });
    assert.equal(image.src, '/original-2.webp');
    image.events.touchstart({ changedTouches: [{ clientX: 100 }] });
    image.events.touchend({ changedTouches: [{ clientX: 250 }] });
    assert.equal(image.src, '/original-1.webp');
});

test('browsers without modal dialog support keep direct image links', () => {
    const { click, links } = fixture(false);
    assert.equal(click(0).prevented, undefined);
    assert.equal(links[0].href, '/original-0.webp');
});
