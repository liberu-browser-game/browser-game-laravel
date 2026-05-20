# Notification System Documentation

This document describes the notification system implemented for the Browser Game Laravel application.

## Overview

The notification system provides both **email notifications** and **in-game notifications** to alert players about important events, achievements, and updates.

## Features

- **Email Notifications**: Players receive email notifications for important events
- **In-game Notifications**: Real-time notifications displayed within the game interface
- **Event-Driven Architecture**: Notifications are triggered by game events
- **RESTful API**: Complete API for managing notifications
- **Mark as Read**: Track which notifications have been viewed
- **Notification Service**: Centralized service for managing all notification operations

## Database Tables

### `notifications` Table
Standard Laravel notifications table for email and database notifications.

### `game_notifications` Table
Custom table for in-game notifications with the following fields:
- `id`: Primary key
- `player_id`: Foreign key to players table
- `type`: Type of notification (quest_completed, level_up, achievement_unlocked, etc.)
- `title`: Notification title
- `message`: Notification message
- `data`: JSON field for additional data
- `is_read`: Boolean flag
- `read_at`: Timestamp when marked as read
- `created_at`, `updated_at`: Standard timestamps

## Notification Types

### 1. Quest Completed
Triggered when a player completes a quest.

```php
use App\Services\NotificationService;

$notificationService = new NotificationService();
$notificationService->notifyQuestCompleted($player, $quest, $reward);
```

### 2. Level Up
Triggered when a player gains a level.

```php
$notificationService->notifyLevelUp($player, $newLevel, $oldLevel);
```

### 3. Achievement Unlocked
Triggered when a player unlocks an achievement.

```php
$notificationService->notifyAchievementUnlocked($player, $achievementName, $achievementDescription);
```

### 4. Guild Invitation
Triggered when a player is invited to join a guild.

```php
$notificationService->notifyGuildInvitation($player, $guild, $inviter);
```

## API Endpoints

All endpoints require authentication via Sanctum.

### Get All Notifications
```
GET /api/notifications
```

Query Parameters:
- `limit` (optional): Number of notifications to return (default: 50)

Response:
```json
{
  "notifications": [...],
  "unread_count": 5
}
```

### Get Unread Notifications
```
GET /api/notifications/unread
```

Response:
```json
{
  "notifications": [...],
  "count": 3
}
```

### Get Unread Count
```
GET /api/notifications/count
```

Response:
```json
{
  "unread_count": 5
}
```

### Mark Notification as Read
```
POST /api/notifications/{id}/read
```

Response:
```json
{
  "message": "Notification marked as read",
  "notification": {...}
}
```

### Mark All Notifications as Read
```
POST /api/notifications/read-all
```

Response:
```json
{
  "message": "All notifications marked as read"
}
```

## Events and Listeners

The notification system uses Laravel's event system:

### Events
- `QuestCompleted`: Dispatched when a quest is completed
- `PlayerLeveledUp`: Dispatched when a player levels up
- `AchievementUnlocked`: Dispatched when an achievement is unlocked
- `GuildInvitationSent`: Dispatched when a guild invitation is sent

### Listeners
Each event has a corresponding listener that:
1. Sends an email notification
2. Creates an in-game notification

Example:
- `SendQuestCompletedNotification`
- `SendLevelUpNotification`
- `SendAchievementNotification`
- `SendGuildInvitationNotification`

## Using the Notification Service

The `NotificationService` provides a convenient API for creating notifications:

```php
use App\Services\NotificationService;

$service = app(NotificationService::class);

// Create a quest completion notification
$service->notifyQuestCompleted($player, $quest, 'Epic Sword');

// Create a level up notification
$service->notifyLevelUp($player, 10, 9);

// Create an achievement notification
$service->notifyAchievementUnlocked($player, 'Dragon Slayer', 'Defeated 100 dragons');

// Create a guild invitation notification
$service->notifyGuildInvitation($player, $guild, $inviter);

// Get unread notifications
$unreadNotifications = $service->getUnreadNotifications($player);

// Get all notifications (with limit)
$allNotifications = $service->getAllNotifications($player, 100);

// Get unread count
$count = $service->getUnreadCount($player);

// Mark a notification as read
$service->markAsRead($notification);

// Mark all notifications as read
$service->markAllAsRead($player);
```

