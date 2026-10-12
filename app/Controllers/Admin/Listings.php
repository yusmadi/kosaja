<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ListingModel;
use App\Models\AuditLogModel;

class Listings extends BaseController
{
    protected $listingModel;
    protected $auditLogModel;

    public function __construct()
    {
        $this->listingModel = new ListingModel();
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * Display moderation list of listings with filter by publication status
     */
    public function index()
    {
        $statusFilter = $this->request->getGet('status') ?? 'all';
        $regionFilter = $this->request->getGet('region') ?? '';
        $keyword      = $this->request->getGet('q') ?? '';

        $db = \Config\Database::connect();
        $builder = $db->table('listings')
            ->select('listings.*, 
                units.name as unit_name, units.size as unit_size, units.floor as unit_floor, units.status as unit_status,
                properties.id as property_id, properties.name as property_name, properties.type, properties.target_gender, properties.address,
                regions.name as region_name,
                users.id as provider_id, users.name as provider_name, users.email as provider_email,
                provider_profiles.verification_status as provider_kyc,
                property_photos.photo_url')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('users', 'users.id = properties.provider_id')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id', 'left')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->join('property_photos', 'property_photos.property_id = properties.id AND property_photos.is_primary = 1', 'left');

        $scopeService = new \App\Libraries\AdminScopeService();
        $isSuperAdmin = $scopeService->isSuperAdmin();
        $scopedRegionIds = $scopeService->getScopedRegionIds();

        if ($statusFilter !== 'all' && in_array($statusFilter, ['draft', 'pending_review', 'published', 'inactive'])) {
            $builder->where('listings.publication_status', $statusFilter);
        }

        if (! empty($regionFilter)) {
            $builder->where('properties.region_id', $regionFilter);
        } elseif (! $isSuperAdmin) {
            $builder->whereIn('properties.region_id', ! empty($scopedRegionIds) ? $scopedRegionIds : [0]);
        }

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('listings.title', $keyword)
                ->orLike('properties.name', $keyword)
                ->orLike('users.name', $keyword)
                ->groupEnd();
        }

        $listings = $builder->orderBy("CASE WHEN listings.publication_status = 'pending_review' THEN 0 ELSE 1 END", 'ASC')
            ->orderBy('listings.updated_at', 'DESC')
            ->get()
            ->getResultArray();

        // Calculate counts for badges
        $countsBuilder = function ($status = null) use ($db, $isSuperAdmin, $scopedRegionIds) {
            $b = $db->table('listings')
                ->join('units', 'units.id = listings.unit_id')
                ->join('properties', 'properties.id = units.property_id');
            if (! $isSuperAdmin) {
                $b->whereIn('properties.region_id', ! empty($scopedRegionIds) ? $scopedRegionIds : [0]);
            }
            if ($status) {
                $b->where('listings.publication_status', $status);
            }
            return $b->countAllResults();
        };

        $counts = [
            'all'            => $countsBuilder(),
            'pending_review' => $countsBuilder('pending_review'),
            'published'      => $countsBuilder('published'),
            'draft'          => $countsBuilder('draft'),
            'inactive'       => $countsBuilder('inactive'),
        ];

        $regions = $db->table('regions')->orderBy('name', 'ASC')->get()->getResultArray();

        return view('admin/listings/index', [
            'title'        => $isSuperAdmin ? 'Master Listing Iklan Global (SA-05) — Super Admin' : 'Moderasi Listing Daerah (ADM-04) — KosAja',
            'role'         => session()->get('role') ?? ($isSuperAdmin ? 'super_admin' : 'admin_daerah'),
            'activeMenu'   => 'moderation',
            'listings'     => $listings,
            'counts'       => $counts,
            'statusFilter' => $statusFilter,
            'regionFilter' => $regionFilter,
            'keyword'      => $keyword,
            'regions'      => $regions,
            'isSuperAdmin' => $isSuperAdmin,
            'scopedLabel'  => $scopeService->getScopedRegionLabel(),
        ]);
    }

