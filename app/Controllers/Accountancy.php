<?php

namespace App\Controllers;
use App\Models\OutletModel;
use App\Models\UserModel;
use App\Models\SupplierModel;
use App\Models\AccountancyContactModel;
use App\Models\AccountancyCOAModel;
use App\Models\AccountancyCategoryModel;

class Accountancy extends BaseController
{
    public function dashboard()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Dashboard - '.lang('Global.accountancyList');
        $data['description']    = 'Dashboard '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/dashboard', $data);
    }

    public function transaction()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Tambah Transaksi - '.lang('Global.accountancyList');
        $data['description']    = 'Tambah Transaksi '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/transaction', $data);
    }

    public function akuncoa()
    {
        // Calling Model
        $AccountancyCOAModel        = new AccountancyCOAModel();
        $AccountancyCategoryModel   = new AccountancyCategoryModel();

        // Populating data
        $coas           = [];
        $akuncoa        = $AccountancyCOAModel->orderBy('name', 'ASC')->findAll();
        $categories     = $AccountancyCategoryModel->orderBy('id', 'ASC')->findAll();
        foreach ($akuncoa as $coa) {
            $category       = $AccountancyCategoryModel->find($coa['cat_a_id']);
            $coas[]  = [
                'id'            => $coa['id'],
                'kode'          => $category['cat_code'].$coa['id'],
                'cat_a_id'      => $category['id'],
                'category'      => $category['name'],
                'coa_type'      => $category['cat_type'],
                'name'          => $coa['name'],
                'description'   => $coa['description'],
                'status_lock'   => $coa['status_lock'],
                'status_active' => $coa['status_active'],
            ];
        }
        array_multisort(array_column($coas, 'cat_a_id'), SORT_ASC, $coas);
        
        // Parsing data to view
        $data                   = $this->data;
        $data['coas']           = $coas;
        $data['categories']     = $categories;
        $data['title']          = 'Akun (COA) - '.lang('Global.accountancyList');
        $data['description']    = 'Akun (COA) '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/akuncoa', $data);
    }

    public function createAkunCOA()
    {
        // Calling Model
        $AccountancyCOAModel   = new AccountancyCOAModel();
        
        // Defining input
        $input = $this->request->getPost();

        $data = [
            'name'          => $input['name'],
            'cat_a_id'      => $input['category'],
            'description'   => $input['description'],
            'status_lock'   => isset($input['status_lock']) ? 1 : 0,
            'status_active' => 1,
        ];

        if (!$this->validate([
            'name'      => "required|max_length[255]",
        ])) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        // Inserting COA
        $AccountancyCOAModel->insert($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function asset()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Asset - '.lang('Global.accountancyList');
        $data['description']    = 'Asset '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/asset', $data);
    }

    public function closingEntries()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Tutup Buku - '.lang('Global.accountancyList');
        $data['description']    = 'Tutup Buku '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/closing-entries', $data);
    }

    public function contact()
    {
        // Calling Model
        $UserModel      = new UserModel();
        $SupplierModel  = new SupplierModel();
        $ContactModel   = new AccountancyContactModel();

        // Populating data
        $contacts               = [];
        $suppliers              = $SupplierModel->findAll();
        $users                  = $UserModel->findAll();
        $accountancycontacts    = $ContactModel->findAll();

        foreach ($suppliers as $supplier) {
            $contacts[]  = [
                'id'        => '10'.$supplier['id'],
                'realid'    => $supplier['id'],
                'name'      => $supplier['name'],
                'email'     => $supplier['email'],
                'phone'     => $supplier['phone'],
                'address'   => $supplier['address'],
                'status'    => 1,
            ];
        }

        foreach ($users as $user) {
            $contacts[]  = [
                'id'        => '20'.$user->id,
                'realid'    => $user->id,
                'name'      => $user->firstname.' '.$user->lastname,
                'firstname' => $user->firstname,
                'lastname'  => $user->lastname,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'address'   => '',
                'status'    => 2,
            ];
        }

        foreach ($accountancycontacts as $accountancycontact) {
            $contacts[]  = [
                'id'        => '30'.$accountancycontact['id'],
                'realid'    => $accountancycontact['id'],
                'name'      => $accountancycontact['name'],
                'email'     => $accountancycontact['email'],
                'phone'     => $accountancycontact['phone'],
                'address'   => $accountancycontact['address'],
                'status'    => 3,
            ];
        }
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        array_multisort(array_column($contacts, 'name'), SORT_ASC, $contacts);
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['contacts']       = $contacts;
        $data['title']          = 'Kontak - '.lang('Global.accountancyList');
        $data['description']    = 'Kontak '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/contact', $data);
    }

    public function createContact()
    {
        // Calling Model
        $ContactModel   = new AccountancyContactModel();
        
        // Defining input
        $input = $this->request->getPost();

        $data = [
            'name'      => $input['name'],
            'phone'     => $input['phone'],
            'email'     => $input['email'],
            'address'   => $input['address'],
        ];

        if (!$this->validate([
            'name'      => "required|max_length[255]",
            'phone'     => 'numeric',
            'email'     => 'valid_email',
            'address'   => 'max_length[255]',
        ])) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        // Inserting Customer
        $ContactModel->insert($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function updateContact($id)
    {
        // Calling Model
        $ContactModel = new AccountancyContactModel();

        // Populating Data
        $input = $this->request->getPost();

        // Validating
        if (!$this->validate([
            'name'    => "required|max_length[255]",
            'phone'   => "permit_empty|numeric",
            'email'   => "permit_empty|valid_email",
            'address' => "permit_empty|max_length[255]",
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update
        $data = [
            'id'      => $id,
            'name'    => $input['name']    ?: '-',
            'phone'   => $input['phone']   ?: '-',
            'email'   => $input['email']   ?: '-',
            'address' => $input['address'] ?: '-',
        ];
        $ContactModel->save($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function deleteContact($id)
    {
        // Calling Model
        $ContactModel = new AccountancyContactModel();

        // Populating Data
        $contact = $ContactModel->find($id);

        // Validating
        if (!$contact) {
            return redirect()->back()->with('errors', ['Data tidak ditemukan']);
        }

        // Delete
        $ContactModel->delete($id);

        return redirect()->back()->with('message', lang('Global.deleted'));
    }

    public function manualAccountingReconciliation()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Rekonsiliasi Transaksi Manual - '.lang('Global.accountancyList');
        $data['description']    = 'Rekonsiliasi Transaksi Manual '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/manual-accounting-reconciliation', $data);
    }

    public function budgetting()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Budgetting - '.lang('Global.accountancyList');
        $data['description']    = 'Budgetting '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/budegetting', $data);
    }

    public function transactionReport()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Transaksi - '.lang('Global.accountancyList');
        $data['description']    = 'Transaksi '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/transaction-report', $data);
    }

    public function generalJournal()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Jurnal Umum - '.lang('Global.accountancyList');
        $data['description']    = 'Jurnal Umum '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/general-journal', $data);
    }

    public function ledger()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Buku Besar - '.lang('Global.accountancyList');
        $data['description']    = 'Buku Besar '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/ledger', $data);
    }

    public function trialBalance()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Neraca Saldo - '.lang('Global.accountancyList');
        $data['description']    = 'Neraca Saldo '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/trial-balance', $data);
    }

    public function profiLossStatement()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Laba Rugi - '.lang('Global.accountancyList');
        $data['description']    = 'Laba Rugi '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/profit-loss-statement', $data);
    }

    public function changesEquity()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Perubahan Modal - '.lang('Global.accountancyList');
        $data['description']    = 'Perubahan Modal '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/change-equity', $data);
    }

    public function balanceSheet()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Neraca - '.lang('Global.accountancyList');
        $data['description']    = 'Neraca '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/balance-sheet', $data);
    }

    public function cashFlow()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Arus Kas - '.lang('Global.accountancyList');
        $data['description']    = 'Arus Kas '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/cash-flow', $data);
    }

    public function receiveablePayableAccount()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Hutang Piutang - '.lang('Global.accountancyList');
        $data['description']    = 'Hutang Piutang '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/receiveable-payable-account', $data);
    }

    public function profile()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Profil - '.lang('Global.accountancyList');
        $data['description']    = 'Profil '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/profile', $data);
    }

    public function company()
    {
        // Calling Model
        $OutletModel    = new OutletModel();

        // Populating data
        $outlets    = $OutletModel->orderBy('id', 'ASC')->findAll();
        
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = 'Perusahaan - '.lang('Global.accountancyList');
        $data['description']    = 'Perusahaan '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/company', $data);
    }

    public function employee()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Karyawan - '.lang('Global.accountancyList');
        $data['description']    = 'Karyawan '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/employee', $data);
    }
}
