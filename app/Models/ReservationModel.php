<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservationModel extends Model
{
    protected $table            = 'reservations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'application_id',
        'expires_at',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'application_id' => 'required|is_not_unique[rental_applications.id]',
        'expires_at'     => 'required',
        'status'         => 'required|in_list[active,converted,expired,cancelled]',
    ];

    /**
     * Get reservation by application ID
     */
    public function getReservationByApplication(int $applicationId)
    {
        return $this->where('application_id', $applicationId)->first();
    }

    /**
     * Get reservation with complete application, unit, property, and user details
     */
    public function getReservationWithDetails(int $id)
    {
        return $this->select('reservations.*, 
                rental_applications.tenant_id,
                rental_applications.listing_id,
                rental_applications.unit_id,
                rental_applications.start_date,
                rental_applications.duration_months,
                rental_applications.payment_schedule,
                rental_applications.rental_amount,
                rental_applications.gateway_fee,
                rental_applications.status as application_status,
                rental_applications.marital_status,
                rental_applications.applicant_notes,
                tenants.name as tenant_name,
                tenants.email as tenant_email,
                tenants.whatsapp as tenant_whatsapp,
                tenants.gender as tenant_gender,
                listings.title as listing_title,
                listings.price_monthly,
                listings.price_yearly,
                units.name as unit_name,
                units.size as unit_size,
                units.floor as unit_floor,
                units.status as unit_status,
                properties.id as property_id,
                properties.name as property_name,
                properties.address as property_address,
                properties.target_gender,
                properties.type as property_type,
                properties.rules as property_rules,
                providers.id as provider_id,
                providers.name as provider_name,
                providers.whatsapp as provider_whatsapp,
                providers.email as provider_email,
                regions.name as region_name,
                (SELECT photo_url FROM unit_photos WHERE unit_photos.unit_id = units.id ORDER BY is_primary DESC, id ASC LIMIT 1) as unit_photo,
                (SELECT photo_url FROM property_photos WHERE property_photos.property_id = properties.id ORDER BY is_primary DESC, id ASC LIMIT 1) as property_photo')
            ->join('rental_applications', 'rental_applications.id = reservations.application_id')
            ->join('users as tenants', 'tenants.id = rental_applications.tenant_id')
            ->join('listings', 'listings.id = rental_applications.listing_id')
            ->join('units', 'units.id = rental_applications.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('users as providers', 'providers.id = properties.provider_id')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->where('reservations.id', $id)
            ->first();
    }

    /**
     * Get reservations for a provider's units
     */
    public function getReservationsByProvider(int $providerId)
    {
        return $this->select('reservations.*, 
                rental_applications.rental_amount,
                rental_applications.start_date,
                rental_applications.duration_months,
                tenants.name as tenant_name,
                tenants.whatsapp as tenant_whatsapp,
                units.name as unit_name,
                properties.name as property_name')
            ->join('rental_applications', 'rental_applications.id = reservations.application_id')
            ->join('users as tenants', 'tenants.id = rental_applications.tenant_id')
            ->join('units', 'units.id = rental_applications.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->where('properties.provider_id', $providerId)
            ->orderBy('reservations.id', 'DESC')
            ->findAll();
    }

    /**
     * Check and expire active reservations that passed expires_at.
     * Transitions status to 'expired', releases unit to 'available',
     * updates rental_application status to 'expired', and writes audit log.
     *
     * @return int Count of expired reservations processed
     */
    public function checkAndExpireReservations(): int
    {
        $now = date('Y-m-d H:i:s');
        $expiredList = $this->select('reservations.*, rental_applications.unit_id, rental_applications.id as app_id')
                            ->join('rental_applications', 'rental_applications.id = reservations.application_id')
                            ->where('reservations.status', 'active')
                            ->where('reservations.expires_at <=', $now)
                            ->findAll();

        if (empty($expiredList)) {
            return 0;
        }

        $db = \Config\Database::connect();
        $auditModel = new \App\Models\AuditLogModel();

        foreach ($expiredList as $res) {
            $db->transStart();

            // 1. Mark reservation expired
            $this->update($res['id'], [
                'status' => 'expired',
            ]);

            // 2. Mark application expired
            $db->table('rental_applications')->where('id', $res['app_id'])->update([
                'status'     => 'expired',
                'updated_at' => $now,
            ]);

            // 3. Release unit status back to available
            $db->table('units')->where('id', $res['unit_id'])->update([
                'status'     => 'available',
                'updated_at' => $now,
            ]);

            $db->transComplete();

            // 4. Log audit event
            $auditModel->record(
                null,
                'EXPIRE_RESERVATION',
                'reservations',
                $res['id'],
                ['status' => 'active'],
                ['status' => 'expired'],
                'Batas waktu pembayaran (Expiration Window) telah terlewati tanpa konfirmasi bayar.'
            );
        }

        return count($expiredList);
    }
}
