//
import Alpine from "alpinejs";

window.Alpine = Alpine;

const SLICK_STYLES = [
    "https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css",
    "https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css",
];

const JQUERY_SCRIPT = "https://code.jquery.com/jquery-3.7.1.min.js";
const SLICK_SCRIPT = "https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js";

let jqueryPromise = null;
let slickPromise = null;

function loadStyle(href) {
    if (document.querySelector(`link[href="${href}"]`)) {
        return;
    }

    const link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = href;
    document.head.appendChild(link);
}

function loadScript(src) {
    const existing = document.querySelector(`script[src="${src}"]`);

    if (existing?.dataset.loaded === "true") {
        return Promise.resolve();
    }

    if (existing) {
        return new Promise((resolve, reject) => {
            existing.addEventListener("load", resolve, { once: true });
            existing.addEventListener("error", reject, { once: true });
        });
    }

    return new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.src = src;
        script.async = true;
        script.onload = () => {
            script.dataset.loaded = "true";
            resolve();
        };
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

function ensureJquery() {
    if (window.jQuery) {
        return Promise.resolve(window.jQuery);
    }

    jqueryPromise ??= loadScript(JQUERY_SCRIPT).then(() => window.jQuery);

    return jqueryPromise;
}

function ensureSlick() {
    if (window.jQuery?.fn?.slick) {
        return Promise.resolve(window.jQuery);
    }

    SLICK_STYLES.forEach(loadStyle);

    slickPromise ??= ensureJquery()
        .then(() => loadScript(SLICK_SCRIPT))
        .then(() => window.jQuery)
        .then(($) => {
            document.dispatchEvent(new CustomEvent("nandini:slick-ready"));
            return $;
        });

    return slickPromise;
}

window.Nandini = {
    ...(window.Nandini || {}),
    ensureSlick,
};

function onPageReady(callback) {
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", callback, { once: true });
    } else {
        callback();
    }

    document.addEventListener("livewire:navigated", callback);
}

function toggleClasses(element, addClasses = [], removeClasses = []) {
    if (!element) {
        return;
    }

    element.classList.remove(...removeClasses);
    element.classList.add(...addClasses);
}

function initNavbarScroll() {
    const navbar = document.getElementById("mainNavbar");
    const logo = document.getElementById("navLogo");
    const inner = document.getElementById("navInner");
    const bookBtn = document.getElementById("navBookBtn");
    const navLeft = document.getElementById("navLeft");
    const navIcons = document.getElementById("navIcons");

    if (!navbar || !logo || !inner) {
        return;
    }

    function handleScroll() {
        const scrolled = navbar.dataset.navbarMode === "solid" || window.scrollY > 30;

        if (scrolled) {
            toggleClasses(navbar, ["bg-white", "text-slate-700", "shadow"], ["bg-black/35", "backdrop-blur-md", "text-white"]);
            toggleClasses(inner, ["lg:h-20"], ["lg:h-28"]);
            toggleClasses(logo, ["lg:h-16"], ["lg:h-24", "brightness-0", "invert"]);
            toggleClasses(navLeft, ["text-slate-700"], ["text-white"]);
            toggleClasses(navIcons, ["text-slate-700"]);
            toggleClasses(bookBtn, ["bg-[#A88444]", "border-[#A88444]", "text-white"], ["bg-white", "border-white", "text-slate-700"]);
        } else {
            toggleClasses(navbar, ["bg-black/35", "text-white"], ["bg-white", "text-slate-700", "shadow", "backdrop-blur-md"]);
            toggleClasses(inner, ["lg:h-28"], ["lg:h-20"]);
            toggleClasses(logo, ["lg:h-24", "brightness-0", "invert"], ["lg:h-16"]);
            toggleClasses(navLeft, ["text-white"], ["text-slate-700"]);
            toggleClasses(navIcons, [], ["text-slate-700"]);
            toggleClasses(bookBtn, ["bg-white", "border-white", "text-slate-700"], ["bg-[#A88444]", "border-[#A88444]", "text-white"]);
        }
    }

    handleScroll();
    window.removeEventListener("scroll", handleScroll);
    window.addEventListener("scroll", handleScroll, { passive: true });
}

function initNavbarActions() {
    const bookButton = document.getElementById("navBookBtn");
    const bookMenu = document.getElementById("navBookMenu");

    if (bookButton?.dataset.nandiniBound !== "true") {
        bookButton?.addEventListener("click", (event) => {
            event.preventDefault();
            event.stopPropagation();
            bookMenu?.classList.toggle("hidden");
        });

        if (bookButton) {
            bookButton.dataset.nandiniBound = "true";
        }
    }

    if (document.documentElement.dataset.nandiniGlobalNavBound !== "true") {
        document.addEventListener("click", (event) => {
            if (!event.target.closest("#navBookBtn, #navBookMenu")) {
                document.getElementById("navBookMenu")?.classList.add("hidden");
            }
        });

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                document.getElementById("navBookMenu")?.classList.add("hidden");
            }
        });

        document.documentElement.dataset.nandiniGlobalNavBound = "true";
    }
}

