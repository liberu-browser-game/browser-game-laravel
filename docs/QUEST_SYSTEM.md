# Quest System Documentation

## Overview

The Quest System allows players to participate in quests, track their progress, and receive rewards upon completion. The system is built with Laravel and provides both a backend service layer and RESTful API endpoints.

## Features

- **Quest Management**: Admins can create and manage quests via the Filament admin panel
- **Quest Acceptance**: Players can browse available quests and accept them
- **Progress Tracking**: The system tracks quest status (in-progress, completed)
- **Reward Distribution**: Automatic distribution of experience points and items upon quest completion
- **Level System**: Players automatically level up when they earn enough experience
- **Quest Abandonment**: Players can abandon quests they no longer wish to complete

## Database Schema

### Tables

#### `quests`
- `id`: Primary key
- `name`: Quest name
- `description`: Quest description
- `experience_reward`: XP awarded on completion
- `item_reward_id`: Optional foreign key to items table
- `timestamps`: Created at / Updated at

#### `player__quests`
- `id`: Primary key
- `player_id`: Foreign key to players table
- `quest_id`: Foreign key to quests table
- `status`: Enum ('in-progress', 'completed')
- `timestamps`: Created at / Updated at

## API Endpoints

All quest endpoints are prefixed with `/api/quests`.

### Get Available Quests
**GET** `/api/quests/available`

Query Parameters:
- `player_id`: The player's ID

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Beginner's Trial",
      "description": "Complete your first challenge...",
      "experience_reward": 50,
      "item_reward_id": 1
    }
  ]
}
```

### Get Active Quests
**GET** `/api/quests/active`

Query Parameters:
- `player_id`: The player's ID

### Get Completed Quests
**GET** `/api/quests/completed`

Query Parameters:
- `player_id`: The player's ID

### Accept a Quest
**POST** `/api/quests/{quest_id}/accept`

Body:
```json
{
  "player_id": 1
}
```

Response:
```json
{
  "success": true,
  "message": "Quest accepted successfully",
  "data": {
    "id": 1,
    "player_id": 1,
    "quest_id": 1,
    "status": "in-progress"
  }
}
```

### Complete a Quest
**POST** `/api/quests/{quest_id}/complete`

Body:
```json
{
  "player_id": 1
}
```

Response:
```json
{
  "success": true,
  "message": "Quest completed successfully",
  "data": {
    "quest": {...},
    "rewards": {
      "experience": 50,
      "item": {...},
      "level_up": true
    }
  }
}
```

### Abandon a Quest
**DELETE** `/api/quests/{quest_id}/abandon`

Body:
```json
{
  "player_id": 1
}
```

## Service Layer

The `QuestService` class provides the following methods:

### `acceptQuest(Player $player, Quest $quest): Player_Quest`
Accepts a quest for a player. Throws an exception if the quest is already accepted or completed.

### `completeQuest(Player $player, Quest $quest): array`
Completes a quest and distributes rewards. Returns an array of rewards.

### `getAvailableQuests(Player $player)`
Returns all quests that the player hasn't accepted yet.

### `getActiveQuests(Player $player)`
Returns all quests the player is currently working on.

### `getCompletedQuests(Player $player)`
Returns all quests the player has completed.

### `abandonQuest(Player $player, Quest $quest): bool`
Removes a quest from the player's active quests.

## Reward System

### Experience Points
- Players earn XP when completing quests
- XP is added to the player's total experience
- Formula: Player's XP = Previous XP + Quest XP Reward

### Leveling Up
- Players level up automatically when they have enough experience
- Formula: Required XP for next level = Current Level × 100
- Example: Level 1 → Level 2 requires 100 XP

### Item Rewards
- Quests can optionally reward items
- Items are automatically added to the player's inventory
- Stored in the `player__items` table with quantity = 1

## Testing

Run the quest system tests:
```bash
php artisan test --filter QuestSystemTest
```

The test suite includes:
- Quest acceptance tests
- Quest completion tests
- Reward distribution tests
- Level-up mechanics tests
- API endpoint tests
- Relationship tests

## Admin Panel

Admins can manage quests through the Filament admin panel at `/app/admin/quests`:
- Create new quests
- Edit existing quests
- View quest details
- Delete quests
- Filter quests by XP reward and item rewards

## Seeding Sample Data

To seed the database with sample quests:
```bash
php artisan db:seed --class=QuestSeeder
```

This will create:
- Sample quests with various difficulty levels
- Sample items for quest rewards
- A variety of quest types (combat, gathering, exploration, etc.)

## Future Enhancements

Potential improvements to consider:
- Quest prerequisites (complete Quest A before Quest B)
- Quest chains (multi-step quests)
- Progress tracking (collect X items, defeat Y enemies)
- Quest categories/types
- Time-limited quests
- Repeatable quests
- Currency/gold rewards
- Multiple item rewards
- Quest difficulty ratings
- Quest giver NPCs
