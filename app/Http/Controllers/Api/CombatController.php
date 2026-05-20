<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\CombatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CombatController extends Controller
{
    protected CombatService $combatService;

    public function __construct(CombatService $combatService)
    {
        $this->combatService = $combatService;
    }

    /**
     * Initiate a PvE battle.
     */
    public function pve(Request $request): JsonResponse
    {
        $request->validate([
            'player_id' => 'required|integer|exists:players,id',
            'opponent_name' => 'required|string|max:100',
            'opponent_level' => 'required|integer|min:1|max:100',
        ]);

        $player = Player::findOrFail($request->input('player_id'));
        $battle = $this->combatService->initiatePvEBattle(
            $player,
            $request->input('opponent_name'),
            (int) $request->input('opponent_level')
        );

        return response()->json([
            'success' => true,
            'data' => $battle,
        ]);
    }

    /**
     * Initiate a PvP battle.
     */
    public function pvp(Request $request): JsonResponse
    {
        $request->validate([
            'attacker_id' => 'required|integer|exists:players,id',
            'defender_id' => 'required|integer|exists:players,id|different:attacker_id',
        ]);

        $attacker = Player::findOrFail($request->input('attacker_id'));
        $defender = Player::findOrFail($request->input('defender_id'));
        $battle = $this->combatService->initiatePvPBattle($attacker, $defender);

        return response()->json([
            'success' => true,
            'data' => $battle,
        ]);
    }

    /**
     * Heal a player to full health.
     */
    public function heal(Request $request): JsonResponse
    {
        $request->validate([
            'player_id' => 'required|integer|exists:players,id',
        ]);

        $player = Player::findOrFail($request->input('player_id'));
        $this->combatService->healPlayer($player);

        return response()->json([
            'success' => true,
            'message' => 'Player healed successfully',
            'data' => $player->fresh(['equipment']),
        ]);
    }
}
