<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegionModel;

class Regions extends BaseController
{
    protected $regionModel;

    public function __construct()
    {
        $this->regionModel = new RegionModel();
    }

    public function index()
    {
        $regions = $this->regionModel->getHierarchy();
        $provincesAndCities = $this->regionModel->whereIn('level', ['province', 'city'])->findAll();

        return view('admin/regions', [
            'title'              => 'Manajemen Wilayah Operasional (Regions)',
            'role'               => 'admin',
            'activeMenu'         => 'regions',
            'regions'            => $regions,
            'provincesAndCities' => $provincesAndCities,
        ]);
    }

    public function create()
    {
        $rules = [
            'name'  => 'required|min_length[2]|max_length[255]',
            'level' => 'required|in_list[province,city,operational]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $parentId = $this->request->getPost('parent_id');
        $data = [
            'name'      => $this->request->getPost('name'),
            'level'     => $this->request->getPost('level'),
            'parent_id' => ! empty($parentId) ? $parentId : null,
        ];

        $this->regionModel->insert($data);

        return redirect()->to('/admin/regions')->with('success', 'Wilayah operasional berhasil ditambahkan!');
    }

    public function delete($id)
    {
        $this->regionModel->delete($id);
        return redirect()->to('/admin/regions')->with('success', 'Wilayah berhasil dihapus.');
    }
}
