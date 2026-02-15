# Player Ranking System

## Overview
The player ranking system calculates and displays player rankings based on their game performance metrics (level and experience).

## Features

### Scoring Algorithm
- **Score Calculation**: `score = (level * 100) + experience`
- Players are ranked by score (highest first)
- Ties are broken by level, then experience

### Database Fields
New fields added to `players` table:
- `rank` (integer, nullable): Player's current rank position
- `score` (integer, default: 0): Calculated performance score
- `last_rank_update` (timestamp, nullable): Last time rankings were updated

## Usage

### Updating Rankings via Command Line
Recalculate all player scores and rankings:
```bash
php artisan players:update-rankings
```

### Using the RankingService Programmatically

```php
use App\Services\RankingService;

$rankingService = new RankingService();

// Recalculate all player scores
$updatedCount = $rankingService->recalculateScores();

// Update all rankings
$rankedCount = $rankingService->updateAllRankings();

// Update a specific player's ranking
$rankingService->updatePlayerRanking($player);

// Get top 10 players
$topPlayers = $rankingService->getTopPlayers(10);

// Get a player's rank
$rank = $rankingService->getPlayerRank($player);
```

### Player Model Methods

```php
// Calculate score for a player
$score = $player->calculateScore();
```

## Admin UI Features

### Player Resource
- **Rank Column**: Displays player rank with colored badges
  - Gold badge (#1) for first place
  - Yellow badge for top 10
  - Gray badge for others
- **Score Column**: Shows total calculated score
- **Filters**: 
  - High Level Players (10+)
  - Top 100 Ranked
- **Default Sort**: By rank (ascending)

### Leaderboard Widget
- Displays top 10 players on admin dashboard
- Shows rank, username, level, experience, and score
- Color-coded badges for top 3 positions
- Link to view player details

## Testing

Run the ranking system tests:
```bash
php artisan test --filter=Ranking
```

Seed sample players with rankings:
```bash
php artisan db:seed --class=PlayerRankingSeeder
```

## Maintenance

Rankings should be updated:
- After player level ups
- After experience gains
- Periodically via cron job (recommended: hourly or daily)

Example cron job in `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('players:update-rankings')->hourly();
}
```
