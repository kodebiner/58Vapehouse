<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\CashModel;
use App\Models\GconfigModel;
use App\Models\OutletModel;
use App\Models\UserModel;
use App\Models\MemberModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\VariantModel;
use App\Models\TransactionModel;
use App\Models\TrxdetailModel;
use App\Models\TrxotherModel;
use App\Models\TrxpaymentModel;
use App\Models\DebtModel;
use App\Models\DailyReportModel;

class Debt extends BaseController
{
    public function indextrx()
    {
        $db         = \Config\Database::connect();
        $pager      = \Config\Services::pager();

        // Calling Models
        $BundleModel            = new BundleModel;
        $BundledetModel         = new BundledetailModel;
        $CashModel              = new CashModel;
        $OutletModel            = new OutletModel;
        $UserModel              = new UserModel;
        $MemberModel            = new MemberModel;
        $PaymentModel           = new PaymentModel;
        $ProductModel           = new ProductModel;
        $VariantModel           = new VariantModel;
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $TrxpaymentModel        = new TrxpaymentModel;
        $DebtModel              = new DebtModel;

        $input  = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }

        // Populating Data
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->orderBy('date', 'DESC')->paginate(20, 'trxhistory');

            if (!empty($input)) {
                $transactions = $TransactionModel->orderBy('date', 'DESC')->where('date >=', $startdate)->where('date <=', $enddate)->paginate(20, 'trxhistory');
            }
        } else {
            $transactions = $TransactionModel->orderBy('date', 'DESC')->where('outletid', $this->data['outletPick'])->paginate(20, 'trxhistory');

            if (!empty($input)) {
                $transactions = $TransactionModel->orderBy('date', 'DESC')->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->paginate(20, 'trxhistory');
            }
        }

        $trxid = array();
        $memberid = array();
        foreach ($transactions as $transaction) {
            $trxid[] = $transaction['id'];
            if ($transaction['memberid'] != '0') {
                $memberid[] = $transaction['memberid'];
            }
        }

        $bundles                = $BundleModel->findAll();
        $bundets                = $BundledetModel->findAll();
        $cash                   = $CashModel->findAll();
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();
        $payments               = $PaymentModel->findAll();

        if (!empty($memberid)) {
            $customers              = $MemberModel->find($memberid);
        } else {
            $customers              = array();
        }

        if (!empty($trxid)) {
            $trxdetails             = $TrxdetailModel->whereIn('transactionid', $trxid)->find();
            $trxpayments            = $TrxpaymentModel->whereIn('transactionid', $trxid)->find();
            $debts                  = $DebtModel->whereIn('transactionid', $trxid)->find();
            $variantid = array();
            foreach ($trxdetails as $trxdetail) {
                $variantid[] = $trxdetail['variantid'];
            }
            $productbuilder         = $db->table('variant');
            $productarray           = $productbuilder->select('product.name as product, variant.name as variant, variant.id as id');
            $productarray           = $productbuilder->join('product', 'variant.productid = product.id', 'left');
            $productarray           = $productbuilder->whereIn('variant.id', $variantid);
            $productarray           = $productbuilder->get();
            $productsresult         = $productarray->getResult();
            $products = array();
            foreach ($productsresult as $prod) {
                $products[] = [
                    'id'    => $prod->id,
                    'name'  => $prod->product . ' - ' . $prod->variant
                ];
            }
        } else {
            $trxdetails             = array();
            $trxpayments            = array();
            $debts                  = array();
            $products               = array();
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.trxHistory');
        $data['description']    = lang('Global.trxHistoryListDesc');
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['cash']           = $cash;
        $data['users']          = $users;
        $data['transactions']   = $transactions;
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['trxdetails']     = $trxdetails;
        $data['trxpayments']    = $trxpayments;
        $data['debts']          = $debts;
        $data['pager']          = $TransactionModel->pager;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/trxhistory', $data);
    }

    public function indexdebt()
    {
        $pager      = \Config\Services::pager();

        // Calling Models
        $OutletModel            = new OutletModel;
        $MemberModel            = new MemberModel;
        $TransactionModel       = new TransactionModel;
        $DebtModel              = new DebtModel;

        // Populating Data
        $outlets                = $OutletModel->findAll();

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }

        // Populating Data
        if (!empty($input)) {
            $debts = $DebtModel->orderBy('deadline', 'DESC')->where('value !=', '0')->where('deadline >=', $startdate)->where('deadline <=', $enddate)->paginate(20, 'debt');
        } else {
            $debts = $DebtModel->orderBy('deadline', 'DESC')->where('value !=', '0')->paginate(20, 'debt');
        }

        $trxid      = array();
        $memberid   = array();
        foreach ($debts as $debt) {
            $trxid[]    = $debt['transactionid'];
            $memberid[] = $debt['memberid'];
        }

        if (!empty($trxid)) {
            $transactions           = $TransactionModel->find($trxid);
        } else {
            $transactions           = array();
        }

        if (!empty($memberid)) {
            $customers              = $MemberModel->find($memberid);
        } else {
            $customers              = array();
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.debt');
        $data['description']    = lang('Global.debtListDesc');
        $data['transactions']   = $transactions;
        $data['outlets']        = $outlets;
        $data['customers']      = $customers;
        $data['debts']          = $debts;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['pager']          = $DebtModel->pager;

        return view('Views/debt', $data);
    }

    public function paydebt($id)
    {
        // Validate Data
        $validation = \Config\Services::validation();

        // Calling Models
        $DebtModel              = new DebtModel;
        $CashModel              = new CashModel;
        $TransactionModel       = new TransactionModel;
        $TrxotherModel          = new TrxotherModel;
        $PaymentModel           = new PaymentModel;
        $MemberModel            = new MemberModel;
        $DailyReportModel       = new DailyReportModel;

        // Populating Data
        $debts                  = $DebtModel->find($id);
        $cash                   = $CashModel->like('name', 'Cash')->where('outletid', $this->data['outletPick'])->first();
        $payments               = $PaymentModel->like('name', 'Cash')->where('cashid', $cash['id'])->where('outletid', $this->data['outletPick'])->first();
        $customers              = $MemberModel->where('id', $debts['memberid'])->first();

        // Date Time Stamp
        $date                   = date_create();
        $tanggal                = date_format($date, 'Y-m-d H:i:s');

        // Initialize
        $input = $this->request->getPost();

        if ($debts['value'] - $input['value'] != "0") {
            $data = [
                'id'            => $id,
                'value'         => $debts['value'] - $input['value'],
                'deadline'      => $input['duedate' . $id],
            ];
        } else {
            $data = [
                'id'            => $id,
                'value'         => $debts['value'] - $input['value'],
                'deadline'      => NULL,
            ];
        }

        // Save Data Debt
        $DebtModel->save($data);

        // Image Capture
        $img                    = $input['image'];
        $folderPath             = "img/tfproof/";
        $image_parts            = explode(";base64,", $img);
        $image_type_aux         = explode("image/", $image_parts[0]);
        $image_type             = $image_type_aux[1];
        $image_base64           = base64_decode($image_parts[1]);
        $fileName               = uniqid() . '.png';
        $file                   = $folderPath . $fileName;
        file_put_contents($file, $image_base64);

        // Trx Other Cash In
        $cashin = [
            'userid'        => $this->data['uid'],
            'outletid'      => $this->data['outletPick'],
            'cashid'        => $cash['id'],
            'description'   => "Debt - " . $customers['name'] . '/' . $customers['phone'],
            'type'          => "0",
            'date'          => $tanggal,
            'qty'           => $input['value'],
            'photo'         => $fileName,
        ];
        $TrxotherModel->save($cashin);

        // Input Value to cash
        $wallet = [
            'id'    => $cash['id'],
            'qty'   => $input['value'] + $cash['qty'],
        ];
        $CashModel->save($wallet);

        // Find Data for Daily Report
        $today                  = date('Y-m-d') . ' 00:00:01';
        $dailyreports           = $DailyReportModel->where('outletid', $this->data['outletPick'])->where('dateopen >', $today)->find();
        foreach ($dailyreports as $dayrep) {
            $tcashin = [
                'id'            => $dayrep['id'],
                'totalcashin'   => $dayrep['totalcashin'] + $input['value'],
            ];
            $DailyReportModel->save($tcashin);
        }

        // Return
        return redirect()->back()->with('massage', lang('global.saved'));
    }

    public function indextopup()
    {
        $pager      = \Config\Services::pager();

        // Calling Models
        $OutletModel            = new OutletModel;
        $TrxotherModel          = new TrxotherModel;

        // Populating Data
        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }

        if ($this->data['outletPick'] === null) {
            $trxothers      = $TrxotherModel->orderBy('id', 'DESC')->like('description', 'Top Up')->paginate(20, 'topup');
            if (!empty($input)) {
                $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Top Up')->paginate(20, 'topup');
            }
        } else {
            $trxothers      = $TrxotherModel->where('outletid', $this->data['outletPick'])->orderBy('id', 'DESC')->like('description', 'Top Up')->paginate(20, 'topup');

            if (!empty($input)) {
                $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Top Up')->where('outletid', $this->data['outletPick'])->paginate(20, 'topup');
            }
        }

        $outlets                = $OutletModel->findAll();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.topup');
        $data['description']    = lang('Global.topupListDesc');
        $data['outlets']        = $outlets;
        $data['trxothers']      = $trxothers;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['pager']          = $TrxotherModel->pager;

        return view('Views/topup', $data);
    }

    public function indexdebtins()
    {
        $pager      = \Config\Services::pager();

        // Calling Model
        $TrxotherModel      = new TrxotherModel;
        $OutletModel        = new OutletModel;

        // Find Data
        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }

        if ($this->data['outletPick'] === null) {
            $trxothers      = $TrxotherModel->orderBy('id', 'DESC')->like('description', 'Debt')->paginate(20, 'debtpay');

            if (!empty($input)) {
                $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Debt')->paginate(20, 'debtpay');
            }
        } else {
            $trxothers      = $TrxotherModel->orderBy('id', 'DESC')->like('description', 'Debt')->where('outletid', $this->data['outletPick'])->paginate(20, 'debtpay');

            if (!empty($input)) {
                $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Debt')->where('outletid', $this->data['outletPick'])->paginate(20, 'debtpay');
            }
        }

        $outlets            = $OutletModel->findAll();

        // Parsing data to view
        $data                       = $this->data;
        $data['title']              = lang('Global.debtInstallments');
        $data['description']        = lang('Global.debtInstallmentsListDesc');
        $data['trxothers']          = $trxothers;
        $data['outlets']            = $outlets;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);
        $data['pager']              = $TrxotherModel->pager;

        return view('Views/debtpay', $data);
    }

    public function create()
    {
        // Calling Models
        $CashModel      = new CashModel;
        $CashmoveModel  = new CashmovementModel;

        // Populating data
        $Cash        =  $CashModel->findAll();

        // initialize
        $input          = $this->request->getPost();

        // save data
        $data = [
            'description'       => $input['description'],
            'origin'            => $input['origin'],
            'destination'       => $input['destination'],
            'qty'               => $input['qty'],
            'date'              => date("Y-m-d H:i:s"),

        ];

        // validation
        if (!$this->validate([
            'description'       =>  "required|max_length[255]',",
            'qty'               =>  "required"
        ])) {

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Inserting Cash Movement
        $CashmoveModel->insert($data);

        // insert minus qty origin
        $cashmin    = $CashModel->where('id', $input['origin'])->first();
        $cashqty    = $cashmin['qty'] - $input['qty'];

        $quantity = [
            'id'    => $cashmin['id'],
            'qty'   => $cashqty,
        ];

        $CashModel->save($quantity);

        // insert plus qty origin
        $cashplus    = $CashModel->where('id', $input['destination'])->first();
        $cashqty    = $cashplus['qty'] + $input['qty'];

        $quant = [
            'id'    => $cashplus['id'],
            'qty'   => $cashqty,
        ];

        $CashModel->save($quant);

        return redirect()->back()->with('message', lang('Global.saved'));
    }


    public function refund($id)
    {
        // Conneting To Database
        $db = \Config\Database::connect();

        // Getting Data Transaction
        $exported   = $db->table('transaction');

        $transactionhist   = $exported->select('transaction.id as id, variant.id as varid, member.id as memberid, users.id as userid, payment.id as paymentid,
        outlet.id as outletid, bundle.id as bundleid,trxdetail.qty as qty, transaction.value as total, bundle.price as bprice, variant.hargadasar as vprice,
        transaction.date as date, transaction.disctype as disctype, transaction.discvalue as discval,
        transaction.pointused as redempoin, member.name as member, product.name as product, variant.name as variant,  
        variant.hargamodal as modal, variant.hargajual as jual, trxdetail.value as trxdetval, trxdetail.discvar as discvar, payment.name as payment,
        outlet.name as outlet,outlet.address as address, bundle.name as bundle, users.username as kasir');

        $transactionhist   = $exported->join('trxdetail', 'transaction.id = trxdetail.transactionid', 'left');
        $transactionhist   = $exported->join('users', 'transaction.userid = users.id', 'left');
        $transactionhist   = $exported->join('outlet', 'transaction.outletid = outlet.id', 'left');
        $transactionhist   = $exported->join('member', 'transaction.memberid = member.id', 'left');
        $transactionhist   = $exported->join('trxpayment', 'trxdetail.transactionid = trxpayment.transactionid', 'left');
        $transactionhist   = $exported->join('bundle', 'trxdetail.bundleid = bundle.id', 'left');
        $transactionhist   = $exported->join('variant', 'trxdetail.variantid = variant.id', 'left');
        $transactionhist   = $exported->join('payment', 'trxpayment.paymentid = payment.id', 'left');
        $transactionhist   = $exported->join('product', 'variant.productid = product.id', 'left');
        $transactionhist   = $exported->where('transaction.outletid', $this->data['outletPick']);
        $transactionhist   = $exported->where('transaction.id', $id);
        $transactionhist   = $exported->get();
        $transactionhist   = $transactionhist->getResultArray();

        $bundleBuilder          = $db->table('bundledetail');
        $bundleVariants         = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as varid, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants         = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants         = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants         = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants         = $bundleBuilder->get();
        $bundleVariants         = $bundleVariants->getResultArray();

        $exported   = $db->table('stock');
        $stockexp   = $exported->select('stock.qty as qty, variant.hargamodal as hargamodal, variant.hargadasar as hargadasar, variant.hargajual as hargajual, variant.hargarekomendasi as hargarekomendasi, variant.name as varname, product.name as prodname, category.name as catname, brand.name as brandname');
        $stockexp   = $exported->join('variant', 'stock.variantid = variant.id', 'left');
        $stockexp   = $exported->join('product', 'variant.productid = product.id', 'left');
        $stockexp   = $exported->join('category', 'product.catid = category.id', 'left');
        $stockexp   = $exported->join('brand', 'product.brandid = brand.id', 'left');
        $stockexp   = $exported->where('stock.outletid', $this->data['outletPick']);
        $stockexp   = $exported->orderBy('product.name', 'ASC');
        $stockexp   = $exported->get();
        $productval = $stockexp->getResultArray();

        $trxdata = array();
        foreach ($transactionhist as $trxhist) {

            if ((!empty($trxhist['discval'])) && ($trxhist['disctype'] === '0')) {
                $discount = $trxhist['discval'];
                $disctype = "0";
            } elseif ((!empty($trxhist['discval'])) && ($trxhist['disctype'] === '1')) {
                $discount = ($trxhist['trxdetval'] * $trxhist['discval'] / 100);
            } else {
                $discount = 0;
            }

            if ($trxhist['disctype'] === '1') {
                $disctype = "%";
            } else {
                $disctype = "Rp";
            }

            if (!empty($trxhist['member'])) {
                $membername = $trxhist['member'];
            } else {
                $membername = "Non Member";
            }

            if (!empty($trxhist['product'])) {
                $product = $trxhist['product'];
            } else {
                $product = $trxhist['bundle'];
            }

            if (!empty($trxhist['discvar'])) {
                $discvar = $trxhist['discvar'];
            } else {
                $discvar = "0";
            }

            $trxdata[] = [
                'kode'          => date(strtotime($trxhist['date'])),
                'tanggal'       => date('l, d M Y', strtotime($trxhist['date'])),
                'jam'           => date('H:i:s', strtotime($trxhist['date'])),
                'kasir'         => $trxhist['kasir'],
                'pembeli'       => $trxhist['member'],
                'produk'        => $trxhist['product'],
                'quantity'      => $trxhist['qty'],
                'hargapro'      => $trxhist['trxdetval'] + $trxhist['discvar'],
                'subtotal'      => ($trxhist['trxdetval'] + $trxhist['discvar']) * $trxhist['qty'],
                // 'subtotal'      => $trxhist['total'] + $discount + $trxhist['discvar'] + $trxhist['redempoin'],
                'diskonpro'     => $trxhist['discval'],
                'typedisc'      => $disctype,
                'redempoin'     => $trxhist['redempoin'],
                'total'         => $trxhist['total'],
                'payment'       => $trxhist['payment'],

            ];
        }

        // dd($bundleVariants);

        /*========================== REFUND DATA ===========================*/

        // Calling Models
        $BundleModel            = new BundleModel();
        $BundledetModel         = new BundledetailModel();
        $CashModel              = new CashModel();
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $MemberModel            = new MemberModel();
        $PaymentModel           = new PaymentModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $StockModel             = new StockModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
        $DailyReportModel       = new DailyReportModel();

        // Populating Data
        $bundles                = $BundleModel->findAll();
        $bundets                = $BundledetModel->findAll();
        $Cash                   = $CashModel->findAll();
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();
        $customers              = $MemberModel->findAll();
        $payments               = $PaymentModel->findAll();
        $products               = $ProductModel->orderBy('name', 'ASC')->findAll();
        $variants               = $VariantModel->findAll();
        $stocks                 = $StockModel->findAll();
        $transactions           = $TransactionModel->findAll();
        $trxdetails             = $TrxdetailModel->findAll();
        $trxpayments            = $TrxpaymentModel->findAll();
        $bookings               = $BookingModel->where('status', '0')->orderBy('created_at', 'DESC')->findAll();
        $bookingdetails         = $BookingdetailModel->findAll();

        // initialize
        $input = $this->request->getPost();

        // date time stamp
        $date = date_create();
        $tanggal = date_format($date, 'Y-m-d H:i:s');

        $variant = [];
        foreach ($transactionhist as $trxhist) {
            if (!empty($trxhist['paymentid'])) {

                // save variants item
                if (!empty($trxhist["varid"]) && $trxhist["varid"] != null) {

                    $varId = $trxhist["varid"];
                    $qty  = $trxhist["qty"];
                    $varPrice = ($trxhist["vprice"] + $trxhist['jual']) * $trxhist["qty"];

                    // Minus Stock
                    $stok = $StockModel->where('variantid', $varId)->where('outletid', $this->data['outletPick'])->first();
                    $newStock = $stok['qty'] + $qty;
                    $data = [
                        'id' => $stok['id'],
                        'qty' => $newStock,
                    ];
                    dd($data);
                    $StockModel->save($data);
                } else {
                    $varPrice = "0";
                }

                // save bundle item
                if (!empty($trxhist['bundleid']) && $trxhist["bundleid"] != null) {

                    $bundles = $trxhist['qty'];
                    $bundId = $trxhist["bundleid"];
                    $qty    = $trxhist["qty"];
                    $bunPrice =  $trxhist["bprice"];

                    // minus stock
                    $bundet = $BundledetModel->where('bundleid', $bundId)->find();
                    foreach ($bundet as $bun => $val) {
                        $bunid = $val['bundleid'];
                        $varid = $val['variantid'];
                        foreach ($stocks as $stock) {
                            $stock = $StockModel->where('variantid', $varid)->where('outletid', $this->data['outletPick'])->first();
                            $newStock = $stock['qty'] + $qty;
                            $stok = [
                                'id' => $stock['id'],
                                'qty' => $newStock,
                            ];
                            $StockModel->save($stok);
                        }
                    }
                } else {
                    $bunPrice = "0";
                }


                //Redem Point Member
                if (!empty($trxhist['memberid'])) {
                    $discPoint   = $trxhist['redempoin'];
                    $member      = $MemberModel->where('id', $trxhist['memberid'])->first();
                    $memberPoint = $member['poin'];
                    // Used Poin 
                    if (!empty($trxhist['redempoin'])) {
                        $point       = $memberPoint + $discPoint;
                    } else {
                        // Not Apply Point
                        $point  = $memberPoint;
                    }
                }

                //Plus Member Point
                $data = [
                    'id' => $member['id'],
                    'poin' => $point,
                ];
                $MemberModel->save($data);

                // Refund Cash
                $cashPlus   = $CashModel->where('id', $trxhist['paymentid'])->first();
                $cashUpdate = $varPrice + $bunPrice + $cashPlus['qty'];
                $data = [
                    'id'    => $cashPlus['id'],
                    'qty'   => $cashUpdate,
                ];
                $CashModel->save($data);
            } else {

                // Variants Value
                if (!empty($trxhist["qty"])) {
                    $variant = $trxhist["qty"];
                    foreach ($variant as $vId => $val) {
                        $varId = $vId;
                        $qty  = $val;
                    }
                    $value = $VariantModel->where('id', $vId)->first();
                    $price = $value['hargamodal'] + $value['hargajual'];
                    $varPrice = $price * $qty;
                } else {
                    $varPrice = "0";
                }

                // Bundle Value
                if (!empty($trxhist['bqty'])) {
                    $bundles = $trxhist['bqty'];
                    foreach ($bundles as $y => $value) {
                        $bundId = $y;
                        $qty    = $value;
                    }
                    $value = $BundleModel->where('id', $bundId)->first();
                    $price = $value['price'];
                    $bunPrice = $price * $qty;
                } else {
                    $bunPrice = "0";
                }

                $totalValue = $varPrice + $bunPrice;

                // Insert Data
                $data = [
                    'outletid'  => $this->data['outletPick'],
                    'userid'    => $this->data['uid'],
                    'memberid'  => $trxhist['customerid'],
                    'paymentid' => "0",
                    'value'     => $totalValue,
                    'disctype'  => $trxhist['disctype'],
                    'discvalue' => $trxhist['discvalue'],
                    'date'      => $tanggal,
                ];
                // save data transaction
                $TransactionModel->save($data);

                // transaction id
                $trxId = $TransactionModel->getInsertID();

                // variants item
                if (!empty($trxhist["qty"])) {
                    $variant = $trxhist["qty"];
                    foreach ($variant as $vId => $val) {
                        $varId = $vId;
                        $qty  = $val;
                    }
                    $value = $VariantModel->where('id', $vId)->first();
                    $price = $value['hargamodal'] + $value['hargajual'];
                    $fprice = $price * $qty;
                    // save transaction detail
                    $data = [
                        'transactionid' => $trxId,
                        'variantid'     => $varId,
                        'bundleid'      => "0",
                        'qty'           => $qty,
                        // 'description'   => $trxhist['description'],
                        'value'         => $fprice,
                    ];
                    $TrxdetailModel->save($data);

                    // Minus Stock
                    $stok = $StockModel->where('variantid', $varId)->where('outletid', $this->data['outletPick'])->first();
                    $newStock = $stok['qty'] - $qty;
                    $data = [
                        'id' => $stok['id'],
                        'qty' => $newStock,
                    ];
                    $StockModel->save($data);
                }

                // bundle item
                if (!empty($trxhist['bqty'])) {
                    $bundles = $trxhist['bqty'];
                    foreach ($bundles as $y => $value) {
                        $bundId = $y;
                        $qty    = $value;
                    }
                    $value = $BundleModel->where('id', $bundId)->first();
                    $price = $value['price'];
                    $fprice = $price * $qty;
                    // save transaction detail
                    $data = [
                        'transactionid' => $trxId,
                        'variantid'     => "0",
                        'bundleid'      => $y,
                        'qty'           => $qty,
                        'value'         => $fprice,
                    ];
                    $TrxdetailModel->save($data);

                    // minus stock
                    $bundet = $BundledetModel->where('bundleid', $y)->find();
                    foreach ($bundet as $bun => $val) {
                        $bunid = $val['bundleid'];
                        $varid = $val['variantid'];
                        foreach ($stocks as $stock) {
                            $stock = $StockModel->where('variantid', $varid)->where('outletid', $this->data['outletPick'])->first();
                            $newStock = $stock['qty'] - $qty;
                            $stok = [
                                'id' => $stock['id'],
                                'qty' => $newStock,
                            ];
                            $StockModel->save($stok);
                        }
                    }
                }

                //Discount Price
                if (!empty($trxhist['discvalue'])) {
                    if ($trxhist['disctype'] === "0") {
                        $discPrice = $trxhist['discvalue'];
                    } else {
                        //Discount Percent 
                        $sumPrice   = $varPrice + $bunPrice;
                        $discPrice   = ($sumPrice * $trxhist['discvalue']) / 100;
                    }
                } else {
                    $discPrice = "0";
                }

                //Discount Point Member
                if (!empty($trxhist['customerid'])) {
                    $discPoint   = $trxhist['poin'];
                    $member      = $MemberModel->where('id', $trxhist['customerid'])->first();
                    $memberPoint = $member['poin'];
                    // Used Poin 
                    if (!empty($trxhist['poin'])) {
                        $point       = $memberPoint - $discPoint;
                    } else {
                        // Not Apply Point
                        $point  = $memberPoint;
                    }
                }

                //Minus Member Point
                $data = [
                    'id' => $member['id'],
                    'poin' => $point,
                ];
                $MemberModel->save($data);

                //Insert First Trx Payment 
                $data = [
                    'paymentid'     => $trxhist['firstpayment'],
                    'transactionid' => $trxId,
                    'value'         => $trxhist['firstpay']
                ];
                $TrxpaymentModel->save($data);

                //Insert Second Trx Payment 
                $data = [
                    'paymentid'     => $trxhist['secondpayment'],
                    'transactionid' => $trxId,
                    'value'         => $trxhist['secpay']
                ];
                $TrxpaymentModel->save($data);

                // Insert First Cash 
                $cashPlus   = $CashModel->where('id', $trxhist['firstpayment'])->first();
                $cashUpdate = $trxhist['firstpay'] + $cashPlus['qty'];
                $data = [
                    'id'    => $cashPlus['id'],
                    'qty'   => $cashUpdate,
                ];
                $CashModel->save($data);

                // Insert Second Cash 
                $cashPlus   = $CashModel->where('id', $trxhist['secondpayment'])->first();
                $cashUpdate = $trxhist['secpay'] + $cashPlus['qty'];
                $data = [
                    'id'    => $cashPlus['id'],
                    'qty'   => $cashUpdate,
                ];
                $CashModel->save($data);
            }
            return redirect()->back()->with('massage', lang('global.saved'));
        }
    }
}
