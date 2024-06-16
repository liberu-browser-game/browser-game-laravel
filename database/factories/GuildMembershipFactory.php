<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Guild;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guild_Membership>
 */
class GuildMembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'player_id' => Player::factory(),
            'guild_id' => Guild::factory(),
            'role' => $this->faker->randomElement(['leader', 'member']),
            'joined_at' => now(),
        ];
    }
}
