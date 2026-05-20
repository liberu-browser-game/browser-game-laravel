# Usage Examples: Real-Time Gameplay System

This guide provides practical examples of how to use the implemented Livewire gameplay system.

## Accessing the Game Dashboard

### Route
```
GET /game
```

**Requirements:**
- User must be authenticated
- User account must be verified
- Uses `auth:sanctum` and `verified` middleware

**Example:**
```php
// In your navigation or links
<a href="{{ route('game.dashboard') }}" class="btn btn-primary">
    Play Game
</a>
```

## Using Livewire Components

### 1. PlayerDashboard Component

**Embedding in a view:**
```blade
<div class="container">
    @livewire('player-dashboard')
</div>
```

**Listening to events:**
```javascript
// Listen for level-up event
window.addEventListener('player-leveled-up', event => {
    console.log('Player leveled up to:', event.detail.level);
    // Show celebration animation or notification
});
```

**Manually refreshing:**
```javascript
// Trigger refresh from JavaScript
Livewire.dispatch('player-updated');
```

### 2. QuestBoard Component

**Embedding in a view:**
```blade
<div class="quests-container">
    @livewire('quest-board')
</div>
```

**Listening to quest events:**
```javascript
// Listen for quest completion
window.addEventListener('quest-completed', event => {
    console.log('Quest completed:', event.detail.questId);
    console.log('XP gained:', event.detail.experienceReward);
    // Show achievement notification
});

// Listen for quest acceptance
window.addEventListener('quest-accepted', event => {
    console.log('Quest accepted:', event.detail.questId);
});
```

### 3. PlayerInventory Component

**Embedding in a view:**
```blade
<div class="inventory-panel">
    @livewire('player-inventory')
</div>
```

**Listening to inventory events:**
```javascript
// Listen for item usage
window.addEventListener('item-used', event => {
    console.log('Item used:', event.detail.itemId);
    // Show item effect animation
});

// Listen for item drops
window.addEventListener('item-dropped', event => {
    console.log('Item dropped:', event.detail.itemId);
});
```

### 4. GuildPanel Component

**Embedding in a view:**
```blade
<div class="guild-section">
    @livewire('guild-panel')
</div>
```

**Listening to guild events:**
```javascript
// Listen for guild joins
window.addEventListener('guild-joined', event => {
    console.log('Joined guild:', event.detail.guildId);
    // Show welcome message
});

// Listen for guild leaves
window.addEventListener('guild-left', event => {
    console.log('Left guild:', event.detail.guildId);
});
```

## Broadcasting Events

### Setting Up Laravel Echo (Client-Side)

**Install dependencies:**
```bash
npm install --save laravel-echo pusher-js
```

**Configure Echo in your JavaScript:**
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

### Listening to Broadcast Events

**1. Player Level Up:**
```javascript
Echo.channel('game-events')
    .listen('.player.leveled-up', (e) => {
        console.log(`${e.player_username} reached level ${e.new_level}!`);
        // Show toast notification
        showToast(e.message, 'success');
    });
```

**2. Quest Completion:**
```javascript
Echo.channel('game-events')
    .listen('.quest.completed', (e) => {
        console.log(`${e.player_username} completed ${e.quest_name}!`);
        console.log(`They gained ${e.experience_gained} XP`);
        // Update leaderboard or activity feed
    });
```

**3. Guild Member Joined:**
```javascript
// Listen to specific guild channel
const guildId = 1;
Echo.channel(`guild.${guildId}`)
    .listen('.guild.member-joined', (e) => {
        console.log(`${e.player_username} (Level ${e.player_level}) joined ${e.guild_name}!`);
        // Refresh guild member list
        Livewire.dispatch('guild-updated');
    });
```

**4. Guild Member Left:**
```javascript
Echo.channel(`guild.${guildId}`)
    .listen('.guild.member-left', (e) => {
        console.log(`${e.player_username} left ${e.guild_name}`);
        // Update guild member list
    });
```

## Programmatic Usage

### Creating a Player

```php
use App\Models\Player;

$player = Player::create([
    'username' => 'DragonSlayer',
    'email' => 'dragon@example.com',
    'password' => bcrypt('secret'),
    'level' => 1,
    'experience' => 0,
]);
```

### Adding a Quest

```php
use App\Models\Quest;
use App\Models\Item;

$item = Item::create([
    'name' => 'Magic Sword',
    'description' => 'A sword imbued with magical power',
    'type' => 'weapon',
    'rarity' => 'epic',
]);

$quest = Quest::create([
    'name' => 'Defeat the Dragon',
    'description' => 'Slay the fearsome dragon in the mountain cave',
    'experience_reward' => 500,
    'item_reward_id' => $item->id,
]);
```

### Assigning a Quest to a Player

```php
$player->quests()->attach($quest->id, [
    'status' => 'in-progress',
    'created_at' => now(),
    'updated_at' => now(),
]);
```

### Completing a Quest Manually

```php
use App\Events\QuestCompleted;
use App\Events\PlayerLeveledUp;

// Update quest status
$player->quests()->updateExistingPivot($quest->id, [
    'status' => 'completed',
    'updated_at' => now(),
]);

// Award experience
$experienceGained = $quest->experience_reward;
$player->experience += $experienceGained;

// Check for level up
while ($player->experience >= ($player->level * 100)) {
    $player->level++;
    event(new PlayerLeveledUp($player, $player->level));
}

$player->save();

// Award item
if ($quest->item_reward_id) {
    $player->items()->attach($quest->item_reward_id, [
        'quantity' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// Broadcast quest completion
event(new QuestCompleted($player, $quest, $experienceGained));
```

