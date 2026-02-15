# Notification System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    NOTIFICATION SYSTEM FLOW                      │
└─────────────────────────────────────────────────────────────────┘

                        Game Event Occurs
                               │
                               ▼
         ┌─────────────────────────────────────────┐
         │           Event Dispatcher               │
         │  (QuestCompleted, PlayerLeveledUp, etc) │
         └─────────────────────────────────────────┘
                               │
                               ▼
         ┌─────────────────────────────────────────┐
         │          Event Listeners                 │
         │  - SendQuestCompletedNotification        │
         │  - SendLevelUpNotification               │
         │  - SendAchievementNotification           │
         │  - SendGuildInvitationNotification       │
         └─────────────────────────────────────────┘
                               │
                ┌──────────────┴──────────────┐
                │                             │
                ▼                             ▼
    ┌──────────────────────┐      ┌──────────────────────┐
    │  Email Notification  │      │  In-Game Notification │
    │  (via Laravel Mail)  │      │  (GameNotification)   │
    └──────────────────────┘      └──────────────────────┘
                │                             │
                ▼                             ▼
         [Player Email]              [Database Table]
                                              │
                                              ▼
                                    ┌──────────────────────┐
                                    │   RESTful API        │
                                    │  /api/notifications  │
                                    └──────────────────────┘
                                              │
                                              ▼
                                      [Frontend Display]
```

## Component Breakdown

### 1. Event Layer
```
Events/
├── QuestCompleted.php         - Quest completion event
├── PlayerLeveledUp.php         - Level up event
├── AchievementUnlocked.php     - Achievement unlock event
└── GuildInvitationSent.php     - Guild invitation event
```

### 2. Listener Layer
```
Listeners/
├── SendQuestCompletedNotification.php
├── SendLevelUpNotification.php
├── SendAchievementNotification.php
└── SendGuildInvitationNotification.php
```

### 3. Notification Layer
```
Notifications/
├── QuestCompletedNotification.php
├── LevelUpNotification.php
├── AchievementUnlockedNotification.php
└── GuildInvitationNotification.php
```

### 4. Service Layer
```
Services/
└── NotificationService.php     - Central notification management
```

### 5. Model Layer
```
Models/
├── Player.php                  - Enhanced with Notifiable trait
└── GameNotification.php        - In-game notification model
```

### 6. API Layer
```
Http/Controllers/
└── NotificationController.php  - RESTful API endpoints
```

### 7. Database Layer
```
migrations/
├── create_notifications_table.php      - Laravel standard
└── create_game_notifications_table.php - Custom in-game
```

## Data Flow Examples

### Example 1: Quest Completion
```php
// 1. Game logic completes quest
$playerQuest->update(['status' => 'completed']);

// 2. Dispatch event
event(new QuestCompleted($player, $quest, $reward));

// 3. Listener catches event
public function handle(QuestCompleted $event) {
    // 4a. Send email
    $event->player->notify(new QuestCompletedNotification(...));
    
    // 4b. Create in-game notification
    GameNotification::create([...]);
}

// 5. Player receives email and sees in-game notification
```

### Example 2: Frontend Fetching Notifications
```javascript
// 1. Frontend requests notifications
GET /api/notifications

// 2. Controller authenticates user
$player = $request->user();

// 3. Service retrieves notifications
$notifications = $notificationService->getAllNotifications($player);

// 4. API returns JSON
{
  "notifications": [...],
  "unread_count": 5
}

// 5. Frontend displays notifications
```

## Database Schema

### notifications table (Laravel Standard)
```sql
CREATE TABLE notifications (
    id UUID PRIMARY KEY,
    type VARCHAR(255),
    notifiable_type VARCHAR(255),
    notifiable_id BIGINT,
    data TEXT,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(notifiable_type, notifiable_id)
);
```

### game_notifications table (Custom)
```sql
CREATE TABLE game_notifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    player_id BIGINT,
    type VARCHAR(255),
    title VARCHAR(255),
    message TEXT,
    data JSON NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX(player_id, is_read),
    INDEX(created_at)
);
```

## API Endpoints Architecture

```
┌──────────────────────────────────────────────────────┐
│             Sanctum Authentication                   │
│              (Bearer Token Required)                 │
└──────────────────────────────────────────────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
        ▼                ▼                ▼
   GET /api/     GET /api/        POST /api/
 notifications  notifications/   notifications/
                   unread         {id}/read
        │                │                │
        ▼                ▼                ▼
