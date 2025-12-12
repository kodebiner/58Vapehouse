<?php

namespace App\Controllers;
use App\Models\OutletModel;
use App\Models\UserModel;
use App\Models\GroupUserModel;
use Myth\Auth\Models\GroupModel;
use App\Models\OutletaccessModel;
use App\Models\SupplierModel;
use App\Models\AccountancyContactModel;
use App\Models\AccountancyCOAModel;
use App\Models\AccountancyCategoryModel;
use App\Models\AccountancyTaxModel;
use App\Models\AccountancyEarlyFundsModel;
use App\Models\AccountancyAssetModel;

class Accountancy extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()
    {
        $this->db       = \Config\Database::connect();
        $validation     = \Config\Services::validation();
        $this->builder  = $this->db->table('users');
        $this->config   = config('Auth');
        $this->auth     = service('authentication');
    }
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
        $AccountancyCOAModel        = new AccountancyCOAModel();
        $AccountancyContactModel    = new AccountancyContactModel();
        $AccountancyTaxModel        = new AccountancyTaxModel();

        // Populating data
        $debitCoas  = $AccountancyCOAModel->findAll();
        $creditCoas = $AccountancyCOAModel->findAll();
        $contacts   = $AccountancyContactModel->findAll();
        $taxes      = $AccountancyTaxModel->findAll();
        
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = 'Tambah Transaksi - '.lang('Global.accountancyList');
        $data['description']    = 'Tambah Transaksi '.lang('Global.accountancyListDesc');
        $data['debitCoas']      = $debitCoas;
        $data['creditCoas']     = $creditCoas;
        $data['contacts']       = $contacts;
        $data['taxes']          = $taxes;

        return view('Views/accountancy/transaction', $data);
    }

    public function akuncoa()
    {
        // Services
        $db = \Config\Database::connect();
        
        // Populating Data
        $query = $db->table('accountancy_coa AS c')
            ->select('
                c.id,
                c.name,
                c.description,
                c.status_lock,
                c.status_active,
                c.cat_a_id,
                cat.cat_code,
                cat.name AS category_name,
                cat.cat_type,
                o.name AS outlet_name
            ')
            ->join('accountancy_categories AS cat', 'cat.id = c.cat_a_id')
            ->join('outlet AS o', 'o.id = c.outletid')
            ->orderBy('c.cat_a_id', 'ASC')
            ->orderBy('c.name', 'ASC')
            ->get()
            ->getResultArray();

        $coas = [];
        foreach ($query as $row) {
            $coas[] = [
                'id'            => $row['id'],
                'kode'          => $row['cat_code'].$row['id'],
                'cat_a_id'      => $row['cat_a_id'],
                'category'      => $row['category_name'],
                'coa_type'      => $row['cat_type'],
                'name'          => $row['name'].' - '.str_replace('58 Vapehouse ','',$row['outlet_name']),
                'description'   => $row['description'],
                'status_lock'   => $row['status_lock'],
                'status_active' => $row['status_active'],
            ];
        }
        
        // Parsing data to view
        $data                = $this->data;
        $data['coas']        = $coas;
        $data['categories']  = (new AccountancyCategoryModel())->orderBy('id','ASC')->findAll();
        $data['title']       = 'Akun (COA) - '.lang('Global.accountancyList');
        $data['description'] = 'Akun (COA) '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/akuncoa', $data);
    }

    public function createAkunCOA()
    {
        // Calling Model
        $AccountancyCOAModel    = new AccountancyCOAModel();
        $OutletModel            = new OutletModel();

        // Populating Data
        $input                  = $this->request->getPost();
        $outlets                = $OutletModel->findAll();

        // Validation
        if (!$this->validate([
            'name' => "required|max_length[255]",
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->data['outletPick'] === null) {
            foreach ($outlets as $outlet) {
                $data = [
                    'name'          => $input['name'],
                    'cat_a_id'      => $input['category'],
                    'outletid'      => $outlet['id'],
                    'description'   => $input['description'],
                    'status_lock'   => isset($input['status_lock']) ? 0 : 1,
                    'status_active' => 1,
                ];

                $AccountancyCOAModel->insert($data);
            }
        } else {
            $data = [
                'name'          => $input['name'],
                'cat_a_id'      => $input['category'],
                'outletid'      => $this->data['outletPick'],
                'description'   => $input['description'],
                'status_lock'   => isset($input['status_lock']) ? 0 : 1,
                'status_active' => 1,
            ];

            $AccountancyCOAModel->insert($data);
        }

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function updateAkunCOA($id)
    {
        $coaModel = new AccountancyCOAModel();

        $coaModel->update($id, [
            'cat_a_id'      => $this->request->getPost('category'),
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'status_active' => $this->request->getPost('status_active') == "0" ? 0 : 1,
        ]);

        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function deleteAkunCOA($id)
    {
        $coaModel = new \App\Models\AccountancyCOAModel();
        $coaModel->delete($id);

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function earlyFunds()
    {
        // Calling Model
        $AccountancyCOAModel        = new AccountancyCOAModel();
        $AccountancyCategoryModel   = new AccountancyCategoryModel();

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
        $coa = $AccountancyCOAModel
            ->select("
                accountancy_coa.*,
                accountancy_categories.cat_code,
                accountancy_categories.name as category_name,
                accountancy_categories.cat_type,
                CONCAT(accountancy_categories.cat_code, accountancy_coa.id) AS full_code
            ")
            ->join('accountancy_categories', 'accountancy_categories.id = accountancy_coa.cat_a_id')
            ->orderBy('accountancy_categories.cat_code')
            ->findAll();
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Saldo Awal - '.lang('Global.accountancyList');
        $data['description']    = 'Saldo Awal '.lang('Global.accountancyListDesc');
        $data['coa_list']       = $coa;

        return view('Views/accountancy/early-funds', $data);
    }

    public function saveEarlyFunds()
    {
        $AccountancyEarlyFundsModel = new AccountancyEarlyFundsModel();

        $convertDate = $this->request->getPost('convert_date');
        $debits      = $this->request->getPost('debit_value');
        $credits     = $this->request->getPost('credit_value');

        if (!$convertDate) {
            return redirect()->back()->with('error', 'Tanggal konversi wajib diisi.');
        }

        if (!is_array($debits)) {
            return redirect()->back()->with('error', 'Data COA tidak ditemukan.');
        }

        foreach ($debits as $coaId => $debitValue) {
            $debit  = floatval($debitValue);
            $credit = floatval($credits[$coaId] ?? 0);

            if ($debit == 0 && $credit == 0) {
                continue;
            }

            $AccountancyEarlyFundsModel->insert([
                'coa_a_id'     => $coaId,
                'debit_value'  => $debit,
                'credit_value' => $credit,
            ]);
        }

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function asset()
    {
        // Calling Model
        $AccountancyAssetModel    = new AccountancyAssetModel();
        $AccountancyCOAModel      = new AccountancyCOAModel();
        $AccountancyTaxModel      = new AccountancyTaxModel();
        $AccountancyCategoryModel = new AccountancyCategoryModel();

        // Populating data
        $cleanOutlet = function($name) {
            return preg_replace('/^58 Vapehouse\s*/i', '', $name);
        };
        $outlets = [];
        foreach ((new OutletModel())->findAll() as $o) {
            $outlets[$o['id']] = $cleanOutlet($o['name']);
        }
        $coas = $AccountancyCOAModel->findAll();
        $filterCoaByCat = function($catId) use ($coas, $outlets) {
            $result = [];
            foreach ($coas as $c) {
                if ($c['cat_a_id'] == $catId) {
                    $result[] = [
                        'id'       => $c['id'],
                        'name'     => $c['name'] . ' - ' . ($outlets[$c['outletid']] ?? ''),
                        'cat_a_id' => $c['cat_a_id'],
                        'cat_type' => $c['cat_type'] ?? null,
                    ];
                }
            }
            return $result;
        };
        $katHartap       = 5;
        $katPenyusutan   = 14;
        $katDepresiasi   = 6;
        
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = 'Asset - ' . lang('Global.accountancyList');
        $data['description']    = 'Asset ' . lang('Global.accountancyListDesc');
        $data['assets']         = $AccountancyAssetModel->findAll();
        $data['coahartaps']     = $filterCoaByCat($katHartap);
        $data['allcoas']        = array_map(function($c) use ($outlets) {
            return [
                'id'       => $c['id'],
                'name'     => $c['name'] . ' - ' . ($outlets[$c['outletid']] ?? ''),
                'cat_a_id' => $c['cat_a_id'],
            ];
        }, $coas);
        $data['weights']        = $filterCoaByCat($katPenyusutan);
        $data['coadepreciation']= $filterCoaByCat($katDepresiasi);
        $data['taxes']          = $AccountancyTaxModel->findAll();

        return view('Views/accountancy/asset', $data);
    }
    
    public function createAsset()
    {
        // Calling Model
        $AssetModel = new \App\Models\AccountancyAssetModel();
        // Model yang mungkin dibutuhkan lainnya (seperti untuk validasi outlet atau transaksi jurnal)
        // $OutletModel = new \App\Models\OutletModel(); 

        // Mengambil Data dari POST
        $input = $this->request->getPost();
        
        // Asumsi: Jika ada logika yang memerlukan loop per outlet, ini perlu dipertimbangkan.
        // Untuk saat ini, saya fokus pada data aset itu sendiri.

        // Menangani File Upload
        $imageFile = $this->request->getFile('image_asset');
        $imageName = null;

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $imageName = $imageFile->getRandomName();
            $imageFile->move(ROOTPATH . 'public/uploads/assets', $imageName); // Sesuaikan path jika perlu
        }

        // Validasi Sederhana
        if (!$this->validate([
            'date'                  => 'required|valid_date',
            'code_asset'            => 'required|max_length[255]',
            'name'                  => 'required|max_length[255]',
            'cat_asset_tetap'       => 'required|integer',
            'value_asset_tetap'     => 'required|numeric|greater_than_equal_to[0]',
            'cat_asset_credit'      => 'required|integer',
            // Tambahkan validasi lain sesuai kebutuhan
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Menyiapkan data aset
        $depreciation_status = isset($input['depreciation_status']);

        $data = [
            'date'                      => $input['date'],
            'code_asset'                => $input['code_asset'],
            'name'                      => $input['name'],
            'description'               => $input['description'] ?? null,
            'cat_asset_tetap'           => $input['cat_asset_tetap'],
            'value_asset_tetap'         => $input['value_asset_tetap'],
            'cat_tax'                   => $input['cat_tax'] ?? null,
            'value_tax'                 => $input['value_tax'] ?? 0,
            'cat_asset_credit'          => $input['cat_asset_credit'],
            'image'                     => $imageName, // Nama file gambar
            'depreciation_status'       => $depreciation_status ? 1 : 0,
            
            // Data Penyusutan (hanya diisi jika depreciation_status dicentang)
            'depreciation_method'       => $depreciation_status ? ($input['depreciation_method'] ?? 'straight_line') : null,
            'depreciation_residu'       => $depreciation_status ? ($input['depreciation_residu'] ?? 0) : 0,
            'depreciation_benefit_era'  => $depreciation_status ? ($input['depreciation_benefit_era'] ?? 0) : 0,
            'depreciation_cat_penyusutan' => $depreciation_status ? ($input['depreciation_cat_penyusutan'] ?? null) : null,
            'depreciation_sum_cat_penyusutan' => $depreciation_status ? ($input['depreciation_sum_cat_penyusutan'] ?? null) : null,
            
            // Asumsi nilai-nilai default/wajib lainnya
            'outletid'                  => $this->data['outletPick'] ?? null, // Sesuaikan dengan logika otentikasi/pemilihan outlet Anda
            'status_active'             => 1, 
            'status_lock'               => 1, // Asumsi aset baru defaultnya aktif/terkunci
        ];

        // Simpan data ke database
        $AssetModel->insert($data);
        
        // Logika Jurnal Akuntansi (Penting, tapi tidak termasuk di sini)
        // Setelah insert, biasanya Anda akan memproses jurnal akuntansi:
        // Debet: Akun Asset Tetap ($data['cat_asset_tetap']) - Sebesar $data['value_asset_tetap']
        // Debet: Akun Pajak ($data['cat_tax']) - Sebesar $data['value_tax'] (jika ada)
        // Kredit: Akun Dikreditkan ($data['cat_asset_credit']) - Sebesar total (value_asset_tetap + value_tax)

        return redirect()->back()->with('message', lang('Global.saved'));
    }
    
    public function updateAsset($id)
    {
        $AssetModel = new \App\Models\AccountancyAssetModel();
        $input = $this->request->getPost();
        
        // Ambil data aset lama untuk cek gambar
        $oldAsset = $AssetModel->find($id);

        if (!$oldAsset) {
            return redirect()->back()->with('error', 'Aset tidak ditemukan.');
        }

        // Menangani File Upload
        $imageFile = $this->request->getFile('image_asset');
        $imageName = $oldAsset['image']; // Tetap gunakan nama lama jika tidak ada upload baru

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            // Hapus file lama jika ada dan berbeda
            if (!empty($oldAsset['image'])) {
                @unlink(ROOTPATH . 'public/uploads/assets/' . $oldAsset['image']);
            }
            $imageName = $imageFile->getRandomName();
            $imageFile->move(ROOTPATH . 'public/uploads/assets', $imageName); // Sesuaikan path jika perlu
        }

        // Validasi
        if (!$this->validate([
            'date'                  => 'required|valid_date',
            'name'                  => 'required|max_length[255]',
            'cat_asset_tetap'       => 'required|integer',
            'value_asset_tetap'     => 'required|numeric|greater_than_equal_to[0]',
            'cat_asset_credit'      => 'required|integer',
            // Tambahkan validasi lain sesuai kebutuhan
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Menyiapkan data update
        $depreciation_status = isset($input['depreciation_status']);

        $updateData = [
            'date'                      => $input['date'],
            'name'                      => $input['name'],
            'description'               => $input['description'] ?? null,
            'cat_asset_tetap'           => $input['cat_asset_tetap'],
            'value_asset_tetap'         => $input['value_asset_tetap'],
            'cat_tax'                   => $input['cat_tax'] ?? null,
            'value_tax'                 => $input['value_tax'] ?? 0,
            'cat_asset_credit'          => $input['cat_asset_credit'],
            'image'                     => $imageName,
            'depreciation_status'       => $depreciation_status ? 1 : 0,
            
            // Data Penyusutan (hanya diisi jika depreciation_status dicentang)
            'depreciation_method'       => $depreciation_status ? ($input['depreciation_method'] ?? 'straight_line') : null,
            'depreciation_residu'       => $depreciation_status ? ($input['depreciation_residu'] ?? 0) : 0,
            'depreciation_benefit_era'  => $depreciation_status ? ($input['depreciation_benefit_era'] ?? 0) : 0,
            'depreciation_cat_penyusutan' => $depreciation_status ? ($input['depreciation_cat_penyusutan'] ?? null) : null,
            'depreciation_sum_cat_penyusutan' => $depreciation_status ? ($input['depreciation_sum_cat_penyusutan'] ?? null) : null,
            
            // Catatan: 'code_asset' dan 'outletid' biasanya tidak diizinkan diubah
            // 'status_lock' mungkin diubah di form lain atau dihapus dari update jika tidak dimaksudkan untuk diubah di sini
        ];

        $AssetModel->update($id, $updateData);

        // Logika Jurnal Akuntansi: Jika nilai perolehan/akun berubah, 
        // perlu dilakukan penyesuaian jurnal (terlalu kompleks untuk disertakan di sini).

        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function deleteAsset($id)
    {
        $AssetModel = new \App\Models\AccountancyAssetModel();
        $asset = $AssetModel->find($id);

        if (!$asset) {
            return redirect()->back()->with('error', 'Aset tidak ditemukan.');
        }

        // Hapus file gambar terkait
        if (!empty($asset['image'])) {
            @unlink(ROOTPATH . 'public/uploads/assets/' . $asset['image']);
        }

        // Catatan: Dalam akuntansi, aset tetap jarang dihapus langsung (hard delete).
        // Biasanya menggunakan 'soft delete' (menandai status_active=0 atau status_deleted=1) 
        // dan mencatat jurnal pelepasan aset.

        $AssetModel->delete($id);

        return redirect()->back()->with('success', 'Data berhasil dihapus');
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

    public function tax()
    {
        // Calling Model
        $AccountancyTaxModel       = new AccountancyTaxModel();
        $AccountancyCOAModel       = new AccountancyCOAModel();

        // Populating data
        $coas1 = $AccountancyCOAModel->findAll();
        $coas2 = $AccountancyCOAModel->findAll();
        // $coas = $AccountancyCOAModel->findAll();
        $taxes = $AccountancyTaxModel->orderBy('name', 'ASC')->findAll();
        
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = 'Pajak - '.lang('Global.accountancyList');
        $data['description']    = 'Pajak '.lang('Global.accountancyListDesc');
        // $data['coas']           = $coas;
        $data['coas1']          = $coas1;
        $data['coas2']          = $coas2;
        $data['taxes']          = $taxes;

        return view('Views/accountancy/tax', $data);
    }

    public function taxCreate()
    {
        $AccountancyTaxModel = new AccountancyTaxModel();

        $data = [
            'name'            => $this->request->getPost('name'),
            'value'           => $this->request->getPost('value'),
            'tax_cut_status'  => $this->request->getPost('tax_cut_status'),
            'tax_cat_sell'    => $this->request->getPost('tax_cut_sell'),
            'tax_cat_buy'     => $this->request->getPost('tax_cut_buy'),
        ];

        $AccountancyTaxModel->insert($data);

        return redirect()->back()->with('success', 'Data pajak berhasil ditambahkan.');
    }

    public function taxDelete($id)
    {
        $AccountancyTaxModel = new AccountancyTaxModel();

        $AccountancyTaxModel->delete($id);

        return redirect()->back()->with('success', 'Data pajak berhasil dihapus.');
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

    public function budgeting()
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

        return view('Views/accountancy/budegeting', $data);
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
        $GroupModel             = new GroupModel();
        $OutletModel            = new OutletModel();
        $OutletAccessModel      = new OutletaccessModel();
        $GroupUserModel         = new GroupUserModel();

        // Populating data
        $this->builder->where('deleted_at', null);
        $this->builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $this->builder->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');
        if ($this->data['role'] === 'supervisor') {
            $this->builder->where('auth_groups.name', 'operator');
        }
        $this->builder->where('users.id !=', $this->data['uid']);
        $this->builder->select('users.id as id, users.username as username, users.firstname as firstname, users.lastname as lastname, users.email as email, users.phone as phone, auth_groups.id as group_id, auth_groups.name as role');
        $query =   $this->builder->get();
        
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = 'Karyawan - '.lang('Global.accountancyList');
        $data['description']    = 'Karyawan '.lang('Global.accountancyListDesc');
        $data['roles']          = $GroupModel->findAll();
        $data['users']          = $query->getResult();
        $data['outlets']        = $OutletModel->findAll();
        $data['outletAccess']   = $OutletAccessModel->findAll();
        $data['groups']         = $GroupUserModel->findAll();

        return view('Views/accountancy/employee', $data);
    }
}
