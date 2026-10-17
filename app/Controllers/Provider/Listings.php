<?php

namespace App\Controllers\Provider;

use App\Controllers\BaseController;
use App\Models\ListingModel;
use App\Models\UnitModel;
use App\Models\PropertyModel;
use App\Models\AuditLogModel;

class Listings extends BaseController
{
    protected $listingModel;
    protected $unitModel;
    protected $propertyModel;
    protected $auditModel;

    public function __construct()
    {
        $this->listingModel  = new ListingModel();
        $this->unitModel     = new UnitModel();
        $this->propertyModel = new PropertyModel();
        $this->auditModel    = new AuditLogModel();
    }

    /**
     * List all individual listings owned by provider
     */
    public function index()
    {
        $providerId = session()->get('user_id') ?? 3;
        $statusFilter = $this->request->getGet('status') ?? 'all';

        $allListings = $this->listingModel->getListingsByProvider($providerId);

        if ($statusFilter !== 'all') {
            $listings = array_values(array_filter($allListings, static function ($l) use ($statusFilter) {
                return $l['publication_status'] === $statusFilter;
            }));
        } else {
            $listings = $allListings;
        }

        // Available units that don't have listing yet
        $unlistedUnits = $this->unitModel->getUnitsWithoutListing($providerId);

        // Counts
        $counts = [
            'all'            => count($allListings),
            'draft'          => count(array_filter($allListings, fn($l) => $l['publication_status'] === 'draft')),
            'pending_review' => count(array_filter($allListings, fn($l) => $l['publication_status'] === 'pending_review')),
            'published'      => count(array_filter($allListings, fn($l) => $l['publication_status'] === 'published')),
            'inactive'       => count(array_filter($allListings, fn($l) => $l['publication_status'] === 'inactive')),
        ];

        return view('provider/listings/index', [
            'title'         => 'Manajemen Listing Iklan — KosAja',
            'role'          => 'provider',
            'activeMenu'    => 'listings',
            'listings'      => $listings,
            'unlistedUnits' => $unlistedUnits,
            'counts'        => $counts,
            'statusFilter'  => $statusFilter,
        ]);
    }

    /**
     * Show form to create individual listing
     */
    public function create(?int $selectedUnitId = null)
    {
        $providerId = session()->get('user_id') ?? 3;
        $unlistedUnits = $this->unitModel->getUnitsWithoutListing($providerId);

        if (empty($unlistedUnits)) {
            return redirect()->to('/provider/listings')->with('warning', 'Semua kamar/unit Anda sudah memiliki listing, atau Anda belum menambahkan unit ke properti Anda.');
        }

        $selectedUnit = null;
        if ($selectedUnitId) {
            foreach ($unlistedUnits as $u) {
                if ((int)$u['id'] === (int)$selectedUnitId) {
                    $selectedUnit = $u;
                    break;
                }
            }
        }

        return view('provider/listings/create', [
            'title'         => 'Buat Listing Iklan Baru — KosAja',
            'role'          => 'provider',
            'activeMenu'    => 'listings',
            'unlistedUnits' => $unlistedUnits,
            'selectedUnit'  => $selectedUnit,
        ]);
    }