function initItemCarousel() {
    const carousels = document.querySelectorAll(".itemcarousel-slick");

    if (!carousels.length) {
        return;
    }

    const updateSlickAccessibility = ($el) => {
        $el.find(".slick-slide[aria-hidden='true'] a, .slick-slide[aria-hidden='true'] button, .slick-slide[aria-hidden='true'] input, .slick-slide[aria-hidden='true'] select, .slick-slide[aria-hidden='true'] textarea, .slick-slide[aria-hidden='true'] [tabindex]")
            .attr("tabindex", "-1")
            .attr("aria-hidden", "true");

        $el.find(".slick-slide[aria-hidden='false'] a, .slick-slide[aria-hidden='false'] button, .slick-slide[aria-hidden='false'] input, .slick-slide[aria-hidden='false'] select, .slick-slide[aria-hidden='false'] textarea")
            .removeAttr("tabindex")
            .removeAttr("aria-hidden");
    };

    ensureSlick().then(($) => {
        $(".itemcarousel-slick").each(function () {
            const $el = $(this);
            const $wrap = $el.closest(".item-carousel-wrap");

            if ($el.hasClass("slick-initialized")) {
                $el.slick("refresh");
                updateSlickAccessibility($el);
                return;
            }

            $el.on("init reInit afterChange setPosition", function () {
                updateSlickAccessibility($el);
            });

            $el.slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: true,
                arrows: true,
                prevArrow: $wrap.find(".itemcarousel-prev"),
                nextArrow: $wrap.find(".itemcarousel-next"),
                dots: true,
                speed: 450,
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 2 } },
                    { breakpoint: 640, settings: { slidesToShow: 1 } },
                ],
            });
        });
    });
}

function preloadSlickWhenNeeded() {
    if (document.querySelector(".itemcarousel-slick, .guest-review-slider, .dashboard-reward-carousel, .dashboard-accommodation-carousel, .reward-carousel-items")) {
        ensureSlick();
    }
}

function initGuestReviewSlider() {
    const sliders = document.querySelectorAll(".guest-review-slider");

    if (!sliders.length) {
        return;
    }

    ensureSlick().then(($) => {
        $(".guest-review-slider").each(function () {
            const $slider = $(this);

            if ($slider.hasClass("slick-initialized")) {
                $slider.slick("refresh");
                return;
            }

            const total = Number($slider.data("total") || 0);

            $slider.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                adaptiveHeight: true,
                arrows: total > 1,
                dots: total > 1,
                appendDots: $slider.closest("section").find(".guest-review-dots"),
                infinite: total > 1,
                prevArrow: $slider.closest("section").find(".guest-review-prev"),
                nextArrow: $slider.closest("section").find(".guest-review-next"),
                speed: 450,
            });
        });
    });
}

function initDeferredYouTubeEmbeds() {
    const embeds = document.querySelectorAll("[data-youtube-embed]:not([data-loaded='true'])");

    if (!embeds.length) {
        return;
    }

    const toPrivacyUrl = (src) => src.replace("https://www.youtube.com/embed/", "https://www.youtube-nocookie.com/embed/");

    const loadEmbed = (embed) => {
        if (embed.dataset.loaded === "true" || !embed.dataset.src) {
            return;
        }

        const iframe = document.createElement("iframe");
        iframe.className = embed.dataset.frameClass || "absolute inset-0 h-full w-full";
        iframe.src = toPrivacyUrl(embed.dataset.src);
        iframe.title = embed.dataset.title || "Video";
        iframe.loading = "lazy";
        iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
        iframe.referrerPolicy = "strict-origin-when-cross-origin";
        iframe.allowFullscreen = true;
        iframe.setAttribute("frameborder", "0");

        embed.appendChild(iframe);
        embed.dataset.loaded = "true";
    };

    const isVisible = (element) => {
        const style = window.getComputedStyle(element);

        return style.display !== "none" && style.visibility !== "hidden" && element.getClientRects().length > 0;
    };

    embeds.forEach((embed) => {
        if (embed.dataset.autoload === "true") {
            if (isVisible(embed)) {
                window.setTimeout(() => loadEmbed(embed), Number(embed.dataset.autoloadDelay || 600));
            }

            return;
        }

        embed.addEventListener("click", () => loadEmbed(embed), { once: true });
        embed.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                loadEmbed(embed);
            }
        }, { once: true });
    });
}

function resetRecaptcha(form = null) {
    if (!window.grecaptcha?.reset) {
        return;
    }

    const widgets = form
        ? form.querySelectorAll("[data-recaptcha-widget]")
        : document.querySelectorAll("[data-recaptcha-widget]");

    widgets.forEach((widget) => {
        const widgetId = widget.dataset.widgetId;

        if (widgetId !== undefined) {
            window.grecaptcha.reset(Number(widgetId));
            return;
        }

        window.grecaptcha.reset();
    });
}

onPageReady(() => {
    initNavbarScroll();
    initNavbarActions();
    preloadSlickWhenNeeded();
    initItemCarousel();
    initGuestReviewSlider();
    initDeferredYouTubeEmbeds();
});

