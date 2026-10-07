<?php

namespace App\Controllers\Provider;

use App\Controllers\BaseController;
use App\Models\UnitModel;
use App\Models\UnitPhotoModel;
use App\Models\PropertyModel;
use App\Libraries\StorageService;

class Units extends BaseController
{
    protected $unitModel;
    protected $unitPhotoModel;
    protected $propertyModel;
    protected $storageService;

    public function __construct()
    {
        $this->unitModel      = new UnitModel();
        $this->unitPhotoModel = new UnitPhotoModel();
        $this->propertyModel  = new PropertyModel();
        $this->storageService = new StorageService();
    }

    /**
     * List all units belonging to provider
     */
    public function index()
    {
        $providerId = session()->get('user_id') ?? 3;
        $units = $this->unitModel->getUnitsByProvider($providerId);
        $properties = $this->propertyModel->where('provider_id', $providerId)->findAll();

        return view('provider/units/index', [
            'title'      => 'Manajemen Unit & Kamar — KosAja',
            'role'       => 'provider',
            'activeMenu' => 'units',
            'units'      => $units,
            'properties' => $properties,
        ]);
    }

    /**
     * Show form to add a unit to a specific property
     */
    public function create(int $propertyId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $property = $this->propertyModel->where('id', $propertyId)->where('provider_id', $providerId)->first();

        if (! $property) {
            return redirect()->to('/provider/properties')->with('error', 'Properti tidak ditemukan.');
        }

        return view('provider/units/create', [
            'title'      => 'Tambah Unit Baru — ' . esc($property['name']),
            'role'       => 'provider',
            'activeMenu' => 'properties',
            'property'   => $property,
        ]);
    }

    /**
     * Store new unit
     */
    public function store(int $propertyId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $property = $this->propertyModel->where('id', $propertyId)->where('provider_id', $providerId)->first();

        if (! $property) {
            return redirect()->to('/provider/properties')->with('error', 'Properti tidak ditemukan.');
        }

        $rules = [
            'name'   => 'required|min_length[1]|max_length[100]',
            'size'   => 'permit_empty|max_length[50]',
            'floor'  => 'permit_empty|max_length[50]',
            'status' => 'required|in_list[available,reserved,occupied,maintenance,inactive]',
        ];

        $messages = [
            'name' => [
                'required' => 'Nama atau nomor kamar/unit wajib diisi.',
            ],
            'status' => [
                'required' => 'Status kamar wajib dipilih.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $unitId = $this->unitModel->insert([
            'property_id' => $propertyId,
            'name'        => $this->request->getPost('name'),
            'size'        => $this->request->getPost('size'),
            'floor'       => $this->request->getPost('floor'),
            'status'      => $this->request->getPost('status'),
        ]);

        if (! $unitId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan unit.');
        }

        // Upload Unit Photos
        $photos = $this->request->getFileMultiple('photos');
        if ($photos) {
            $isFirst = true;
            foreach ($photos as $photo) {
                if ($photo->isValid() && ! $photo->hasMoved()) {
                    $uploadResult = $this->storageService->uploadImage($photo, 'units');
                    if ($uploadResult['success']) {
                        $this->unitPhotoModel->insert([
                            'unit_id'    => $unitId,
                            'photo_url'  => $uploadResult['url'],
                            'caption'    => $this->request->getPost('name'),
                            'is_primary' => $isFirst ? 1 : 0,
                        ]);
                        $isFirst = false;
                    }
                }
            }
        }

        return redirect()->to('/provider/properties/' . $propertyId)->with('success', 'Unit "' . esc($this->request->getPost('name')) . '" berhasil ditambahkan! Anda dapat membuat listing iklan untuk unit ini.');
    }

    /**
     * Show edit unit form
     */
    public function edit(int $unitId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $unit = $this->unitModel->getUnitWithDetails($unitId);

        if (! $unit || (int)$unit['provider_id'] !== (int)$providerId) {
            return redirect()->to('/provider/properties')->with('error', 'Unit tidak ditemukan.');
        }

        return view('provider/units/edit', [
            'title'      => 'Edit Unit — ' . esc($unit['name']),
            'role'       => 'provider',
            'activeMenu' => 'properties',
            'unit'       => $unit,
        ]);
    }

    /**
     * Update unit
     */
    public function update(int $unitId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $unit = $this->unitModel->getUnitWithDetails($unitId);

        if (! $unit || (int)$unit['provider_id'] !== (int)$providerId) {
            return redirect()->to('/provider/properties')->with('error', 'Unit tidak ditemukan.');
        }

        $rules = [
            'name'   => 'required|min_length[1]|max_length[100]',
            'size'   => 'permit_empty|max_length[50]',
            'floor'  => 'permit_empty|max_length[50]',
            'status' => 'required|in_list[available,reserved,occupied,maintenance,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->unitModel->update($unitId, [
            'name'   => $this->request->getPost('name'),
            'size'   => $this->request->getPost('size'),
            'floor'  => $this->request->getPost('floor'),
            'status' => $this->request->getPost('status'),
        ]);

        // Additional photos
        $photos = $this->request->getFileMultiple('photos');
        if ($photos) {
            $existingPhotoCount = $this->unitPhotoModel->where('unit_id', $unitId)->countAllResults();
            foreach ($photos as $photo) {
                if ($photo->isValid() && ! $photo->hasMoved()) {
                    $uploadResult = $this->storageService->uploadImage($photo, 'units');
                    if ($uploadResult['success']) {
                        $this->unitPhotoModel->insert([
                            'unit_id'    => $unitId,
                            'photo_url'  => $uploadResult['url'],
                            'caption'    => $this->request->getPost('name'),
                            'is_primary' => ($existingPhotoCount === 0) ? 1 : 0,
                        ]);
                        $existingPhotoCount++;
                    }
                }
            }
        }

        return redirect()->to('/provider/properties/' . $unit['property_id'])->with('success', 'Data unit berhasil diperbarui!');
    }

    /**
     * Delete unit
     */
    public function delete(int $unitId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $unit = $this->unitModel->getUnitWithDetails($unitId);

        if (! $unit || (int)$unit['provider_id'] !== (int)$providerId) {
            return redirect()->to('/provider/properties')->with('error', 'Unit tidak ditemukan.');
        }

        $propertyId = $unit['property_id'];

        // Delete photos
        $photos = $this->unitPhotoModel->getPhotosByUnit($unitId);
        foreach ($photos as $p) {
            $this->storageService->deleteImage($p['photo_url']);
        }

        $this->unitModel->delete($unitId);

        return redirect()->to('/provider/properties/' . $propertyId)->with('success', 'Unit berhasil dihapus.');
    }

    /**
     * Delete single unit photo
     */
    public function deletePhoto(int $photoId)
    {
        $providerId = session()->get('user_id') ?? 3;
        $photo = $this->unitPhotoModel->select('unit_photos.*, properties.provider_id')
                                      ->join('units', 'units.id = unit_photos.unit_id')
                                      ->join('properties', 'properties.id = units.property_id')
                                      ->where('unit_photos.id', $photoId)
                                      ->where('properties.provider_id', $providerId)
                                      ->first();

        if (! $photo) {
            return redirect()->back()->with('error', 'Foto tidak ditemukan.');
        }

        $this->storageService->deleteImage($photo['photo_url']);
        $this->unitPhotoModel->delete($photoId);

        return redirect()->back()->with('success', 'Foto unit berhasil dihapus.');
    }
}
