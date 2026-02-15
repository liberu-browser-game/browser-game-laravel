<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Models\GameNotification;
use App\Notifications\AchievementUnlockedNotification;

class SendAchievementNotification
{
    public function handle(AchievementUnlocked $event): void
    {
        // Send email notification
        $event->player->notify(new AchievementUnlockedNotification(
            $event->achievementName,
            $event->achievementDescription
        ));

        // Create in-game notification
        GameNotification::create([
            'player_id' => $event->player->id,
            'type' => 'achievement_unlocked',
            'title' => 'Achievement Unlocked!',
            'message' => 'You unlocked: ' . $event->achievementName,
            'data' => [
                'achievement_name' => $event->achievementName,
                'achievement_description' => $event->achievementDescription,
            ],
        ]);
    }
}
