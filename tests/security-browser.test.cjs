const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');
const root = path.resolve(__dirname, '..');

test('HTML escaping keeps attacker-controlled text out of markup', () => {
    const source = fs.readFileSync(path.join(root, 'resources/views/orders.blade.php'), 'utf8');
    const escape = source.match(/function escapeHtml\(value\)\{[\s\S]*?\n\}/)[0];
    const context = vm.createContext({});
    vm.runInContext(escape, context);
    assert.equal(context.escapeHtml('<img src=x onerror="alert(1)">'), '&lt;img src=x onerror=&quot;alert(1)&quot;&gt;');
    assert.equal(context.escapeHtml("' & <script>"), '&#039; &amp; &lt;script&gt;');
});

test('receipt renders malicious customer and product names as text', () => {
    const source = fs.readFileSync(path.join(root, 'resources/views/orders.blade.php'), 'utf8');
    const payload = '<img src=x onerror="alert(1)">';
    const receipt = {};
    const context = vm.createContext({
        document: { getElementById: () => receipt },
        getBranchInfo: () => ({ name: 'Kota Park', address: 'Madridejos' }),
        getLogoSrc: () => '/logo.png',
        paymentInfo: () => ({ label: 'Cash', pendingLabel: 'Pending', paidLabel: 'Paid' }),
    });
    for (const name of ['escapeHtml', 'generateReceipt']) {
        vm.runInContext(source.match(new RegExp('function ' + name + '\\([^]*?\\n\\}'))[0], context);
    }
    context.generateReceipt({
        id: 1, customer: payload, type: 'Dine In', payment: 'Cash', paymentStatus: 'pending',
        items: [{ name: payload, size: 'R', qty: 1, price: 79 }], subtotal: 79, total: 79, discount: 0,
    });
    assert.ok(!receipt.innerHTML.includes(payload));
    assert.equal(receipt.innerHTML.match(/&lt;img/g).length, 2);
});

function worker() {
    const handlers = {};
    vm.runInNewContext(fs.readFileSync(path.join(root, 'public/sw.js'), 'utf8'), {
        URL,
        self: {
            location: { origin: 'https://shop.test' },
            addEventListener: (name, handler) => { handlers[name] = handler; },
        },
        fetch: async () => ({ ok: true, headers: { get: () => 'no-store, private' } }),
        caches: { open: () => { throw new Error('Private response must not be cached'); } },
    });
    return handlers;
}

test('service worker never intercepts private pages or API responses', () => {
    const handlers = worker();
    for (const url of ['/orders', '/chat', '/customer/csrf-token', '/api/v1/auth/me', '/api/v1/my/reservations', '/staff/dashboard/sales', '/manifest.webmanifest?private=1', 'https://other.test/manifest.webmanifest']) {
        handlers.fetch({
            request: { method: 'GET', url: new URL(url, 'https://shop.test').href },
            respondWith: () => assert.fail(`Intercepted ${url}`),
        });
    }
});

test('even allowlisted assets respect private/no-store response headers', async () => {
    let response;
    worker().fetch({
        request: { method: 'GET', url: 'https://shop.test/manifest.webmanifest' },
        respondWith: promise => { response = promise; },
    });
    assert.equal((await response).ok, true);
});
