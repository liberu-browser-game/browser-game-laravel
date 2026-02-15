# Browser Game Quick Start Guide

## 🎮 Getting Started

Welcome to our persistent browser-based RPG! This guide will help you get started quickly.

## Prerequisites

- PHP 8.3 or higher
- Composer
- MySQL or PostgreSQL database
- Node.js and NPM (for frontend assets)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/liberu-browser-game/browser-game-laravel.git
cd browser-game-laravel
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=browser_game
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate

# Seed the database with game content
php artisan db:seed --class=GameSeeder
php artisan db:seed --class=GameContentSeeder
php artisan db:seed --class=AchievementSeeder
```

### 6. Build Frontend Assets

```bash
npm run build
# or for development with hot reload
npm run dev
```

### 7. Start the Application

```bash
# Using PHP built-in server
php artisan serve

# Or using Laravel Octane (RoadRunner)
php artisan octane:start

# Or using Docker
docker-compose up
```

Visit `http://localhost:8000` in your browser!

## First Steps in the Game

### 1. Create Your Account
- Register a new account
- Verify your email (if enabled)
- Log in to the game

### 2. Claim Your Daily Reward
- First thing you see is the daily reward
- Click "Claim Reward" to get starting gold and XP
- Come back daily for streak bonuses!

### 3. Check Your Player Stats
- View your level, health, mana
- See your current gold and resources
- Check your character stats

### 4. Start Your First Quest
- Go to the Quest Board
- Accept a beginner quest like "Defeat the Goblins"
- Complete the quest for XP and items

### 5. Try Your First Battle
- Navigate to the Combat Arena
- Select an opponent level (start with level 1-2)
- Click "Start Battle" to engage in combat
- Watch the battle log to see what happens
- Heal if your health gets low!

### 6. Explore the Marketplace
- Check what other players are selling
- Sell items you don't need
- Buy equipment to improve your stats

### 7. Learn Crafting
- Visit the Crafting Workshop
- See available recipes
- Gather materials from quests
- Craft your first item!

### 8. Join a Guild
- Browse available guilds
- Join one that fits your playstyle
- Participate in guild activities
- Make friends and team up

## Game Loop

The optimal daily routine:

1. **Login** → Claim daily reward
2. **Quests** → Complete 3-5 quests
3. **Combat** → Battle to gain XP and gold
4. **Craft** → Make useful items
5. **Trade** → Sell unwanted items, buy upgrades
6. **Social** → Interact with guild members
7. **Compete** → Check leaderboards and aim higher

## Tips for Beginners

### Combat Tips
- Always heal before important battles
- Healing costs 50 gold
- Don't fight enemies more than 2-3 levels above you
- Battle regularly to level up faster

### Economy Tips
- Save gold early for equipment
- Sell common items, keep rare ones
- Check marketplace for deals daily
- Crafting can be more profitable than buying

### Progression Tips
- Focus on quests for steady XP
- Allocate stat points wisely (can't respec easily)
- Join an active guild for bonuses
- Complete daily tasks for streak rewards

### Social Tips
- Active guilds provide better experience
- Help lower-level players for karma
- Trade with friends for better deals
- Participate in guild events

## Advanced Features

### Equipment System
- Equip items in 6 slots: Weapon, Armor, Helmet, Boots, Gloves, Accessory
- Each item provides stat bonuses
- Higher rarity = better stats
- Check item requirements before buying

### Crafting System
- Learn recipes from quests or NPCs
- Gather materials from battles and quests
- Success rate varies by recipe
- Failed crafts still consume materials

### Leaderboards
- Compete in 4 categories: Level, PvP, Quests, Wealth
- Top 20 players displayed
- Rankings update regularly
- Seasonal rewards (coming soon)

### Skills & Abilities
- Learn new skills as you level up
- Skills cost mana to use
- Cooldowns prevent spam
- Higher skill levels = more power

## Troubleshooting

### Common Issues

**Database connection error:**
```bash
# Check your .env database settings
# Make sure MySQL/PostgreSQL is running
php artisan config:clear
php artisan cache:clear
```

**Assets not loading:**
```bash
npm run build
php artisan optimize:clear
```

**Livewire not working:**
```bash
php artisan livewire:publish --config
php artisan livewire:publish --assets
```

**Permissions error:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Development Commands

```bash
# Run tests
php artisan test

# Run linter
./vendor/bin/pint

# Clear all caches
php artisan optimize:clear

# Reset database (CAUTION: Deletes all data)
php artisan migrate:fresh --seed
```

## Support

- **Documentation**: See `/docs` directory
- **Issues**: Report on GitHub
- **Discord**: Join our community server
- **Email**: support@liberu.co.uk

## Next Steps

Once you're comfortable with the basics:

1. Explore all game systems
2. Optimize your character build
3. Compete for top leaderboard positions
4. Help grow the community
5. Suggest new features!

## Resources

- [Full Game Features](docs/GAME_FEATURES.md)
- [API Documentation](docs/API.md)
- [Contributing Guidelines](CONTRIBUTING.md)
- [Player Tracking Guide](docs/PLAYER_TRACKING.md)

---

**Have fun and happy gaming!** 🎮✨
