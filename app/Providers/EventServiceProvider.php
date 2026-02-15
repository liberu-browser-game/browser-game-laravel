<?php

namespace App\Providers;

use App\Events\AchievementUnlocked;
use App\Events\GuildInvitationSent;
use App\Events\PlayerLeveledUp;
use App\Events\QuestCompleted;
use App\Listeners\SendAchievementNotification;
use App\Listeners\SendGuildInvitationNotification;
use App\Listeners\SendLevelUpNotification;
use App\Listeners\SendQuestCompletedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        QuestCompleted::class => [
            SendQuestCompletedNotification::class,
        ],
        PlayerLeveledUp::class => [
            SendLevelUpNotification::class,
        ],
        AchievementUnlocked::class => [
            SendAchievementNotification::class,
        ],
        GuildInvitationSent::class => [
            SendGuildInvitationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
