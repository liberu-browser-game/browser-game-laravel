<?php

namespace App\Listeners;

use App\Events\GuildInvitationSent;
use App\Models\GameNotification;
use App\Notifications\GuildInvitationNotification;

class SendGuildInvitationNotification
{
    public function handle(GuildInvitationSent $event): void
    {
        // Send email notification
        $event->player->notify(new GuildInvitationNotification($event->guild, $event->inviter));

        // Create in-game notification
        GameNotification::create([
            'player_id' => $event->player->id,
            'type' => 'guild_invitation',
            'title' => 'Guild Invitation',
            'message' => 'You have been invited to join ' . $event->guild->name,
            'data' => [
                'guild_id' => $event->guild->id,
                'guild_name' => $event->guild->name,
                'inviter_id' => $event->inviter->id,
                'inviter_username' => $event->inviter->username,
            ],
        ]);
    }
}
