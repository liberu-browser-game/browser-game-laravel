# Player Tracking System Architecture

## Entity Relationship Diagram

```
┌─────────────────────┐
│      Player         │
├─────────────────────┤
│ id                  │
│ username            │
│ email               │
│ level               │◄──────────┐
│ experience          │           │
└─────────────────────┘           │
         │                        │
         │ 1:1                    │
         ▼                        │
┌─────────────────────┐           │
│ PlayerStatistic     │           │
├─────────────────────┤           │
│ id                  │           │
│ player_id           │───────────┘
│ total_quests_...    │
│ total_items_...     │
│ total_playtime_...  │
│ highest_level_...   │
│ total_experience... │
│ quests_in_progress  │
│ achievements_...    │
└─────────────────────┘

         │
         │ N:M (via player_achievements)
         ▼
┌─────────────────────┐         ┌─────────────────────┐
│ player_achievements │◄────────│   Achievement       │
├─────────────────────┤         ├─────────────────────┤
│ player_id           │         │ id                  │
│ achievement_id      │─────────│ name                │
│ progress (0-100)    │         │ description         │
│ unlocked_at         │         │ icon                │
└─────────────────────┘         │ points              │
                                │ requirement_type    │
                                │ requirement_value   │
                                └─────────────────────┘

         │
         │ 1:N
         ▼
┌─────────────────────┐         ┌─────────────────────┐
│   Player_Quest      │◄────────│      Quest          │
├─────────────────────┤         ├─────────────────────┤
│ id                  │         │ id                  │
│ player_id           │         │ name                │
│ quest_id            │─────────│ description         │
│ status              │         │ experience_reward   │
│ progress_percentage │         │ item_reward_id      │
│ completed_at        │         └─────────────────────┘
└─────────────────────┘

         │
         │ 1:N
         ▼
┌─────────────────────┐         ┌─────────────────────┐
│   Player_Item       │◄────────│       Item          │
├─────────────────────┤         ├─────────────────────┤
│ id                  │         │ id                  │
│ player_id           │         │ name                │
│ item_id             │─────────│ description         │
│ quantity            │         │ type                │
└─────────────────────┘         └─────────────────────┘
```

## API Endpoints Flow

```
┌──────────────────────────────────────────────────────┐
│                    Client/Frontend                    │
└──────────────────────────────────────────────────────┘
                          │
                          │ HTTP Requests
                          ▼
┌──────────────────────────────────────────────────────┐
│                   API Routes Layer                    │
│  /api/achievements                                    │
│  /api/players/{id}/statistics                        │
│  /api/players/{id}/progression                       │
│  /api/players/{id}/achievements                      │
│  /api/players/{id}/achievements/unlocked             │
└──────────────────────────────────────────────────────┘
                          │
                          ▼
┌──────────────────────────────────────────────────────┐
│                   Controllers                         │
│  PlayerStatisticsController                          │
│  PlayerAchievementsController                        │
└──────────────────────────────────────────────────────┘
                          │
                          ▼
┌──────────────────────────────────────────────────────┐
│                   Models Layer                        │
│  Player, Achievement, PlayerStatistic                │
│  Player_Quest, Player_Item                           │
└──────────────────────────────────────────────────────┘
                          │
                          ▼
┌──────────────────────────────────────────────────────┐
│                   Database Layer                      │
│  players, achievements, player_achievements          │
│  player_statistics, player__quests, player__items    │
└──────────────────────────────────────────────────────┘
```

## Filament Admin Panel Structure

```
┌─────────────────────────────────────────────────────┐
│                  Filament Admin                      │
└─────────────────────────────────────────────────────┘
                          │
        ┌─────────────────┼─────────────────┐
        │                 │                 │
        ▼                 ▼                 ▼
┌──────────────┐  ┌──────────────┐  ┌─────────────┐
│   Players    │  │ Achievements │  │   Widgets   │
│   Resource   │  │   Resource   │  │             │
└──────────────┘  └──────────────┘  └─────────────┘
        │                 │                 │
        │                 │                 │
   ┌────┴────┐       ┌────┴────┐      ┌────┴────┐
   │         │       │         │      │         │
   ▼         ▼       ▼         ▼      ▼         ▼
Quests  Achievements List   Create  Player   Recent
Manager   Manager    View    Edit   Progress Achiev.
                                    Widget   Widget
```

## Achievement Tracking Flow

```
1. Player Action (e.g., level up, complete quest)
           │
           ▼
2. Update Player Statistics
   - Increment counters
   - Update highest values
           │
           ▼
3. Check Achievement Requirements
   - Compare player stats vs requirements
   - Calculate progress percentage
           │
           ▼
4. Update Achievement Progress
   - Update progress in player_achievements
   - Set unlocked_at if requirement met
           │
           ▼
5. Award Points & Update UI
   - Increment achievement count
   - Show notification/widget update
```

## Data Flow Example: Quest Completion

```
User completes quest
       │
       ▼
Update Player_Quest
  - status = 'completed'
  - progress_percentage = 100
  - completed_at = now()
       │
       ▼
Update PlayerStatistic
  - total_quests_completed++
  - quests_in_progress--
       │
       ▼
Check Quest-based Achievements
  - Get all 'quests_completed' achievements
  - Calculate progress for each
  - Update player_achievements pivot
       │
       ▼
Unlock Achievement if threshold met
  - Set unlocked_at = now()
  - achievements_unlocked++
       │
       ▼
Update Widgets
  - Recent achievements widget
  - Player progress widget
```

## Component Interaction Matrix

| Component               | Models Used                          | APIs Exposed                  | UI Elements          |
|------------------------|--------------------------------------|-------------------------------|----------------------|
| Achievement System     | Achievement, Player                  | GET /api/achievements         | AchievementResource  |
| Player Statistics      | PlayerStatistic, Player             | GET /api/players/{id}/stats   | Relation Manager     |
| Quest Tracking         | Player_Quest, Quest                 | -                             | QuestsManager        |
| Progress Monitoring    | All models                          | GET /api/players/{id}/prog    | PlayerProgressWidget |
| Achievement Tracking   | Achievement, Player                 | GET /api/players/{id}/achiev  | AchievementsManager  |

## Security Considerations

```
┌─────────────────────────────────────────┐
│         API Authentication               │
│  (Optional - can be added via Sanctum)  │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│         Route Middleware                 │
│  - Rate limiting                         │
│  - CORS headers                          │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│      Filament Authorization             │
│  - Admin-only access                     │
│  - Role-based permissions                │
└─────────────────────────────────────────┘
```

## Testing Strategy

```
┌──────────────────────┐
│    Unit Tests        │
│  - Models            │
│  - Relationships     │
│  - Data validation   │
└──────────────────────┘
          │
          ▼
┌──────────────────────┐
│   Feature Tests      │
│  - API endpoints     │
│  - Data integrity    │
│  - Business logic    │
└──────────────────────┘
          │
          ▼
┌──────────────────────┐
│  Integration Tests   │
│  - Full workflows    │
│  - UI interactions   │
│  - End-to-end flows  │
└──────────────────────┘
```
