<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractModel extends Model
{
    protected $table            = 'contracts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'application_id',
        'tenant_id',
        'provider_id',
        'property_id',
        'unit_id',
        'start_date',
        'end_date',
        'agreed_price',
        'payment_schedule',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'application_id'   => 'required|is_not_unique[rental_applications.id]',
        'tenant_id'        => 'required|is_not_unique[users.id]',
        'provider_id'      => 'required|is_not_unique[users.id]',
        'property_id'      => 'required|is_not_unique[properties.id]',
        'unit_id'          => 'required|is_not_unique[units.id]',
        'start_date'       => 'required|valid_date',
        'end_date'         => 'required|valid_date',
        'agreed_price'     => 'required|numeric',
        'payment_schedule' => 'required|in_list[full_upfront,monthly,3_monthly,6_monthly,yearly]',
        'status'           => 'required|in_list[active,completed,terminated]',
    ];

    /**
     * Get all contracts for a specific tenant
     */
    public function getContractsByTenant(int $tenantId)
    {
        return $this->select('contracts.*, 
                properties.name as property_name, 
                properties.address as property_address,
                properties.type as property_type,
                units.name as unit_name, 
                units.size as unit_size,
                providers.name as provider_name,
                providers.whatsapp as provider_whatsapp,
                providers.email as provider_email,
                (SELECT photo_url FROM unit_photos WHERE unit_photos.unit_id = units.id ORDER BY is_primary DESC, id ASC LIMIT 1) as unit_photo,
                (SELECT photo_url FROM property_photos WHERE property_photos.property_id = properties.id ORDER BY is_primary DESC, id ASC LIMIT 1) as property_photo')
            ->join('properties', 'properties.id = contracts.property_id')
            ->join('units', 'units.id = contracts.unit_id')
            ->join('users as providers', 'providers.id = contracts.provider_id')
            ->where('contracts.tenant_id', $tenantId)
            ->orderBy('contracts.id', 'DESC')
            ->findAll();
    }

    /**
     * Get all contracts for a specific provider
     */
    public function getContractsByProvider(int $providerId)
    {
        return $this->select('contracts.*, 
                properties.name as property_name, 
                units.name as unit_name, 
                tenants.name as tenant_name,
                tenants.email as tenant_email,
                tenants.whatsapp as tenant_whatsapp,
                tenants.gender as tenant_gender')
            ->join('properties', 'properties.id = contracts.property_id')
            ->join('units', 'units.id = contracts.unit_id')
            ->join('users as tenants', 'tenants.id = contracts.tenant_id')
            ->where('contracts.provider_id', $providerId)
            ->orderBy('contracts.id', 'DESC')
            ->findAll();
    }

    /**
     * Get single contract with comprehensive details
     */
    public function getContractWithDetails(int $id)
    {
        return $this->select('contracts.*, 
                properties.name as property_name, 
                properties.address as property_address,
                properties.rules as property_rules,
                properties.type as property_type,
                properties.target_gender,
                units.name as unit_name, 
                units.size as unit_size,
                units.floor as unit_floor,
                tenants.name as tenant_name,
                tenants.email as tenant_email,
                tenants.whatsapp as tenant_whatsapp,
                tenants.gender as tenant_gender,
                providers.name as provider_name,
                providers.email as provider_email,
                providers.whatsapp as provider_whatsapp,
                regions.name as region_name,
                (SELECT photo_url FROM unit_photos WHERE unit_photos.unit_id = units.id ORDER BY is_primary DESC, id ASC LIMIT 1) as unit_photo,
                (SELECT photo_url FROM property_photos WHERE property_photos.property_id = properties.id ORDER BY is_primary DESC, id ASC LIMIT 1) as property_photo')
            ->join('properties', 'properties.id = contracts.property_id')
            ->join('units', 'units.id = contracts.unit_id')
            ->join('users as tenants', 'tenants.id = contracts.tenant_id')
            ->join('users as providers', 'providers.id = contracts.provider_id')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->where('contracts.id', $id)
            ->first();
    }
}
