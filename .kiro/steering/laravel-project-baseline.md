---
inclusion: auto
---

# Laravel project baseline

Apply this baseline to Laravel work unless the repository documents an approved exception.

## Architecture and product delivery
- Default to server-rendered Blade plus Livewire. A different frontend architecture, framework version, package version, hosting platform, and external provider each require an explicit decision with rationale, owner, cost/risk, and rollback path.
- Keep features focused. Define success, empty, loading, validation, authorization-denied, error, and recovery states as applicable.
- Preserve responsive, semantic, keyboard-operable interfaces with visible focus, accessible names, sufficient contrast, appropriate landmarks, and reduced-motion support.

## Security, authorization, and privacy
- Enforce authentication, authorization, validation, CSRF protection, rate limiting, and business rules on the server. Browser and Livewire state are untrusted inputs.
- Apply least privilege. Keep secrets out of source, client output, logs, analytics, and error responses. Treat webhook payloads, uploads, third-party APIs, and AI output as untrusted.
- Collect, retain, log, and share the least personal data required. Obtain required consent and document AI data flow, retention, provider use, access controls, and human review. Do not send sensitive data to AI services without explicit approval.

## Application data and concurrency
- For every mutation, analyze duplicate submissions, concurrent requests, stale reads, retries, idempotency, transaction boundaries, locks or optimistic conflict detection, and user-visible conflict handling.
- Make MySQL the final integrity boundary. Use appropriate column types, foreign keys, unique constraints, check constraints where the chosen MySQL version supports them, indexes, and transactions. State version-dependent behavior explicitly.
- Use safe, reversible migrations where possible. Plan backfills, compatibility windows, data preservation, and rollback before destructive or irreversible changes.

## Quality and performance
- Add or update automated tests for behavior, policy boundaries, validation, failure cases, and concurrent mutation behavior where relevant. Run the repository's targeted checks and report checks not run.
- Avoid N+1 queries, unbounded result sets, blocking external calls in requests, and unnecessary client payloads. Measure before making optimization claims.
- Add actionable structured logs, metrics, health signals, and error reporting appropriate to the chosen platform. Do not log secrets or sensitive payloads.

## Images and media
- Validate uploads by content and size, store them outside executable paths, authorize access, and use safe generated filenames.
- Define image transformations, responsive sizes, lazy loading, alt text, cache strategy, storage, and fallback behavior. Keep image-processing, storage, and CDN/provider choices explicit.

## Deployment and rollback
- Define configuration, schema migration order, queue/worker compatibility, cache invalidation, health checks, monitoring, alert thresholds, release ownership, and rollback before production changes.
- Do not deploy, change DNS, publish an app, or activate a paid/external provider without explicit approval. Ensure rollback is compatible with the running application and data state.

## SEO, schema, and Search Console
- Use accurate titles, descriptions, canonical URLs, robots directives, sitemap coverage, and accessible crawlable content. Add only schema.org structured data that truthfully matches visible content and validate it.
- Treat Google Search Console property verification, sitemap submission, indexing review, and performance monitoring as explicit release tasks. Do not use deceptive SEO techniques.

## PWA and Android Trusted Web Activity
- A PWA and Android/Google Play Trusted Web Activity (TWA) are separate release surfaces. Define ownership and acceptance criteria for both.
- For the PWA, specify a valid web app manifest, icons, installability, service-worker lifecycle, cache rules, offline strategy and fallback, update behavior, network failure behavior, and recovery from stale cached content.
- For the TWA, verify the Android package/application ID, launch URL scope, verified origin, `assetlinks.json` hosting, Digital Asset Links certificate fingerprints, Android signing key custody, signed release artifact, versioning, target/compile SDK decisions, and testing on supported devices.
- Before Google Play release, complete store listing, privacy/data-safety disclosures, content rating, app access instructions, policy declarations, signing/app-bundle checks, pre-launch testing, production rollout/monitoring, and rollback or halt plan. Keep Play policy and Android toolchain versions as explicit current decisions.
