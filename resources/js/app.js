//
import Alpine from "alpinejs";

window.Alpine = Alpine;

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

Alpine.start();
