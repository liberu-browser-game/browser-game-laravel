# Quest System API Usage Examples

This file contains example API requests to demonstrate the quest system functionality.

## Prerequisites
- Have a player in the database (player_id = 1)
- Have quests seeded in the database

## Example API Requests

### 1. Get Available Quests
```bash
curl -X GET "http://localhost:8000/api/quests/available?player_id=1" \
  -H "Accept: application/json"
```

Expected Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Beginner's Trial",
      "description": "Complete your first challenge in the game world...",
      "experience_reward": 50,
      "item_reward_id": 1,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

### 2. Accept a Quest
```bash
curl -X POST "http://localhost:8000/api/quests/1/accept" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"player_id": 1}'
```

Expected Response:
```json
{
  "success": true,
  "message": "Quest accepted successfully",
  "data": {
    "id": 1,
    "player_id": 1,
    "quest_id": 1,
    "status": "in-progress",
    "quest": {
      "id": 1,
      "name": "Beginner's Trial",
      "description": "Complete your first challenge...",
      "experience_reward": 50,
      "item_reward_id": 1
    }
  }
}
```

### 3. Get Active Quests
```bash
curl -X GET "http://localhost:8000/api/quests/active?player_id=1" \
  -H "Accept: application/json"
```

Expected Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Beginner's Trial",
      "description": "Complete your first challenge in the game world...",
      "experience_reward": 50,
      "item_reward_id": 1,
      "pivot": {
        "player_id": 1,
        "quest_id": 1,
        "status": "in-progress"
      }
    }
  ]
}
```

### 4. Complete a Quest
```bash
curl -X POST "http://localhost:8000/api/quests/1/complete" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"player_id": 1}'
```

Expected Response:
```json
{
  "success": true,
  "message": "Quest completed successfully",
  "data": {
    "quest": {
      "id": 1,
      "name": "Beginner's Trial",
      "description": "Complete your first challenge...",
      "experience_reward": 50,
      "item_reward_id": 1
    },
    "rewards": {
      "experience": 50,
      "item": {
        "id": 1,
        "name": "Iron Sword",
        "description": "A sturdy iron sword for combat"
      }
    }
  }
}
```

### 5. Get Completed Quests
```bash
curl -X GET "http://localhost:8000/api/quests/completed?player_id=1" \
  -H "Accept: application/json"
```

Expected Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Beginner's Trial",
      "description": "Complete your first challenge in the game world...",
      "experience_reward": 50,
      "item_reward_id": 1,
      "pivot": {
        "player_id": 1,
        "quest_id": 1,
        "status": "completed"
      }
    }
  ]
}
```

### 6. Abandon a Quest
```bash
curl -X DELETE "http://localhost:8000/api/quests/2/abandon" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"player_id": 1}'
```

Expected Response:
```json
{
  "success": true,
  "message": "Quest abandoned successfully"
}
```

## Error Responses

### Quest Already Accepted
```json
{
  "success": false,
  "message": "Quest already in progress"
}
```

### Quest Already Completed
```json
{
  "success": false,
  "message": "Quest already completed"
}
```

### Quest Not Found or Not In Progress
```json
{
  "success": false,
  "message": "Quest not found or not in progress"
}
```

## Testing Workflow

1. **Setup**: Run migrations and seed the database
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Create a Player**: Use Filament admin or artisan tinker
   ```bash
   php artisan tinker
   >>> $player = App\Models\Player::create(['username' => 'hero', 'email' => 'hero@game.com', 'password' => bcrypt('password'), 'level' => 1, 'experience' => 0]);
   ```

3. **Test the Flow**:
   - Get available quests
   - Accept a quest
   - Check active quests
   - Complete the quest
   - Verify rewards were distributed
   - Check completed quests

## Integration with Frontend

For a frontend application, you would typically:

1. Fetch and display available quests
2. Allow player to click "Accept Quest" button
3. Show active quests in player's quest log
4. Allow player to complete quests (when objectives are met)
5. Display rewards and level-up notifications
6. Show quest history (completed quests)

Example JavaScript fetch:
```javascript
// Accept a quest
fetch('/api/quests/1/accept', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({ player_id: 1 })
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Quest accepted:', data.data);
    // Update UI to show quest in active quests
  }
});
```
