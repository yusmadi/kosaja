<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ContractModel;
use App\Models\InvoiceModel;
use App\Models\PaymentModel;

class Contract extends BaseController
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
     * Tenant Contracts List
     */
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $userId = (int) session()->get('user_id');
        $contracts = $this->contractModel->getContractsByTenant($userId);

        return view('tenant/contracts/index', [
            'title'     => 'Kontrak Sewa Saya — KosAja',
            'contracts' => $contracts,
        ]);
    }

    /**
     * Tenant Contract Detail & Official Agreement Document
     */
    public function show(int $id)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $userId = (int) session()->get('user_id');
        $contract = $this->contractModel->getContractWithDetails($id);

        if (! $contract) {
            return redirect()->to(base_url('/contracts'))->with('error', 'Dokumen kontrak sewa tidak ditemukan.');
        }

        // Authorization check: tenant, provider, or admin
        if ((int) $contract['tenant_id'] !== $userId && (int) $contract['provider_id'] !== $userId && ! in_array(session()->get('role'), ['super_admin', 'admin_daerah'])) {
            return redirect()->to(base_url('/contracts'))->with('error', 'Anda tidak memiliki hak akses ke dokumen kontrak ini.');
        }

        // Fetch invoices & payments for this contract
        $invoices = $this->invoiceModel->getInvoicesByContract($id);
        $payments = [];
        foreach ($invoices as $inv) {
            $pmts = $this->paymentModel->getPaymentsByInvoice($inv['id']);
            $payments = array_merge($payments, $pmts);
        }

        return view('tenant/contracts/show', [
            'title'    => 'Dokumen Kontrak Sewa #' . $contract['id'] . ' — KosAja',
            'contract' => $contract,
            'invoices' => $invoices,
            'payments' => $payments,
        ]);
    }
}
