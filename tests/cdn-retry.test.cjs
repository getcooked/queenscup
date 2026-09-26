const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

const source = fs.readFileSync(path.resolve(__dirname, '../public/js/cdn-retry.js'), 'utf8');
const ORIGIN = 'https://queenscup-pos.com';

// Loads the script against a scripted sequence of responses and reports how
// many requests actually went out.
function load(responses) {
    const calls = [];
    const window = {
        location: { href: ORIGIN + '/orders', origin: ORIGIN },
        fetch: (input, init) => {
            calls.push({ input, init });
            return Promise.resolve(responses[Math.min(calls.length - 1, responses.length - 1)]);
        },
    };
    vm.runInContext(source, vm.createContext({ window, URL, Headers, Promise, setTimeout: (fn) => fn() }));
    return { fetch: window.fetch, calls };
}

function reply(status, contentType) {
    return { status, headers: new Headers({ 'Content-Type': contentType }) };
}

const cdnPage = reply(404, 'text/html');
const json = (status) => reply(status, 'application/json');
const asJson = { method: 'POST', headers: { Accept: 'application/json' } };

test('a JSON request answered with the CDN error page is sent again', async () => {
    const { fetch, calls } = load([cdnPage, reply(403, 'text/html'), json(200)]);
    const response = await fetch('/customer/login', asJson);
    assert.equal(response.status, 200);
    assert.equal(calls.length, 3);
});

test('a real JSON 404 from the app is not retried', async () => {
    const { fetch, calls } = load([json(404)]);
    const response = await fetch('/api/v1/reservations/QC-NOPE', asJson);
    assert.equal(response.status, 404);
    assert.equal(calls.length, 1);
});

test('it gives up after two retries and hands back the last response', async () => {
    const { fetch, calls } = load([cdnPage]);
    const response = await fetch('/customer/login', asJson);
    assert.equal(response.status, 404);
    assert.equal(calls.length, 3);
});

test('requests that do not ask for JSON are left alone', async () => {
    const { fetch, calls } = load([cdnPage, json(200)]);
    await fetch('/staff-logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': 't' } });
    assert.equal(calls.length, 1);
});

test('other sites are never retried', async () => {
    const { fetch, calls } = load([cdnPage, json(200)]);
    await fetch('https://example.com/api', asJson);
    assert.equal(calls.length, 1);
});

test('a Headers object is read as well as a plain object', async () => {
    const { fetch, calls } = load([cdnPage, json(200)]);
    await fetch('/chat', { headers: new Headers({ Accept: 'application/json' }) });
    assert.equal(calls.length, 2);
});
