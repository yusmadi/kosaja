<?php

namespace App\Controllers\Provider;

use App\Controllers\BaseController;
use App\Models\PropertyModel;
use App\Models\PropertyFacilityModel;
use App\Models\PropertyPhotoModel;
use App\Models\RegionModel;
use App\Models\UnitModel;
use App\Libraries\StorageService;

class Properties extends BaseController
{
    protected $propertyModel;
    protected $facilityModel;
    protected $photoModel;
    protected $regionModel;
    protected $unitModel;
    protected $storageService;

    public function __construct()
    {
        $this->propertyModel  = new PropertyModel();
        $this->facilityModel  = new PropertyFacilityModel();
        $this->photoModel     = new PropertyPhotoModel();
        $this->regionModel    = new RegionModel();
        $this->unitModel      = new UnitModel();
        $this->storageService = new StorageService();
    }

    /**
     * List all properties owned by provider
     */
    public function index()
    {
        $providerId = session()->get('user_id') ?? 3;
        $properties = $this->propertyModel->getPropertiesByProvider($providerId);

        return view('provider/properties/index', [
            'title'      => 'Manajemen Properti — KosAja',
            'role'       => 'provider',
            'activeMenu' => 'properties',
            'properties' => $properties,
        ]);
    }

    /**
     * Show form to create new property
     */
    public function create()
    {
        $regions = $this->regionModel->getHierarchy();

        $facilityPresets = [
            'WiFi Berkecepatan Tinggi',
            'Dapur Bersama + Kulkas',
            'Parkir Motor Luas & Aman',
            'Parkir Mobil Pribadi',
            'Keamanan CCTV 24 Jam',
            'Mushola Bersama',
            'Ruang Tamu Terbuka',
            'Penjaga Kos 24 Jam',
            'Mesin Cuci Bersama',
            'Listrik Token Mandiri',
            'Air PDAM & Sumur Lancar',
        ];

        return view('provider/properties/create', [
            'title'           => 'Tambah Properti Baru — KosAja',
            'role'            => 'provider',
            'activeMenu'      => 'properties',
            'regions'         => $regions,
            'facilityPresets' => $facilityPresets,
        ]);
    }

