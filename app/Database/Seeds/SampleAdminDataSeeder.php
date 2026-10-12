<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SampleAdminDataSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Assign Admin Daerah Sleman (user 2) to region 2 (Kabupaten Sleman)
        $this->db->table('users')->where('id', 2)->update(['region_id' => 2]);

        // 2. Sample Reports (Laporan Pelanggaran)
        if ($this->db->table('reports')->countAllResults() === 0) {
            $reports = [
                [
                    'id'             => 1,
                    'reporter_id'    => 4, // Budi Pratama
                    'object_type'    => 'listing',
                    'object_id'      => 1, // Listing 1
                    'region_id'      => 5, // Depok (Sleman)
                    'title'          => 'Fasilitas WiFi dan AC tidak sesuai deskripsi listing',
                    'reason'         => 'Di iklan dicantumkan AC split 1 PK dan WiFi 50Mbps, namun saat survei AC rusak dan belum diperbaiki pemilik.',
                    'evidence_url'   => null,
                    'status'         => 'open',
                    'admin_notes'    => null,
                    'resolved_by_id' => null,
                    'resolved_at'    => null,
                    'created_at'     => date('Y-m-d H:i:s', strtotime('-2 days')),
                    'updated_at'     => date('Y-m-d H:i:s', strtotime('-2 days')),
                ],
                [
                    'id'             => 2,
                    'reporter_id'    => 5, // Siti Aisyah
                    'object_type'    => 'property',
                    'object_id'      => 2, // Property 2
                    'region_id'      => 5, // Depok (Sleman)
                    'title'          => 'Klaim lingkungan kos putri tidak sesuai realita',
                    'reason'         => 'Papan nama properti tidak mencantumkan aturan jam malam tamu lawan jenis dengan jelas.',
                    'evidence_url'   => null,
                    'status'         => 'investigating',
                    'admin_notes'    => 'Admin Sleman sedang menghubungi pemilik kos untuk verifikasi penegakan aturan tata tertib.',
                    'resolved_by_id' => 2,
                    'resolved_at'    => null,
                    'created_at'     => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'updated_at'     => $now,
                ],
            ];
            $this->db->table('reports')->ignore(true)->insertBatch($reports);
        }

        // 3. Sample Settlements (Pencairan Dana Mitra Provider untuk Super Admin)
        if ($this->db->table('settlements')->countAllResults() === 0) {
            $settlements = [
                [
                    'id'                  => 1,
                    'provider_id'         => 3, // Haji Ahmad Santoso
                    'contract_id'         => 1,
                    'gross_amount'        => 1250000.00,
                    'platform_fee'        => 25000.00,
                    'net_amount'          => 1225000.00,
                    'bank_name'           => 'Bank Syariah Indonesia (BSI)',
                    'bank_account_number' => '7123456789',
                    'bank_account_holder' => 'Haji Ahmad Santoso',
                    'status'              => 'completed',
                    'payout_reference'    => 'PAYOUT-202610-001',
                    'processed_by_id'     => 1, // Super Admin
                    'processed_at'        => date('Y-m-d H:i:s', strtotime('-3 days')),
                    'notes'               => 'Pencairan termin sewa bulan ke-1 untuk Kos Muslim Putri Zahra.',
                    'created_at'          => date('Y-m-d H:i:s', strtotime('-4 days')),
                    'updated_at'          => date('Y-m-d H:i:s', strtotime('-3 days')),
                ],
                [
                    'id'                  => 2,
                    'provider_id'         => 3, // Haji Ahmad Santoso
                    'contract_id'         => 1,
                    'gross_amount'        => 1250000.00,
                    'platform_fee'        => 25000.00,
                    'net_amount'          => 1225000.00,
                    'bank_name'           => 'Bank Syariah Indonesia (BSI)',
                    'bank_account_number' => '7123456789',
                    'bank_account_holder' => 'Haji Ahmad Santoso',
                    'status'              => 'pending',
                    'payout_reference'    => null,
                    'processed_by_id'     => null,
                    'processed_at'        => null,
                    'notes'               => 'Menunggu verifikasi pembayaran termin kedua penyewa Budi Pratama.',
                    'created_at'          => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'updated_at'          => $now,
                ],
            ];
            $this->db->table('settlements')->ignore(true)->insertBatch($settlements);
        }
    }
}
