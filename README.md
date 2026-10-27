# Iran Crane Platform

Laravel and Blade application for a crane catalog, project quote requests, direct orders, and manual payment review.

## Local setup

1. Use PHP 8.3+, Composer, Node.js, and a supported database (SQLite is the default example).
2. Copy `.env.example` to `.env`, then set `APP_KEY`, database credentials, and any mail configuration for your environment.
3. **Never commit `.env` or any file containing credentials.** This repository intentionally has no `.gitignore`; each developer and deployment process must prevent `.env`, local databases, build output, logs, and secrets from being staged.
4. Install dependencies with `composer install`, install frontend dependencies with `npm install`, generate the key with `php artisan key:generate`, run `php artisan migrate`, and build assets with `npm run build`.
5. To load sample records locally or in testing, set a unique `LOCAL_DEMO_ADMIN_PASSWORD` in your local `.env`, then run `php artisan db:seed`. The seeded administrator (`admin@souri.test`) is created **only** when `APP_ENV` is `local` or `testing`. Its fallback password is `local-demo-password-change-me`; change it before using the account. No demo administrator is created in production.

The sandbox used for this work cannot load PHP dependencies. Composer, Artisan, and PHP test commands were deliberately not run here. Run migrations and the application test suite in a local or CI environment with installed dependencies before release.

## Roles and privacy

- `super_admin` can manage all modules, settings, and user roles.
- `store_manager` can manage categories and products.
- `content_manager` can manage articles and case studies.
- `sales_manager` can manage quote requests, orders, and manual payment decisions.

Only `super_admin` and `sales_manager` may view orders, payment submissions, quote-request customer data, or sales dashboard data. Authorization is enforced by controllers, not sidebar visibility.

## Demo-mode visibility

Demo mode is off by default outside local/testing seeds. When disabled, public queries hide every `is_demo` category, navigation item, product, featured-home product, catalog/product-detail record, article, and case study. Administrators can still edit this hidden content and future non-demo content remains visible. The seed process does not overwrite an existing demo-mode choice.

## Sales and payment lifecycle

Products may inherit the global sales mode or use `direct`, `quote`, or `both`. Any active product whose effective mode permits direct purchase must have a strictly positive IRR price. Quote-only products may leave the price empty. Changing the global mode to `direct` or `both` is rejected if an active inherited product lacks a positive price.

A direct order is created with an immutable item-price snapshot and enters `pending_payment`. The customer then submits an off-platform bank-transfer or cash-deposit reference. A sales manager or super administrator can approve it (`paid`) or cancel it (`cancelled`) while it is `payment_submitted`. There is no online payment gateway in this release.

## PWA and TWA status

The public layout links the web app manifest, self-contained SVG any/maskable icons, service worker, and localized offline page. The service worker only caches these static shell files and returns the offline page for failed anonymous public document navigation; it intentionally does not cache admin, cart, checkout, order, or customer data.

This is **not TWA release-ready**. `public/.well-known/assetlinks.json` is deliberately an invalid-for-association placeholder, not a verified Digital Asset Links statement. Before an Android/TWA or Play release, the release owner must supply the real Android application ID and the SHA-256 fingerprint of the production signing certificate, replace the entire placeholder with a valid Digital Asset Links JSON array, verify the HTTPS origin, sign and test the Android artifact, and complete store privacy, data-safety, access, rollout, monitoring, and rollback requirements. Do not invent either value.

The PWA still requires device/browser installation, offline, update, and accessibility validation in the target deployment before it can be called release-ready.
