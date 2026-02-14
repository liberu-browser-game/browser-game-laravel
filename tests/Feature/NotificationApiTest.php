<?php

namespace Tests\Feature;

use App\Models\GameNotification;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
    }

    public function test_unauthenticated_user_cannot_access_notifications()
    {
        $response = $this->getJson('/api/notifications');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_without_player_gets_error()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/notifications');

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Player not found']);
    }

    public function test_can_get_all_notifications()
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();
        
        // Mock the player relationship
        $user->player = $player;
        Sanctum::actingAs($user);

        GameNotification::create([
            'player_id' => $player->id,
            'type' => 'test',
            'title' => 'Test Notification',
            'message' => 'Test message',
        ]);

        $response = $this->getJson('/api/notifications');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications',
            'unread_count',
        ]);
    }

    public function test_can_get_unread_notifications()
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();
        
        $user->player = $player;
        Sanctum::actingAs($user);

        GameNotification::create([
            'player_id' => $player->id,
            'type' => 'test1',
            'title' => 'Unread Notification',
            'message' => 'Message 1',
        ]);

        GameNotification::create([
            'player_id' => $player->id,
            'type' => 'test2',
            'title' => 'Read Notification',
            'message' => 'Message 2',
            'is_read' => true,
        ]);

        $response = $this->getJson('/api/notifications/unread');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications',
            'count',
        ]);
    }

    public function test_can_get_notification_count()
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();
        
        $user->player = $player;
        Sanctum::actingAs($user);

        GameNotification::create([
            'player_id' => $player->id,
            'type' => 'test',
            'title' => 'Test',
            'message' => 'Message',
        ]);

        $response = $this->getJson('/api/notifications/count');

        $response->assertStatus(200);
        $response->assertJsonStructure(['unread_count']);
    }

    public function test_can_mark_notification_as_read()
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();
        
        $user->player = $player;
        Sanctum::actingAs($user);

        $notification = GameNotification::create([
            'player_id' => $player->id,
            'type' => 'test',
            'title' => 'Test',
            'message' => 'Message',
        ]);

        $response = $this->postJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Notification marked as read']);
        
        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_cannot_mark_another_players_notification_as_read()
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();
        $otherPlayer = Player::factory()->create();
        
        $user->player = $player;
        Sanctum::actingAs($user);

        $notification = GameNotification::create([
            'player_id' => $otherPlayer->id,
            'type' => 'test',
            'title' => 'Test',
            'message' => 'Message',
        ]);

        $response = $this->postJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Notification not found']);
    }

    public function test_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();
        
        $user->player = $player;
        Sanctum::actingAs($user);

        GameNotification::create([
            'player_id' => $player->id,
            'type' => 'test1',
            'title' => 'Test 1',
            'message' => 'Message 1',
        ]);

        GameNotification::create([
            'player_id' => $player->id,
            'type' => 'test2',
            'title' => 'Test 2',
            'message' => 'Message 2',
        ]);

        $response = $this->postJson('/api/notifications/read-all');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'All notifications marked as read']);
        
        $this->assertEquals(0, $player->unreadNotifications()->count());
    }
}
