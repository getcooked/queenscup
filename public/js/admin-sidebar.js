(function () {
    'use strict';

    /*
     * Sidebar icons are inlined Bootstrap Icons (MIT licence) rather than the
     * bootstrap-icons webfont. The font was pulled in through an @import inside
     * admin-shell.css, which forced the browser to fetch the shell stylesheet,
     * then the icon stylesheet, then the woff2 before a single glyph could
     * paint. Only these seven icons are ever used, so they ship as markup and
     * render on the first frame with no network request at all.
     */
    var ICON_PATHS = {
        dashboard: '<path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.39.39 0 0 0-.029-.518z"/><path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A8 8 0 0 1 0 10m8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3"/>',
        pos: '<path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0"/><path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z"/><path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z"/><path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567"/>',
        reservations: '<path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>',
        orders: '<path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0z"/><path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5"/>',
        inventory: '<path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>',
        reports: '<path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1zm1 12h2V2h-2zm-3 0V7H7v7zm-5 0v-3H2v3z"/>',
        settings: '<path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/><path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>',
        profile: '<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>'
    };

    function iconMarkup(page) {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false">' + ICON_PATHS[page] + '</svg>';
    }

    function pageForLink(link) {
        var dataPage = link.getAttribute('data-page') || link.getAttribute('data-current-page');
        if (dataPage && ICON_PATHS[dataPage]) return dataPage;

        try {
            var path = new URL(link.href, window.location.origin).pathname.replace(/\/+$/, '');
            if (path === '/dashboard') return 'dashboard';
            if (path === '/pos') return 'pos';
            if (path === '/orders' || path === '') return 'orders';
            if (path === '/reservations') return 'reservations';
            if (path === '/inventory') return 'inventory';
            if (path === '/reports') return 'reports';
            if (path === '/settings') return 'settings';
            if (path === '/profile') return 'profile';
        } catch (error) {
            return '';
        }

        return '';
    }

    function selectedBranch() {
        try {
            var stored = localStorage.getItem('qc_branch');
            if (!stored) return '';
            var branch = JSON.parse(stored);
            return typeof branch === 'string' ? branch : '';
        } catch (error) {
            return '';
        }
    }

    function orderCounts() {
        var counts = { active: 0, cashPending: 0 };

        try {
            var orders = JSON.parse(localStorage.getItem('qc_orders') || '[]');
            if (!Array.isArray(orders)) return counts;

            /*
             * Fall back to counting every branch when no branch has been picked
             * yet. The old code hard-coded 'kotapark', so an MCC order never
             * reached the badge and the count silently sat at zero.
             */
            var branch = selectedBranch();

            orders.forEach(function (order) {
                if (!order) return;
                if (branch && order.branch && order.branch !== branch) return;
                if (order.status === 'pending' || order.status === 'preparing') counts.active++;
                if (order.paymentStatus === 'pending') counts.cashPending++;
            });
        } catch (error) {
            return counts;
        }

        return counts;
    }

    function setNavIcon(link, page) {
        var icon = link.querySelector('i');
        if (!icon || !ICON_PATHS[page]) return;
        if (icon.getAttribute('data-nav-icon') === page) return;

        icon.className = 'nav-icon';
        icon.setAttribute('data-nav-icon', page);
        icon.setAttribute('aria-hidden', 'true');
        icon.innerHTML = iconMarkup(page);
    }

    function badge(link, marker, extraClass) {
        var el = link.querySelector('[' + marker + ']');
        if (el) return el;

        el = document.createElement('span');
        el.className = 'nav-badge ' + extraClass;
        el.setAttribute(marker, '');
        link.appendChild(el);
        return el;
    }

    function setOrdersIndicator(link, counts) {
        /*
         * The orders page renders and maintains both badges itself, scoped to
         * the signed-in user (a guest only counts their own orders). Leave its
         * numbers alone there and only keep the pending badge visible.
         */
        var managed = link.querySelector('#pendingOrdersBadge');
        if (managed) {
            managed.classList.add('nav-badge', 'orders-indicator');
            managed.style.display = 'inline-flex';
            return;
        }

        var indicator = badge(link, 'data-orders-indicator', 'orders-indicator');
        var label = counts.active + ' active order' + (counts.active === 1 ? '' : 's');
        if (indicator.textContent !== String(counts.active)) indicator.textContent = String(counts.active);
        indicator.style.display = 'inline-flex';
        indicator.setAttribute('aria-label', label);
        indicator.title = label;

        /*
         * Mirror the orders page cash-pending badge so the Orders row looks the
         * same on every page instead of growing a second badge only there.
         */
        var cash = badge(link, 'data-cash-pending-indicator', 'cash-pending');
        var cashLabel = counts.cashPending + ' order' + (counts.cashPending === 1 ? '' : 's') + ' awaiting payment';
        if (cash.textContent !== String(counts.cashPending)) cash.textContent = String(counts.cashPending);
        cash.style.display = counts.cashPending > 0 ? 'inline-flex' : 'none';
        cash.setAttribute('aria-label', cashLabel);
        cash.title = cashLabel;
    }

    function refresh() {
        var counts = orderCounts();

        document.querySelectorAll('.sidebar .nav-item').forEach(function (link) {
            var page = pageForLink(link);
            setNavIcon(link, page);
            if (page === 'orders') setOrdersIndicator(link, counts);
        });
    }

    function init() {
        refresh();

        var scheduled = false;
        var observer = new MutationObserver(function () {
            if (scheduled) return;
            scheduled = true;
            window.requestAnimationFrame(function () {
                scheduled = false;
                refresh();
            });
        });

        document.querySelectorAll('.sidebar-nav').forEach(function (nav) {
            observer.observe(nav, { childList: true, subtree: true });
        });

        /* A storage event only fires in other tabs, so poll for same-tab edits. */
        window.addEventListener('storage', function (event) {
            if (!event.key || event.key === 'qc_orders' || event.key === 'qc_branch') refresh();
        });

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) refresh();
        });

        window.addEventListener('pageshow', function () { refresh(); });
        window.setInterval(function () {
            if (!document.hidden) refresh();
        }, 5000);

        window.refreshAdminSidebar = refresh;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
