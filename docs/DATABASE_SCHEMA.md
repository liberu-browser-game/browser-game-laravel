# Database Schema Documentation

## Overview

This document describes the database schema for the persistent browser-based game. The schema is designed to support player management, in-game resources, quests, items, inventory management, and guilds.

## Database Design Principles

1. **Normalization**: The schema follows database normalization best practices to minimize data redundancy.
2. **Referential Integrity**: All foreign keys have proper constraints with cascading rules.
3. **Scalability**: The design supports future growth and can handle increasing numbers of players and data.
4. **Performance**: Proper indexing is applied on foreign keys and frequently queried columns.

## Tables

### Players

The `players` table stores core player account information.

| Column       | Type      | Description                        |
|--------------|-----------|-----------------------------------|
| id           | bigint    | Primary key                       |
| username     | string    | Unique player username            |
| email        | string    | Unique player email               |
| password     | string    | Hashed password                   |
| level        | integer   | Player's current level (default: 1)|
| experience   | integer   | Player's experience points (default: 0)|
| created_at   | timestamp | Record creation timestamp         |
| updated_at   | timestamp | Record last update timestamp      |

**Indexes:**
- Primary key on `id`
- Unique index on `username`
- Unique index on `email`

**Relationships:**
- Has one `Player_Profile`
- Has many `Resources`
- Has many `Guild_Memberships`
- Belongs to many `Items` through `Player_Items`
- Belongs to many `Quests` through `Player_Quests`
- Belongs to many `Guilds` through `Guild_Memberships`

### Player_Profiles

The `player__profiles` table stores additional profile information for players.

| Column      | Type      | Description                    |
|-------------|-----------|--------------------------------|
| id          | bigint    | Primary key                    |
| player_id   | bigint    | Foreign key to players table   |
| avatar_url  | string    | URL to player's avatar (nullable)|
| bio         | text      | Player biography (nullable)    |
| created_at  | timestamp | Record creation timestamp      |
| updated_at  | timestamp | Record last update timestamp   |

**Indexes:**
- Primary key on `id`
- Foreign key index on `player_id`

**Relationships:**
- Belongs to `Player`

**Constraints:**
- Foreign key `player_id` references `players(id)` with `ON DELETE CASCADE`

### Guilds

The `guilds` table stores information about player guilds/clans.

| Column      | Type      | Description                    |
|-------------|-----------|--------------------------------|
| id          | bigint    | Primary key                    |
| name        | string    | Unique guild name              |
| description | text      | Guild description              |
| created_at  | timestamp | Record creation timestamp      |
| updated_at  | timestamp | Record last update timestamp   |

**Indexes:**
- Primary key on `id`
- Unique index on `name`

**Relationships:**
- Has many `Guild_Memberships`
- Belongs to many `Players` through `Guild_Memberships`

### Guild_Memberships

The `guild__memberships` table stores the many-to-many relationship between players and guilds.

| Column      | Type      | Description                            |
|-------------|-----------|----------------------------------------|
| id          | bigint    | Primary key                            |
| player_id   | bigint    | Foreign key to players table           |
| guild_id    | bigint    | Foreign key to guilds table            |
| role        | string    | Member role (e.g., 'leader', 'member') |
| joined_at   | timestamp | When the player joined the guild       |
| created_at  | timestamp | Record creation timestamp              |
| updated_at  | timestamp | Record last update timestamp           |

**Indexes:**
- Primary key on `id`
- Foreign key index on `player_id`
- Foreign key index on `guild_id`

**Relationships:**
- Belongs to `Player`
- Belongs to `Guild`

**Constraints:**
- Foreign key `player_id` references `players(id)` with `ON DELETE CASCADE`
- Foreign key `guild_id` references `guilds(id)` with `ON DELETE CASCADE`

### Items

The `items` table stores all game items that players can collect.

| Column      | Type      | Description                                    |
|-------------|-----------|------------------------------------------------|
| id          | bigint    | Primary key                                    |
| name        | string    | Item name                                      |
| description | text      | Item description                               |
| type        | string    | Item type (e.g., 'weapon', 'armor', 'potion')  |
| rarity      | string    | Item rarity (e.g., 'common', 'rare', 'legendary')|
| created_at  | timestamp | Record creation timestamp                      |
| updated_at  | timestamp | Record last update timestamp                   |