    /**
     * Store newly created listing
     */
    public function store()
    {
        $providerId = session()->get('user_id') ?? 3;
        $unitId = (int) $this->request->getPost('unit_id');

        // Verify unit belongs to this provider and has no listing
        $unit = $this->unitModel->getUnitWithDetails($unitId);
        if (! $unit || (int)$unit['provider_id'] !== (int)$providerId) {
            return redirect()->back()->withInput()->with('error', 'Unit yang dipilih tidak valid.');
        }

        $existing = $this->listingModel->getListingByUnitId($unitId);
        if ($existing) {
            return redirect()->to('/provider/listings')->with('error', 'Unit ini sudah memiliki listing iklan.');
        }

        // Bersihkan separator angka jika dikirim terformat dari inputmask
        $postData = $this->request->getPost();
        foreach (['price_monthly', 'price_six_monthly', 'price_yearly'] as $field) {
            if (isset($postData[$field]) && $postData[$field] !== '') {
                $cleaned = preg_replace('/[^\d]/', '', (string)$postData[$field]);
                $postData[$field] = $cleaned !== '' ? $cleaned : null;
            } else {
                $postData[$field] = null;
            }
        }

        $rules = [
            'unit_id'       => 'required|numeric',
            'title'         => 'required|min_length[5]|max_length[255]',
            'description'   => 'required|min_length[10]',
            'price_monthly'     => 'permit_empty|numeric',
            'price_six_monthly' => 'permit_empty|numeric',
            'price_yearly'      => 'permit_empty|numeric',
        ];

        $messages = [
            'title' => [
                'required'   => 'Judul iklan listing wajib diisi.',
                'min_length' => 'Judul iklan minimal 5 karakter.',
            ],
            'description' => [
                'required'   => 'Deskripsi lengkap iklan wajib diisi.',
                'min_length' => 'Deskripsi minimal 10 karakter.',
            ],
        ];

        if (! $this->validateData($postData, $rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Action determines publication_status
        $action = $this->request->getPost('action'); // 'draft' or 'submit_review'
        $publicationStatus = ($action === 'submit_review') ? 'pending_review' : 'draft';

        $priceMonthly    = $postData['price_monthly'];
        $priceSixMonthly = $postData['price_six_monthly'];
        $priceYearly     = $postData['price_yearly'];

        $newListingId = $this->listingModel->insert([
            'unit_id'            => $unitId,
            'title'              => $this->request->getPost('title'),
            'description'        => $this->request->getPost('description'),
            'price_monthly'      => !empty($priceMonthly) ? (float) $priceMonthly : null,
            'price_six_monthly'  => !empty($priceSixMonthly) ? (float) $priceSixMonthly : null,
            'price_yearly'       => !empty($priceYearly) ? (float) $priceYearly : null,
            'publication_status' => $publicationStatus,
        ]);

        $providerId = session()->get('user_id') ?? 3;
        $title      = $this->request->getPost('title');
        $this->auditModel->record(
            $providerId,
            ($publicationStatus === 'pending_review' ? 'SUBMIT_LISTING_REVIEW' : 'CREATE_LISTING_DRAFT'),
            'listings',
            $newListingId,
            null,
            ['title' => $title, 'status' => $publicationStatus],
            "Mitra provider membuat iklan listing baru: '{$title}' (Status: " . ($publicationStatus === 'pending_review' ? 'Menunggu Review Admin' : 'Draf') . ")"
        );

        $statusMsg = ($publicationStatus === 'pending_review')
            ? 'Listing berhasil dibuat dan diajukan ke Admin untuk ditinjau (Pending Review)!'
            : 'Listing berhasil disimpan sebagai Draf.';

        return redirect()->to('/provider/listings')->with('success', $statusMsg);
    }

    /**
     * Show edit listing form
     */
    public function edit(int $listingId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $listing = $this->listingModel->getListingWithDetails($listingId, $providerId);

        if (! $listing) {
            return redirect()->to('/provider/listings')->with('error', 'Listing tidak ditemukan.');
        }

        return view('provider/listings/edit', [
            'title'      => 'Edit Listing — ' . esc($listing['title']),
            'role'       => 'provider',
            'activeMenu' => 'listings',
            'listing'    => $listing,
        ]);
    }

    /**
     * Update listing
     */
    public function update(int $listingId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $listing = $this->listingModel->getListingWithDetails($listingId, $providerId);

        if (! $listing) {
            return redirect()->to('/provider/listings')->with('error', 'Listing tidak ditemukan.');
        }

        // Bersihkan separator angka jika dikirim terformat dari inputmask
        $postData = $this->request->getPost();
        foreach (['price_monthly', 'price_six_monthly', 'price_yearly'] as $field) {
            if (isset($postData[$field]) && $postData[$field] !== '') {
                $cleaned = preg_replace('/[^\d]/', '', (string)$postData[$field]);
                $postData[$field] = $cleaned !== '' ? $cleaned : null;
            } else {
                $postData[$field] = null;
            }
        }

        $rules = [
            'title'             => 'required|min_length[5]|max_length[255]',
            'description'       => 'required|min_length[10]',
            'price_monthly'     => 'permit_empty|numeric',
            'price_six_monthly' => 'permit_empty|numeric',
            'price_yearly'      => 'permit_empty|numeric',
        ];

        if (! $this->validateData($postData, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $action = $this->request->getPost('action');
        $publicationStatus = $listing['publication_status'];

        if ($action === 'submit_review') {
            $publicationStatus = 'pending_review';
        }

        $priceMonthly    = $postData['price_monthly'];
        $priceSixMonthly = $postData['price_six_monthly'];
        $priceYearly     = $postData['price_yearly'];

        $updateData = [
            'title'              => $this->request->getPost('title'),
            'description'        => $this->request->getPost('description'),
            'price_monthly'      => !empty($priceMonthly) ? (float) $priceMonthly : null,
            'price_six_monthly'  => !empty($priceSixMonthly) ? (float) $priceSixMonthly : null,
            'price_yearly'       => !empty($priceYearly) ? (float) $priceYearly : null,
            'publication_status' => $publicationStatus,
        ];

        // Clear rejection reason if resubmitted
        if ($publicationStatus === 'pending_review') {
            $updateData['rejection_reason'] = null;
        }

        $this->listingModel->update($listingId, $updateData);

        return redirect()->to('/provider/listings')->with('success', 'Listing berhasil diperbarui!');
    }

    /**
     * Submit draft listing for admin review
     */
    public function submitReview(int $listingId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $listing = $this->listingModel->getListingWithDetails($listingId, $providerId);

        if (! $listing) {
            return redirect()->back()->with('error', 'Listing tidak ditemukan.');
        }

        $this->listingModel->update($listingId, [
            'publication_status' => 'pending_review',
            'rejection_reason'   => null,
        ]);

        $this->auditModel->record(
            $providerId,
            'SUBMIT_LISTING_REVIEW',
            'listings',
            $listingId,
            ['publication_status' => $listing['publication_status']],
            ['publication_status' => 'pending_review'],
            "Mitra provider mengajukan listing '{$listing['title']}' untuk ditinjau oleh Admin Daerah."
        );

        return redirect()->back()->with('success', 'Listing telah diajukan ke Admin untuk proses peninjauan!');
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleStatus(int $listingId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $listing = $this->listingModel->getListingWithDetails($listingId, $providerId);

        if (! $listing) {
            return redirect()->back()->with('error', 'Listing tidak ditemukan.');
        }

        $newStatus = ($listing['publication_status'] === 'published') ? 'inactive' : 'published';
        $this->listingModel->update($listingId, ['publication_status' => $newStatus]);

        $msg = ($newStatus === 'published') ? 'Listing berhasil diaktifkan kembali.' : 'Listing berhasil dinonaktifkan sementara.';
        return redirect()->back()->with('success', $msg);
    }

    /**
     * Delete draft listing
     */
    public function delete(int $listingId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $listing = $this->listingModel->getListingWithDetails($listingId, $providerId);

        if (! $listing) {
            return redirect()->back()->with('error', 'Listing tidak ditemukan.');
        }

        $this->listingModel->delete($listingId);

        return redirect()->to('/provider/listings')->with('success', 'Listing berhasil dihapus.');
    }
}
