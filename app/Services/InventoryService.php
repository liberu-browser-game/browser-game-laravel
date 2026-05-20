<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Player_Item;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Cache TTL in seconds (15 minutes)
     */
    private const CACHE_TTL = 900;

    /**
     * Get a player's inventory with caching.
     *
     * @param int $playerId
     * @return Collection
     */
    public function getPlayerInventory(int $playerId): Collection
    {
        $cacheKey = $this->getInventoryCacheKey($playerId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($playerId) {
            return Player_Item::where('player_id', $playerId)
                ->with('item')
                ->get();
        });
    }

    /**
     * Get a specific item from player's inventory.
     *
     * @param int $playerId
     * @param int $itemId
     * @return Player_Item|null
     */
    public function getPlayerItem(int $playerId, int $itemId): ?Player_Item
    {
        $cacheKey = $this->getPlayerItemCacheKey($playerId, $itemId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($playerId, $itemId) {
            return Player_Item::where('player_id', $playerId)
                ->where('item_id', $itemId)
                ->with('item')
                ->first();
        });
    }

    /**
     * Add an item to player's inventory or increase quantity.
     *
     * @param int $playerId
     * @param int $itemId
     * @param int $quantity
     * @return Player_Item
     */
    public function addItem(int $playerId, int $itemId, int $quantity = 1): Player_Item
    {
        $playerItem = Player_Item::firstOrCreate(
            [
                'player_id' => $playerId,
                'item_id' => $itemId,
            ],
            [
                'quantity' => 0,
            ]
        );

        $playerItem->increment('quantity', $quantity);
        $playerItem->load('item');

        // Invalidate cache
        $this->invalidatePlayerInventoryCache($playerId);
        $this->invalidatePlayerItemCache($playerId, $itemId);

        return $playerItem->fresh(['item']);
    }

    /**
     * Remove an item from player's inventory or decrease quantity.
     *
     * @param int $playerId
     * @param int $itemId
     * @param int $quantity
     * @return bool
     */
    public function removeItem(int $playerId, int $itemId, int $quantity = 1): bool
    {
        $playerItem = Player_Item::where('player_id', $playerId)
            ->where('item_id', $itemId)
            ->first();

        if (!$playerItem || $playerItem->quantity < $quantity) {
            return false;
        }

        if ($playerItem->quantity === $quantity) {
            $playerItem->delete();
        } else {
            $playerItem->decrement('quantity', $quantity);
        }

        // Invalidate cache
        $this->invalidatePlayerInventoryCache($playerId);
        $this->invalidatePlayerItemCache($playerId, $itemId);

        return true;
    }

    /**
     * Update item quantity directly.
     *
     * @param int $playerId
     * @param int $itemId
     * @param int $quantity
     * @return Player_Item|null
     */
    public function updateItemQuantity(int $playerId, int $itemId, int $quantity): ?Player_Item
    {
        $playerItem = Player_Item::where('player_id', $playerId)
            ->where('item_id', $itemId)
            ->first();

        if (!$playerItem) {
            return null;
        }

        if ($quantity <= 0) {
            $playerItem->delete();
            $this->invalidatePlayerInventoryCache($playerId);
            $this->invalidatePlayerItemCache($playerId, $itemId);
            return null;
        }

        $playerItem->update(['quantity' => $quantity]);
        $playerItem->load('item');

        // Invalidate cache
        $this->invalidatePlayerInventoryCache($playerId);
        $this->invalidatePlayerItemCache($playerId, $itemId);

        return $playerItem;
    }

    /**
     * Get inventory statistics for a player.
     *
     * @param int $playerId
     * @return array
     */
    public function getInventoryStats(int $playerId): array
    {
        $cacheKey = $this->getInventoryStatsCacheKey($playerId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($playerId) {
            return [
                'total_items' => Player_Item::where('player_id', $playerId)->sum('quantity'),
                'unique_items' => Player_Item::where('player_id', $playerId)->count(),
                'items_by_type' => Player_Item::where('player_id', $playerId)
                    ->join('items', 'player__items.item_id', '=', 'items.id')
                    ->select('items.type', DB::raw('count(*) as count'))
                    ->groupBy('items.type')
                    ->pluck('count', 'type')
                    ->toArray(),
            ];
        });
    }

    /**
     * Invalidate all inventory cache for a player.
     *
     * @param int $playerId
     * @return void
     */
    public function invalidatePlayerInventoryCache(int $playerId): void
    {
        Cache::forget($this->getInventoryCacheKey($playerId));
        Cache::forget($this->getInventoryStatsCacheKey($playerId));
    }

    /**
     * Invalidate specific player item cache.
     *
     * @param int $playerId
     * @param int $itemId
     * @return void
     */
    public function invalidatePlayerItemCache(int $playerId, int $itemId): void
    {
        Cache::forget($this->getPlayerItemCacheKey($playerId, $itemId));
    }

    /**
     * Get cache key for player inventory.
     *
     * @param int $playerId
     * @return string
     */
    private function getInventoryCacheKey(int $playerId): string
    {
        return "player.{$playerId}.inventory";
    }

    /**
     * Get cache key for specific player item.
     *
     * @param int $playerId
     * @param int $itemId
     * @return string
     */
    private function getPlayerItemCacheKey(int $playerId, int $itemId): string
    {
        return "player.{$playerId}.item.{$itemId}";
    }

    /**
     * Get cache key for inventory statistics.
     *
     * @param int $playerId
     * @return string
     */
    private function getInventoryStatsCacheKey(int $playerId): string
    {
        return "player.{$playerId}.inventory.stats";
    }
}
