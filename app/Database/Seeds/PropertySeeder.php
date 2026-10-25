<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Properties
        $properties = [
            [
                'id'            => 1,
                'provider_id'   => 3, // H. Ahmad
                'region_id'     => 5, // Depok Sleman
                'name'          => 'Kos Muslim Putri Barokah Pogung',
                'type'          => 'kos',
                'target_gender' => 'putri',
                'description'   => 'Kos putri eksklusif, aman, tenang, dan dekat dengan Fakultas Kedokteran & Teknik UGM. Mengutamakan kenyamanan belajar dan kepatuhan syariat.',
                'address'       => 'Jl. Pogung Baru No. 12, Pogung Kidul, Sinduadi, Mlati / Depok, Sleman, D.I. Yogyakarta',
                'latitude'      => -7.76120000,
                'longitude'     => 110.37340000,
                'rules'         => "1. Wajib menjaga kesopanan & ketertiban.\n2. Lawan jenis dilarang masuk kamar (hanya di ruang tamu terbuka).\n3. Gerbang ditutup pukul 22.00 WIB demi keamanan.",
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 2,
                'provider_id'   => 3,
                'region_id'     => 5,
                'name'          => 'Kos Putra Insan Cendekia Seturan',
                'type'          => 'kos',
                'target_gender' => 'putra',
                'description'   => 'Kos putra modern fasilitas lengkap kamar mandi dalam, AC, WiFi 100Mbps. Dekat UPN, YKPN, dan Atma Jaya.',
                'address'       => 'Jl. Seturan Raya Gg. Mawar No. 4, Caturtunggal, Depok, Sleman',
                'latitude'      => -7.77850000,
                'longitude'     => 110.41120000,
                'rules'         => "1. Bebas rokok di dalam kamar.\n2. Jam tenang mulai pukul 23:00 WIB.",
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 3,
                'provider_id'   => 3,
                'region_id'     => 6, // Mlati
                'name'          => 'Paviliun Keluarga Harmoni Jombor',
                'type'          => 'kontrakan',
                'target_gender' => 'campur_keluarga',
                'description'   => 'Kontrakan paviliun 2 kamar tidur sangat cocok untuk pasangan suami istri baru atau keluarga kecil.',
                'address'       => 'Jl. Magelang KM 6.5, Sinduadi, Mlati, Sleman',
                'latitude'      => -7.75540000,
                'longitude'     => 110.36210000,
                'rules'         => "Wajib menyertakan fotokopi Buku Nikah / Kartu Keluarga bagi penyewa pasangan.",
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        $this->db->table('properties')->ignore(true)->insertBatch($properties);

        // 2. Property Facilities
        $facilities = [
            // Property 1
            ['property_id' => 1, 'facility_name' => 'WiFi Berkecepatan Tinggi', 'icon' => 'bi-wifi', 'created_at' => $now],
            ['property_id' => 1, 'facility_name' => 'Dapur Bersama + Kulkas', 'icon' => 'bi-cup-hot', 'created_at' => $now],
            ['property_id' => 1, 'facility_name' => 'Parkir Motor Luas & Aman (CCTV)', 'icon' => 'bi-shield-check', 'created_at' => $now],
            ['property_id' => 1, 'facility_name' => 'Mushola Bersama', 'icon' => 'bi-building', 'created_at' => $now],

            // Property 2
            ['property_id' => 2, 'facility_name' => 'WiFi Fiber 100 Mbps', 'icon' => 'bi-wifi', 'created_at' => $now],
            ['property_id' => 2, 'facility_name' => 'AC Tiap Kamar', 'icon' => 'bi-snow', 'created_at' => $now],
            ['property_id' => 2, 'facility_name' => 'Parkir Mobil & Motor', 'icon' => 'bi-car-front', 'created_at' => $now],

            // Property 3
            ['property_id' => 3, 'facility_name' => '2 Kamar Tidur & Ruang Tamu', 'icon' => 'bi-door-open', 'created_at' => $now],
            ['property_id' => 3, 'facility_name' => 'Carport Mobil Pribadi', 'icon' => 'bi-car-front', 'created_at' => $now],
            ['property_id' => 3, 'facility_name' => 'Listrik Token 2200W', 'icon' => 'bi-lightning', 'created_at' => $now],
        ];
        $this->db->table('property_facilities')->ignore(true)->insertBatch($facilities);

        // 3. Property Photos
        $photos = [
            ['property_id' => 1, 'photo_url' => 'assets/images/exterior-kos-1.jpg', 'caption' => 'Tampak Depan Gedung Kos', 'is_primary' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['property_id' => 2, 'photo_url' => 'assets/images/exterior-kos-2.jpg', 'caption' => 'Fasad Kos Modern', 'is_primary' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['property_id' => 3, 'photo_url' => 'assets/images/exterior-kos-3.jpg', 'caption' => 'Tampak Depan Rumah Kontrakan', 'is_primary' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('property_photos')->ignore(true)->insertBatch($photos);

        // 3b. Unit Photos (Inside Kamar)
        $unitPhotos = [
            ['unit_id' => 1, 'photo_url' => 'assets/images/sample-kos-1.jpg', 'caption' => 'Inside Kamar Tidur & Meja Belajar', 'is_primary' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 3, 'photo_url' => 'assets/images/sample-kos-2.jpg', 'caption' => 'Inside Kamar Deluxe & Setup Kerja', 'is_primary' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 4, 'photo_url' => 'assets/images/sample-kos-3.jpg', 'caption' => 'Inside Ruang Tamu & Kamar Paviliun', 'is_primary' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('unit_photos')->ignore(true)->insertBatch($unitPhotos);

        // 4. Units
        $units = [
            [
                'id'          => 1,
                'property_id' => 1,
                'name'        => 'Kamar A-01 (Lantai 1)',
                'size'        => '3 x 4 m',
                'floor'       => 'Lantai 1',
                'status'      => 'available',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 2,
                'property_id' => 1,
                'name'        => 'Kamar A-02 (Lantai 1)',
                'size'        => '3 x 4 m',
                'floor'       => 'Lantai 1',
                'status'      => 'occupied',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 3,
                'property_id' => 2,
                'name'        => 'Kamar Deluxe 101',
                'size'        => '4 x 4 m',
                'floor'       => 'Lantai 1',
                'status'      => 'available',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 4,
                'property_id' => 3,
                'name'        => 'Unit Paviliun Seluruhnya',
                'size'        => '6 x 10 m (60 m2)',
                'floor'       => '1 Lantai',
                'status'      => 'available',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];
        $this->db->table('units')->ignore(true)->insertBatch($units);

        // 5. Listings
        $listings = [
            [
                'id'                 => 1,
                'unit_id'            => 1,
                'title'              => 'Kamar Nyaman AC Kamar Mandi Dalam Dekat Kampus UGM Pogung',
                'description'        => 'Kamar kos putri luas 3x4 meter dilengkapi tempat tidur spring bed, lemari pakaian, meja belajar, AC, dan kamar mandi dalam dengan shower.',
                'price_monthly'      => 1250000.00,
                'price_yearly'       => 14000000.00,
                'publication_status' => 'published',
                'rejection_reason'   => null,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'id'                 => 2,
                'unit_id'            => 3,
                'title'              => 'Kos Putra Mewah Seturan Fasilitas Lengkap Dekat UPN & YKPN',
                'description'        => 'Kamar kos putra eksklusif full furnished, AC, Water Heater, Smart TV, WiFi kencang, parkir mobil leluasa.',
                'price_monthly'      => 1750000.00,
                'price_yearly'       => 19500000.00,
                'publication_status' => 'published',
                'rejection_reason'   => null,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'id'                 => 3,
                'unit_id'            => 4,
                'title'              => 'Kontrakan Rumah Paviliun 2 KT Nyaman & Asri di Jombor Sleman',
                'description'        => 'Rumah sewa siap huni cocok untuk pasutri baru. Lingkungan perumahan tenang, dekat Jalan Magelang dan Jogja City Mall.',
                'price_monthly'      => 2200000.00,
                'price_yearly'       => 24000000.00,
                'publication_status' => 'published',
                'rejection_reason'   => null,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
        ];
        $this->db->table('listings')->ignore(true)->insertBatch($listings);
    }
}