┌─────────────┐  ┌──────────────┐  ┌──────────────┐
│ All Notifs  │  │ Unread Only  │  │  Mark Read   │
│ (paginated) │  │ (with count) │  │  (single)    │
└─────────────┘  └──────────────┘  └──────────────┘
```

## Security Architecture

```
┌─────────────────────────────────────────────────────┐
│                 Security Layers                      │
└─────────────────────────────────────────────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
        ▼                ▼                ▼
┌─────────────┐  ┌──────────────┐  ┌──────────────┐
│ Sanctum     │  │ Authorization│  │ Input        │
│ Auth        │  │ Checks       │  │ Validation   │
│ (Required)  │  │ (Own Data)   │  │ (Eloquent)   │
└─────────────┘  └──────────────┘  └──────────────┘
```

## Testing Architecture

```
┌─────────────────────────────────────────────────────┐
│                   Test Coverage                      │
└─────────────────────────────────────────────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
        ▼                ▼                ▼
┌─────────────┐  ┌──────────────┐  ┌──────────────┐
│ Unit Tests  │  │  API Tests   │  │ Integration  │
│ (Events,    │  │ (Endpoints,  │  │ (Service     │
│  Models)    │  │  Auth)       │  │  Methods)    │
└─────────────┘  └──────────────┘  └──────────────┘
      │                  │                  │
      └──────────────────┴──────────────────┘
                         │
                         ▼
              19 Tests - All Passing ✓
```

## Performance Considerations

### Database Optimization
- Indexed player_id for fast player-specific queries
- Indexed is_read for filtering unread notifications
- Indexed created_at for chronological sorting
- Foreign key constraints with cascading deletes

### Query Optimization
```php
// Scoped query for unread notifications
$player->unreadNotifications() // Only queries is_read = false

// Pagination built-in
$service->getAllNotifications($player, $limit = 50);

// Eager loading support
$player->with('gameNotifications')->get();
```

### Scalability
- Queueable notifications for async processing
- RESTful API enables frontend caching
- Event-driven architecture for loose coupling
- Service layer for business logic centralization

## Extension Points

### Adding New Notification Types
1. Create Event class in `app/Events/`
2. Create Listener in `app/Listeners/`
3. Create Notification in `app/Notifications/`
4. Register in `EventServiceProvider`
5. Add service method in `NotificationService`

### Customizing Email Templates
1. Publish mail views: `php artisan vendor:publish --tag=mail`
2. Edit templates in `resources/views/vendor/mail/`
3. Customize notification `toMail()` methods

### Adding Push Notifications
1. Implement `via()` method to include 'fcm' or 'apn'
2. Add push notification classes
3. Configure push notification credentials
4. Update frontend to handle push notifications

## Deployment Diagram

```
┌──────────────────────────────────────────────────────────┐
│                    Production Stack                       │
└──────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Laravel    │    │   Database   │    │ Queue Worker │
│   App        │◄───│   MySQL      │    │ (Optional)   │
│              │    │              │    │              │
└──────────────┘    └──────────────┘    └──────────────┘
        │
        ▼
┌──────────────┐
│ Email Server │
│ (SMTP)       │
└──────────────┘
```

## Monitoring & Logging

### What to Monitor
- Email delivery success rate
- Notification creation rate
- API endpoint response times
- Database query performance
- Queue worker status (if using queues)

### Laravel Log Integration
```php
// All notification events are logged
Log::info('Notification sent', [
    'type' => $notification->type,
    'player_id' => $player->id
]);
```

## Best Practices Implemented

✅ **Separation of Concerns**: Events, listeners, and notifications are separate
✅ **Single Responsibility**: Each class has one clear purpose
✅ **Dependency Injection**: Services injected via constructor
✅ **Type Safety**: Full type hinting on all methods
✅ **RESTful Design**: API follows REST principles
✅ **Security First**: Authentication and authorization on all endpoints
✅ **Test Coverage**: Comprehensive test suite
✅ **Documentation**: Extensive inline and external documentation
✅ **Error Handling**: Proper error responses and logging
✅ **Performance**: Database indexing and query optimization

## Summary

This architecture provides a robust, scalable, and maintainable notification system that:
- Decouples game logic from notification delivery
- Supports multiple notification channels (email, database)
- Provides a clean API for frontend integration
- Ensures security through authentication and authorization
- Scales with the application through proper indexing and optional queuing
- Maintains code quality through comprehensive testing
