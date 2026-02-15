# Database Schema Summary

## Quick Reference

This document provides a quick reference for the database schema implementation.

### Tables Created

1. ✅ **players** - Core player accounts
2. ✅ **player__profiles** - Extended player information  
3. ✅ **guilds** - Player guilds/clans
4. ✅ **guild__memberships** - Player-guild relationships
5. ✅ **items** - Game items catalog
6. ✅ **player__items** - Player inventory
7. ✅ **quests** - Available quests
8. ✅ **player__quests** - Player quest progress
9. ✅ **resources** - Player resources (gold, wood, stone)

### Migrations Location

All migrations are in: `/database/migrations/`

Key migrations:
- `2024_06_16_155510_create_players_table.php`
- `2024_06_16_162045_create_player__profiles_table.php`
- `2024_06_16_162446_create_guilds_table.php`
- `2024_06_16_162813_create_guild__memberships_table.php`
- `2024_06_16_163236_create_items_table.php`
- `2024_06_16_163524_create_player__items_table.php`
- `2024_06_16_163824_create_quests_table.php`
- `2024_06_16_164224_create_player__quests_table.php`
- `2024_06_16_164733_create_resources_table.php`

### Models Location

All models are in: `/app/Models/`

Models with full Eloquent relationships:
- `Player.php` - 6 relationships
- `Guild.php` - 3 relationships
- `Item.php` - 2 relationships
- `Quest.php` - 2 relationships
- `Player_Profile.php` - 1 relationship
- `Guild_Membership.php` - 2 relationships
- `Player_Item.php` - 2 relationships
- `Player_Quest.php` - 2 relationships
- `Resource.php` - 1 relationship

### Factories Location

All factories are in: `/database/factories/`

Factories for test data generation:
- `PlayerFactory.php`
- `PlayerProfileFactory.php`
- `GuildFactory.php`
- `GuildMembershipFactory.php`
- `ItemFactory.php`
- `PlayerItemFactory.php`
- `QuestFactory.php`
- `PlayerQuestFactory.php`
- `ResourceFactory.php`

### Seeders Location

Seeders are in: `/database/seeders/`

- `DatabaseSeeder.php` - Main seeder (calls GameSeeder)
- `GameSeeder.php` - Comprehensive game data seeder

### Usage Commands

```bash
# Run all migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration (drop all tables and re-migrate)
php artisan migrate:fresh

# Seed the database
php artisan db:seed

# Seed only game data
php artisan db:seed --class=GameSeeder

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### Sample Data Created by GameSeeder

- **10 Items**: Weapons, armor, and potions with varying rarities
- **5 Quests**: Various quests with experience and item rewards
- **4 Guilds**: Different guild types with descriptions
- **10 Players**: Sample players with profiles and resources
- **Guild Memberships**: Players assigned to guilds with roles
- **Player Items**: Players given random inventory items
- **Player Quests**: Players assigned random quests

### Key Features

✅ **Referential Integrity**: All foreign keys with proper cascading  
✅ **Indexing**: Automatic indexes on all foreign keys  
✅ **Relationships**: Full Eloquent ORM relationships  
✅ **Data Validation**: Unique constraints on usernames and emails  
✅ **Timestamps**: Automatic created_at and updated_at on all tables  
✅ **Factories**: Test data generation for all models  
✅ **Seeders**: Comprehensive initial data population  
✅ **Documentation**: Complete schema documentation in `/docs/DATABASE_SCHEMA.md`

### Relationship Summary

**Player** relationships:
- hasOne: profile
- hasMany: resources, guildMemberships
- belongsToMany: items, quests, guilds

**Guild** relationships:
- hasMany: memberships
- belongsToMany: players, leaders

**Item** relationships:
- belongsToMany: players
- hasMany: questRewards

**Quest** relationships:
- belongsTo: itemReward
- belongsToMany: players

### Security Features

- ✅ Password hashing using Laravel Hash
- ✅ Unique constraints on sensitive fields
- ✅ Hidden password field in Player model
- ✅ Foreign key constraints prevent orphaned records
- ✅ Cascading deletes maintain data integrity

### Performance Optimizations

- ✅ Indexed foreign keys
- ✅ Efficient pivot table structure
- ✅ Normalized database design
- ✅ Support for eager loading (N+1 query prevention)

### Next Steps (Future Enhancements)

Consider implementing:
- Soft deletes for data retention
- Achievements system
- Skills/abilities system
- Trading system
- Leaderboards
- Chat/messaging
- Events system

### Complete Documentation

For full documentation including ERD, field descriptions, and design decisions, see:
📖 `/docs/DATABASE_SCHEMA.md`
