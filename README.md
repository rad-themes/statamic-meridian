# Meridian for Statamic

Meridian is a consulting and professional services starter kit by [Rad Themes](https://statamic.com/creators/rad-themes). It uses native Statamic collections, Antlers, and Tailwind CSS 4. The design assets are bundled locally, with no Webflow runtime or paid addon required.

Meridian gives a consulting team an editable service catalogue, case-study library, insights journal, pricing page, and discovery form. Case studies and pricing are **illustrative sample content**; replace them with verified information before launching a client site.

## Included

- Seven main pages, three service details, six case studies, and seven insight articles: 23 content routes in all.
- Eleven editable page sections for editorial copy, images, metrics, service/case/insight listings, process steps, pricing, FAQs, contact, and calls to action.
- Mounted collections, native navigation, brand globals, managed local assets, SEO fields, a responsive mobile menu, and a custom 404 page.
- A discovery form with server validation, CSRF protection, a honeypot, and stored submissions.

## Requirements

- Statamic 6.34 or later in the 6.x series and PHP 8.3 or newer within 8.x.
- Composer 2. Compiled frontend assets are included; Node.js 22.12 or newer and npm are needed to rebuild them.

Statamic licensing is separate from this kit; see [Statamic pricing](https://statamic.com/pricing).

## Installation

Install into a **new** Statamic project. Once a Marketplace release is available, use `rad-themes/statamic-meridian` with the Statamic CLI. Repository collaborators can install this private preview from a local checkout:

```sh
composer config repositories.meridian path /absolute/path/to/statamic-meridian
php please starter-kit:install rad-themes/statamic-meridian --local --without-user --clear-site
php please make:user
php please stache:refresh
npm ci
npm run build
php artisan serve
```

Use your own checkout path. The kit copies content and configuration into the new project, so back up any existing project before trying to merge it. It does not include accounts, passwords, an `.env` file, or form submissions.

## Editing

Open `/cp` after creating your user. **Pages** contain the hero and reorderable sections. **Services**, **Case Studies**, and **Insights** are separate collections with mounted detail routes. **Globals → Brand** holds the site name, email, footer, and primary call to action. **Navigation → Main navigation** controls the menu; **Assets** manages imagery. Each content blueprint exposes SEO title, description, and social image fields.

Discovery submissions are stored under **Forms → Discovery enquiries**. Email notifications are not configured. Set a mail transport and form recipients on your own server if you need alerts, then test delivery before launch.

Edit `resources/css/site.css`, `resources/js/site.js`, and `resources/views` to customize the frontend. Run `npm run dev` during development or `npm run build` for production. Installed project files are editable and are not automatically overwritten by future kit releases.

Before launch, replace the sample contact address, case studies, pricing, and service claims; configure `APP_URL`, mail, backups, HTTPS, and your own privacy information. The supplied case studies are marked as illustrative in their entries and on the frontend.

## Checks

```sh
php artisan test --compact tests/Feature/MeridianKitTest.php
npm run build
```

The tests exercise content routes, the native form, validation, stored enquiries, spam handling, and the 404 page. This package was also installed and tested in a separate fresh Statamic project before publication.

## Credits and support

Meridian adapts the authorized [Meridian source template by Kevin Dakin](https://webflow.com/templates/html/meridian-website-template) into native Statamic content models and Antlers/Tailwind templates. See [CREDITS.md](CREDITS.md) for provenance. For development issues, use [GitHub Issues](https://github.com/rad-themes/statamic-meridian/issues).
