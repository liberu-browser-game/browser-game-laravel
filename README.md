# Liberu Persistent Browser-Based Game (PBBG)

[![Install](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/install.yml/badge.svg)](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/install.yml)
[![Tests](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/tests.yml)
[![Docker](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/main.yml/badge.svg)](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/main.yml)
[![Codecov](https://codecov.io/gh/liberu-browser-game/browser-game-laravel/branch/main/graph/badge.svg)](https://codecov.io/gh/liberu-browser-game/browser-game-laravel)

A modular Laravel application scaffold used for the Liberu browser-based game ecosystem.

Quick highlights:
- PHP 8.4, Laravel 12, Filament, Jetstream, Livewire
- Modular architecture for extensibility
- Admin panel, user profiles, notifications, and real-time features

---

## Features
- Secure authentication (Jetstream)
- Profiles with avatars and metadata
- Real-time notifications and private messaging
- Admin management via Filament
- Modular codebase for feature packages

---

## Quick start
1. Ensure PHP 8.3+ and Composer are installed and configured.
2. Copy or create your `.env` (the setup script can offer to copy `.env.example`).
3. Run the project setup script (recommended for local development):

```bash
./setup.sh
```

The script will run Composer install, generate the application key, and optionally run migrations/seeds. Review the script before running if you wish to skip seeding.

Common manual steps:

```bash
composer install
cp .env.example .env   # or adjust your existing .env
php artisan key:generate
php artisan migrate --seed
```

---

## Docker
Build and run the included Dockerfile:

```bash
docker build -t browser-game-laravel .
docker run -p 8000:8000 browser-game-laravel
```

This repository also supports Laravel Sail (Docker-based development):

```bash
./vendor/bin/sail up
```

---

## Related projects
A compact list of projects in the Liberu ecosystem:

| Project | Description |
|---|---|
| liberu-accounting/accounting-laravel | Accounting package |
| liberu-automation/automation-laravel | Automation tools |
| liberu-billing/billing-laravel | Billing services |
| liberusoftware/boilerplate | Boilerplate starter |
| liberu-browser-game/browser-game-laravel | This repository |
| liberu-cms/cms-laravel | CMS package |
| liberu-control-panel/control-panel-laravel | Control panel |
| liberu-crm/crm-laravel | CRM system |
| liberu-ecommerce/ecommerce-laravel | E‑commerce package |
| liberu-genealogy/genealogy-laravel | Genealogy features |
| liberu-maintenance/maintenance-laravel | Maintenance package |
| liberu-real-estate/real-estate-laravel | Real estate package |
| liberu-social-network/social-network-laravel | Social network package |

---

## Contributing
Contributions are welcome. Please open issues or submit pull requests. Follow PSR-12 coding style and include tests for new behavior where applicable.

---

## License
This project is licensed under the MIT License. See the `LICENSE` file for details.

---

For full project details and developer notes, see the `docs/` directory and the in-repo markdown files.
