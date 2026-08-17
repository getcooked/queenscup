(function () {
    'use strict';

    var iconByPage = {
        dashboard: 'bi-speedometer2',
        pos: 'bi-cash-register',
        orders: 'bi-receipt',
        inventory: 'bi-box-seam',
        reports: 'bi-bar-chart-line',
        settings: 'bi-gear',
        profile: 'bi-person-circle'
    };

    function pageForLink(link) {
        var dataPage = link.getAttribute('data-page') || link.getAttribute('data-current-page');
        if (dataPage && iconByPage[dataPage]) return dataPage;

        try {
            var path = new URL(link.href, window.location.origin).pathname.replace(/\/+$/, '');
            if (path === '/dashboard') return 'dashboard';
            if (path === '/pos') return 'pos';
            if (path === '/orders' || path === '') return 'orders';
            if (path === '/inventory') return 'inventory';
            if (path === '/reports') return 'reports';
            if (path === '/settings') return 'settings';
            if (path === '/profile') return 'profile';
        } catch (error) {
            return '';
        }

        return '';
    }

    function activeOrderCount() {
        try {
            var orders = JSON.parse(localStorage.getItem('qc_orders') || '[]');
            if (!Array.isArray(orders)) return 0;

            return orders.filter(function (order) {
                return order &&
                    order.branch === 'kotapark' &&
                    (order.status === 'pending' || order.status === 'preparing');
            }).length;
        } catch (error) {
            return 0;
        }
    }

    function setBootstrapIcon(link, page) {
        var icon = link.querySelector('i');
        if (!icon || !iconByPage[page]) return;

        icon.className = 'bi ' + iconByPage[page];
        icon.setAttribute('aria-hidden', 'true');
    }

    function setOrdersIndicator(link, count) {
        var indicator = link.querySelector('[data-orders-indicator], #pendingOrdersBadge');

        if (!indicator) {
            indicator = document.createElement('span');
            indicator.className = 'nav-badge orders-indicator';
            indicator.setAttribute('data-orders-indicator', '');
            link.appendChild(indicator);
        } else {
            indicator.classList.add('nav-badge', 'orders-indicator');
            indicator.setAttribute('data-orders-indicator', '');
        }

        var label = count + ' active order' + (count === 1 ? '' : 's');
        if (indicator.textContent !== String(count)) indicator.textContent = String(count);
        indicator.style.display = 'inline-flex';
        indicator.setAttribute('aria-label', label);
        indicator.title = label;
    }

    function refresh() {
        var count = activeOrderCount();

        document.querySelectorAll('.sidebar .nav-item').forEach(function (link) {
            var page = pageForLink(link);
            setBootstrapIcon(link, page);
            if (page === 'orders') setOrdersIndicator(link, count);
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

        window.addEventListener('storage', function (event) {
            if (event.key === 'qc_orders') refresh();
        });

        window.refreshAdminSidebar = refresh;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
