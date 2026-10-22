<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $plansTable = $db->table('subscription_plans');

        $plans = [
            // ==================== LISTING FEE (15 HARI) ====================
            [
                'name'             => 'Listing Fee 3',
                'slug'             => 'listing-fee-3',
                'type'             => 'listing_fee',
                'billing_cycle'    => '15_days',
                'price'            => 10000.00,
                'duration_days'    => 15,
                'listing_capacity' => 3,
                'description'      => 'Slot pasang 1-3 listing iklan aktif selama 15 hari.',
                'features'         => json_encode([
                    'Kapasitas hingga 3 listing iklan aktif',
                    'Masa aktif 15 hari kalender',
                    'Dukungan promosi listing standar',
                    'Dukungan sistem reservasi & kontrak online',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'name'             => 'Listing Fee 10',
                'slug'             => 'listing-fee-10',
                'type'             => 'listing_fee',
                'billing_cycle'    => '15_days',
                'price'            => 25000.00,
                'duration_days'    => 15,
                'listing_capacity' => 10,
                'description'      => 'Slot pasang 4-10 listing iklan aktif selama 15 hari.',
                'features'         => json_encode([
                    'Kapasitas hingga 10 listing iklan aktif',
                    'Masa aktif 15 hari kalender',
                    'Prioritas pencarian tingkat menengah',
                    'Dukungan reservasi, invoice & pembayaran otomatis',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'name'             => 'Listing Fee 20',
                'slug'             => 'listing-fee-20',
                'type'             => 'listing_fee',
                'billing_cycle'    => '15_days',
                'price'            => 45000.00,
                'duration_days'    => 15,
                'listing_capacity' => 20,
                'description'      => 'Slot pasang 11-20 listing iklan aktif selama 15 hari.',
                'features'         => json_encode([
                    'Kapasitas hingga 20 listing iklan aktif',
                    'Masa aktif 15 hari kalender',
                    'Prioritas pencarian tingkat tinggi',
                    'Dukungan multi-properti & unit',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'name'             => 'Listing Fee 50',
                'slug'             => 'listing-fee-50',
                'type'             => 'listing_fee',
                'billing_cycle'    => '15_days',
                'price'            => 100000.00,
                'duration_days'    => 15,
                'listing_capacity' => 50,
                'description'      => 'Slot pasang 21-50 listing iklan aktif selama 15 hari.',
                'features'         => json_encode([
                    'Kapasitas hingga 50 listing iklan aktif',
                    'Masa aktif 15 hari kalender',
                    'Prioritas pencarian unggulan',
                    'Analisis konversi & kunjungan properti',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'name'             => 'Listing Fee 100',
                'slug'             => 'listing-fee-100',
                'type'             => 'listing_fee',
                'billing_cycle'    => '15_days',
                'price'            => 180000.00,
                'duration_days'    => 15,
                'listing_capacity' => 100,
                'description'      => 'Slot pasang 51-100 listing iklan aktif selama 15 hari.',
                'features'         => json_encode([
                    'Kapasitas hingga 100 listing iklan aktif',
                    'Masa aktif 15 hari kalender',
                    'Penempatan prioritas maksimal di marketplace',
                    'Dukungan prioritas tim KosAja',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],

            // ==================== SUBSCRIPTION BULANAN (30 HARI) ====================
            [
                'name'             => 'Subscription Starter',
                'slug'             => 'subscription-starter',
                'type'             => 'subscription',
                'billing_cycle'    => 'monthly',
                'price'            => 49000.00,
                'duration_days'    => 30,
                'listing_capacity' => 10,
                'description'      => 'Langganan bulanan hemat untuk pemilik hingga 10 listing.',
                'features'         => json_encode([
                    'Kapasitas hingga 10 listing iklan aktif',
                    'Masa aktif 30 hari penuh',
                    'Pengingat perpanjangan otomatis',
                    'Dukungan kontrak digital & settlement dana',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'name'             => 'Subscription Growth',
                'slug'             => 'subscription-growth',
                'type'             => 'subscription',
                'billing_cycle'    => 'monthly',
                'price'            => 99000.00,
                'duration_days'    => 30,
                'listing_capacity' => 30,
                'description'      => 'Langganan bulanan ideal untuk juragan kos berkembang hingga 30 listing.',
                'features'         => json_encode([
                    'Kapasitas hingga 30 listing iklan aktif',
                    'Masa aktif 30 hari penuh',
                    'Badge Mitra Pilihan di hasil pencarian',
                    'Laporan analitik okupansi bulanan',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'name'             => 'Subscription Business',
                'slug'             => 'subscription-business',
                'type'             => 'subscription',
                'billing_cycle'    => 'monthly',
                'price'            => 199000.00,
                'duration_days'    => 30,
                'listing_capacity' => 100,
                'description'      => 'Langganan bulanan bisnis kos-kosan skala besar hingga 100 listing.',
                'features'         => json_encode([
                    'Kapasitas hingga 100 listing iklan aktif',
                    'Masa aktif 30 hari penuh',
                    'Prioritas rekomendasi pencarian algoritma',
                    'Dukungan multi-lokasi tanpa batas',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'name'             => 'Subscription Enterprise',
                'slug'             => 'subscription-enterprise',
                'type'             => 'subscription',
                'billing_cycle'    => 'monthly',
                'price'            => 249000.00,
                'duration_days'    => 30,
                'listing_capacity' => 999,
                'description'      => 'Paket korporasi/enterprise untuk manajemen properti > 100 listing.',
                'features'         => json_encode([
                    'Kapasitas tanpa batas (> 100 listing aktif)',
                    'Masa aktif 30 hari penuh',
                    'Dedicated account manager KosAja',
                    'Fitur terlengkap & prioritas maksimal',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($plans as $plan) {
            $existing = $plansTable->where('slug', $plan['slug'])->get()->getRowArray();
            if ($existing) {
                $plansTable->where('slug', $plan['slug'])->update($plan);
            } else {
                $plansTable->insert($plan);
            }
        }
    }
}
