# Deploying to Hostinger

Written for Hostinger shared hosting. The awkward parts are the document root,
the `.env`, and the fact that `vendor/` and `.env` are gitignored and so are
never in the repository you upload.

## 1. Document root

Laravel must be served from `public/`, never from the project root. Serving the
root exposes `.env`, `storage/` and every source file to the internet.

Two ways, pick one:

**Point the domain at `public/` (preferred).** In hPanel go to *Websites →
Manage → Advanced → Change website root* and set it to the `public` folder of
where you uploaded the project, for example `/domains/yourdomain.com/app/public`.

**Or split it.** Upload the project somewhere outside the web root (say
`/home/uXXXX/app`), then copy `app/public/*` into `public_html/` and edit
`public_html/index.php` so both requires point at the real location:

```php
require __DIR__.'/../app/vendor/autoload.php';
$app = require_once __DIR__.'/../app/bootstrap/app.php';
```

## 2. Dependencies

`vendor/` is gitignored, so it will not arrive with a `git clone` or a repo zip.

With SSH (Business plans and up):

```bash
cd ~/domains/yourdomain.com/app
composer install --no-dev --optimize-autoloader
```

Without SSH, run `composer install --no-dev --optimize-autoloader` on your own
machine and upload the resulting `vendor/` folder over FTP.

## 3. Environment

`.env` is gitignored too, so create it on the server from `.env.example`:

```
APP_NAME="Queen's Cup"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=uXXXXXXXX_queenscup
DB_USERNAME=uXXXXXXXX_queenscup
DB_PASSWORD=...

QC_TAKEOUT_FEE_PER_CUP=5.00
```

`APP_DEBUG=false` matters: with it on, any error page prints your database
credentials to whoever triggered it.

Then generate the key. Without it every encrypted cookie and session fails:

```bash
php artisan key:generate --force
```

No SSH? Generate one locally with `php artisan key:generate --show` and paste
the `base64:...` value into `APP_KEY` by hand.

## 4. Database

Create the database and user in hPanel → *Databases → MySQL*, put the
credentials in `.env`, then:

```bash
php artisan migrate --force
```

`--force` is required because Laravel refuses to migrate in production without
it. This creates the `reservations`, `reservation_items` and `device_tokens`
tables the app needs.

If you have no SSH, Hostinger's *Advanced → Cron jobs* can run a one-off
command, or import the schema through phpMyAdmin.

## 5. Storage and permissions

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

`storage:link` is what makes uploaded product images reachable at
`/storage/inventory/...`. If symlinks are blocked on your plan, copy
`storage/app/public` into `public/storage` instead and re-copy after uploads.

## 6. Caches

Run these after every deploy, and always in this order:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If you change `.env` afterwards the change is ignored until you re-run
`config:cache`, which is a common and confusing trap. To undo:
`php artisan config:clear`.

## 7. HTTPS

Enable the free SSL certificate in hPanel and force HTTPS.

`app/Http/Middleware/TrustProxies.php` already trusts the forwarded headers.
That is required here: Hostinger terminates TLS at a proxy, so without it
Laravel thinks the request is plain HTTP and `asset()` emits `http://` URLs on
an `https://` page. Browsers block those as mixed content, and the stylesheets,
product images and the icon font silently fail to load.

## 8. Push notifications

Only needed for the Android app.

1. Create a Firebase project, add an Android app with the id
   `ph.queenscup.customer`, and download `google-services.json` into
   `android/app/`.
2. In Firebase, *Project settings → Service accounts → Generate new private
   key*. Upload that JSON **outside** `public_html`, for example
   `/home/uXXXX/private/fcm.json`.
3. Add to `.env`:

   ```
   FCM_PROJECT_ID=your-project-id
   FCM_CREDENTIALS_PATH=/home/uXXXX/private/fcm.json
   ```

Leaving these empty disables push; nothing else breaks, and the app falls back
to polling while it is open.

Never put that key inside `public/` — it grants the ability to send push to
every one of your users.

## 9. Point the app at the live site

In `android/gradle.properties`:

```
QC_API_BASE_URL_RELEASE=https://yourdomain.com/api/v1/
```

Then build the APK:

```bash
cd android
./gradlew assembleRelease
```

The trailing slash matters. Without setting this, a release build inherits the
debug URL (`10.0.2.2`, the emulator's alias for your own PC) and will not reach
your server from a real phone.

## Checklist

- [ ] Document root points at `public/`
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `.env` created, `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_KEY` generated
- [ ] `php artisan migrate --force`
- [ ] `php artisan storage:link`
- [ ] `storage/` and `bootstrap/cache/` writable
- [ ] config, route and view caches built
- [ ] HTTPS on and forced
- [ ] Visit `/staff-login` and sign in
