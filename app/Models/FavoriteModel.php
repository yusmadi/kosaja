<?php

namespace App\Models;

use CodeIgniter\Model;

class FavoriteModel extends Model
{
    protected $table            = 'favorites';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'listing_id',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Check if a specific listing is favorited by a user
     */
    public function isFavorited(int $userId, int $listingId): bool
    {
        return $this->where('user_id', $userId)
            ->where('listing_id', $listingId)
            ->countAllResults() > 0;
    }

    /**
     * Toggle favorite status (Add if not exists, delete if already exists)
     * Returns true if added, false if removed
     */
    public function toggleFavorite(int $userId, int $listingId): bool
    {
        $existing = $this->where('user_id', $userId)
            ->where('listing_id', $listingId)
            ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return false;
        }

        $this->insert([
            'user_id'    => $userId,
            'listing_id' => $listingId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    /**
     * Get list of favorited listing IDs for a user
     */
    public function getUserFavoriteListingIds(int $userId): array
    {
        $rows = $this->select('listing_id')
            ->where('user_id', $userId)
            ->findAll();

        return array_column($rows, 'listing_id');
    }

    /**
     * Get all favorite listings for a user with related details
     */
    public function getUserFavorites(int $userId): array
    {
        return $this->select('favorites.id as favorite_id, favorites.created_at as favorited_at,
            listings.id as id, listings.title, listings.description, listings.price_monthly, listings.price_six_monthly, listings.price_yearly, listings.publication_status,
            units.name as unit_name, units.size as unit_size, units.status as unit_status,
            properties.name as property_name, properties.type, properties.target_gender, properties.address,
            regions.name as region_name,
            property_photos.photo_url')
            ->join('listings', 'listings.id = favorites.listing_id')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->join('property_photos', 'property_photos.property_id = properties.id AND property_photos.is_primary = 1', 'left')
            ->where('favorites.user_id', $userId)
            ->orderBy('favorites.id', 'DESC')
            ->findAll();
    }
}