### Managing Player Inventory

```php
// Add an item
$player->items()->attach($itemId, [
    'quantity' => 5,
    'created_at' => now(),
    'updated_at' => now(),
]);

// Update item quantity
$player->items()->updateExistingPivot($itemId, [
    'quantity' => 10,
]);

// Remove an item
$player->items()->detach($itemId);

// Get all items with quantities
$items = $player->items()->get();
foreach ($items as $item) {
    echo "{$item->name}: {$item->pivot->quantity}\n";
}
```

### Guild Management

```php
use App\Models\Guild;
use App\Events\GuildMemberJoined;
use App\Events\GuildMemberLeft;

// Create a guild
$guild = Guild::create([
    'name' => 'Dragon Slayers',
    'description' => 'Elite warriors dedicated to slaying dragons',
]);

// Player joins guild
$player->guilds()->attach($guild->id, [
    'role' => 'member',
    'joined_at' => now(),
    'created_at' => now(),
    'updated_at' => now(),
]);
event(new GuildMemberJoined($guild, $player));

// Promote to leader
$player->guilds()->updateExistingPivot($guild->id, [
    'role' => 'leader',
]);

// Player leaves guild
$player->guilds()->detach($guild->id);
event(new GuildMemberLeft($guild, $player));

// Get all guild members
$members = $guild->members()->get();
```

## Advanced Examples

### Custom Event Handler

```php
namespace App\Listeners;

use App\Events\QuestCompleted;
use Illuminate\Support\Facades\Log;

class LogQuestCompletion
{
    public function handle(QuestCompleted $event)
    {
        Log::info('Quest completed', [
            'player' => $event->player->username,
            'quest' => $event->quest->name,
            'xp_gained' => $event->experienceGained,
        ]);
        
        // Additional logic like achievements, statistics, etc.
    }
}
```

**Register in EventServiceProvider:**
```php
protected $listen = [
    QuestCompleted::class => [
        LogQuestCompletion::class,
    ],
];
```

### Custom Livewire Action

```php
// In your custom component
public function startBattle($enemyId)
{
    $enemy = Enemy::find($enemyId);
    
    // Battle logic...
    $won = rand(0, 1);
    
    if ($won) {
        // Award XP
        $this->player->experience += 100;
        $this->player->save();
        
        // Dispatch events
        $this->dispatch('player-updated');
        event(new PlayerLeveledUp($this->player, $this->player->level));
        
        session()->flash('success', 'You defeated ' . $enemy->name . '!');
    } else {
        session()->flash('error', 'You were defeated!');
    }
}
```

## Testing Examples

### Feature Test

```php
public function test_player_can_complete_quest_and_level_up()
{
    $player = Player::factory()->create([
        'level' => 1,
        'experience' => 80,
    ]);
    
    $quest = Quest::factory()->create([
        'experience_reward' => 50,
    ]);
    
    $player->quests()->attach($quest->id, [
        'status' => 'in-progress',
    ]);
    
    Event::fake();
    
    Livewire::test(QuestBoard::class)
        ->call('completeQuest', $quest->id)
        ->assertDispatched('quest-completed')
        ->assertDispatched('player-updated');
    
    $this->assertEquals(130, $player->fresh()->experience);
    $this->assertEquals(2, $player->fresh()->level);
    
    Event::assertDispatched(QuestCompleted::class);
    Event::assertDispatched(PlayerLeveledUp::class);
}
```

## Common Patterns

### Conditional Rendering

```blade
@if($player->level >= 10)
    <div class="veteran-badge">Veteran Player</div>
@endif

@if($player->guilds->count() > 0)
    <div class="guild-member-badge">Guild Member</div>
@endif
```

### Combining Components

```blade
<div class="game-layout">
    <div class="sidebar">
        @livewire('player-dashboard')
    </div>
    <div class="main-content">
        <div class="top-section">
            @livewire('quest-board')
        </div>
        <div class="bottom-section">
            <div class="inventory">
                @livewire('player-inventory')
            </div>
            <div class="social">
                @livewire('guild-panel')
            </div>
        </div>
    </div>
</div>
```

### Real-Time Notifications

```javascript
// Toast notification helper
function showGameNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// Listen for all game events
Echo.channel('game-events')
    .listen('.player.leveled-up', (e) => {
        showGameNotification(e.message, 'success');
    })
    .listen('.quest.completed', (e) => {
        showGameNotification(e.message, 'info');
    });
```

## Troubleshooting

### Issue: Events not firing

**Solution:**
Ensure broadcasting is configured:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=your-cluster
```

### Issue: Components not updating

**Solution:**
Check wire:poll is working:
```blade
{{-- Add to component view --}}
<div wire:poll.5s></div>
```

### Issue: Player not found

**Solution:**
Ensure a player exists or create a demo player:
```php
Player::firstOrCreate([
    'email' => 'demo@example.com'
], [
    'username' => 'Demo Player',
    'password' => bcrypt('password'),
    'level' => 1,
    'experience' => 0,
]);
```

## Best Practices

1. **Always validate data** before operations
2. **Use events** for cross-component communication
3. **Eager load relationships** to prevent N+1 queries
4. **Test thoroughly** before deployment
5. **Monitor performance** of polling intervals
6. **Handle errors gracefully** with try-catch blocks
7. **Use transactions** for multi-step operations
8. **Cache frequently accessed data**
9. **Log important events** for debugging
10. **Follow Laravel conventions** for maintainability