**Indexes:**
- Primary key on `id`

**Relationships:**
- Belongs to many `Players` through `Player_Items`
- Has many `Quests` (as item rewards)

### Player_Items

The `player__items` table stores the many-to-many relationship between players and items (inventory).

| Column      | Type      | Description                    |
|-------------|-----------|--------------------------------|
| id          | bigint    | Primary key                    |
| player_id   | bigint    | Foreign key to players table   |
| item_id     | bigint    | Foreign key to items table     |
| quantity    | integer   | Number of items owned          |
| created_at  | timestamp | Record creation timestamp      |
| updated_at  | timestamp | Record last update timestamp   |

**Indexes:**
- Primary key on `id`
- Foreign key index on `player_id`
- Foreign key index on `item_id`

**Relationships:**
- Belongs to `Player`
- Belongs to `Item`

**Constraints:**
- Foreign key `player_id` references `players(id)` with `ON DELETE CASCADE`
- Foreign key `item_id` references `items(id)` with `ON DELETE CASCADE`

### Quests

The `quests` table stores all available quests in the game.

| Column             | Type      | Description                            |
|--------------------|-----------|----------------------------------------|
| id                 | bigint    | Primary key                            |
| name               | string    | Quest name                             |
| description        | text      | Quest description                      |
| experience_reward  | integer   | Experience points awarded on completion|
| item_reward_id     | bigint    | Foreign key to items table (nullable)  |
| created_at         | timestamp | Record creation timestamp              |
| updated_at         | timestamp | Record last update timestamp           |

**Indexes:**
- Primary key on `id`
- Foreign key index on `item_reward_id`

**Relationships:**
- Belongs to `Item` (item reward)
- Belongs to many `Players` through `Player_Quests`

**Constraints:**
- Foreign key `item_reward_id` references `items(id)` with `ON DELETE SET NULL`

### Player_Quests

The `player__quests` table stores the many-to-many relationship between players and quests.

| Column      | Type      | Description                                       |
|-------------|-----------|--------------------------------------------------|
| id          | bigint    | Primary key                                       |
| player_id   | bigint    | Foreign key to players table                      |
| quest_id    | bigint    | Foreign key to quests table                       |
| status      | enum      | Quest status ('in-progress' or 'completed')       |
| created_at  | timestamp | Record creation timestamp                         |
| updated_at  | timestamp | Record last update timestamp                      |

**Indexes:**
- Primary key on `id`
- Foreign key index on `player_id`
- Foreign key index on `quest_id`

**Relationships:**
- Belongs to `Player`
- Belongs to `Quest`

**Constraints:**
- Foreign key `player_id` references `players(id)` with `ON DELETE CASCADE`
- Foreign key `quest_id` references `quests(id)` with `ON DELETE CASCADE`

### Resources

The `resources` table stores player resources (gold, wood, stone, etc.).

| Column        | Type      | Description                                      |
|---------------|-----------|--------------------------------------------------|
| id            | bigint    | Primary key                                      |
| player_id     | bigint    | Foreign key to players table                     |
| resource_type | string    | Type of resource (e.g., 'gold', 'wood', 'stone') |
| quantity      | integer   | Amount of resource                               |
| created_at    | timestamp | Record creation timestamp                        |
| updated_at    | timestamp | Record last update timestamp                     |

**Indexes:**
- Primary key on `id`
- Foreign key index on `player_id`

**Relationships:**
- Belongs to `Player`

**Constraints:**
- Foreign key `player_id` references `players(id)` with `ON DELETE CASCADE`

## Entity Relationship Diagram (ERD) - Text Representation

```
Players (1) ─────────── (1) Player_Profiles
   │
   │ (1)
   │
   ├─────────────────── (*) Resources
   │
   │ (*)                (*) 
   ├─────── Guild_Memberships ─────── Guilds (*)
   │
   │ (*)                (*) 
   ├─────── Player_Items ─────── Items (*)
   │                              │
   │ (*)                (*)       │ (1)
   └─────── Player_Quests ─────── Quests ──┘
                                  (item_reward)
```

## Laravel Eloquent Relationships

### Player Model