    /**
     * Show detailed preview of a listing for review & moderation
     */
    public function show(int $id)
    {
        $db = \Config\Database::connect();

        $listing = $db->table('listings')
            ->select('listings.*, 
                units.id as unit_id, units.name as unit_name, units.size as unit_size, units.floor as unit_floor, units.status as unit_status,
                properties.id as property_id, properties.name as property_name, properties.type, properties.target_gender, properties.description as property_description, properties.address, properties.rules, properties.latitude, properties.longitude,
                regions.name as region_name,
                users.id as provider_id, users.name as provider_name, users.email as provider_email, users.whatsapp as provider_whatsapp,
                provider_profiles.verification_status as provider_kyc, provider_profiles.bank_name, provider_profiles.bank_account_number')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('users', 'users.id = properties.provider_id')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id', 'left')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->where('listings.id', $id)
            ->get()
            ->getRowArray();

        if (!$listing) {
            return redirect()->to('/admin/listings')->with('error', 'Listing iklan tidak ditemukan.');
        }

        // Get Property photos and facilities
        $propertyPhotos = $db->table('property_photos')
            ->where('property_id', $listing['property_id'])
            ->orderBy('is_primary', 'DESC')
            ->get()
            ->getResultArray();

        $facilities = $db->table('property_facilities')
            ->where('property_id', $listing['property_id'])
            ->get()
            ->getResultArray();

        // Get Unit photos
        $unitPhotos = $db->table('unit_photos')
            ->where('unit_id', $listing['unit_id'])
            ->orderBy('is_primary', 'DESC')
            ->get()
            ->getResultArray();

        // Get Audit history for this listing
        $auditLogs = $db->table('audit_logs')
            ->select('audit_logs.*, users.name as actor_name')
            ->join('users', 'users.id = audit_logs.actor_id', 'left')
            ->where('object_type', 'listing')
            ->where('object_id', $id)
            ->orderBy('audit_logs.id', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/listings/show', [
            'title'          => 'Review Moderasi: ' . esc($listing['title']),
            'role'           => 'admin',
            'activeMenu'     => 'moderation',
            'listing'        => $listing,
            'propertyPhotos' => $propertyPhotos,
            'unitPhotos'     => $unitPhotos,
            'facilities'     => $facilities,
            'auditLogs'      => $auditLogs,
        ]);
    }

    /**
     * Approve listing for publication
     */
    public function approve(int $id)
    {
        $listing = $this->listingModel->find($id);
        if (!$listing) {
            return redirect()->to('/admin/listings')->with('error', 'Listing tidak ditemukan.');
        }

        $previousState = $listing;

        $this->listingModel->update($id, [
            'publication_status' => 'published',
            'rejection_reason'   => null,
        ]);

        $newState = $this->listingModel->find($id);

        $actorId = session()->get('user_id');
        $this->auditLogModel->record(
            $actorId,
            'APPROVE_LISTING',
            'listing',
            $id,
            $previousState,
            $newState,
            'Listing disetujui oleh admin daerah / kurator dan siap tayang publik.'
        );

        return redirect()->to('/admin/listings/' . $id)->with('success', 'Listing iklan BERHASIL DISETUJUI dan sekarang tayang aktif di marketplace!');
    }

    /**
     * Reject listing with mandatory reason
     */
    public function reject(int $id)
    {
        $listing = $this->listingModel->find($id);
        if (!$listing) {
            return redirect()->to('/admin/listings')->with('error', 'Listing tidak ditemukan.');
        }

        $reason = $this->request->getPost('reason');
        if (empty(trim($reason ?? ''))) {
            return redirect()->back()->with('error', 'Alasan penolakan listing wajib diisi.');
        }

        $previousState = $listing;

        // Status returned to draft with reason so provider can correct and resubmit
        $this->listingModel->update($id, [
            'publication_status' => 'draft',
            'rejection_reason'   => trim($reason),
        ]);

        $newState = $this->listingModel->find($id);

        $actorId = session()->get('user_id');
        $this->auditLogModel->record(
            $actorId,
            'REJECT_LISTING',
            'listing',
            $id,
            $previousState,
            $newState,
            trim($reason)
        );

        return redirect()->to('/admin/listings/' . $id)->with('warning', 'Listing DITOLAK dan dikembalikan ke status draf mitra provider.');
    }

    /**
     * Suspend / Inactivate or Reactivate listing
     */
    public function toggle(int $id)
    {
        $listing = $this->listingModel->find($id);
        if (!$listing) {
            return redirect()->to('/admin/listings')->with('error', 'Listing tidak ditemukan.');
        }

        $targetStatus = ($listing['publication_status'] === 'published') ? 'inactive' : 'published';
        $reason = $this->request->getPost('reason') ?? ($targetStatus === 'inactive' ? 'Dinonaktifkan oleh administrator' : 'Diaktifkan kembali oleh administrator');

        $previousState = $listing;

        $this->listingModel->update($id, [
            'publication_status' => $targetStatus,
            'rejection_reason'   => ($targetStatus === 'inactive' ? $reason : null),
        ]);

        $newState = $this->listingModel->find($id);

        $actorId = session()->get('user_id');
        $this->auditLogModel->record(
            $actorId,
            ($targetStatus === 'inactive' ? 'SUSPEND_LISTING' : 'ACTIVATE_LISTING'),
            'listing',
            $id,
            $previousState,
            $newState,
            $reason
        );

        $msg = ($targetStatus === 'inactive') ? 'Listing berhasil dinonaktifkan (Status: Inactive).' : 'Listing berhasil diaktifkan kembali (Status: Published).';
        return redirect()->back()->with('success', $msg);
    }
}
