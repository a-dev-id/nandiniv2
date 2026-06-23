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
        const scrolled = window.scrollY > 30;

        if (scrolled) {
            toggleClasses(navbar, ["bg-white", "text-slate-700", "shadow"], ["bg-black/35", "backdrop-blur-md", "text-white"]);
            toggleClasses(inner, ["lg:h-20"], ["lg:h-28"]);
            toggleClasses(logo, ["lg:h-16"], ["lg:h-24", "brightness-0", "invert"]);
            toggleClasses(navLeft, ["text-slate-700"], ["text-white"]);
            toggleClasses(navIcons, ["text-slate-700"]);
            toggleClasses(bookBtn, ["bg-[#A67C3D]", "border-[#A67C3D]", "text-white"], ["bg-white", "border-white", "text-slate-700"]);
        } else {
            toggleClasses(navbar, ["bg-black/35", "text-white"], ["bg-white", "text-slate-700", "shadow", "backdrop-blur-md"]);
            toggleClasses(inner, ["lg:h-28"], ["lg:h-20"]);
            toggleClasses(logo, ["lg:h-24", "brightness-0", "invert"], ["lg:h-16"]);
            toggleClasses(navLeft, ["text-white"], ["text-slate-700"]);
            toggleClasses(navIcons, [], ["text-slate-700"]);
            toggleClasses(bookBtn, ["bg-white", "border-white", "text-slate-700"], ["bg-[#A67C3D]", "border-[#A67C3D]", "text-white"]);
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

    ensureSlick().then(($) => {
        $(".itemcarousel-slick").each(function () {
            const $el = $(this);
            const $wrap = $el.closest(".item-carousel-wrap");

            if ($el.hasClass("slick-initialized")) {
                $el.slick("refresh");
                return;
            }

            $el.slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: false,
                arrows: true,
                prevArrow: $wrap.find(".itemcarousel-prev"),
                nextArrow: $wrap.find(".itemcarousel-next"),
                dots: false,
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
    if (document.querySelector(".itemcarousel-slick, .dashboard-reward-carousel, .dashboard-accommodation-carousel, .reward-carousel-items")) {
        ensureSlick();
    }
}

function initDeferredYouTubeEmbeds() {
    const embeds = document.querySelectorAll("[data-youtube-embed]:not([data-loaded='true'])");

    if (!embeds.length) {
        return;
    }

    const loadEmbeds = () => {
        embeds.forEach((embed) => {
            if (embed.dataset.loaded === "true" || !embed.dataset.src) {
                return;
            }

            const iframe = document.createElement("iframe");
            iframe.className = embed.dataset.frameClass || "absolute inset-0 h-full w-full";
            iframe.src = embed.dataset.src;
            iframe.title = embed.dataset.title || "Video";
            iframe.loading = "lazy";
            iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
            iframe.referrerPolicy = "strict-origin-when-cross-origin";
            iframe.allowFullscreen = true;
            iframe.setAttribute("frameborder", "0");

            embed.appendChild(iframe);
            embed.dataset.loaded = "true";
        });
    };

    const scheduleLoad = () => {
        if ("requestIdleCallback" in window) {
            window.requestIdleCallback(loadEmbeds, { timeout: 2500 });
            return;
        }

        window.setTimeout(loadEmbeds, 1800);
    };

    if (document.readyState === "complete") {
        scheduleLoad();
    } else {
        window.addEventListener("load", scheduleLoad, { once: true });
    }
}

onPageReady(() => {
    initNavbarScroll();
    initNavbarActions();
    preloadSlickWhenNeeded();
    initItemCarousel();
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
        const heading =
            container.querySelector("h1") ||
            document.querySelector("h1") ||
            document.querySelector("meta[property='og:title']");
        const ogImage = document.querySelector("meta[property='og:image']");
        const image = container.querySelector("img") || document.querySelector("main img, section img");

        this.itemTitle =
            heading?.content ||
            heading?.textContent?.trim() ||
            document.title ||
            "Nandini Inquiry";

        this.itemImage = ogImage?.content || image?.currentSrc || image?.src || "";
    },

    close() {
        if (this.isSubmitting) {
            return;
        }

        this.isOpen = false;
        this.message = "";
        this.error = "";
        document.documentElement.classList.remove("overflow-hidden");
        document.body.classList.remove("overflow-hidden");
    },

    async submit(event) {
        if (this.isSubmitting) {
            return;
        }

        const form = event.target;
        this.isSubmitting = true;
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
        } catch (error) {
            this.error = error.message || "We could not send your inquiry. Please try again.";
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
