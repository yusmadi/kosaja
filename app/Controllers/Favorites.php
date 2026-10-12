<?php

namespace App\Controllers;

use App\Models\FavoriteModel;
use App\Models\ListingModel;

class Favorites extends BaseController
{
    protected $favoriteModel;
    protected $listingModel;

    public function __construct()
    {
        $this->favoriteModel = new FavoriteModel();
        $this->listingModel  = new ListingModel();
    }

    /**
     * Display tenant's list of favorited properties
     */
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('warning', 'Silakan masuk terlebih dahulu untuk melihat daftar hunian favorit Anda.');
        }

        $userId    = (int)session()->get('user_id');
        $favorites = $this->favoriteModel->getUserFavorites($userId);

        return view('public/favorites', [
            'title'     => 'Hunian Favorit Saya — KosAja',
            'favorites' => $favorites,
        ]);
    }

    /**
     * Toggle favorite status (AJAX & Form POST compatible)
     */
    public function toggle(int $listingId)
    {
        $isAjax = $this->request->isAJAX();

        if (!session()->get('isLoggedIn')) {
            if ($isAjax) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success'       => false,
                    'require_login' => true,
                    'redirect'      => base_url('/login'),
                    'message'       => 'Silakan masuk untuk menyimpan hunian favorit.',
                ]);
            }
            return redirect()->to('/login')->with('warning', 'Silakan masuk untuk menyimpan hunian favorit.');
        }

        // Validate listing exists
        $listing = $this->listingModel->find($listingId);
        if (!$listing) {
            if ($isAjax) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Listing tidak ditemukan.',
                ]);
            }
            return redirect()->back()->with('error', 'Listing tidak ditemukan.');
        }

        $userId  = (int)session()->get('user_id');
        $isAdded = $this->favoriteModel->toggleFavorite($userId, $listingId);

        if ($isAjax) {
            return $this->response->setJSON([
                'success'      => true,
                'is_favorited' => $isAdded,
                'message'      => $isAdded ? 'Hunian berhasil disimpan ke daftar favorit!' : 'Hunian dihapus dari daftar favorit.',
            ]);
        }

        $msg = $isAdded ? 'Hunian berhasil disimpan ke favorit.' : 'Hunian dihapus dari favorit.';
        return redirect()->back()->with('success', $msg);
    }

    /**
     * Explicit remove from favorites
     */
    public function remove(int $listingId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = (int)session()->get('user_id');
        $this->favoriteModel->where('user_id', $userId)
            ->where('listing_id', $listingId)
            ->delete();

        return redirect()->back()->with('success', 'Hunian berhasil dihapus dari daftar favorit.');
    }
}
