<?php

namespace App\Events;

use App\Models\Player;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerLeveledUp
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerLeveledUp implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player;
    public $newLevel;
    public $oldLevel;

    public function __construct(Player $player, $newLevel, $oldLevel)
    {
        $this->player = $player;
        $this->newLevel = $newLevel;
        $this->oldLevel = $oldLevel;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Player $player, int $newLevel)
    {
        $this->player = $player;
        $this->newLevel = $newLevel;
        $this->message = "{$player->username} reached level {$newLevel}!";
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('game-events');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'player.leveled-up';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'player_id' => $this->player->id,
            'player_username' => $this->player->username,
            'new_level' => $this->newLevel,
            'message' => $this->message,
        ];
    }
}
