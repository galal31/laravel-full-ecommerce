(() => {
    'use strict';
    const page = document.querySelector('.store-home');
    if (!page) return;

    // الصور الناقصة تعرض بديلًا محايدًا بدل صورة منتج مختلف.
    document.querySelectorAll('img[data-fallback-src]').forEach(image => {
        const fallback = () => {
            if (!image.dataset.fallbackSrc) return;
            const source = image.dataset.fallbackSrc;
            delete image.dataset.fallbackSrc;
            image.src = source;
        };
        image.addEventListener('error', fallback, { once: true });
        if (image.complete && image.naturalWidth === 0) fallback();
    });

    const slides = [...document.querySelectorAll('.sh-hero-slide')];
    const controls = [...document.querySelectorAll('[data-slide]')];
    controls.forEach(button => button.addEventListener('click', () => {
        const selected = Number(button.dataset.slide);
        slides.forEach((slide, index) => { slide.hidden = index !== selected; });
        controls.forEach((control, index) => control.setAttribute('aria-pressed', String(index === selected)));
    }));

    const toast = document.querySelector('.sh-toast');
    let toastTimer;
    const showMessage = message => {
        if (!toast) return;
        clearTimeout(toastTimer);
        toast.textContent = message;
        toast.hidden = false;
        toastTimer = setTimeout(() => { toast.hidden = true; }, 3500);
    };

    document.querySelectorAll('.sh-wishlist').forEach(button => {
        button.addEventListener('click', async () => {
            button.disabled = true;
            try {
                const response = await fetch(button.dataset.url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ product_id: button.dataset.productId }),
                });
                if (response.status === 401) {
                    window.location.assign(page.dataset.loginUrl);
                    return;
                }
                if (!response.ok) throw new Error('Wishlist request failed');
                const data = await response.json();
                // نفس المنتج قد يظهر في أكثر من قسم؛ نحافظ على تطابق كل أزراره.
                document.querySelectorAll('.sh-wishlist').forEach(other => {
                    if (other.dataset.productId !== button.dataset.productId) return;
                    other.classList.toggle('is-active', Boolean(data.added));
                    other.setAttribute('aria-pressed', String(Boolean(data.added)));
                    other.setAttribute('aria-label', data.added ? page.dataset.removeWishlist : page.dataset.addWishlist);
                });
                showMessage(data.message);
            } catch {
                showMessage(page.dataset.requestFailed);
            } finally {
                button.disabled = false;
            }
        });
    });
})();
