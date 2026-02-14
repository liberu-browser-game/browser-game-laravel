# Quest System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                         QUEST SYSTEM                                 │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                      FRONTEND / CLIENT                               │
│  (Web App, Mobile App, Game Client)                                 │
└──────────────────────────┬──────────────────────────────────────────┘
                           │ HTTP/JSON
                           ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      API ROUTES (routes/api.php)                     │
├─────────────────────────────────────────────────────────────────────┤
│  GET  /api/quests/available    → List available quests              │
│  GET  /api/quests/active       → List active quests                 │
│  GET  /api/quests/completed    → List completed quests              │
│  POST /api/quests/{id}/accept  → Accept a quest                     │
│  POST /api/quests/{id}/complete→ Complete quest (get rewards)       │
│  DEL  /api/quests/{id}/abandon → Abandon a quest                    │
└──────────────────────────┬──────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────────┐
│              CONTROLLER (QuestController.php)                        │
├─────────────────────────────────────────────────────────────────────┤
│  - available(Request): JsonResponse                                  │
│  - active(Request): JsonResponse                                     │
│  - completed(Request): JsonResponse                                  │
│  - accept(Request, Quest): JsonResponse                              │
│  - complete(Request, Quest): JsonResponse                            │
│  - abandon(Request, Quest): JsonResponse                             │
└──────────────────────────┬──────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────────┐
│              SERVICE LAYER (QuestService.php)                        │
├─────────────────────────────────────────────────────────────────────┤
│  Business Logic:                                                     │
│  - acceptQuest(Player, Quest): Player_Quest                          │
│  - completeQuest(Player, Quest): array (rewards)                     │
│  - getAvailableQuests(Player): Collection                            │
│  - getActiveQuests(Player): Collection                               │
│  - getCompletedQuests(Player): Collection                            │
│  - abandonQuest(Player, Quest): bool                                 │
│  - distributeRewards(Player, Quest): array (private)                 │
└──────────────────────────┬──────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    ELOQUENT MODELS                                   │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌──────────────┐         ┌──────────────────┐      ┌────────────┐ │
│  │   Player     │◄───────►│  Player_Quest    │◄────►│   Quest    │ │
│  ├──────────────┤         ├──────────────────┤      ├────────────┤ │
│  │ id           │         │ id               │      │ id         │ │
│  │ username     │         │ player_id (FK)   │      │ name       │ │
│  │ email        │         │ quest_id (FK)    │      │ description│ │
│  │ level        │         │ status           │      │ xp_reward  │ │
│  │ experience   │         │ timestamps       │      │ item_id(FK)│ │
│  └──────────────┘         └──────────────────┘      └────────────┘ │
│         │                                                    │       │
│         │                                                    │       │
│         │                                                    ▼       │
│         │                                            ┌────────────┐ │
│         └───────────────────────────────────────────►│   Item     │ │
│                                                       ├────────────┤ │
│                     Player Items (Inventory)          │ id         │ │
│                     via player__items table           │ name       │ │
│                                                       │ description│ │
│                                                       └────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         DATABASE                                     │
├─────────────────────────────────────────────────────────────────────┤
│  Tables:                                                             │
│  - players                                                           │
│  - quests                                                            │
│  - player__quests (pivot table)                                      │
│  - items                                                             │
│  - player__items (inventory)                                         │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                      ADMIN PANEL (Filament)                          │
├─────────────────────────────────────────────────────────────────────┤
│  QuestResource.php → CRUD operations for quests                      │
│  /app/admin/quests → Manage quests via UI                           │
└─────────────────────────────────────────────────────────────────────┘


QUEST WORKFLOW:
===============

1. ACCEPT QUEST
   Player → API → Controller → Service → Create Player_Quest record
   Status: 'in-progress'

2. COMPLETE QUEST
   Player → API → Controller → Service → [Transaction]:
     - Update Player_Quest status to 'completed'
     - Award XP to player
     - Check if player levels up
     - Add item to player inventory
     - Return rewards summary

3. ABANDON QUEST
   Player → API → Controller → Service → Delete Player_Quest record


REWARD DISTRIBUTION:
====================

When quest is completed:
  1. Add XP to player.experience
  2. Check if player.experience >= (player.level × 100)
  3. If yes: player.level++
  4. If quest has item_reward_id:
     - Insert into player__items table
     - quantity = 1
  5. Return rewards object with:
     - experience (int)
     - item (object, if applicable)
     - level_up (bool, if applicable)


TESTING:
========

QuestSystemTest.php - 18 test cases:
  ✓ Accept quest
  ✓ Cannot accept same quest twice
  ✓ Cannot accept completed quest
  ✓ Complete quest and receive XP
  ✓ Complete quest and receive item
  ✓ Level up on sufficient XP
  ✓ Cannot complete quest not in progress
  ✓ Get available quests
  ✓ Get active quests
  ✓ Get completed quests
  ✓ Abandon quest
  ✓ Cannot abandon quest not in progress
  ✓ API: Get available quests
  ✓ API: Accept quest
  ✓ API: Complete quest
  ✓ API: Abandon quest
  ✓ Relationships work correctly
  ✓ All endpoints return proper JSON
```
