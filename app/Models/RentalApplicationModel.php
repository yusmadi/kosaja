<?php

namespace App\Models;

use CodeIgniter\Model;

class RentalApplicationModel extends Model
{
    protected $table            = 'rental_applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tenant_id',
        'listing_id',
        'unit_id',
        'start_date',
        'duration_months',
        'payment_schedule',
        'marital_status',
        'rental_amount',
        'gateway_fee',
        'applicant_notes',
        'marriage_certificate_url',
        'is_family_verified',
        'status',
        'rejection_reason',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'tenant_id'        => 'required|is_not_unique[users.id]',
        'listing_id'       => 'required|is_not_unique[listings.id]',
        'unit_id'          => 'required|is_not_unique[units.id]',
        'start_date'       => 'required|valid_date',
        'duration_months'  => 'required|integer|greater_than_equal_to[1]',
        'payment_schedule' => 'required|in_list[full_upfront,monthly,3_monthly,6_monthly,yearly]',
        'rental_amount'    => 'required|numeric',
        'status'           => 'required|in_list[pending,approved,rejected,cancelled,expired]',
    ];

    /**
     * Get all applications for a specific tenant
     */
    public function getApplicationsByTenant(int $tenantId)
    {
        return $this->select('rental_applications.*, 
                listings.title as listing_title, 
                units.name as unit_name, 
                units.size as unit_size, 
                units.status as unit_status,
                properties.id as property_id, 
                properties.name as property_name, 
                properties.address as property_address, 
                properties.target_gender,
                properties.type as property_type,
                providers.name as provider_name,
                providers.whatsapp as provider_whatsapp,
                reservations.id as reservation_id,
                reservations.expires_at as reservation_expires_at,
                reservations.status as reservation_status,
                (SELECT photo_url FROM unit_photos WHERE unit_photos.unit_id = units.id ORDER BY is_primary DESC, id ASC LIMIT 1) as unit_photo,
                (SELECT photo_url FROM property_photos WHERE property_photos.property_id = properties.id ORDER BY is_primary DESC, id ASC LIMIT 1) as property_photo')
            ->join('listings', 'listings.id = rental_applications.listing_id')
            ->join('units', 'units.id = rental_applications.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('users as providers', 'providers.id = properties.provider_id')
            ->join('reservations', 'reservations.application_id = rental_applications.id', 'left')
            ->where('rental_applications.tenant_id', $tenantId)
            ->orderBy('rental_applications.id', 'DESC')
            ->findAll();
    }

    /**
     * Get applications for a provider's properties
     */
    public function getApplicationsByProvider(int $providerId, ?string $statusFilter = null)
    {
        $builder = $this->select('rental_applications.*, 
                tenants.name as tenant_name, 
                tenants.email as tenant_email, 
                tenants.whatsapp as tenant_whatsapp, 
                tenants.gender as tenant_gender,
                listings.title as listing_title, 
                units.name as unit_name, 
                units.size as unit_size, 
                units.status as unit_status,
                properties.id as property_id, 
                properties.name as property_name, 
                properties.target_gender,
                reservations.id as reservation_id,
                reservations.expires_at as reservation_expires_at,
                reservations.status as reservation_status')
            ->join('users as tenants', 'tenants.id = rental_applications.tenant_id')
            ->join('listings', 'listings.id = rental_applications.listing_id')
            ->join('units', 'units.id = rental_applications.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('reservations', 'reservations.application_id = rental_applications.id', 'left')
            ->where('properties.provider_id', $providerId);

        if ($statusFilter !== null && $statusFilter !== '' && $statusFilter !== 'all') {
            $builder->where('rental_applications.status', $statusFilter);
        }

        return $builder->orderBy('rental_applications.id', 'DESC')->findAll();
    }

    /**
     * Get single application with comprehensive details
     */
    public function getApplicationWithDetails(int $id)
    {
        return $this->select('rental_applications.*, 
                tenants.name as tenant_name, 
                tenants.email as tenant_email, 
                tenants.whatsapp as tenant_whatsapp, 
                tenants.gender as tenant_gender,
                listings.title as listing_title, 
                listings.price_monthly,
                listings.price_six_monthly,
                listings.price_yearly,
                units.name as unit_name, 
                units.size as unit_size, 
                units.floor as unit_floor,
                units.status as unit_status,
                properties.id as property_id, 
                properties.name as property_name, 
                properties.address as property_address,
                properties.rules as property_rules,
                properties.target_gender,
                properties.type as property_type,
                properties.provider_id,
                providers.name as provider_name,
                providers.whatsapp as provider_whatsapp,
                providers.email as provider_email,
                regions.name as region_name,
                reservations.id as reservation_id,
                reservations.expires_at as reservation_expires_at,
                reservations.status as reservation_status,
                (SELECT photo_url FROM unit_photos WHERE unit_photos.unit_id = units.id ORDER BY is_primary DESC, id ASC LIMIT 1) as unit_photo,
                (SELECT photo_url FROM property_photos WHERE property_photos.property_id = properties.id ORDER BY is_primary DESC, id ASC LIMIT 1) as property_photo')
            ->join('users as tenants', 'tenants.id = rental_applications.tenant_id')
            ->join('listings', 'listings.id = rental_applications.listing_id')
            ->join('units', 'units.id = rental_applications.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('users as providers', 'providers.id = properties.provider_id')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->join('reservations', 'reservations.application_id = rental_applications.id', 'left')
            ->where('rental_applications.id', $id)
            ->first();
    }

    /**
     * Check if a tenant already has an active or pending application on the given unit
     */
    public function hasActiveApplication(int $tenantId, int $unitId): bool
    {
        $existing = $this->where('tenant_id', $tenantId)
                         ->where('unit_id', $unitId)
                         ->whereIn('status', ['pending', 'approved'])
                         ->first();

        return ! empty($existing);
    }
}