```php
// One-to-One
public function profile(): HasOne // Returns Player_Profile
    
// One-to-Many
public function resources(): HasMany // Returns Collection<Resource>
public function guildMemberships(): HasMany // Returns Collection<Guild_Membership>

// Many-to-Many
public function items(): BelongsToMany // Returns Collection<Item>
public function quests(): BelongsToMany // Returns Collection<Quest>
public function guilds(): BelongsToMany // Returns Collection<Guild>
```

### Guild Model

```php
// One-to-Many
public function memberships(): HasMany // Returns Collection<Guild_Membership>

// Many-to-Many
public function players(): BelongsToMany // Returns Collection<Player>
public function leaders(): BelongsToMany // Returns Collection<Player> (filtered by role)
```

### Item Model

```php
// Many-to-Many
public function players(): BelongsToMany // Returns Collection<Player>

// One-to-Many (inverse)
public function questRewards(): HasMany // Returns Collection<Quest>
```

### Quest Model

```php
// Many-to-One
public function itemReward(): BelongsTo // Returns Item

// Many-to-Many
public function players(): BelongsToMany // Returns Collection<Player>
```

## Migration Order

Migrations must be run in the following order to respect foreign key constraints:

1. `create_players_table`
2. `create_player__profiles_table`
3. `create_guilds_table`
4. `create_guild__memberships_table`
5. `create_items_table`
6. `create_player__items_table`
7. `create_quests_table` (depends on items for item_reward_id)
8. `create_player__quests_table`
9. `create_resources_table`

## Seeding Strategy

The `GameSeeder` populates the database in this order:

1. **Items** - Creates initial weapons, armor, and potions
2. **Quests** - Creates quests with item rewards
3. **Guilds** - Creates initial guilds
4. **Players** - Creates sample players with profiles and resources
5. **Guild Memberships** - Assigns players to guilds with roles
6. **Player Items** - Gives players initial inventory
7. **Player Quests** - Assigns quests to players

To seed the database:

```bash
php artisan db:seed --class=GameSeeder
```

Or to seed everything including game data:

```bash
php artisan db:seed
```

## Design Decisions

### Table Naming Conventions

- Main entity tables use plural names (e.g., `players`, `guilds`)
- Junction tables use singular entity names separated by underscores (e.g., `guild__memberships`, `player__items`)
- Double underscores in table names match the Laravel convention for pivot tables with the `Player_` prefix models

### Soft Deletes

Soft deletes are not implemented in this initial schema but can be added later if needed for data retention and audit purposes.

### Cascading Rules

- **CASCADE on DELETE**: When a player is deleted, all related records (profile, resources, memberships, items, quests) are automatically deleted
- **SET NULL**: When an item used as a quest reward is deleted, the `item_reward_id` in quests is set to NULL rather than deleting the quest

### Password Storage

Player passwords are stored using Laravel's Hash facade, which uses bcrypt by default for secure password hashing.

### Timestamps

All tables include `created_at` and `updated_at` timestamps managed automatically by Laravel.

## Performance Considerations

1. **Indexing**: All foreign keys are automatically indexed
2. **Query Optimization**: Use Laravel's eager loading to avoid N+1 query problems
3. **Pagination**: Implement pagination for large result sets
4. **Caching**: Consider caching frequently accessed data like items and quests

## Future Enhancements

Potential future additions to the schema:

1. **Achievements Table**: Track player achievements
2. **Skills Table**: Player skills and abilities
3. **Trading System**: Player-to-player item trades
4. **Guild Wars**: Guild vs guild combat system
5. **Leaderboards**: Rankings and statistics
6. **Events Table**: Time-limited game events
7. **Chat/Messages**: In-game communication

## Security Considerations

1. Passwords are hashed using bcrypt
2. Email addresses should be validated before storage
3. Implement rate limiting on quest completions and resource gathering
4. Use Laravel's built-in CSRF protection
5. Validate all user input before database operations
6. Implement proper authorization checks using Laravel policies

## Testing

To test the schema:

```bash
# Run migrations
php artisan migrate:fresh

# Seed the database
php artisan db:seed

# Run tests
php artisan test
```

## Maintenance

Regular maintenance tasks:

1. Monitor query performance
2. Review and optimize slow queries
3. Clean up orphaned records (if any)
4. Backup database regularly
5. Monitor database size and plan for scaling
