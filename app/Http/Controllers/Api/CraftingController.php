<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Recipe;
use App\Services\CraftingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CraftingController extends Controller
{
    protected CraftingService $craftingService;

    public function __construct(CraftingService $craftingService)
    {
        $this->craftingService = $craftingService;
    }

    /**
     * Craft an item using a recipe.
     */
    public function craft(Request $request, Recipe $recipe): JsonResponse
    {
        $request->validate([
            'player_id' => 'required|integer|exists:players,id',
        ]);

        $player = Player::findOrFail($request->input('player_id'));
        $result = $this->craftingService->craftItem($player, $recipe);

        $statusCode = $result['success'] ? 200 : 422;

        return response()->json($result, $statusCode);
    }

    /**
     * Learn a recipe.
     */
    public function learn(Request $request, Recipe $recipe): JsonResponse
    {
        $request->validate([
            'player_id' => 'required|integer|exists:players,id',
        ]);

        $player = Player::findOrFail($request->input('player_id'));
        $learned = $this->craftingService->learnRecipe($player, $recipe);

        if ($learned) {
            return response()->json([
                'success' => true,
                'message' => 'Recipe learned successfully',
                'data' => $recipe,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Recipe already known',
        ], 409);
    }

    /**
     * Get all recipes known by a player.
     */
    public function playerRecipes(Request $request, Player $player): JsonResponse
    {
        $recipes = $player->recipes()->with(['materials.item', 'resultItem'])->get();

        return response()->json([
            'success' => true,
            'data' => $recipes,
        ]);
    }
}
