# Queen's Cup — customer app

Native Android client for reserving drinks. Customers browse the menu, reserve
for dine in or take out, and get a push notification the moment the counter
marks their order ready.

It talks to the Laravel API in this same repository (`routes/api.php`, prefix
`/api/v1`). Nothing is priced on the device: the basket is sent to the server,
which returns the totals, so the app and the counter can never disagree.

## Layout

```
app/src/main/java/ph/queenscup/customer/
  data/
    api/          Retrofit service + client (bearer token read per call)
    local/        DataStore: name, token, reservation references
    model/        Serializable API models
    repository/   ReservationRepository, the only thing screens talk to
  ui/
    menu/         Browse and add to basket
    cart/         Service type, server quote, confirm
    track/        Status trail, lookup by reference, cancel
    account/      Who you are reserving as, how it works
  push/           FCM receiver for "your order is ready"
```

Four bottom-navigation tabs: **Menu**, **Reserve**, **Track**, **Account**. The
Reserve tab carries a badge with the current cup count.

## Running it

1. Start the Laravel API:

   ```
   php artisan migrate
   php artisan serve
   ```

2. Point the app at your server. The default is the emulator's alias for the
   host machine:

   ```kotlin
   // app/build.gradle.kts
   buildConfigField("String", "API_BASE_URL", "\"http://10.0.2.2:8000/api/v1/\"")
   ```

   On a physical device use your machine's LAN address, and add that host to
   `res/xml/network_security_config.xml` if you are still on plain HTTP.

3. Open the `android/` folder in Android Studio and run.

## Push notifications

Push is optional. Without Firebase the app still shows live status, because the
Track tab polls every 20 seconds while a reservation is open.

To turn on real push:

1. Create a Firebase project and add an Android app with the application id
   `ph.queenscup.customer` (or `ph.queenscup.customer.debug` for debug builds).
2. Download `google-services.json` into `android/app/`. It is gitignored.
3. In Firebase, open *Project settings -> Service accounts -> Generate new
   private key* and save the JSON somewhere the Laravel server can read.
4. Add to the Laravel `.env`:

   ```
   FCM_PROJECT_ID=your-firebase-project-id
   FCM_CREDENTIALS_PATH=/absolute/path/to/service-account.json
   ```

The server sends over FCM HTTP v1. Leaving those variables empty makes sending
a silent no-op rather than an error.

## Take-out surcharge

Take out adds a fee per **cup**, not per line, and dine in never pays it. The
amount lives in `config/queenscup.php` on the server
(`queenscup.takeout_fee_per_cup`, default 5.00) and reaches the app through the
menu and quote endpoints, so it is never hardcoded here.