Alpine.data("inquiryModal", () => ({
    isOpen: false,
    isSubmitting: false,
    message: "",
    error: "",
    sourceUrl: window.location.href,
    itemTitle: "",
    itemImage: "",
    today: new Date().toISOString().slice(0, 10),
    reserveTime: "",
    closeCountdown: 0,
    closeCountdownTimer: null,

    get isLateActivity() {
        const title = this.itemTitle.toLowerCase();

        return title.includes("dinner") || title.includes("night");
    },

    init() {
        document.addEventListener("click", (event) => {
            const trigger = event.target.closest("a, button");

            if (!trigger || trigger.closest("[role='dialog']")) {
                return;
            }

            const label = trigger.textContent.trim().toLowerCase();
            const opensInquiry =
                trigger.hasAttribute("data-inquiry-button") ||
                label.includes("inquire") ||
                label.includes("inquiry");

            if (!opensInquiry) {
                return;
            }

            event.preventDefault();
            this.open(trigger);
        });

        window.addEventListener("open-inquiry-modal", () => this.open());
    },

    open(trigger = null) {
        this.setInquiryItem(trigger);
        this.clearCloseCountdown();
        this.isOpen = true;
        this.message = "";
        this.error = "";
        this.sourceUrl = window.location.href;

        if (this.isLateActivity && (!this.reserveTime || this.reserveTime < "16:00")) {
            this.reserveTime = "16:00";
        }

        document.documentElement.classList.add("overflow-hidden");
        document.body.classList.add("overflow-hidden");
    },

    setInquiryItem(trigger) {
        const container = trigger?.closest("section, article, main") || document;
        const triggerTitle = trigger?.dataset?.inquiryTitle?.trim();
        const triggerImage = trigger?.dataset?.inquiryImage?.trim();
        const heading =
            container.querySelector("h1") ||
            document.querySelector("h1") ||
            document.querySelector("meta[property='og:title']");
        const ogImage = document.querySelector("meta[property='og:image']");
        const image = container.querySelector("img") || document.querySelector("main img, section img");

        this.itemTitle =
            triggerTitle ||
            heading?.content ||
            heading?.textContent?.trim() ||
            document.title ||
            "Nandini Inquiry";

        this.itemImage = triggerImage || ogImage?.content || image?.currentSrc || image?.src || "";
    },

    close() {
        if (this.isSubmitting) {
            return;
        }

        this.clearCloseCountdown();
        this.isOpen = false;
        this.message = "";
        this.error = "";
        document.documentElement.classList.remove("overflow-hidden");
        document.body.classList.remove("overflow-hidden");
    },

    startCloseCountdown(seconds = 5) {
        this.clearCloseCountdown();
        this.closeCountdown = seconds;

        this.closeCountdownTimer = window.setInterval(() => {
            this.closeCountdown -= 1;

            if (this.closeCountdown <= 0) {
                this.close();
            }
        }, 1000);
    },

    clearCloseCountdown() {
        if (this.closeCountdownTimer) {
            window.clearInterval(this.closeCountdownTimer);
            this.closeCountdownTimer = null;
        }

        this.closeCountdown = 0;
    },

    async submit(event) {
        if (this.isSubmitting) {
            return;
        }

        const form = event.target;
        this.isSubmitting = true;
        this.clearCloseCountdown();
        this.message = "";
        this.error = "";

        try {
            const response = await fetch(form.action, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: new FormData(form),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const firstError = data.errors
                    ? Object.values(data.errors).flat()[0]
                    : null;

                throw new Error(firstError || data.message || "Please check the form and try again.");
            }

            this.message = data.message || "Thank you. Your inquiry has been sent.";
            form.reset();
            resetRecaptcha(form);
            this.startCloseCountdown(5);
        } catch (error) {
            this.error = error.message || "We could not send your inquiry. Please try again.";
            resetRecaptcha(form);
        } finally {
            this.isSubmitting = false;
        }
    },
}));

Alpine.data("redemptionModal", () => ({
    isOpen: false,
    isSubmitting: false,
    actionUrl: "",
    rewardTitle: "",
    rewardPoints: "",
    today: new Date().toISOString().slice(0, 10),

    init() {
        document.addEventListener("click", (event) => {
            const trigger = event.target.closest("[data-reward-redeem-button]");

            if (!trigger || trigger.closest("[role='dialog']")) {
                return;
            }

            event.preventDefault();
            this.open(trigger);
        });
    },

    open(trigger) {
        this.actionUrl = trigger.dataset.redeemAction || "";
        this.rewardTitle = trigger.dataset.rewardTitle || "Selected Reward";
        this.rewardPoints = trigger.dataset.rewardPoints || "";
        this.isSubmitting = false;
        this.isOpen = true;

        document.documentElement.classList.add("overflow-hidden");
        document.body.classList.add("overflow-hidden");
    },

    close() {
        if (this.isSubmitting) {
            return;
        }

        this.isOpen = false;
        this.actionUrl = "";
        this.rewardTitle = "";
        this.rewardPoints = "";
        document.documentElement.classList.remove("overflow-hidden");
        document.body.classList.remove("overflow-hidden");
    },
}));

Alpine.start();
