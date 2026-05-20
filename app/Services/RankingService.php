<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Support\Facades\DB;

class RankingService
{
    /**
     * Calculate and update rankings for all players.
     *
     * @return int Number of players updated
     */
    public function updateAllRankings(): int
    {
        // Get all players ordered by score (desc), then level (desc), then experience (desc)
        $players = Player::orderByDesc('score')
            ->orderByDesc('level')
            ->orderByDesc('experience')
            ->get();

        $updatedCount = 0;
        $currentRank = 1;

        foreach ($players as $player) {
            $player->rank = $currentRank;
            $player->last_rank_update = now();
            $player->save();
            
            $currentRank++;
            $updatedCount++;
        }

        return $updatedCount;
    }

    /**
     * Recalculate scores for all players.
     *
     * @return int Number of players updated
     */
    public function recalculateScores(): int
    {
        $players = Player::all();
        $updatedCount = 0;

        foreach ($players as $player) {
            $newScore = $player->calculateScore();
            
            if ($player->score !== $newScore) {
                $player->score = $newScore;
                $player->save();
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Update score and ranking for a specific player.
     *
     * @param Player $player
     * @return void
     */
    public function updatePlayerRanking(Player $player): void
    {
        // Recalculate the player's score
        $player->score = $player->calculateScore();
        $player->save();

        // Update rankings for all players
        $this->updateAllRankings();
    }

    /**
     * Get top players by rank.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopPlayers(int $limit = 10)
    {
        return Player::whereNotNull('rank')
            ->orderBy('rank')
            ->limit($limit)
            ->get();
    }

    /**
     * Get player's ranking position.
     *
     * @param Player $player
     * @return int|null
     */
    public function getPlayerRank(Player $player): ?int
    {
        return $player->rank;
    }
}
