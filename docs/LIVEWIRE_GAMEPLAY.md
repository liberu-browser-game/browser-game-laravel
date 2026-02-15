# Real-Time Gameplay Interactions with Livewire

This implementation provides real-time multiplayer gameplay interactions using Laravel Livewire components.

## Features

### 1. Player Dashboard (`PlayerDashboard.php`)
- **Real-time player stats display**: Level, experience, XP progress bar
- **Live updates**: Auto-refreshes every 5 seconds using wire:poll
- **Statistics cards**: Active quests, inventory items, guild memberships
- **Event listening**: Responds to quest completions and level-ups

### 2. Quest Board (`QuestBoard.php`)
- **Quest categories**:
  - Available quests (can be accepted)
  - Active quests (in progress)
  - Completed quests (historical)
- **Quest actions**:
  - Accept quest
  - Complete quest (awards XP and items)
  - Abandon quest
- **Real-time synchronization**: Updates every 10 seconds
- **Broadcast events**: Quest completion notifications

### 3. Player Inventory (`PlayerInventory.php`)
- **Item management**:
  - View items with quantity
  - Rarity-based color coding (common, uncommon, rare, epic, legendary)
  - Use items
  - Drop items
- **Resource tracking**: Display player resources (gold, materials, etc.)
- **Real-time updates**: Auto-refreshes every 10 seconds

### 4. Guild Panel (`GuildPanel.php`)
- **Guild membership**:
  - View player's guilds
  - See guild members with roles and join dates
  - Join available guilds
  - Leave guilds
- **Real-time guild updates**: Refreshes every 15 seconds
- **Broadcast events**: Guild join/leave notifications to all guild members

## Broadcasting Events

The system implements four broadcast events for real-time multiplayer synchronization:

### 1. PlayerLeveledUp
- **Channel**: `game-events` (public channel)
- **Triggered when**: A player gains enough XP to level up
- **Data**: player_id, player_username, new_level, message

### 2. QuestCompleted
- **Channel**: `game-events` (public channel)
- **Triggered when**: A player completes a quest
- **Data**: player_id, quest_id, experience_gained, message

### 3. GuildMemberJoined
- **Channel**: `guild.{guild_id}` (guild-specific channel)
- **Triggered when**: A player joins a guild
- **Data**: guild_id, player_id, player_username, player_level, message

### 4. GuildMemberLeft
- **Channel**: `guild.{guild_id}` (guild-specific channel)
- **Triggered when**: A player leaves a guild
- **Data**: guild_id, player_id, player_username, message

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Configure Broadcasting (Optional)
For full real-time functionality, configure broadcasting in `.env`:

```env
# For local development (use log driver)
BROADCAST_DRIVER=log

# For production (use Pusher, Ably, or Redis)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=your-cluster
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Start the Application
```bash
php artisan serve
```

### 5. Access the Game Dashboard
Navigate to: `http://localhost:8000/game`

## How It Works

### Real-Time Updates
The Livewire components use multiple techniques for real-time synchronization:

1. **Wire Polling**: Components automatically refresh at regular intervals
   - PlayerDashboard: 5 seconds
   - QuestBoard: 10 seconds
   - PlayerInventory: 10 seconds
   - GuildPanel: 15 seconds

2. **Event Dispatching**: Components dispatch events to communicate with each other
   - `player-updated`: Triggers when player data changes
   - `quest-completed`: Triggers when a quest is completed
   - `player-leveled-up`: Triggers when player levels up

3. **Event Listeners**: Components listen for events and respond accordingly
   ```php
   protected $listeners = [
       'player-updated' => 'refreshPlayer',
       'quest-completed' => 'handleQuestCompleted',
   ];
   ```

4. **Broadcasting**: Server-side events are broadcast to all connected clients
   - Uses Laravel's built-in broadcasting system
   - Can be configured with Pusher, Ably, Redis, or other drivers

### Component Communication Flow

```
Player completes quest (QuestBoard)
    ↓
QuestBoard dispatches 'quest-completed' event
    ↓
PlayerDashboard listens and updates XP
    ↓
If level up → Broadcast PlayerLeveledUp event
    ↓
All connected clients receive notification
```

## Model Relationships

### Player Model
- `quests()`: Many-to-many relationship with Quest
- `activeQuests()`: Filtered quests with 'in-progress' status
- `completedQuests()`: Filtered quests with 'completed' status
- `items()`: Many-to-many relationship with Item (includes quantity)
- `resources()`: One-to-many relationship with Resource
- `guilds()`: Many-to-many relationship with Guild
- `profile()`: One-to-one relationship with Player_Profile

### Quest Model
- `players()`: Many-to-many relationship with Player
- `itemReward()`: Belongs-to relationship with Item

### Guild Model
- `members()`: Many-to-many relationship with Player
- `memberships()`: One-to-many relationship with Guild_Membership

### Item Model
- `players()`: Many-to-many relationship with Player
- `questRewards()`: One-to-many relationship with Quest

## Testing

### Manual Testing
1. Visit `/game` route
2. Test quest acceptance: Click "Accept Quest" on an available quest
3. Test quest completion: Click "Complete" on an active quest
4. Observe XP gain and level progression in PlayerDashboard
5. Check inventory for quest rewards in PlayerInventory
6. Test guild joining: Click "Join Guild" on an available guild
7. View guild members by clicking "View Members"
8. Test guild leaving: Click "Leave Guild"

### Unit Testing
Create tests in `tests/Feature/Livewire/`:
- PlayerDashboardTest.php
- QuestBoardTest.php
- PlayerInventoryTest.php
- GuildPanelTest.php

Example test:
```php
public function test_player_can_accept_quest()
{
    Livewire::test(QuestBoard::class)
        ->call('acceptQuest', 1)
        ->assertDispatched('quest-accepted');
}
```

## Performance Considerations

1. **Polling Intervals**: Adjusted to balance real-time updates with server load
   - Critical updates (dashboard): 5s
   - Moderate updates (quests/inventory): 10s
   - Low priority updates (guilds): 15s

2. **Lazy Loading**: Components load data only when mounted
3. **Eager Loading**: Related data is eager loaded to prevent N+1 queries
4. **Event Debouncing**: Events are dispatched only when necessary

## Future Enhancements

- [ ] Add WebSocket support for instant updates
- [ ] Implement private player channels for personalized notifications
- [ ] Add real-time chat within guilds
- [ ] Create PvP battle system with live updates
- [ ] Add marketplace for item trading
- [ ] Implement leaderboards with live rankings
- [ ] Add notification system for important events

## Security Considerations

- All Livewire components validate user permissions
- Quest completion requires validation
- Guild actions are authorized
- XSS protection via Blade templating
- CSRF protection via Livewire middleware
