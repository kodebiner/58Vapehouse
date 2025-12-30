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

        // Calling Model
        $AccountancyCategoryModel   = new AccountancyCategoryModel();
        
        // Populating Data
        $query = $db->table('accountancy_coa AS c')
            ->select('
                c.id, c.name, c.coa_code, c.description, c.status_lock, c.status_active, c.cat_a_id,
                cat.cat_code, cat.name AS category_name, cat.cat_type,
                o.name AS outlet_name
            ')
            ->join('accountancy_categories AS cat', 'cat.id = c.cat_a_id')
            ->join('outlet AS o', 'o.id = c.outletid')
            ->orderBy('cat.cat_code', 'ASC')
            ->orderBy('c.coa_code', 'ASC')
            ->get()
            ->getResultArray();

        $coas = [];
        foreach ($query as $row) {
            $coas[] = [
                'id'            => $row['id'],
                'kode'          => $row['coa_code'],
                'cat_code'      => $row['cat_code'],
                'full_kode'     => $row['cat_code'] . $row['coa_code'],
                'cat_a_id'      => $row['cat_a_id'],
                'category'      => $row['category_name'],
                'coa_type'      => $row['cat_type'],
                'name'          => $row['name'] . ' - ' . str_replace('58 Vapehouse ', '', $row['outlet_name']),
                'description'   => $row['description'],
                'status_lock'   => $row['status_lock'],
                'status_active' => $row['status_active'],
            ];
        }

        $lastCoaMap = [];
        foreach ($query as $row) {
            $lastCoaMap[$row['cat_a_id']] = $row['coa_code'];
        }

        $categorydata   = [];
        $categories = $AccountancyCategoryModel->orderBy('id','ASC')->findAll();
        foreach ($categories as $category) {
            $catId = $category['id'];
            
            $categorydata[] = [
                'id'       => $catId,
                'name'     => $category['name'],
                'cat_code' => $category['cat_code'],
                'cat_type' => $category['cat_type'],
                'coa_code' => $lastCoaMap[$catId] ?? ''
            ];
        }

        // Parsing data to view
        $data                = $this->data;
        $data['coas']        = $coas;
        $data['categories']  = $categorydata;
        $data['title']       = 'Akun (COA) - '.lang('Global.accountancyList');
        $data['description'] = 'Akun (COA) '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/akuncoa', $data);
    }

    public function createAkunCOA()
    {
        $AccountancyCOAModel = new AccountancyCOAModel();
        $OutletModel         = new OutletModel();

        $input = $this->request->getPost();
        
        if (!$this->validate(['name' => "required|max_length[255]", 'category' => "required"])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $outlets = $OutletModel->findAll();
        $targetOutlets = ($this->data['outletPick'] === null) ? $outlets : [['id' => $this->data['outletPick']]];

        $insertedCount = 0;
        $skippedCount  = 0;

        // Ambil basis nomor dari input manual, jika kosong gunakan sistem auto-increment
        $baseCode = !empty($input['coa_code']) ? (int)$input['coa_code'] : null;

        foreach ($targetOutlets as $outlet) {
            $outletId = $outlet['id'];

            // 1. Cek duplikasi nama di outlet yang sama
            $existing = $AccountancyCOAModel->where([
                'name'     => $input['name'],
                'cat_a_id' => $input['category'],
                'outletid' => $outletId
            ])->first();

            if (!$existing) {
                
                // 2. TENTUKAN COA CODE
                if ($baseCode !== null) {
                    // Jika user input manual (misal 001), cari nomor yang tersedia mulai dari 001 dst
                    $currentTrialCode = $baseCode;
                } else {
                    // Jika user kosongkan, cari nomor terakhir di database + 1
                    $lastRecord = $AccountancyCOAModel->where('cat_a_id', $input['category'])
                                                    ->orderBy('coa_code', 'DESC')
                                                    ->first();
                    $currentTrialCode = $lastRecord ? (int)$lastRecord['coa_code'] + 1 : 1;
                }

                // Validasi: Pastikan nomor tidak duplikat di kategori yang sama (Global)
                // Jika nomor sudah ada, cari nomor berikutnya yang kosong
                while ($AccountancyCOAModel->where(['cat_a_id' => $input['category'], 'coa_code' => str_pad($currentTrialCode, 3, '0', STR_PAD_LEFT)])->first()) {
                    $currentTrialCode++;
                }

                $finalCoaCode = str_pad($currentTrialCode, 3, '0', STR_PAD_LEFT);

                // 3. Simpan data
                $data = [
                    'name'          => $input['name'],
                    'cat_a_id'      => $input['category'],
                    'coa_code'      => $finalCoaCode,
                    'outletid'      => $outletId,
                    'description'   => $input['description'],
                    'status_lock'   => isset($input['status_lock']) ? 0 : 1,
                    'status_active' => 1,
                ];

                $AccountancyCOAModel->insert($data);
                
                // Jika outletPick null, maka akun berikutnya harus menggunakan nomor setelahnya
                if ($this->data['outletPick'] === null) {
                    $baseCode = $currentTrialCode + 1;
                }

                $insertedCount++;
            } else {
                $skippedCount++;
            }
        }

        return redirect()->back()->with('message', "Selesai. $insertedCount data dibuat, $skippedCount dilewati.");
    }

    public function updateAkunCOA($id)
    {
        $coaModel = new AccountancyCOAModel();
        $input    = $this->request->getPost();

        $currentData = $coaModel->find($id);
        if (!$currentData) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $existing = $coaModel->where([
            'cat_a_id' => $input['category'],
            'coa_code' => $input['coa_code'],
            'id !='    => $id
        ])->first();

        if ($existing) {
            return redirect()->back()->with('errors', [
                'coa_code' => 'Kode ' . $input['coa_code'] . ' sudah digunakan oleh outlet lain di kategori ini.'
            ]);
        }

        // 3. Eksekusi Update
        $coaModel->update($id, [
            'cat_a_id'      => $input['category'],
            'coa_code'      => $input['coa_code'],
            'name'          => $input['name'],
            'description'   => $input['description'],
            'status_active' => (isset($input['status_active']) && $input['status_active'] == "1") ? 1 : 0,
        ]);

        return redirect()->back()->with('message', 'Data berhasil diperbarui');
    }

    public function deleteAkunCOA($id)
    {
        $coaModel = new AccountancyCOAModel();
        $coa = $coaModel->find($id);
        if (!$coa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        if ($coa['status_lock'] == 1) {
            return redirect()->back()->with('error', 'Akun ini terkunci oleh sistem dan tidak dapat dihapus.');
        }

        /** * 3. (Opsional) Cek Transaksi
         * Di sistem akuntansi, sebaiknya cek apakah ID ini sudah dipakai di tabel Jurnal.
         * if ($this->checkIfUsedInJurnal($id)) { 
         * return redirect()->back()->with('error', 'Gagal dihapus! Akun sudah memiliki riwayat transaksi.'); 
         * }
         */

        $coaModel->delete($id);

        return redirect()->back()->with('message', 'Data berhasil dihapus');
    }

    public function earlyFunds()
    {
        // Calling Model
        $AccountancyCOAModel        = new AccountancyCOAModel();
        $AccountancyCategoryModel   = new AccountancyCategoryModel();

        // Populating date range
        $input = $this->request->getGet('daterange');
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0] . ' 00:00:00';
            $enddate    = $daterange[1] . ' 23:59:59';
        } else {
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }

        // Penyesuaian Query COA
        $builder = $AccountancyCOAModel
            ->select("
                accountancy_coa.*,
                accountancy_categories.cat_code,
                accountancy_categories.name as category_name,
                accountancy_categories.cat_type,
                outlet.name as outlet_name,
                CONCAT(accountancy_categories.cat_code, accountancy_coa.coa_code) AS full_code
            ")
            ->join('accountancy_categories', 'accountancy_categories.id = accountancy_coa.cat_a_id')
            ->join('outlet', 'outlet.id = accountancy_coa.outletid'); // Join ke tabel outlet

        // Tambahkan filter Outlet jika outletPick tidak null (Sedang memilih outlet spesifik)
        if ($this->data['outletPick'] !== null) {
            $builder->where('accountancy_coa.outletid', $this->data['outletPick']);
        }

        $coa_raw = $builder->orderBy('accountancy_categories.cat_code', 'ASC')
                    ->orderBy('accountancy_coa.coa_code', 'ASC')
                    ->findAll();

        // Memproses Nama Akun secara dinamis (Menambahkan nama outlet di belakang)
        foreach ($coa_raw as &$row) {
            // Membersihkan prefix nama toko
            $cleanOutletName = str_replace('58 Vapehouse ', '', $row['outlet_name']);
            
            // Append nama outlet ke nama akun agar user tidak bingung saat view "Semua Outlet"
            $row['name'] = $row['name'] . ' - ' . $cleanOutletName;
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Saldo Awal - '.lang('Global.accountancyList');
        $data['description']    = 'Saldo Awal '.lang('Global.accountancyListDesc');
        $data['coa_list']       = $coa_raw;

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
        $categories = [];
        foreach ($AccountancyCategoryModel->findAll() as $c) {
            $categories[$c['id']] = $c['cat_code'];
        }
        $coas = $AccountancyCOAModel->findAll();
        $filterCoaByCat = function ($catId) use ($coas, $categories, $outlets) {
            $result = [];

            foreach ($coas as $c) {
                if ($c['cat_a_id'] == $catId) {
                    $fullCode = ($categories[$c['cat_a_id']] ?? '') . $c['coa_code'];

                    $result[] = [
                        'id'        => $c['id'],
                        'full_code' => $fullCode,
                        'name'      => $fullCode . ' - ' . $c['name'] . ' - ' . ($outlets[$c['outletid']] ?? ''),
                        'cat_a_id'  => $c['cat_a_id'],
                    ];
                }
            }

            return $result;
        };
        $katHartap       = 5;
        $katPenyusutan   = 14;
        $katDepresiasi   = 6;

        $assets = $AccountancyAssetModel
            ->select("
                accountancy_asset.*,
                
                CASE
                    WHEN accountancy_asset.depreciation_status = 1 THEN 'Ya'
                    ELSE 'Tidak'
                END AS depreciation_status_label,

                CASE
                    WHEN accountancy_asset.depreciation_status = 1 THEN 'Berjalan'
                    ELSE ''
                END AS depreciation_status_text,

                CONCAT(
                    cat_asset.cat_code, coa_asset.coa_code, ' - ',
                    coa_asset.name, ' - ',
                    outlet_asset.name
                ) AS cat_asset_tetap,

                CONCAT(
                    cat_tax.cat_code, coa_tax.coa_code, ' - ',
                    coa_tax.name, ' - ',
                    outlet_tax.name
                ) AS cat_tax,

                CONCAT(
                    cat_credit.cat_code, coa_credit.coa_code, ' - ',
                    coa_credit.name, ' - ',
                    outlet_credit.name
                ) AS cat_asset_credit,

                CONCAT(
                    cat_dep.cat_code, coa_dep.coa_code, ' - ',
                    coa_dep.name, ' - ',
                    outlet_dep.name
                ) AS depreciation_cat_penyusutan,

                CONCAT(
                    cat_sum.cat_code, coa_sum.coa_code, ' - ',
                    coa_sum.name, ' - ',
                    outlet_sum.name
                ) AS depreciation_sum_cat_penyusutan
            ")
            ->join('accountancy_coa AS coa_asset', 'coa_asset.id = accountancy_asset.cat_asset_tetap', 'left')
            ->join('accountancy_categories AS cat_asset', 'cat_asset.id = coa_asset.cat_a_id', 'left')
            ->join('outlet AS outlet_asset', 'outlet_asset.id = coa_asset.outletid', 'left')
            ->join('accountancy_coa AS coa_tax', 'coa_tax.id = accountancy_asset.cat_tax', 'left')
            ->join('accountancy_categories AS cat_tax', 'cat_tax.id = coa_tax.cat_a_id', 'left')
            ->join('outlet AS outlet_tax', 'outlet_tax.id = coa_tax.outletid', 'left')
            ->join('accountancy_coa AS coa_credit', 'coa_credit.id = accountancy_asset.cat_asset_credit', 'left')
            ->join('accountancy_categories AS cat_credit', 'cat_credit.id = coa_credit.cat_a_id', 'left')
            ->join('outlet AS outlet_credit', 'outlet_credit.id = coa_credit.outletid', 'left')
            ->join('accountancy_coa AS coa_dep', 'coa_dep.id = accountancy_asset.depreciation_cat_penyusutan', 'left')
            ->join('accountancy_categories AS cat_dep', 'cat_dep.id = coa_dep.cat_a_id', 'left')
            ->join('outlet AS outlet_dep', 'outlet_dep.id = coa_dep.outletid', 'left')
            ->join('accountancy_coa AS coa_sum', 'coa_sum.id = accountancy_asset.depreciation_sum_cat_penyusutan', 'left')
            ->join('accountancy_categories AS cat_sum', 'cat_sum.id = coa_sum.cat_a_id', 'left')
            ->join('outlet AS outlet_sum', 'outlet_sum.id = coa_sum.outletid', 'left')
            ->orderBy('accountancy_asset.date', 'DESC')
            ->findAll();

            foreach ($assets as &$asset) {
                $asset['nilai_buku']            = $this->hitungNilaiBuku($asset);
                // $asset['akumulasi_penyusutan']  = ((float)$asset['value_asset_tetap'] - (float)$asset['value_tax']) - $asset['nilai_buku'];
                $asset['akumulasi_penyusutan']  = (float)$asset['value_asset_tetap'] - $asset['nilai_buku'];
                $asset['journals'] = $this->generateAssetJournals($asset);
            }
            unset($asset);
        
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = 'Asset - ' . lang('Global.accountancyList');
        $data['description']    = 'Asset ' . lang('Global.accountancyListDesc');
        $data['assets']         = $assets;
        $data['allcoas'] = array_map(function ($c) use ($categories, $outlets) {
            $fullCode = ($categories[$c['cat_a_id']] ?? '') . $c['coa_code'];

            return [
                'id'        => $c['id'],
                'full_code' => $fullCode,
                'name'      => $fullCode . ' - ' . $c['name'] . ' - ' . ($outlets[$c['outletid']] ?? ''),
                'cat_a_id'  => $c['cat_a_id'],
            ];
        }, $coas);
        $data['coahartaps']     = $filterCoaByCat($katHartap);
        $data['weights']        = $filterCoaByCat($katPenyusutan);
        $data['coadepreciation']= $filterCoaByCat($katDepresiasi);
        $data['taxes']          = $AccountancyTaxModel->findAll();

        return view('Views/accountancy/asset', $data);
    }

    private function hitungNilaiBuku(array $asset): float
    {
        // Jika tidak disusutkan
        if ((int)$asset['depreciation_status'] !== 1) {
            // return (float)$asset['value_asset_tetap'] - (float)$asset['value_tax'];
            return (float)$asset['value_asset_tetap'];
        }

        // $biayaPerolehan = (float)$asset['value_asset_tetap'] - (float)$asset['value_tax'];
        $biayaPerolehan = (float)$asset['value_asset_tetap'];
        $umurManfaat    = (int)$asset['depreciation_benefit_era']; // bulan

        if ($umurManfaat <= 0) {
            return $biayaPerolehan;
        }

        $tanggalAkuisisi = new \DateTime($asset['date']);
        $hariIni         = new \DateTime();

        $diff = $tanggalAkuisisi->diff($hariIni);
        $bulanBerjalan = ($diff->y * 12) + $diff->m;

        // Maksimal sampai umur manfaat
        $bulanBerjalan = min($bulanBerjalan, $umurManfaat);

        $depresiasiBulanan = $biayaPerolehan / $umurManfaat;
        $akumulasi         = $depresiasiBulanan * $bulanBerjalan;

        return max(0, $biayaPerolehan - $akumulasi);
    }

    private function generateAssetJournals(array $asset): array
    {
        $journals = [];

        /** =====================
         * JURNAL AKUISISI
         * ===================== */
        $journals[] = [
            'tanggal'       => $asset['date'],
            'transaksi'     => 'Beli Aset',
            'kode'          => $asset['code_asset'],
            'akun_debit'    => $asset['cat_asset_tetap'],
            'akun_kredit'   => $asset['cat_asset_credit'],
            'nilai'         => (float)$asset['value_asset_tetap'],
            'catatan'       => $asset['name']
        ];

        /** =====================
         * JURNAL PENYUSUTAN
         * (ditampilkan terbaru → terlama)
         * ===================== */
        if ((int)$asset['depreciation_status'] === 1) {

            $depreciationJournals = [];

            $biayaPerolehan = (float)$asset['value_asset_tetap'];
            $umurManfaat    = max(1, (int)$asset['depreciation_benefit_era']);
            $nilaiBulanan   = $biayaPerolehan / $umurManfaat;

            $tanggalAkuisisi   = new \DateTime($asset['date']);
            $tanggalSekarang   = new \DateTime(date('Y-m-01'));
            $tanggalPenyusutan = (clone $tanggalAkuisisi)->modify('first day of next month');

            $bulanKe = 1;

            while (
                $tanggalPenyusutan <= $tanggalSekarang &&
                $bulanKe <= $umurManfaat
            ) {
                $depreciationJournals[] = [
                    'tanggal'       => $tanggalPenyusutan->format('Y-m-d'),
                    'transaksi'     => 'Penyusutan',
                    'kode'          => '',
                    'akun_debit'    => $asset['depreciation_cat_penyusutan'],
                    'akun_kredit'   => $asset['depreciation_sum_cat_penyusutan'],
                    'nilai'         => $nilaiBulanan,
                    'catatan'       => 'Penyusutan bulan ke-' . $bulanKe
                ];

                $tanggalPenyusutan->modify('+1 month');
                $bulanKe++;
            }

            // ⬅️ ini kuncinya
            $journals = array_merge(
                $journals,
                array_reverse($depreciationJournals)
            );
        }

        return $journals;
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
        $AccountancyCategoryModel  = new AccountancyCategoryModel();
        $OutletModel               = new OutletModel();

        // Populating data
        $cleanOutlet = function($name) {
            return preg_replace('/^58 Vapehouse\s*/i', '', $name);
        };
        $outlets = [];
        foreach (($OutletModel)->findAll() as $o) {
            $outlets[$o['id']] = $cleanOutlet($o['name']);
        }
        $categories = [];
        foreach ($AccountancyCategoryModel->findAll() as $c) {
            $categories[$c['id']] = $c['cat_code'];
        }
        $coas = $AccountancyCOAModel->findAll();
        $filterCoaByCat = function (array $catIds) use ($coas, $categories, $outlets) {
            $result = [];
            foreach ($coas as $c) {
                $catId = (int) $c['cat_a_id'];
                if (!isset($categories[$catId])) {
                    continue;
                }

                if (in_array($catId, $catIds, true)) {
                    $fullCode = $categories[$catId] . $c['coa_code'];
                    $result[] = [
                        'id'        => $c['id'],
                        'full_code' => $fullCode,
                        'name'      => $fullCode . ' - ' . $c['name'] . ' - ' . ($outlets[$c['outletid']] ?? ''),
                        'cat_a_id'  => $catId,
                    ];
                }
            }
            return $result;
        };
        $cat1       = [8,9,12,14,15,16];
        $cat2       = [2,3,4,12,14,15,16];
        $taxes = $AccountancyTaxModel
            ->select("
                accountancy_tax.*,

                CONCAT(
                    cat_sell.cat_code, sell_coa.coa_code,
                    ' - ', sell_coa.name,
                    ' - ', outlet_sell.name
                ) AS tax_cut_sell,

                CONCAT(
                    cat_buy.cat_code, buy_coa.coa_code,
                    ' - ', buy_coa.name,
                    ' - ', outlet_buy.name
                ) AS tax_cut_buy,

                CASE 
                    WHEN accountancy_tax.tax_cut_status = 1
                    THEN CONCAT('-', accountancy_tax.value, '%')
                    ELSE CONCAT(accountancy_tax.value, '%')
                END AS value
            ")
            ->join('accountancy_coa AS sell_coa', 'sell_coa.id = accountancy_tax.tax_cat_sell', 'left')
            ->join('accountancy_coa AS buy_coa',  'buy_coa.id  = accountancy_tax.tax_cat_buy',  'left')
            ->join('accountancy_categories AS cat_sell', 'cat_sell.id = sell_coa.cat_a_id', 'left')
            ->join('accountancy_categories AS cat_buy',  'cat_buy.id  = buy_coa.cat_a_id',  'left')
            ->join('outlet AS outlet_sell', 'outlet_sell.id = sell_coa.outletid', 'left')
            ->join('outlet AS outlet_buy',  'outlet_buy.id  = buy_coa.outletid',  'left')
            ->orderBy('accountancy_tax.name', 'ASC')
            ->findAll();
        
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = 'Pajak - '.lang('Global.accountancyList');
        $data['description']    = 'Pajak '.lang('Global.accountancyListDesc');
        $data['coas1']          = $filterCoaByCat($cat1);
        $data['coas2']          = $filterCoaByCat($cat2);
        $data['taxes']          = $taxes;

        return view('Views/accountancy/tax', $data);
    }

    public function taxCreate()
    {
        $AccountancyTaxModel = new AccountancyTaxModel();

        $data = [
            'name'            => $this->request->getPost('name'),
            'value'           => (float) $this->request->getPost('value'),
            'tax_cut_status'  => (int) $this->request->getPost('tax_cut_status'),
            'tax_cat_sell'    => $this->request->getPost('tax_cut_sell'),
            'tax_cat_buy'     => $this->request->getPost('tax_cut_buy'),
        ];

        $AccountancyTaxModel->insert($data);

        return redirect()->back()->with('success', 'Data pajak berhasil ditambahkan.');
    }

    public function taxDelete($id)
    {
        $taxModel = new AccountancyTaxModel();

        if (!$taxModel->find($id)) {
            return redirect()->back()->with('error', 'Data pajak tidak ditemukan.');
        }
        $taxModel->delete($id);

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
