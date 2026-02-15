# Player Progression Tracking Features

This document describes the player progression tracking, statistics, and achievements system implemented in the browser game.

## Overview

The system provides comprehensive tracking of player activities including:
- **Player Statistics**: Detailed metrics about player performance
- **Achievements**: Unlockable goals based on player actions
- **Quest Progress**: Enhanced tracking with completion timestamps and progress percentages
- **API Endpoints**: RESTful API for accessing player data

## Database Schema

### Achievements Table
Stores available achievements that players can unlock.

**Fields:**
- `name`: Unique achievement name
- `description`: What the achievement is for
- `icon`: Icon identifier (e.g., heroicon-o-trophy)
- `points`: Points awarded for unlocking
- `requirement_type`: Type of requirement (level, quests_completed, items_collected, experience)
- `requirement_value`: Threshold value to unlock

### Player Achievements (Pivot Table)
Tracks player progress toward achievements.

**Fields:**
- `player_id`: Reference to player
- `achievement_id`: Reference to achievement
- `progress`: Percentage progress (0-100)
- `unlocked_at`: Timestamp when unlocked (null if locked)

### Player Statistics Table
Comprehensive statistics for each player.

**Fields:**
- `player_id`: Reference to player (unique)
- `total_quests_completed`: Count of completed quests
- `total_items_collected`: Count of collected items
- `total_playtime_minutes`: Total playtime in minutes
- `highest_level_achieved`: Highest level reached
- `total_experience_earned`: Total experience points earned
- `quests_in_progress`: Count of active quests
- `achievements_unlocked`: Count of unlocked achievements

### Enhanced Player Quests
Added progress tracking to existing quest system.

**New Fields:**
- `progress_percentage`: Quest completion percentage (0-100)
- `completed_at`: Timestamp when quest was completed

## API Endpoints

### Player Statistics

**GET** `/api/players/{player}/statistics`
- Returns detailed statistics for a player
- Auto-creates statistics record if it doesn't exist

**GET** `/api/players/{player}/progression`
- Returns a summary of player progression
- Includes: level, experience, quests, items, achievements

### Achievements

**GET** `/api/achievements`
- Returns all available achievements

**GET** `/api/players/{player}/achievements`
- Returns all achievements with player's progress

**GET** `/api/players/{player}/achievements/unlocked`
- Returns only unlocked achievements for a player

## Admin Panel (Filament)

### Achievement Management
Navigate to **Game Management → Achievements** to:
- View all achievements
- Create new achievements
- Edit achievement requirements
- Delete achievements

### Player Management
Enhanced player views with:
- **Quests Relation Manager**: View player's quest progress with completion status
- **Achievements Relation Manager**: View player's achievement progress and unlocked achievements
- Inline statistics display

### Widgets

**Player Progress Widget**
- Bar chart showing quest completion metrics
- Visual representation of player activity

**Recent Achievements Widget**
- Table showing recently unlocked achievements
- Displays player, achievement name, points, and unlock time

**Player Level Chart** (existing, enhanced)
- Distribution of players across level ranges

## Models and Relationships

### Player Model
New relationships added:
```php
public function statistics()      // hasOne PlayerStatistic
public function achievements()    // belongsToMany Achievement
public function quests()          // hasMany Player_Quest
public function items()           // hasMany Player_Item
```

### Achievement Model
```php
public function players()         // belongsToMany Player
```

### PlayerStatistic Model
```php
public function player()          // belongsTo Player
```

## Demo Data

Run the demo seeder to populate sample data:

```bash
php artisan db:seed --class=PlayerTrackingDemoSeeder
```

This creates:
- 11 predefined achievements (levels, quests, items, experience)
- 4 demo players with varying progression levels
- Sample quest completions
- Sample item collections
- Achievement progress and unlocks

## Testing

### Unit Tests
- `tests/Unit/AchievementTest.php`: Tests Achievement model
- `tests/Unit/PlayerStatisticTest.php`: Tests PlayerStatistic model

### Feature Tests
- `tests/Feature/PlayerStatisticsApiTest.php`: Tests statistics API endpoints
- `tests/Feature/PlayerAchievementsApiTest.php`: Tests achievements API endpoints

Run tests:
```bash
php artisan test --filter=Achievement
php artisan test --filter=PlayerStatistic
```

## Usage Examples

### Creating an Achievement
```php
Achievement::create([
    'name' => 'Master Collector',
    'description' => 'Collect 500 items',
    'points' => 100,
    'requirement_type' => 'items_collected',
    'requirement_value' => 500,
]);
```

### Tracking Achievement Progress
```php
$player->achievements()->attach($achievementId, [
    'progress' => 50,
    'unlocked_at' => null,
]);

// Update progress
$player->achievements()->updateExistingPivot($achievementId, [
    'progress' => 100,
    'unlocked_at' => now(),
]);
```

### Retrieving Player Statistics
```php
$statistics = $player->statistics;
$completedQuests = $statistics->total_quests_completed;
$totalPlaytime = $statistics->total_playtime_minutes;
```

### Checking Quest Progress
```php
$quest = $player->quests()->where('quest_id', $questId)->first();
$progress = $quest->progress_percentage;
$isCompleted = $quest->status === 'completed';
$completedAt = $quest->completed_at;
```

## Future Enhancements

Potential improvements:
- Leaderboards based on statistics
- Achievement categories and rarities
- Daily/weekly challenges
- Social features (compare with friends)
- Achievement notifications
- Progress tracking events/observers
- Automated achievement checking on player actions
