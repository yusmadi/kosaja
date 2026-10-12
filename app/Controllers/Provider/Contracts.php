<?php

namespace App\Controllers\Provider;

use App\Controllers\BaseController;
use App\Models\ContractModel;
use App\Models\InvoiceModel;
use App\Models\PaymentModel;

class Contracts extends BaseController
{
    protected ContractModel $contractModel;
    protected InvoiceModel $invoiceModel;
    protected PaymentModel $paymentModel;

    public function __construct()
    {
        $this->contractModel = new ContractModel();
        $this->invoiceModel  = new InvoiceModel();
        $this->paymentModel  = new PaymentModel();
    }

    /**
     * Provider Contracts List
     */
    public function index()
    {
        $userId = (int) (session()->get('user_id') ?? 3);
        $contracts = $this->contractModel->getContractsByProvider($userId);

        return view('provider/contracts/index', [
            'title'      => 'Daftar Kontrak Penyewa — KosAja',
            'role'       => 'provider',
            'activeMenu' => 'contracts',
            'contracts'  => $contracts,
        ]);
    }

    /**
     * Provider Contract Detail & Legal Agreement View
     */
    public function show(int $id)
    {
        $userId = (int) (session()->get('user_id') ?? 3);
        $contract = $this->contractModel->getContractWithDetails($id);

        if (! $contract) {
            return redirect()->to(base_url('/provider/contracts'))->with('error', 'Kontrak tidak ditemukan.');
        }

        if ((int) $contract['provider_id'] !== $userId && session()->get('role') !== 'super_admin') {
            return redirect()->to(base_url('/provider/contracts'))->with('error', 'Akses ditolak.');
        }

        $invoices = $this->invoiceModel->getInvoicesByContract($id);
        $payments = [];
        foreach ($invoices as $inv) {
            $pmts = $this->paymentModel->getPaymentsByInvoice($inv['id']);
            $payments = array_merge($payments, $pmts);
        }

        return view('provider/contracts/show', [
            'title'      => 'Detail Kontrak #' . $contract['id'] . ' — ' . esc($contract['tenant_name']),
            'role'       => 'provider',
            'activeMenu' => 'contracts',
            'contract'   => $contract,
            'invoices'   => $invoices,
            'payments'   => $payments,
        ]);
    }
}
