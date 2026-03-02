/**
 * base64-spa.js  (v2)
 * Vanilla JS SPA engine for the Base64 Tools section.
 *
 * Bug fixes vs v1:
 *  - Removed call to undefined formDataToMultipart()
 *  - Safe JSON parsing: gracefully handles non-JSON error bodies
 *  - Laravel 422 validation errors are now surfaced properly
 *  - fetch() now has AbortController timeout (30 s panel / 60 s API)
 *  - Live elapsed-time counter on submit button ("Working… 5 s")
 */

(function () {
    "use strict";

    // ─── Config ─────────────────────────────────────────────────────────────────
    const BASE_PATH = "/tools/base64";
    const PANEL_URL = (slug) => `${BASE_PATH}/ui/${slug}`;
    const API_URL = (slug) => `${BASE_PATH}/api/${slug}`;
    const CSRF_TOKEN = () =>
        document.querySelector('meta[name="csrf-token"]')?.content ?? "";
    const PANEL_TIMEOUT = 30_000; // ms — panel fetch
    const API_TIMEOUT = 60_000; // ms — API form submit (allow slow external APIs)

    // ─── Translations ────────────────────────────────────────────────────────────
    const __t = (key, fallback) =>
        window.spaTranslations ? window.spaTranslations[key] : fallback;

    // ─── DOM helpers ─────────────────────────────────────────────────────────────
    const panel = () => document.getElementById("spa-main-panel");
    const welcome = () => document.getElementById("spa-welcome");

    // ─── Icons ───────────────────────────────────────────────────────────────────
    function reinitIcons() {
        if (window.feather) feather.replace();
    }

    // ─── Safe JSON parse ─────────────────────────────────────────────────────────
    /**
     * Try to parse response as JSON. If it fails (e.g. server returned an HTML
     * error page) return a simple object with the raw text as the error field.
     */
    async function safeJson(res) {
        try {
            return await res.json();
        } catch (_) {
            const text = await res.text().catch(() => "");
            return { error: text || `HTTP ${res.status} (non-JSON response)` };
        }
    }

    // ─── Fetch with timeout ───────────────────────────────────────────────────────
    function fetchWithTimeout(url, options, timeoutMs) {
        const controller = new AbortController();
        const timerId = setTimeout(() => controller.abort(), timeoutMs);
        return fetch(url, { ...options, signal: controller.signal }).finally(
            () => clearTimeout(timerId),
        );
    }

    // ─── Extract human-readable error from Laravel JSON response ─────────────────
    function extractError(data, httpStatus) {
        // Laravel validation error (422): { message: '...', errors: { field: ['msg'] } }
        if (data.errors && typeof data.errors === "object") {
            const messages = Object.values(data.errors).flat();
            return messages.join(" · ");
        }
        // Generic error key
        if (data.error) return data.error;
        if (data.message) return data.message;
        return `Request failed (HTTP ${httpStatus})`;
    }

    // ─── Sidebar active state ─────────────────────────────────────────────────────
    function setActiveItem(slug) {
        document.querySelectorAll("[data-spa-item]").forEach((el) => {
            el.classList.toggle("spa-item-active", el.dataset.spaItem === slug);
        });
    }

    // ─── Loading skeleton ─────────────────────────────────────────────────────────
    function showSkeleton() {
        panel().innerHTML = `
      <div class="spa-skeleton animate-pulse space-y-6">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-12 h-12 rounded-2xl bg-slate-700/60"></div>
          <div class="flex-1 space-y-2">
            <div class="h-3 w-24 rounded bg-slate-700/60"></div>
            <div class="h-6 w-48 rounded bg-slate-700/60"></div>
          </div>
        </div>
        <div class="grid gap-6 lg:grid-cols-2">
          <div class="glass-card rounded-2xl border border-white/10 bg-slate-900/60 p-6 space-y-3">
            <div class="h-4 w-20 rounded bg-slate-700/60"></div>
            <div class="h-32 rounded-lg bg-slate-700/40"></div>
            <div class="h-10 w-28 rounded-lg bg-slate-700/40"></div>
          </div>
          <div class="glass-card rounded-2xl border border-white/10 bg-slate-900/60 p-6 space-y-3">
            <div class="h-4 w-20 rounded bg-slate-700/60"></div>
            <div class="h-24 rounded-lg bg-slate-700/40"></div>
          </div>
        </div>
      </div>`;
    }

    // ─── Panel-level error (tool failed to load) ──────────────────────────────────
    function showPanelError(message) {
        panel().innerHTML = `
      <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-5 py-4 text-red-200">
        <div class="font-semibold mb-1 flex items-center gap-2">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          ${__t("failedToLoad", "Failed to load tool")}
        </div>
        <p class="text-sm font-mono opacity-80">${escHtml(message)}</p>
      </div>`;
    }

    // ─── Response-box error (form submission failed) ───────────────────────────────
    function showResponseError(message) {
        const responseBox = document.getElementById("spa-response");
        if (!responseBox) return;
        responseBox.innerHTML = `
      <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
        <div class="font-semibold mb-1 flex items-center gap-2">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          ${__t("error", "Error")}
        </div>
        <p class="font-mono opacity-90">${escHtml(message)}</p>
      </div>`;
    }

    // ─── HTML escape helper ───────────────────────────────────────────────────────
    function escHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    // ─── Update page meta ─────────────────────────────────────────────────────────
    function updateMeta(title, description) {
        if (title) document.title = title;
        const desc = document.querySelector('meta[name="description"]');
        if (desc && description) desc.setAttribute("content", description);
    }

    // ─── Submit button state management ──────────────────────────────────────────
    function setButtonWorking(btn, label, working) {
        if (!btn) return;
        btn.disabled = working;
        btn.classList.toggle("opacity-60", working);
        btn.classList.toggle("cursor-not-allowed", working);
        if (label && !working) label.textContent = __t("submit", "Submit");
    }

    /**
     * Starts a live elapsed-time counter on the submit button.
     * Returns a stop function that must be called when the request ends.
     */
    function startElapsedTimer(label) {
        if (!label) return () => {};
        const start = Date.now();
        const workingText = __t("working", "Working...");
        const timer = setInterval(() => {
            const secs = Math.floor((Date.now() - start) / 1000);
            label.textContent = `${workingText} ${secs}s`;
        }, 1000);
        return () => clearInterval(timer);
    }

    // ─── Load a tool panel ────────────────────────────────────────────────────────
    async function loadTool(slug, pushState = true) {
        if (!slug) return;

        setActiveItem(slug);
        if (welcome()) welcome().style.display = "none";
        showSkeleton();

        try {
            const res = await fetchWithTimeout(
                PANEL_URL(slug),
                {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                },
                PANEL_TIMEOUT,
            );

            const data = await safeJson(res);

            if (!res.ok) {
                showPanelError(extractError(data, res.status));
                return;
            }

            panel().innerHTML = data.html ?? "";
            updateMeta(data.title, data.description);
            reinitIcons();
            bindFormListeners();

            if (pushState) {
                history.pushState({ slug }, "", `${BASE_PATH}/${slug}`);
            }
        } catch (err) {
            // AbortError means timeout
            const msg =
                err.name === "AbortError"
                    ? `Tool panel timed out after ${PANEL_TIMEOUT / 1000} s. The server may be unavailable.`
                    : err.message;
            showPanelError(msg);
        }
    }

    // ─── Form submit dispatcher ────────────────────────────────────────────────────
    function handleFormSubmit(form) {
        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            const slug = this.dataset.spaSlug;
            const hasFile = this.dataset.spaHasFile === "true";
            const isBinary =
                hasFile &&
                ["file-decode", "image-decode", "bulk-csv-to-zip"].includes(
                    slug,
                );

            if (isBinary) {
                await handleBinaryDownload(this, slug);
            } else {
                await handleJsonSubmit(this, slug);
            }
        });
    }

    // ─── JSON form submission ─────────────────────────────────────────────────────
    async function handleJsonSubmit(form, slug) {
        const btn = form.querySelector("[data-spa-submit-btn]");
        const label = form.querySelector("[data-spa-btn-label]");

        setButtonWorking(btn, label, true);
        const stopTimer = startElapsedTimer(label);

        try {
            const res = await fetchWithTimeout(
                API_URL(slug),
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN(),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: new FormData(form),
                },
                API_TIMEOUT,
            );

            const data = await safeJson(res);

            if (!res.ok) {
                // Surface validation errors or generic API errors clearly
                showResponseError(extractError(data, res.status));
                return;
            }

            const responseBox = document.getElementById("spa-response");
            if (responseBox && data.html) {
                responseBox.innerHTML = data.html;
                reinitIcons();
            }
        } catch (err) {
            const msg =
                err.name === "AbortError"
                    ? `Request timed out after ${API_TIMEOUT / 1000} s. The external API may be unavailable.`
                    : err.message;
            showResponseError(msg);
        } finally {
            stopTimer();
            setButtonWorking(btn, label, false);
        }
    }

    // ─── Binary / file download ────────────────────────────────────────────────────
    async function handleBinaryDownload(form, slug) {
        const btn = form.querySelector("[data-spa-submit-btn]");
        const label = form.querySelector("[data-spa-btn-label]");

        setButtonWorking(btn, label, true);
        if (label) label.textContent = __t("downloading", "Downloading...");
        const stopTimer = startElapsedTimer(label);

        try {
            const res = await fetchWithTimeout(
                API_URL(slug),
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN(),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: new FormData(form),
                },
                API_TIMEOUT,
            );

            if (!res.ok) {
                const data = await safeJson(res);
                showResponseError(extractError(data, res.status));
                return;
            }

            const blob = await res.blob();
            const disposition = res.headers.get("Content-Disposition") ?? "";
            const match = disposition.match(/filename="?([^";]+)"?/i);
            const filename = match ? match[1] : "download";

            const url = URL.createObjectURL(blob);
            const a = Object.assign(document.createElement("a"), {
                href: url,
                download: filename,
                style: "display:none",
            });
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);

            // Show a success toast in the response box
            const responseBox = document.getElementById("spa-response");
            if (responseBox) {
                responseBox.innerHTML = `
          <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            <div class="font-semibold mb-1">${__t("downloadReady", "Download ready")}</div>
            <p>${__t("downloadSaved", "File has been saved to your downloads folder.")} <span class="font-mono">${escHtml(filename)}</span></p>
          </div>`;
            }
        } catch (err) {
            const msg =
                err.name === "AbortError"
                    ? `Download timed out after ${API_TIMEOUT / 1000} s.`
                    : err.message;
            showResponseError(msg);
        } finally {
            stopTimer();
            setButtonWorking(btn, label, false);
        }
    }

    // ─── Bind form listeners ──────────────────────────────────────────────────────
    function bindFormListeners() {
        document.querySelectorAll("[data-spa-form]").forEach((form) => {
            if (!form._spabound) {
                form._spabound = true;
                handleFormSubmit(form);
            }
        });
    }

    // ─── Sidebar item click ────────────────────────────────────────────────────────
    function bindSidebarItems() {
        document.querySelectorAll("[data-spa-item]").forEach((el) => {
            el.addEventListener("click", function (e) {
                e.preventDefault();
                loadTool(this.dataset.spaItem);
            });
        });
    }

    // ─── Category filter ───────────────────────────────────────────────────────────
    function bindCategoryFilter() {
        document.querySelectorAll("[data-spa-category]").forEach((btn) => {
            btn.addEventListener("click", function () {
                const cat = this.dataset.spaCategory;
                document
                    .querySelectorAll("[data-spa-category]")
                    .forEach((b) => {
                        b.classList.toggle("spa-cat-active", b === this);
                        b.classList.toggle("spa-cat-inactive", b !== this);
                    });
                document.querySelectorAll("[data-spa-item]").forEach((item) => {
                    item.style.display =
                        cat === "all" || item.dataset.spaCategory === cat
                            ? ""
                            : "none";
                });
            });
        });
    }

    // ─── Mobile sidebar toggle ────────────────────────────────────────────────────
    function bindMobileToggle() {
        const toggle = document.getElementById("spa-sidebar-toggle");
        const drawer = document.getElementById("spa-sidebar-drawer");
        const overlay = document.getElementById("spa-sidebar-overlay");
        if (!toggle || !drawer) return;

        const open = () => {
            drawer.classList.remove("-translate-x-full");
            overlay?.classList.remove("hidden");
        };
        const close = () => {
            drawer.classList.add("-translate-x-full");
            overlay?.classList.add("hidden");
        };

        toggle.addEventListener("click", open);
        overlay?.addEventListener("click", close);

        document.querySelectorAll("[data-spa-item]").forEach((el) => {
            el.addEventListener("click", () => {
                if (window.innerWidth < 1024) close();
            });
        });
    }

    // ─── Back / Forward ───────────────────────────────────────────────────────────
    window.addEventListener("popstate", function (e) {
        const slug = e.state?.slug ?? slugFromPath();
        if (slug) {
            loadTool(slug, false);
        } else {
            setActiveItem(null);
            if (panel()) panel().innerHTML = "";
            if (welcome()) welcome().style.display = "";
        }
    });

    // ─── Derive slug from URL ─────────────────────────────────────────────────────
    function slugFromPath() {
        const parts = location.pathname
            .replace(BASE_PATH, "")
            .split("/")
            .filter(Boolean);
        return parts[0] ?? null;
    }

    // ─── Boot ─────────────────────────────────────────────────────────────────────
    document.addEventListener("DOMContentLoaded", function () {
        bindSidebarItems();
        bindCategoryFilter();
        bindMobileToggle();

        const initialSlug = slugFromPath();
        if (initialSlug && initialSlug !== "ui" && initialSlug !== "api") {
            loadTool(initialSlug, false);
        }
    });
})();
