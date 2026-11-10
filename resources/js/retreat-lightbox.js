document.querySelectorAll('[data-retreat-story]').forEach((story) => {
    const links = Array.from(story.querySelectorAll('[data-retreat-photo]'));
    const dialog = story.querySelector('[data-retreat-lightbox]');
    if (!dialog || typeof dialog.showModal !== 'function') return;

    const image = dialog.querySelector('[data-lightbox-image]');
    const caption = dialog.querySelector('[data-lightbox-caption]');
    const counter = dialog.querySelector('[data-lightbox-counter]');
    let index = 0;
    let trigger;
    let touchStart;

    const show = (next) => {
        index = (next + links.length) % links.length;
        const link = links[index];
        image.alt = link.dataset.alt;
        image.src = link.href;
        caption.textContent = link.dataset.alt;
        counter.textContent = `${index + 1} / ${links.length}`;
    };

    links.forEach((link, position) => link.addEventListener('click', (event) => {
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        trigger = link;
        show(position);
        dialog.showModal();
    }));
    dialog.querySelector('[data-lightbox-close]').addEventListener('click', () => dialog.close());
    dialog.querySelector('[data-lightbox-previous]').addEventListener('click', () => show(index - 1));
    dialog.querySelector('[data-lightbox-next]').addEventListener('click', () => show(index + 1));
    dialog.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
            event.preventDefault();
            show(index + (event.key === 'ArrowLeft' ? -1 : 1));
        }
    });
    // Native modal dialog provides Escape and focus containment.
    dialog.addEventListener('close', () => {
        image.removeAttribute('src');
        trigger?.focus({ preventScroll: true });
    });
    image.addEventListener('touchstart', (event) => { touchStart = event.changedTouches[0].clientX; }, { passive: true });
    image.addEventListener('touchend', (event) => {
        const distance = event.changedTouches[0].clientX - touchStart;
        if (Math.abs(distance) > 60) show(index + (distance > 0 ? -1 : 1));
    }, { passive: true });
    image.addEventListener('error', () => { caption.textContent = 'This photograph could not load. Please try the next photograph or reopen it.'; });
});
