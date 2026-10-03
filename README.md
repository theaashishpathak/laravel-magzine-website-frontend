# Blogger4U

An AI-ready, multi-role news and magazine CMS built on Laravel and Livewire. Blogger4U powers a full editorial workflow — from drafting and review to publishing and reader engagement — across three dedicated portals: **Super Admin**, **Author/Editorial**, and **Visitor/Subscriber**.

Blogger4U is a clean, purpose-built Laravel application with a normalized multi-language content schema, rich editorial workflow, and AI assistant capabilities.

---

## Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Roles & Permissions](#roles--permissions)
- [Database Design](#database-design)
- [Project Structure](#project-structure)
- [Local Setup](#local-setup)
- [Deployment](#deployment)
- [Media Pipeline](#media-pipeline)
- [Troubleshooting](#troubleshooting)
- [Roadmap Status](#roadmap-status)
- [License](#license)

---

## Overview

Blogger4U provides:

- **Three dedicated portals** — Super Admin, Author, and Visitor — each with its own dashboard and permission surface.
- **Role-based access control** across 10 staff/subscriber roles via Spatie Permission.
- **Multi-language-ready content architecture** — posts and their translatable fields (title, slug, content, SEO metadata) are fully decoupled, so new languages can be added without schema changes.
- **A reactive, JavaScript-light frontend** via Livewire — dashboards, subscriber management, and the editorial workflow update live without hand-written API/JS glue code.
- **Full SEO parity with the legacy site** — meta fields, canonical URLs, Open Graph tags, XML sitemap, robots.txt, and 301 redirect mapping from old WordPress URLs.
- **A migrated content archive** — users, categories, posts, and physical media assets imported from the original WordPress installation.

### Why Laravel over WordPress
The move off WordPress was driven by:
1. **Performance & clean data** — no plugin-query bloat; normalized relational schema instead.
2. **Role-specific portals** — distinct Admin/Author/Visitor dashboards that WordPress can't natively support.
3. **Multi-language foundation** — `posts` + `post_translations` split, ready for additional languages.
4. **Custom editorial & AI workflows** — room to build automated content tooling that a plugin-based CMS can't accommodate cleanly.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel `^13.0` |
| Runtime | PHP `^8.3` |
| Reactive UI | Livewire `^4.1` (ships its own bundled Alpine.js — no separate Alpine dependency needed) |
| Styling / Build | Tailwind CSS `^4.0.7` (via `@tailwindcss/vite`), Vite `^8.0.0`, `laravel-vite-plugin` |
| Rich Text Editor | TipTap `^2.10.3` (core + table, link, image, youtube, typography, underline, text-align, placeholder extensions) |
| Auth | Laravel Fortify `^1.36` |
| RBAC | Spatie Laravel Permission `^7.3` |
| Audit Logging | Spatie Laravel Activitylog `^4.12` |
| Import/Export | Maatwebsite Excel `^3.1` |
| PDF Generation | Barry vd. Heuvel's Laravel DomPDF `^3.1` |
| AI Dev Tooling | Laravel Boost `^2.2` (dev dependency — MCP-based AI coding context for this repo) |
| Testing | Pest `^4.5` (`pestphp/pest`, `pest-plugin-laravel`) |
| Linting | Laravel Pint `^1.27` |
| Database | MySQL 8.0 |
| Hosting | Hostinger shared hosting (SSH access) |
| Package Manager | Composer 2.x, npm |
| License | MIT |

---

## Architecture

Blogger4U follows Laravel's standard **MVC** pattern, with Livewire components replacing most traditional controller+API-endpoint pairs for interactive UI.

### Portal Routing

```
/dashboard/my        → Super Admin command centre
/dashboard/author     → Author / Editorial studio
/visitor/dashboard    → Reader / Subscriber hub
/login                → Unified authentication gateway
/admin                → Redirects to /dashboard
```

Each `User` has a `portal_type` column (`admin`, `author`, `visitor`). On login, Fortify's custom `LoginResponse` inspects `portal_type` and the user's role(s) to route them to the correct portal.

> **Note:** `portal_type` controls *which dashboard UI* a user lands on. Spatie roles/permissions control *what they're allowed to do* within it — the two systems are independent but used together.

### Reactive UI (Livewire)

Livewire components live in `app/Livewire/`, paired with Blade views in `resources/views/livewire/`. A component is a PHP class holding state and methods (e.g. `activate()`, `promoteToAuthor()`); Blade templates wire UI elements to those methods (`wire:click="activate(...)"`) and Livewire handles the round-trip without any hand-written JavaScript or REST endpoint.

Where a pure-JS widget is needed (TipTap), the component's editor region is wrapped in `wire:ignore` and bridged into Livewire's state via Alpine — content is passed in through `@js($value)` on initialization and written back to a hidden field on change.

---

## Roles & Permissions

10 roles, managed via Spatie Laravel Permission:

1. Super Admin — unrestricted system control
2. Admin — operational administration, user management, configuration
3. Manager — editorial oversight, categories, staff workflows
4. Editor — review, approval, publishing, scheduling
5. Author — drafting, submission, profile management
6. Contributor — pitching and draft submission requiring review
7. Employee — internal operations
8. Ad Manager — ad zones, banners, click analytics
9. SEO Manager — metadata overrides, canonical tags, sitemap
10. Subscriber — reading history, bookmarks, reactions, newsletter

Permissions are enforced at the route level (`middleware('permission:posts.create')`) and in Blade via `@can` / `@canany`:

```blade
@can('subscribers.promote')
    <button wire:click="promoteToAuthor({{ $user->id }})">Promote to Author</button>
@endcan
```

---

## Database Design

### `posts` vs `post_translations`

Structural and translatable data are deliberately split so new languages can be added without a schema change:

```
posts                                  post_translations
------                                  ------------------
id                                      id
author_id                              post_id       (FK → posts.id)
category_id                            language_id   (FK → languages.id)
featured_image_id                      title
status                                 slug
published_at                           excerpt
is_featured / is_trending              content
source_url                             meta_title, meta_description
```

**Rule of thumb:** structural fields (status, author, timestamps) live on `Post`. All text and SEO fields live on `PostTranslation`. Any query needing post content must eager-load `translations` or use `$post->translation()` — never assume `title`/`content`/`slug` exist directly on `Post`.

### `portal_type` on `users`

Defaults to a fallback value in the schema — **always set it explicitly** in any user-creation path (registration, admin creation, seeders) to avoid users landing in the wrong portal.

---

## Project Structure

```
app/
  Models/            Eloquent models (Post, PostTranslation, User, Media, ...)
  Livewire/           Reactive components (Admin, Author, Visitor, Frontend)
  Services/           Business logic (Settings, Migration, Content transforms)
  Console/Commands/   Custom Artisan commands (custom artisan commands)
routes/
  web.php             Public + portal routing
  admin.php            Admin-portal routes
  settings.php         Settings routes
  console.php           Scheduled/console routing
resources/views/       Blade templates
database/
  migrations/          Schema definitions
  seeders/              Seed data (Blogger4UDemoSeeder.php)
public/                 Web root (only directory served directly)
storage/                Logs, cache, uploaded media (storage/app/public)
deploy.sh               Automated production deployment script
```

---

## Local Setup

**Requirements:** PHP 8.3+, Composer, MySQL 8.0, Node.js (for Vite).

```bash
git clone https://github.com/theaashishpathak/wordpress-blog-website-migration-laravel.git
cd wordpress-blog-website-migration-laravel

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

> A one-command setup is also available via Composer's built-in script:
> ```bash
> composer run setup
> ```
> This installs dependencies, copies `.env.example` to `.env`, generates the app key, runs migrations, and builds frontend assets in one go.

**Running locally in development** (server + queue worker together):
```bash
composer run dev
```
Or, to also run the Vite dev server concurrently:
```bash
composer run dev:vite
```

Configure `.env` with your local database:

```env
APP_URL=http://localhost/Blogger4U
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Blogger4U
DB_USERNAME=root
DB_PASSWORD=
```

Then:

```bash
php artisan migrate
php artisan storage:link
php artisan serve
```


---

## Deployment

Blogger4U deploys to Hostinger shared hosting (PHP 8.3 CLI, SSH access, no cPanel, no remote MySQL) via an automated script.

```bash
./deploy.sh
```

`deploy.sh` performs:

1. Auto-detects the PHP 8.3 binary (`/opt/alt/php83/usr/bin/php` or `php8.3`)
2. Enables maintenance mode — `artisan down --retry=60`
3. Pulls latest changes from `main`
4. Installs dependencies — `composer install --no-dev --optimize-autoloader`
5. Verifies `APP_KEY`, runs `artisan storage:link`
6. Runs pending migrations — `artisan migrate --force`
7. Rebuilds caches — `config:cache`, `route:cache`, `view:cache`
8. Hardens permissions — `chmod -R 775 storage bootstrap/cache`
9. Disables maintenance mode — `artisan up`

Because there's no remote MySQL access, all database operations against production must be run **from the server itself** over SSH (this is what `artisan migrate --force` inside `deploy.sh` handles).

---

## Media Pipeline

Physical media assets from the legacy WordPress site are migrated via a concurrent streaming downloader rather than manual FTP transfer:

```bash
php artisan wp:download-media --concurrency=15
```

- Downloads in concurrent batches (15–20 at a time) over HTTP
- Skips files already present locally (safe to re-run / resume)
- Validates each response before writing to disk, skipping dead links or timeouts without halting the batch
- Stores assets in date-partitioned paths (`storage/app/public/media/YYYY/MM/`) and records metadata in the `media` table

---

## Troubleshooting

**Uploaded/cached assets not loading:**
```bash
chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

**Config or env changes not taking effect:**
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Inspecting live data safely:**
```bash
php artisan tinker
>>> \App\Models\Post::count();
>>> \App\Models\Media::count();
```

**All routes broken after adding a Livewire component:** Laravel validates that a component's class file exists on disk at boot time — a missing file can break route registration app-wide, not just that one route. Confirm the file exists at the expected path under `app/Livewire/`.

**A user lands in the wrong portal:** check that `portal_type` was explicitly set at creation time rather than left to its schema default.

**Post content not loading:** confirm the code is reading from `post_translations` (via `$post->translation`), not directly off `Post`.

---

## Roadmap Status

| Milestone | Description | Status |
|---|---|---|
| Data Migration | Import legacy users, categories, media metadata, posts | ✅ Complete |
| Media Pipeline | Concurrent migration of physical image assets | ✅ Complete |
| Subscribers System | `/admin/subscribers` dashboard — promote/activate/deactivate | ✅ Complete |
| Public Registration | Fortify visitor registration with role assignment | ✅ Complete |
| Livewire Pagination | `WithPagination` on category/tag feeds | ✅ Complete |
| TipTap Rich Text Editor | Content initialization and persistence fix | ✅ Complete |
| Route Model Binding | Primary key & translation resolution fix | ✅ Complete |
| Data Remediation | Legacy content cleanup, URL mapping | ✅ Complete |
| Clean URLs & Slugs | Canonical slug normalization | ✅ Complete |
| SEO & Redirect Engine | Meta fields, Open Graph, sitemap, 301 redirects | ✅ Complete |
| Admin Panel Polish | Dashboards, activity logs, permission management | ✅ Complete |
| Production Deployment | Hostinger deployment via `deploy.sh` | ✅ Complete |
| Multi-Language Expansion | Additional languages (e.g. Bengali) in `post_translations` | ⏸ Deferred |

---

## Testing & Code Style

Tests are written with [Pest](https://pestphp.com/):
```bash
php artisan test
```

Code style is enforced with [Laravel Pint](https://laravel.com/docs/pint):
```bash
composer run lint          # auto-fix
composer run lint:check    # check only, no changes (used in CI)
```

Run the full check suite (config clear + lint check + tests) with:
```bash
composer run ci:check
```

---

## License

Released under the [MIT License](LICENSE).