## Email Templates

Email notifications use Laravel's Mailable system with customizable templates. Each notification type has:
- Subject line
- Greeting
- Body text
- Call-to-action button
- Signature

You can customize email templates in `resources/views/vendor/mail`.

## Testing

The notification system includes comprehensive tests:

### Unit Tests
- Event dispatching
- Notification creation
- Email sending (mocked)
- Service methods

### Feature Tests
- API endpoint authentication
- API endpoint responses
- Notification CRUD operations
- Mark as read functionality

Run tests:
```bash
php artisan test --filter NotificationTest
php artisan test --filter NotificationApiTest
```

## Integration Examples

### In a Quest Controller

```php
use App\Services\NotificationService;

class QuestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function complete(Request $request, Quest $quest)
    {
        $player = $request->user()->player;
        
        // Mark quest as complete
        $playerQuest = PlayerQuest::where('player_id', $player->id)
            ->where('quest_id', $quest->id)
            ->first();
            
        $playerQuest->update(['status' => 'completed']);
        
        // Give rewards
        $reward = $this->giveQuestReward($player, $quest);
        
        // Send notification
        $this->notificationService->notifyQuestCompleted($player, $quest, $reward);
        
        return response()->json(['message' => 'Quest completed!']);
    }
}
```

### In a Player Model Observer

```php
use App\Services\NotificationService;

class PlayerObserver
{
    public function updated(Player $player)
    {
        if ($player->wasChanged('level')) {
            $notificationService = app(NotificationService::class);
            $notificationService->notifyLevelUp(
                $player, 
                $player->level, 
                $player->getOriginal('level')
            );
        }
    }
}
```

## Frontend Integration

### Fetching Notifications (JavaScript Example)

```javascript
// Get all notifications
fetch('/api/notifications', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    console.log('Notifications:', data.notifications);
    console.log('Unread count:', data.unread_count);
});

// Mark notification as read
fetch(`/api/notifications/${notificationId}/read`, {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => console.log(data.message));

// Mark all as read
fetch('/api/notifications/read-all', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => console.log(data.message));
```

## Configuration

### Email Configuration

Configure email settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@browsergame.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Queue Configuration (Optional)

For better performance, queue email notifications:

```env
QUEUE_CONNECTION=database
```

Then run the queue worker:
```bash
php artisan queue:work
```

## Security Considerations

1. **Authentication**: All API endpoints require Sanctum authentication
2. **Authorization**: Players can only access their own notifications
3. **Input Validation**: All notification data is validated before storage
4. **XSS Protection**: Notification messages are escaped in frontend display

## Future Enhancements

Potential improvements to the notification system:
- Push notifications for mobile devices
- WebSocket support for real-time notifications
- Notification preferences (allow players to customize what they receive)
- Notification categories and filtering
- Batch notifications for similar events
- Notification templates with localization support

## Troubleshooting

### Emails not being sent
1. Check `.env` mail configuration
2. Verify MAIL_MAILER is set correctly
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test mail configuration: `php artisan tinker` then `Mail::raw('Test', function($msg) { $msg->to('test@example.com'); });`

### Notifications not appearing in database
1. Ensure migrations have been run: `php artisan migrate`
2. Check event listeners are registered in `EventServiceProvider`
3. Verify events are being dispatched correctly

### API endpoints returning 401
1. Ensure user is authenticated with Sanctum
2. Check API token is being sent in Authorization header
3. Verify token hasn't expired

## Support

For issues or questions about the notification system, please refer to:
- Laravel Notifications Documentation: https://laravel.com/docs/notifications
- Laravel Events Documentation: https://laravel.com/docs/events
- Project Repository Issues
