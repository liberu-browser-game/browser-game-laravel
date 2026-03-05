# Liberu Persistent Browser-Based Game (PBBG)

[![](https://avatars.githubusercontent.com/u/158830885?s=200&v=4)](https://www.liberu.co.uk)

### Build. Battle. Conquer. — An open-source PBBG platform powered by Laravel 12 & Filament 5.

![](https://img.shields.io/badge/PHP-8.5-informational?style=flat&logo=php&color=4f5b93)
![](https://img.shields.io/badge/Laravel-12-informational?style=flat&logo=laravel&color=ef3b2d)
![](https://img.shields.io/badge/Filament-5-informational?style=flat&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0OCIgaGVpZ2h0PSI0OCIgeG1sbnM6dj0iaHR0cHM6Ly92ZWN0YS5pby9uYW5vIj48cGF0aCBkPSJNMCAwaDQ4djQ4SDBWMHoiIGZpbGw9IiNmNGIyNWUiLz48cGF0aCBkPSJNMjggN2wtMSA2LTMuNDM3LjgxM0wyMCAxNWwtMSAzaDZ2NWgtN2wtMyAxOEg4Yy41MTUtNS44NTMgMS40NTQtMTEuMzMgMy0xN0g4di01bDUtMSAuMjUtMy4yNUMxNCAxMSAxNCAxMSAxNS40MzggOC41NjMgMTkuNDI5IDYuMTI4IDIzLjQ0MiA2LjY4NyAyOCA3eiIgZmlsbD0iIzI4MjQxZSIvPjxwYXRoIGQ9Ik0zMCAxOGg0YzIuMjMzIDUuMzM0IDIuMjMzIDUuMzM0IDEuMTI1IDguNUwzNCAyOWMtLjE2OCAzLjIwOS0uMTY4IDMuMjA5IDAgNmwtMiAxIDEgM2gtNXYyaC0yYy44NzUtNy42MjUuODc1LTcuNjI1IDItMTFoMnYtMmgtMnYtMmwyLTF2LTQtM3oiIGZpbGw9IiMyYTIwMTIiLz48cGF0aCBkPSJNMzUuNTYzIDYuODEzQzM4IDcgMzggNyAzOSA4Yy4xODggMi40MzguMTg4IDIuNDM4IDAgNWwtMiAyYy0yLjYyNS0uMzc1LTIuNjI1LS4zNzUtNS0xLS42MjUtMi4zNzUtLjYyNS0yLjM3NS0xLTUgMi0yIDItMiA0LjU2My0yLjE4N3oiIGZpbGw9IiM0MDM5MzEiLz48cGF0aCBkPSJNMzAgMThoNGMyLjA1NSA1LjMxOSAyLjA1NSA1LjMxOSAxLjgxMyA4LjMxM0wzNSAyOGwtMyAxdi0ybC00IDF2LTJsMi0xdi00LTN6IiBmaWxsPSIjMzEyODFlIi8+PHBhdGggZD0iTTI5IDI3aDN2MmgydjJoLTJ2MmwtNC0xdi0yaDJsLTEtM3oiIGZpbGw9IiMxNTEzMTAiLz48cGF0aCBkPSJNMzAgMThoNHYzaC0ydjJsLTMgMSAxLTZ6IiBmaWxsPSIjNjA0YjMyIi8+PC9zdmc+&&color=fdae4b&link=https://filamentphp.com)
![](https://img.shields.io/badge/Livewire-4-informational?style=flat&logo=Livewire&color=fb70a9)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Open Source Love](https://img.shields.io/badge/Open%20Source-%E2%9D%A4-red.svg)](https://github.com/liberu-browser-game/browser-game-laravel)
[![Latest Release](https://img.shields.io/github/v/release/liberu-browser-game/browser-game-laravel?include_prereleases)](https://github.com/liberu-browser-game/browser-game-laravel/releases)
[![Test Coverage](https://img.shields.io/badge/coverage-passing-brightgreen)](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/tests.yml)

[![Install](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/install.yml/badge.svg)](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/install.yml)
[![Tests](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/tests.yml)
[![Docker](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/main.yml/badge.svg)](https://github.com/liberu-browser-game/browser-game-laravel/actions/workflows/main.yml)

[![Contact us on WhatsApp](https://img.shields.io/badge/WhatsApp-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://wa.me/+441793200950)
[![YouTube](https://img.shields.io/badge/YouTube-%23FF0000.svg?style=for-the-badge&logo=YouTube&logoColor=white)](https://www.youtube.com/@liberusoftware)

---

## About This Project

**Liberu Browser Game** is a fully open-source, feature-rich **Persistent Browser-Based Game (PBBG)** platform built with modern PHP technologies. It delivers engaging, long-running gameplay — combat, character progression, crafting, trading, guilds, quests, and leaderboards — all within a browser. No plugins or downloads required for players.

The codebase is built on [Laravel 12](https://laravel.com), [PHP 8.5](https://php.net), [Filament 5](https://filamentphp.com) for the admin panel, and [Livewire 4](https://livewire.laravel.com) for reactive UI components. It follows PSR-12 standards and is designed with a modular service-layer architecture so developers can extend or customise any game system with minimal friction.

Whether you want to host your own PBBG, study the architecture, or contribute new features, this repository is the starting point.

---

## 🎮 Game Features

### Core Gameplay Systems

#### ⚔️ Combat System
- **PvE Battles**: Fight AI opponents with dynamic difficulty scaling
- **Turn-Based Mechanics**: Strategic combat with damage calculations
- **Battle Logs**: Detailed round-by-round combat history
- **Victory Rewards**: Earn experience and gold
- **Healing System**: Manage health between battles

#### 📈 Character Progression
- **Core Stats**: Strength, Defense, Agility, Intelligence
- **Resource Management**: Health and Mana pools
- **Level System**: XP-based progression with meaningful rewards
- **Stat Points**: Customise your character (5 points per level)
- **Equipment Bonuses**: Items enhance your capabilities

#### 🛡️ Equipment System
- **6 Equipment Slots**: Weapon, Armor, Helmet, Boots, Gloves, Accessory
- **Stat Bonuses**: Each piece provides specific increases
- **Rarity Tiers**: Common → Uncommon → Rare → Legendary
- **Level Requirements**: Progressive gear unlocking

#### ✨ Skills & Abilities
- **Diverse Skill Types**: Attack, Defense, Heal, Buff
- **Mana System**: Strategic resource management
- **Cooldown Mechanics**: Prevents ability spam
- **Skill Progression**: Level up for increased power

#### 🔨 Crafting System
- **Recipe Learning**: Discover through quests and exploration
- **Material Gathering**: Collect resources from various sources
- **Success Rates**: Variable difficulty adds challenge
- **Quality Crafting**: Create powerful equipment

#### 💰 Player Economy
- **Marketplace Trading**: Buy and sell with other players
- **Custom Pricing**: Set your own market prices
- **Secure Transactions**: Safe gold and item transfers
- **Supply & Demand**: Dynamic player-driven economy

#### 🏆 Competitive Features
- **Leaderboards**: Compete in 4 categories (Level, PvP, Quests, Wealth)
- **Rankings**: See top 20 players
- **Daily Rewards**: Login bonuses with streak system
- **Achievement Tracking**: 11 predefined achievements

#### 👥 Social Features
- **Guild System**: Join communities, participate in activities
- **Guild Roles**: Leader, Officer, Member hierarchy
- **Quests**: Complete objectives for rewards and items
- **Real-time Notifications**: Stay informed of game events

---

## 🌟 Key Features

### For Players
- ✅ Persistent character progression
- ✅ Engaging turn-based combat
- ✅ Player-driven economy and marketplace
- ✅ Competitive leaderboards (4 categories)
- ✅ Daily login rewards with streak bonuses
- ✅ Guild system with roles and activities
- ✅ Crafting & trading systems
- ✅ Mobile-responsive design

### For Developers
- ✅ Laravel 12 best practices and conventions
- ✅ Livewire 4 reactive real-time UI components
- ✅ Filament 5 admin panel with full game management
- ✅ Service layer architecture (modular, testable)
- ✅ Comprehensive automated test suite
- ✅ Security-first: SQL injection, XSS, CSRF protection
- ✅ Well-documented codebase (20+ KB of guides)
- ✅ PSR-12 compliant code quality

---

## 🛠️ Technical Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 12, PHP 8.5, MySQL / PostgreSQL, Laravel Octane |
| **Frontend** | Livewire 4, Alpine.js, Tailwind CSS, Blade Templates |
| **Admin Panel** | Filament 5 — real-time dashboard, player & content management |
| **Security** | SQL injection prevention, XSS & CSRF protection, secure authentication |
| **Tooling** | PHPUnit, Docker / Laravel Sail, GitHub Actions CI |

---

## 🚀 Installation

### Prerequisites
- **PHP 8.5+** and Composer
- MySQL or PostgreSQL database
- Node.js and NPM

### Option 1 — Automated installer (recommended)

Run the interactive setup script from the command line. It guides you through `.env` configuration, installs dependencies, generates the application key, runs migrations and seeds, and optionally starts the dev server:

```bash
git clone https://github.com/liberu-browser-game/browser-game-laravel.git
cd browser-game-laravel
./setup.sh
```

> A **graphical installer** is also available for desktop users — download and run the platform-specific installer from the [Releases page](https://github.com/liberu-browser-game/browser-game-laravel/releases), which wraps the same `setup.sh` logic in a user-friendly GUI.

### Option 2 — Manual setup

```bash
git clone https://github.com/liberu-browser-game/browser-game-laravel.git
cd browser-game-laravel

# Install PHP and JS dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure your database credentials in .env, then:
php artisan migrate
php artisan db:seed --class=GameSeeder
php artisan db:seed --class=GameContentSeeder

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

Visit `http://localhost:8000` and start playing!

### Option 3 — Docker

```bash
docker build -t browser-game-laravel .
docker run -p 8000:8000 browser-game-laravel
```

Or use **Laravel Sail** for a fully containerised development environment:

```bash
./vendor/bin/sail up
```

**📖 For detailed setup instructions, see [Quick Start Guide](docs/QUICK_START.md)**

---

## 📚 Documentation

- **[Quick Start Guide](docs/QUICK_START.md)** — Get running in minutes
- **[Game Features](docs/GAME_FEATURES.md)** — Complete feature reference
- **[Admin Panel Guide](docs/ADMIN_PANEL_GUIDE.md)** — Admin panel documentation
- **[Architecture](docs/ARCHITECTURE.md)** — Modular design overview
- **[Mobile Responsiveness](docs/MOBILE_RESPONSIVENESS.md)** — Mobile optimisation guide

---

## 🌐 Our Projects

Liberu Browser Game is part of a broader ecosystem of open-source Laravel applications. Follow the links below to explore, use, or contribute to our related projects.

| Project | Repository | Description |
|---|---|---|
| Accounting | [liberu-accounting/accounting-laravel](https://github.com/liberu-accounting/accounting-laravel) | Accounting and invoicing features for Laravel applications. |
| Automation | [liberu-automation/automation-laravel](https://github.com/liberu-automation/automation-laravel) | Automation tooling and workflow integrations. |
| Billing | [liberu-billing/billing-laravel](https://github.com/liberu-billing/billing-laravel) | Subscription and billing management (payments, invoices). |
| Boilerplate (core) | [liberusoftware/boilerplate](https://github.com/liberusoftware/boilerplate) | Core starter and shared utilities used across Liberu projects. |
| Browser Game | [liberu-browser-game/browser-game-laravel](https://github.com/liberu-browser-game/browser-game-laravel) | This repository — open-source PBBG platform. |
| CMS | [liberu-cms/cms-laravel](https://github.com/liberu-cms/cms-laravel) | Content management and modular page administration. |
| Control Panel | [liberu-control-panel/control-panel-laravel](https://github.com/liberu-control-panel/control-panel-laravel) | Administration components for managing services. |
| CRM | [liberu-crm/crm-laravel](https://github.com/liberu-crm/crm-laravel) | Customer relationship management features and integrations. |
| E‑commerce | [liberu-ecommerce/ecommerce-laravel](https://github.com/liberu-ecommerce/ecommerce-laravel) | E‑commerce storefront, product and order management. |
| Genealogy | [liberu-genealogy/genealogy-laravel](https://github.com/liberu-genealogy/genealogy-laravel) | Family tree and genealogy features built on Laravel. |
| Maintenance | [liberu-maintenance/maintenance-laravel](https://github.com/liberu-maintenance/maintenance-laravel) | Scheduling, tracking and reporting for maintenance tasks. |
| Real Estate | [liberu-real-estate/real-estate-laravel](https://github.com/liberu-real-estate/real-estate-laravel) | Property listings and real-estate management features. |
| Social Network | [liberu-social-network/social-network-laravel](https://github.com/liberu-social-network/social-network-laravel) | Social features, profiles, feeds and messaging. |

---

## 🤝 Contributing

Contributions are warmly welcomed! Whether you're fixing a bug, adding a feature, improving documentation, or writing tests — every contribution helps.

**Pull Request process:**
1. **Fork** the repository and create a descriptive feature branch (`feature/my-new-feature`).
2. **Write** your code following [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards.
3. **Add tests** that cover your changes — new behaviour must be tested.
4. **Run the test suite** locally (`./vendor/bin/phpunit`) and make sure all tests pass.
5. **Open a Pull Request** against the `main` branch with a clear description of what you changed and why.

For larger changes, please open an issue first to discuss your approach. See [our issue template](.github/issue_template.md) for guidance.

---

## 📄 License

This project is licensed under the **[MIT License](LICENSE)**.

The MIT licence is one of the most permissive open-source licences available. It means you are free to:
- **Use** this software for any purpose — personal, commercial, or educational.
- **Modify** the source code to suit your own needs.
- **Distribute** copies of the original or modified software.
- **Incorporate** it into your own projects, even proprietary ones.

The only requirement is that the original copyright notice and licence text are included with any substantial distribution of the software. There is **no warranty** — the software is provided "as is".

This licence makes Liberu Browser Game ideal for learning, prototyping, building commercial products, or hosting your own PBBG community.

---

## 📞 Support

- **Documentation**: See the [`/docs`](docs/) directory
- **Issues & Feature Requests**: [GitHub Issues](https://github.com/liberu-browser-game/browser-game-laravel/issues)
- **WhatsApp**: [+44 1793 200950](https://wa.me/+441793200950)
- **Email**: support@liberu.co.uk
- **YouTube**: [@liberusoftware](https://www.youtube.com/@liberusoftware)

---

**Built with ❤️ by the [Liberu Team](https://www.liberu.co.uk)**

*Build. Battle. Conquer.* 🎮✨
