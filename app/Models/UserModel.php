<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'password',
        'whatsapp',
        'gender',
        'role',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[255]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'gender'   => 'required|in_list[L,P]',
        'role'     => 'required|in_list[tenant,provider,admin_daerah,super_admin]',
        'status'   => 'permit_empty|in_list[active,suspended,inactive]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.',
        ],
        'gender' => [
            'required' => 'Jenis kelamin wajib diisi demi kepatuhan Syariat.',
            'in_list'  => 'Pilihan jenis kelamin tidak valid (Wajib Laki-laki atau Perempuan).',
        ],
    ];

    /**
     * Find user by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}
