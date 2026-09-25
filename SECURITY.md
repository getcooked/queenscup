# Application security controls

Laravel responses receive CSP, clickjacking protection, MIME sniffing prevention,
referrer and permissions policies, and private/no-store cache headers. HTTPS
responses also receive one-year HSTS without includeSubDomains or preload.
Inline script blocks use a fresh nonce for each response. Existing inline event
handlers and styles remain allowed for compatibility; migrating handlers to
addEventListener is required before removing `script-src-attr 'unsafe-inline'`.
This CSP is defense in depth, not a guarantee that all XSS is eliminated.

Chat formatting is reconstructed from text and a small set of formatting tags,
without copying attributes. Customer/product text in receipts, notifications and
toasts is HTML-escaped. Quick replies use textContent and event listeners.
The service worker caches only explicitly listed public assets and removes old
Queen's Cup caches, which could contain private customer or staff responses.

Responses also send COOP/CORP same-origin, `frame-src 'none'`, and (over HTTPS)
`upgrade-insecure-requests`. `public/.htaccess` mirrors the headers on static
files, applies a strict CSP to directly opened SVG/HTML/XML, refuses dotfiles and
backup/config files, and blocks TRACE/TRACK.

Because every proxy is trusted with `X-Forwarded-Host`, `TrustHosts` restricts
the host to `APP_URL` and its subdomains outside local/testing. **`APP_URL` must be
the real public URL in production**, or requests are rejected with 400.

Unauthenticated reservation lookup/cancel, quote, device-token and chat
endpoints have their own tighter rate limits to slow reference guessing.

Customer login, registration, verification, OTP and chat endpoints are throttled.
Already-verified accounts must use password login; the verification endpoint
cannot issue a session or API token merely from a known email address.
Laravel CSRF protection remains enabled on browser mutations. Session cookies
remain HttpOnly and SameSite=Lax, and default to Secure in production.

Deploy behind HTTPS with `APP_ENV=production`, `APP_DEBUG=false`, and
`SESSION_SECURE_COOKIE=true`. Configure trusted proxies correctly if TLS ends at
a reverse proxy. Mirror relevant headers on static files at the web server;
Laravel middleware does not run for files served directly by the web server.
The CSP permits self-hosted scripts and Google Fonts, not Vite's development
server. Current Blade pages use public assets directly.

Checks: `php artisan test --filter=SecurityHardeningTest` and
`node --test tests/security-browser.test.cjs`.

Guidance: [OWASP XSS prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
and [OWASP security headers](https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html).
