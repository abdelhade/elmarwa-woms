/**
 * Work Order Management System — Global JS
 */
(function () {
    "use strict";

    // ── Dark / Light Toggle ───────────────────────────────────
    const THEME_KEY = "woms-theme";

    function getStoredTheme() {
        return localStorage.getItem(THEME_KEY) || "light";
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute("data-bs-theme", theme);
        localStorage.setItem(THEME_KEY, theme);

        var icon = document.getElementById("themeIcon");
        if (icon) {
            icon.className =
                theme === "dark" ? "bi bi-sun-fill" : "bi bi-moon-fill";
        }
    }

    // Apply on load (before DOMContentLoaded to avoid flash)
    applyTheme(getStoredTheme());

    document.addEventListener("DOMContentLoaded", function () {
        // Re-apply to update icon
        applyTheme(getStoredTheme());

        // Toggle button
        var toggleBtn = document.getElementById("themeToggle");
        if (toggleBtn) {
            toggleBtn.addEventListener("click", function () {
                var current = getStoredTheme();
                applyTheme(current === "dark" ? "light" : "dark");
            });
        }

        // ── Sidebar toggle (mobile) ──────────────────────────
        var sidebarToggle = document.getElementById("sidebarToggle");
        var sidebar = document.getElementById("sidebar");
        var overlay = document.getElementById("sidebarOverlay");

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener("click", function () {
                sidebar.classList.toggle("show");
                if (overlay) overlay.classList.toggle("show");
            });
        }

        if (overlay) {
            overlay.addEventListener("click", function () {
                sidebar.classList.remove("show");
                overlay.classList.remove("show");
            });
        }

        // ── Global Keyboard Shortcuts ───────────────────────
        
        // 1. Prevent Form Submission on Enter (except in textareas)
        document.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                const target = e.target;
                // If the target is an input (but not a submit/button/textarea), prevent submission
                if (
                    (target.tagName === "INPUT" && !["submit", "button"].includes(target.type)) ||
                    target.tagName === "SELECT"
                ) {
                    e.preventDefault();
                    return false;
                }
            }

            // 2. Global Save with F12
            if (e.key === "F12") {
                // Find all forms
                const forms = document.getElementsByTagName("form");
                if (forms.length > 0) {
                    e.preventDefault();
                    
                    // Priority 1: A form that has an id containing 'Create' or 'Edit' or 'Form'
                    // Priority 2: Use the last form that is not a logout form
                    let formToSubmit = null;
                    for (let f of forms) {
                        // Skip logout forms
                        if (f.action.includes('logout')) continue;
                        
                        // If it's a visible form, it's a good candidate
                        if (f.offsetWidth > 0 || f.offsetHeight > 0) {
                            formToSubmit = f;
                        }
                    }

                    if (formToSubmit) {
                        // Check if it's already being submitted to avoid duplicates
                        if (!formToSubmit.dataset.submitting) {
                            formToSubmit.dataset.submitting = "true";
                            formToSubmit.submit();
                        }
                    }
                }
            }
        });
    });
})();