    /**
     * Store newly created property
     */
    public function store()
    {
        $providerId = session()->get('user_id') ?? 3;

        $rules = [
            'name'          => 'required|min_length[3]|max_length[255]',
            'type'          => 'required|in_list[kos,kontrakan,rumah_sewa,lainnya]',
            'region_id'     => 'required|is_not_unique[regions.id]',
            'target_gender' => 'required|in_list[putra,putri,campur_keluarga]',
            'address'       => 'required|min_length[5]',
            'description'   => 'permit_empty',
            'rules'         => 'permit_empty',
            'latitude'      => 'permit_empty|numeric',
            'longitude'     => 'permit_empty|numeric',
        ];

        $messages = [
            'name' => [
                'required'   => 'Nama bangunan properti wajib diisi.',
                'min_length' => 'Nama properti minimal 3 karakter.',
            ],
            'region_id' => [
                'required'      => 'Wilayah operasional wajib dipilih.',
                'is_not_unique' => 'Wilayah tidak valid.',
            ],
            'target_gender' => [
                'required' => 'Ketentuan Syariat Hunian (Gender) wajib dipilih.',
            ],
            'address' => [
                'required' => 'Alamat lengkap properti wajib diisi.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $propertyData = [
            'provider_id'   => $providerId,
            'region_id'     => (int) $this->request->getPost('region_id'),
            'name'          => $this->request->getPost('name'),
            'type'          => $this->request->getPost('type'),
            'target_gender' => $this->request->getPost('target_gender'),
            'address'       => $this->request->getPost('address'),
            'description'   => $this->request->getPost('description'),
            'rules'         => $this->request->getPost('rules'),
            'latitude'      => $this->request->getPost('latitude') ?: null,
            'longitude'     => $this->request->getPost('longitude') ?: null,
        ];

        $propertyId = $this->propertyModel->insert($propertyData);

        if (! $propertyId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data properti. Silakan coba lagi.');
        }

        // Process Facilities
        $facilities = $this->request->getPost('facilities') ?? [];
        $customFacilities = $this->request->getPost('custom_facilities');
        if (!empty($customFacilities)) {
            $extras = explode(',', $customFacilities);
            foreach ($extras as $ex) {
                if (!empty(trim($ex))) {
                    $facilities[] = trim($ex);
                }
            }
        }
        if (!empty($facilities)) {
            $this->facilityModel->syncFacilities($propertyId, $facilities);
        }

        // Process Property Photos upload
        $photos = $this->request->getFileMultiple('photos');
        if ($photos) {
            $isFirst = true;
            foreach ($photos as $photo) {
                if ($photo->isValid() && ! $photo->hasMoved()) {
                    $uploadResult = $this->storageService->uploadImage($photo, 'properties');
                    if ($uploadResult['success']) {
                        $this->photoModel->insert([
                            'property_id' => $propertyId,
                            'photo_url'   => $uploadResult['url'],
                            'caption'     => $this->request->getPost('name'),
                            'is_primary'  => $isFirst ? 1 : 0,
                        ]);
                        $isFirst = false;
                    }
                }
            }
        }

        return redirect()->to('/provider/properties/' . $propertyId)->with('success', 'Properti berhasil dibuat! Selanjutnya, silakan tambahkan unit/kamar fisik di dalam properti ini.');
    }

    /**
     * Show detail of a property
     */
    public function show(int $propertyId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $property = $this->propertyModel->getPropertyWithDetails($propertyId, $providerId);

        if (! $property) {
            return redirect()->to('/provider/properties')->with('error', 'Properti tidak ditemukan atau Anda tidak memiliki akses.');
        }

        return view('provider/properties/show', [
            'title'      => esc($property['name']) . ' — Detail Properti',
            'role'       => 'provider',
            'activeMenu' => 'properties',
            'property'   => $property,
        ]);
    }

    /**
     * Show edit form
     */
    public function edit(int $propertyId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $property = $this->propertyModel->getPropertyWithDetails($propertyId, $providerId);

        if (! $property) {
            return redirect()->to('/provider/properties')->with('error', 'Properti tidak ditemukan.');
        }

        $regions = $this->regionModel->getHierarchy();
        $facilityPresets = [
            'WiFi Berkecepatan Tinggi',
            'Dapur Bersama + Kulkas',
            'Parkir Motor Luas & Aman',
            'Parkir Mobil Pribadi',
            'Keamanan CCTV 24 Jam',
            'Mushola Bersama',
            'Ruang Tamu Terbuka',
            'Penjaga Kos 24 Jam',
            'Mesin Cuci Bersama',
            'Listrik Token Mandiri',
            'Air PDAM & Sumur Lancar',
        ];

        $currentFacilityNames = array_column($property['facilities'], 'facility_name');

        return view('provider/properties/edit', [
            'title'                => 'Edit Properti — ' . esc($property['name']),
            'role'                 => 'provider',
            'activeMenu'           => 'properties',
            'property'             => $property,
            'regions'              => $regions,
            'facilityPresets'      => $facilityPresets,
            'currentFacilityNames' => $currentFacilityNames,
        ]);
    }

    /**
     * Update existing property
     */
    public function update(int $propertyId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $property = $this->propertyModel->where('id', $propertyId)->where('provider_id', $providerId)->first();

        if (! $property) {
            return redirect()->to('/provider/properties')->with('error', 'Properti tidak ditemukan.');
        }

        $rules = [
            'name'          => 'required|min_length[3]|max_length[255]',
            'type'          => 'required|in_list[kos,kontrakan,rumah_sewa,lainnya]',
            'region_id'     => 'required|is_not_unique[regions.id]',
            'target_gender' => 'required|in_list[putra,putri,campur_keluarga]',
            'address'       => 'required|min_length[5]',
            'description'   => 'permit_empty',
            'rules'         => 'permit_empty',
            'latitude'      => 'permit_empty|numeric',
            'longitude'     => 'permit_empty|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'region_id'     => (int) $this->request->getPost('region_id'),
            'name'          => $this->request->getPost('name'),
            'type'          => $this->request->getPost('type'),
            'target_gender' => $this->request->getPost('target_gender'),
            'address'       => $this->request->getPost('address'),
            'description'   => $this->request->getPost('description'),
            'rules'         => $this->request->getPost('rules'),
            'latitude'      => $this->request->getPost('latitude') ?: null,
            'longitude'     => $this->request->getPost('longitude') ?: null,
        ];

        $this->propertyModel->update($propertyId, $updateData);

        // Update facilities
        $facilities = $this->request->getPost('facilities') ?? [];
        $customFacilities = $this->request->getPost('custom_facilities');
        if (!empty($customFacilities)) {
            $extras = explode(',', $customFacilities);
            foreach ($extras as $ex) {
                if (!empty(trim($ex))) {
                    $facilities[] = trim($ex);
                }
            }
        }
        $this->facilityModel->syncFacilities($propertyId, $facilities);

        // Upload additional photos if any
        $photos = $this->request->getFileMultiple('photos');
        if ($photos) {
            $existingPhotoCount = $this->photoModel->where('property_id', $propertyId)->countAllResults();
            foreach ($photos as $photo) {
                if ($photo->isValid() && ! $photo->hasMoved()) {
                    $uploadResult = $this->storageService->uploadImage($photo, 'properties');
                    if ($uploadResult['success']) {
                        $this->photoModel->insert([
                            'property_id' => $propertyId,
                            'photo_url'   => $uploadResult['url'],
                            'caption'     => $this->request->getPost('name'),
                            'is_primary'  => ($existingPhotoCount === 0) ? 1 : 0,
                        ]);
                        $existingPhotoCount++;
                    }
                }
            }
        }

        return redirect()->to('/provider/properties/' . $propertyId)->with('success', 'Perubahan properti berhasil disimpan!');
    }

    /**
     * Delete property
     */
    public function delete(int $propertyId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $property = $this->propertyModel->where('id', $propertyId)->where('provider_id', $providerId)->first();

        if (! $property) {
            return redirect()->to('/provider/properties')->with('error', 'Properti tidak ditemukan.');
        }

        // Delete photos files
        $photos = $this->photoModel->getPhotosByProperty($propertyId);
        foreach ($photos as $photo) {
            $this->storageService->deleteImage($photo['photo_url']);
        }

        $this->propertyModel->delete($propertyId);

        return redirect()->to('/provider/properties')->with('success', 'Properti berhasil dihapus.');
    }

    /**
     * Delete a single photo
     */
    public function deletePhoto(int $photoId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $photo = $this->photoModel->select('property_photos.*, properties.provider_id')
                                  ->join('properties', 'properties.id = property_photos.property_id')
                                  ->where('property_photos.id', $photoId)
                                  ->where('properties.provider_id', $providerId)
                                  ->first();

        if (! $photo) {
            return redirect()->back()->with('error', 'Foto tidak ditemukan.');
        }

        $propertyId = $photo['property_id'];
        $this->storageService->deleteImage($photo['photo_url']);
        $this->photoModel->delete($photoId);

        // If primary photo was deleted, set next photo as primary
        if ($photo['is_primary']) {
            $nextPhoto = $this->photoModel->where('property_id', $propertyId)->first();
            if ($nextPhoto) {
                $this->photoModel->update($nextPhoto['id'], ['is_primary' => 1]);
            }
        }

        return redirect()->back()->with('success', 'Foto berhasil dihapus.');
    }

    /**
     * Set a photo as primary
     */
    public function setPrimaryPhoto(int $propertyId, int $photoId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $property = $this->propertyModel->where('id', $propertyId)->where('provider_id', $providerId)->first();

        if (! $property) {
            return redirect()->back()->with('error', 'Properti tidak ditemukan.');
        }

        $this->photoModel->setPrimary($propertyId, $photoId);

        return redirect()->back()->with('success', 'Foto utama berhasil diperbarui.');
    }
}
