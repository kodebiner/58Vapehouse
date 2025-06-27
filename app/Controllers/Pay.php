<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\CashModel;
use App\Models\DebtModel;
use App\Models\GconfigModel;
use App\Models\OutletModel;
use App\Models\UserModel;
use App\Models\MemberModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\VariantModel;
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\TransactionModel;
use App\Models\TrxotherModel;
use App\Models\TrxdetailModel;
use App\Models\TrxpaymentModel;
use App\Models\DailyReportModel;

class Pay extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function create()
    {
        // Load Models
        $db                 = \Config\Database::connect();
        $BundleModel        = new BundleModel();
        $BundledetModel     = new BundledetailModel();
        $BookingModel       = new BookingModel();
        $BookingdetailModel = new BookingdetailModel();
        $CashModel          = new CashModel();
        $DebtModel          = new DebtModel();
        $GconfigModel       = new GconfigModel();
        $OutletModel        = new OutletModel();
        $UserModel          = new UserModel();
        $MemberModel        = new MemberModel();
        $PaymentModel       = new PaymentModel();
        $ProductModel       = new ProductModel();
        $VariantModel       = new VariantModel();
        $StockModel         = new StockModel();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $TrxpaymentModel    = new TrxpaymentModel();

        $input = $this->request->getPost();
        $date  = date('Y-m-d H:i:s');
        $Gconfig = $GconfigModel->first();

        // Handle image upload
        $fileName = null;
        if (!empty($input['image'])) {
            $img = $input['image'];
            $folderPath = "img/tfproof/";
            $image_parts = explode(";base64,", $img);
            if (count($image_parts) === 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = uniqid() . '.png';
                $file = $folderPath . $fileName;
                file_put_contents($file, $image_base64);
            }
        }

        // Calculate item and bundle values
        $varvalues = [];
        $bundvalues = [];
        $memberid = !empty($input['customerid']) ? $input['customerid'] : '';
        $memberdisc = 0;

        // Calculate variant values
        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);
                $discvar = isset($input['varprice'][$varid]) ? (int)$input['varprice'][$varid] * (int)$varqty : 0;
                $globaldisc = 0;
                if (!empty($this->data['gconfig']['globaldisc'])) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (int)$this->data['gconfig']['globaldisc'] * (int)$varqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = ((int)$this->data['gconfig']['globaldisc'] / 100) * ((int)$variant['hargamodal'] + (int)$variant['hargajual']) * (int)$varqty;
                    }
                }
                $memberdisc = 0;
                if ($memberid) {
                    if ($this->data['gconfig']['memberdisctype'] === '0') {
                        $memberdisc = $this->data['gconfig']['memberdisc'] * (int)$varqty;
                    } elseif ($this->data['gconfig']['memberdisctype'] === '1') {
                        $memberdisc = ((int)$this->data['gconfig']['memberdisc'] / 100) * ((int)$variant['hargamodal'] + (int)$variant['hargajual']) * (int)$varqty;
                    }
                    $maxdisc = $this->data['gconfig']['maxmemberdisc'] * (int)$varqty;
                    if ($memberdisc > $maxdisc) $memberdisc = $maxdisc;
                }
                $varvalues[] = (((int)$variant['hargamodal'] + (int)$variant['hargajual']) * (int)$varqty) - $discvar - $globaldisc - $memberdisc;
            }
        } else {
            $varvalues[] = 0;
        }

        // Calculate bundle values
        if (!empty($input['bqty'])) {
            foreach ($input['bqty'] as $bunid => $bundqty) {
                $bundle = $BundleModel->find($bunid);
                $bundleval = (int)$bundqty * (int)$bundle['price'];
                $globaldisc = 0;
                if (!empty($this->data['gconfig']['globaldisc'])) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (int)$this->data['gconfig']['globaldisc'] * (int)$bundqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = ((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$bundle['price'] * (int)$bundqty;
                    }
                }
                $memberdisc = 0;
                if ($memberid) {
                    if ($this->data['gconfig']['memberdisctype'] === '0') {
                        $memberdisc = $this->data['gconfig']['memberdisc'] * (int)$bundqty;
                    } elseif ($this->data['gconfig']['memberdisctype'] === '1') {
                        $memberdisc = ((int)$this->data['gconfig']['memberdisc'] / 100) * (int)$bundle['price'] * (int)$bundqty;
                    }
                    $maxdisc = $this->data['gconfig']['maxmemberdisc'] * (int)$bundqty;
                    if ($memberdisc > $maxdisc) $memberdisc = $maxdisc;
                }
                $bundvalues[] = $bundleval - $globaldisc - $memberdisc;
            }
        } else {
            $bundvalues[] = 0;
        }

        $subtotal = array_sum($varvalues) + array_sum($bundvalues);

        // Transaction discount
        $discount = 0;
        if (!empty($input['discvalue'])) {
            if ($input['disctype'] === '0') {
                $discount = $input['discvalue'];
            } elseif ($input['disctype'] === '1') {
                $discount = ((int)$input['discvalue'] / 100) * (int)$subtotal;
            }
        }

        $poin = !empty($input['poin']) ? (int)$input['poin'] : 0;
        $value = (int)$subtotal - (int)$discount - (int)$poin;

        // Insert Transaction
        $trxData = [
            'outletid'    => $this->data['outletPick'],
            'userid'      => $this->data['uid'],
            'memberid'    => $memberid,
            'paymentid'   => $input['payment'] ?? '0',
            'value'       => $value,
            'disctype'    => $input['disctype'] ?? '0',
            'discvalue'   => $discount,
            'date'        => $date,
            'pointused'   => $poin,
            'amountpaid'  => $input['value'] ?? 0,
            'photo'       => $fileName,
        ];
        $TransactionModel->insert($trxData);
        $trxId = $TransactionModel->getInsertID();

        // Insert Transaction Details and update stock
        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);
                $discvar = isset($input['varprice'][$varid]) ? (int)$input['varprice'][$varid] * $varqty : 0;
                $globaldisc = 0;
                if (!empty($this->data['gconfig']['globaldisc'])) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (int)$this->data['gconfig']['globaldisc'] * (int)$varqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = ((int)$this->data['gconfig']['globaldisc'] / 100) * ((int)$variant['hargamodal'] + (int)$variant['hargajual']) * (int)$varqty;
                    }
                }
                $memberdisc = 0;
                if ($memberid) {
                    if ($this->data['gconfig']['memberdisctype'] === '0') {
                        $memberdisc = $this->data['gconfig']['memberdisc'] * (int)$varqty;
                    } elseif ($this->data['gconfig']['memberdisctype'] === '1') {
                        $memberdisc = ((int)$this->data['gconfig']['memberdisc'] / 100) * ((int)$variant['hargamodal'] + (int)$variant['hargajual']) * (int)$varqty;
                    }
                    $maxdisc = $this->data['gconfig']['maxmemberdisc'] * (int)$varqty;
                    if ($memberdisc > $maxdisc) $memberdisc = $maxdisc;
                }
                $varPrice = ((int)$variant['hargamodal'] + (int)$variant['hargajual']) - ($discvar / max(1, (int)$varqty)) - ($globaldisc / max(1, (int)$varqty)) - ($memberdisc / max(1, (int)$varqty));
                $marginmodal = (int)$varPrice - (int)$variant['hargamodal'];
                $margindasar = (int)$varPrice - (int)$variant['hargadasar'];
                $TrxdetailModel->save([
                    'transactionid' => $trxId,
                    'variantid'     => $varid,
                    'qty'           => $varqty,
                    'value'         => $varPrice,
                    'discvar'       => $discvar,
                    'globaldisc'    => $globaldisc,
                    'memberdisc'    => $memberdisc,
                    'margindasar'   => $margindasar,
                    'marginmodal'   => $marginmodal,
                ]);
                // Update stock
                $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $varid)->first();
                if ($stock) {
                    $StockModel->save([
                        'id'  => $stock['id'],
                        'sale'=> $date,
                        'qty' => max(0, (int)$stock['qty'] - (int)$varqty)
                    ]);
                }
            }
        }

        // Insert bundle details and update stock
        if (!empty($input['bqty'])) {
            foreach ($input['bqty'] as $bunid => $bunqty) {
                $bundle = $BundleModel->find($bunid);
                $bundleprice = (int)$bundle['price'];
                $globaldisc = 0;
                if (!empty($this->data['gconfig']['globaldisc'])) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (int)$this->data['gconfig']['globaldisc'] * (int)$bunqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = ((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$bundleprice * (int)$bunqty;
                    }
                }
                $memberdisc = 0;
                if ($memberid) {
                    if ($this->data['gconfig']['memberdisctype'] === '0') {
                        $memberdisc = $this->data['gconfig']['memberdisc'] * (int)$bunqty;
                    } elseif ($this->data['gconfig']['memberdisctype'] === '1') {
                        $memberdisc = ((int)$this->data['gconfig']['memberdisc'] / 100) * (int)$bundle['price'] * (int)$bunqty;
                    }
                    $maxdisc = $this->data['gconfig']['maxmemberdisc'] * (int)$bunqty;
                    if ($memberdisc > $maxdisc) $memberdisc = $maxdisc;
                }
                $bundlefinprice = (int)$bundleprice - ($globaldisc / max(1, (int)$bunqty)) - ($memberdisc / max(1, (int)$bunqty));
                $TrxdetailModel->save([
                    'transactionid' => $trxId,
                    'bundleid'      => $bunid,
                    'qty'           => $bunqty,
                    'globaldisc'    => $globaldisc,
                    'memberdisc'    => $memberdisc,
                    'value'         => $bundlefinprice
                ]);
                // Update stock for each variant in bundle
                $bundledetail = $BundledetModel->where('bundleid', $bunid)->find();
                foreach ($bundledetail as $BundleDetail) {
                    $bunstock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $BundleDetail['variantid'])->first();
                    if ($bunstock) {
                        $StockModel->save([
                            'id'  => $bunstock['id'],
                            'sale'=> $date,
                            'qty' => max(0, (int)$bunstock['qty'] - (int)$bunqty)
                        ]);
                    }
                }
            }
        }

        // Deduct member points
        if ($memberid) {
            $cust = $MemberModel->find($memberid);
            if ($cust) {
                $MemberModel->save([
                    'id'   => $cust['id'],
                    'poin' => max(0, (int)$cust['poin'] - $poin),
                ]);
            }
        }

        // PPN
        $ppn = (int)$value * ((int)$Gconfig['ppn'] / 100);
        $total = (int)$subtotal - (int)$discount - $poin + (int)$ppn;

        // 1. Handle split payment (jika tidak hutang dan bukan kombinasi)
        if (
            !empty($input['firstpayment']) &&
            !empty($input['secpayment']) &&
            empty($input['duedate']) // split tidak dipakai bersama hutang
        ) {
            foreach (['firstpayment' => 'firstpay', 'secpayment' => 'secondpay'] as $payKey => $valKey) {
                $payId  = $input[$payKey];
                $payVal = $input[$valKey];

                $TrxpaymentModel->insert([
                    'paymentid'     => $payId,
                    'transactionid' => $trxId,
                    'value'         => $payVal,
                ]);

                $payment = $PaymentModel->find($payId);
                if ($payment) {
                    $cash = $CashModel->find($payment['cashid']);
                    if ($cash) {
                        $CashModel->save([
                            'id'  => $cash['id'],
                            'qty' => (int)$cash['qty'] + (int)$payVal,
                        ]);
                    }
                }
            }
        }

        // 2. Handle debt (termasuk kombinasi dengan down payment)
        if (!empty($input['duedate'])) {
            $debtValue = $input['debt'] ?? ($total - ((int)$input['value'] ?? 0));

            // Simpan ke tabel hutang
            $DebtModel->insert([
                'memberid'      => $memberid,
                'transactionid' => $trxId,
                'value'         => $debtValue,
                'deadline'      => $input['duedate'],
            ]);

            // Catat transaksi hutang (dengan paymentid = 0)
            $TrxpaymentModel->insert([
                'paymentid'     => '0',
                'transactionid' => $trxId,
                'value'         => $debtValue,
            ]);

            // Jika ada down payment, catat juga
            if (!empty($input['payment']) && !empty($input['value'])) {
                $TrxpaymentModel->insert([
                    'paymentid'     => $input['payment'],
                    'transactionid' => $trxId,
                    'value'         => $input['value'],
                ]);

                $payment = $PaymentModel->find($input['payment']);
                if ($payment) {
                    $cash = $CashModel->find($payment['cashid']);
                    if ($cash) {
                        $CashModel->save([
                            'id'  => $cash['id'],
                            'qty' => (int)$cash['qty'] + (int)$input['value'],
                        ]);
                    }
                }
            }
        }

        // 3. Handle pembayaran normal (bukan split, bukan hutang)
        if (
            empty($input['firstpayment']) &&
            empty($input['secpayment']) &&
            empty($input['duedate']) &&
            !empty($input['payment']) &&
            !empty($input['value'])
        ) {
            $TrxpaymentModel->insert([
                'paymentid'     => $input['payment'],
                'transactionid' => $trxId,
                'value'         => $input['value'],
            ]);

            $payment = $PaymentModel->find($input['payment']);
            if ($payment) {
                $cash = $CashModel->find($payment['cashid']);
                if ($cash) {
                    $CashModel->save([
                        'id'  => $cash['id'],
                        'qty' => (int)$cash['qty'] + (int)$input['value'],
                    ]);
                }
            }
        }

        // Update member points (earn)
        if ($memberid) {
            $member = $MemberModel->find($memberid);
            if ($member) {
                $minimTrx = $Gconfig['poinorder'];
                $poinval = $Gconfig['poinvalue'];
                $poinresult = 0;
                if ($minimTrx && $total >= $minimTrx) {
                    $poinresult = floor($total / $minimTrx) * $poinval;
                }
                $MemberModel->save([
                    'id'   => $member['id'],
                    'poin' => (int)$member['poin'] + (int)$poinresult,
                    'trx'  => (int)$member['trx'] + 1,
                ]);
            }
        }

        // Prepare data for print/redirect
        $transactions = $TransactionModel->find($trxId);
        $user = $UserModel->where('id', $transactions['userid'])->first();
        $actual_link = "https://{$_SERVER['HTTP_HOST']}/pay/copyprint/$trxId";
        $data = $this->data;
        $data['title'] = lang('Global.transaction');
        $data['description'] = lang('Global.transactionListDesc');
        $data['transactions'] = $transactions;
        $data['user'] = $user ? $user->username : '';
        $data['date'] = $transactions['date'];
        $data['transactionid'] = $trxId;
        $data['subtotal'] = $subtotal;
        $data['total'] = $total;
        $data['discount'] = $discount;
        $data['change'] = (!empty($input['value']) && $input['value'] > $total) ? ((int)$input['value'] - (int)$total) : 0;
        $data['pay'] = $input['value'] ?? 0;
        $data['poinused'] = $poin;
        $data['poinearn'] = isset($poinresult) ? $poinresult : 0;
        $data['link'] = $actual_link;

        // WhatsApp redirect if phone provided
        if (!empty($input['customerid'])) {
            $memberdata = $MemberModel->find($transactions['memberid']);
            if ($memberdata && !empty($memberdata['phone'])) {
                $waLink = "https://wa.me/+62{$memberdata['phone']}?text=" . urlencode(
                    "Terimakasih telah berbelanja di 58 Vapehouse, untuk detail struk pembelian bisa cek link dibawah lur. ✨✨\n\n$actual_link\n\nJika menemukan kendala, kerusakan produk, atau ingin memberi kritik & saran hubungi 58 Customer Solution kami di wa.me/6288983741558"
                );
                return redirect()->to($waLink);
            }
        }

        return redirect()->to('');
    }

    public function save()
    {
        // Calling Models
        $BundleModel            = new BundleModel();
        $BundledetModel         = new BundledetailModel();
        $CashModel              = new CashModel();
        $DebtModel              = new DebtModel();
        $GconfigModel           = new GconfigModel();
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $MemberModel            = new MemberModel();
        $PaymentModel           = new PaymentModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $StockModel             = new StockModel();
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $MemberModel            = new MemberModel();

        // Getting Inputs
        $input = $this->request->getPost();

        // Populating Data
        $date           = date('Y-m-d H:i:s');
        $Gconfig        = $GconfigModel->first();
        $customers      = $MemberModel->findAll();

        // Inserting Transaction
        $varvalues = array();
        $bundvalues = array();

        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);
                $discvar = (int)$input['varprice'][$varid] * (int)$varqty;

                if (!empty($input['varprice'][$varid])) {
                    $varval    = ((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) - (int)$discvar;
                } else {
                    $varval    = (int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual']);
                }

                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$varqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$varval) * (int)$varqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $varvalues[]    = (Int)$varval - (Int)$globaldisc;
            }
        } else {
            $varvalues[] = '0';
        }

        if (!empty($input['bqty'])) {
            foreach ($input['bqty'] as $bunid => $bundqty) {
                $bundle         = $BundleModel->find($bunid);
                
                $bundleval      = (Int)$bundqty * (Int)$bundle['price'];

                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$bundqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$bundleval) * (int)$bundqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $bundvalues[]    = (Int)$bundleval - (Int)$globaldisc;
            }
        } else {
            $bundvalues[] = '0';
        }

        $varvalue = array_sum($varvalues);
        $bundvalue = array_sum($bundvalues);

        $subtotal = $varvalue + $bundvalue;

        // ===================== Member Discount GConfig =============================== //
        if ($input['customerid'] != '0') {
            $memberid = $input['customerid'];
            if ($this->data['gconfig']['memberdisctype'] === '0') {
                $memberdisc = $this->data['gconfig']['memberdisc'];
            } elseif ($this->data['gconfig']['memberdisctype'] === '1') {
                $memberdisc = ((int)$this->data['gconfig']['memberdisc'] / 100) * (int)$subtotal;
            }
        } else {
            $memberid = '';
            $memberdisc = 0;
        }

        if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
            $discount = $input['discvalue'];
        } elseif ((!empty($input['discvalue'])) && ($input['disctype'] === '1')) {
            $discount = ((int)$input['discvalue'] / 100) * (int)$subtotal;
        } else {
            $discount = 0;
        }

        if (!empty($input['poin'])) {
            $poin = $input['poin'];
        } else {
            $poin = 0;
        }

        $value = (int)$subtotal - (int)$memberdisc - (int)$discount - (int)$poin;
        // foreach ($input['varprice'] as $variantprice) {
        //     $varprice = $variantprice;
        // }

        // foreach ($input['varbargain'] as $bargain) {
        //     $varbargain = $bargain;
        // }
        $book = [
            'outletid'      => $this->data['outletPick'],
            'userid'        => $this->data['uid'],
            'memberid'      => $memberid,
            'memberdisc'    => $memberdisc,
            'value'         => $value,
            'disctype'      => $input['disctype'],
            'discvalue'     => $input['discvalue'],
        ];
        $BookingModel->insert($book);
        $bookId = $BookingModel->getInsertID();

        // Booking Detail & Stock
        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);
                $discvar = (int)$input['varprice'][$varid] * (int)$varqty;

                if (!empty($input['varprice'][$varid])) {
                    $varPrices  = (((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) - (int)$discvar) / (int)$varqty;
                } else {
                    $varPrices  = ((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) / (int)$varqty;
                }

                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$varqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$varPrices) * (int)$varqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $varPrice   = (Int)$varPrices - (Int)$globaldisc;
                
                $trxvar = [
                    'bookingid'     => $bookId,
                    'variantid'     => $varid,
                    'qty'           => $varqty,
                    'value'         => $varPrice,
                    'discvar'       => $discvar,
                ];
                $BookingdetailModel->save($trxvar);

                $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $varid)->first();
                $saleVarStock = [
                    'id'        => $stock['id'],
                    'sale'      => $date,
                    'qty'       => (int)$stock['qty'] - (int)$varqty
                ];
                $StockModel->save($saleVarStock);
            }
        }

        if (!empty($input['bqty'])) {
            foreach ($input['bqty'] as $bunid => $bunqty) {
                $bundle         = $BundleModel->find($bunid);
                $bundleprice    = (int)$bundle['price'] * (int)$bunqty;

                // When member discount applied per item
                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$bunqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$bundleprice) * (int)$bunqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $bundlefinprice = (Int)$bundleprice - (Int)$globaldisc;

                $trxbun = [
                    'bookingid'     => $bookId,
                    'bundleid'      => $bunid,
                    'qty'           => $bunqty,
                    'value'         => $bundlefinprice
                ];
                $BookingdetailModel->save($trxbun);

                $bundledetail = $BundledetModel->where('bundleid', $bunid)->find();
                foreach ($bundledetail as $BundleDetail) {
                    $bunstock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $BundleDetail['variantid'])->first();
                    $saleBunStock = [
                        'id'        => $bunstock['id'],
                        'sale'      => $date,
                        'qty'       => (int)$bunstock['qty'] - (int)$bunqty
                    ];
                    $StockModel->save($saleBunStock);
                }
            }
        }

        // PPN Value
        $ppn = (int)$value * ((int)$Gconfig['ppn'] / 100);

        //Insert Trx Payment 
        $total = (int)$subtotal - (int)$discount - (int)$input['poin'] - (int)$memberdisc + (int)$ppn;

        $db                 = \Config\Database::connect();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->findAll();
        $booking            = $BookingModel->find($bookId);
        $bookingdetails     = $BookingdetailModel->where('bookingid', $bookId)->find();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $member             = $MemberModel->where('id', $booking['memberid'])->first();
        $user               = $UserModel->where('id', $booking['userid'])->first();

        $bundleBuilder      = $db->table('bundledetail');
        $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants     = $bundleBuilder->get();

        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['cash']           = $Cash;
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $TrxdetailModel->findAll();
        $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
        $data['bookings']       = $booking;
        $data['bookingdetails'] = $bookingdetails;
        $data['bundleVariants'] = $bundleVariants->getResult();

        if (!empty($input['customerid'])) {
            $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            $data['poinused']       = $input['poin'];
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinused']       = "0";
        }

        if (!empty($input['value'])) {
            $change             = (int)$input['value'] - (int)$total;

            if ($change > '0') {
                $data['change']     = $change;
            } else {
                $data['change']     = '0';
            }
        } else {
            $data['change']     = "0";
        }


        if (!empty($input['value'])) {
            $data['pay']            = "UNPAID";
        } elseif (!empty($input['firstpay']) && (!empty($input['secondpay']))) {
            $data['pay']            = (int)$input['firstpay'] + (int)$input['secondpay'];
        }

        $data['discount'] = "0";

        if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
            $data['discount'] += $input['discvalue'];
        } elseif ((isset($input['discvalue'])) && ($input['disctype'] === '1')) {
            $data['discount'] += ((int)$input['discvalue'] / 100) * (int)$subtotal;
        } else {
            $data['discount'] += 0;
        }

        if (!empty($input['debt'])) {
            $data['debt']       = $input['debt'];
            $data['totaldebt']  = $member['kasbon'];
        } else {
            $data['debt']       = "0";
            $data['totaldebt']  = "0";
        }

        if (!empty($value)) {
            $data['total']          = $value;
        } else {
            $data['total']          = "0";
        }

        // Gconfig poin setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['poinvalue'];

        if (($minimTrx != "0") && ($value >= $minimTrx)) {
            $subval  = (int)$value / (int)$minimTrx;
            $result = floor($subval);
            $poin   = (int)$result * (int)$poinval;
        }

        if (!empty($booking['memberid'])) {
            $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            $data['poinearn']       = $poin;
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinearn']       = "0";
        }

        $data['user']           = $user->username;
        $data['date']           = $booking['created_at'];
        $data['bookingid']      = $booking['id'];
        $data['subtotal']       = $subtotal;
        $data['member']         = $MemberModel->where('id', $booking['memberid'])->first();

        return view('Views/print', $data);
    }

    public function copyprint($id)
    {
        // Calling Models
        $BundleModel            = new BundleModel();
        $BundledetModel         = new BundledetailModel();
        $CashModel              = new CashModel();
        $DebtModel              = new DebtModel();
        $GconfigModel           = new GconfigModel();
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $MemberModel            = new MemberModel();
        $PaymentModel           = new PaymentModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $StockModel             = new StockModel();
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        // $MemberModel            = new MemberModel();
        // $GconfigModel           = new GconfigModel();

        $db                 = \Config\Database::connect();
        $transactions       = $TransactionModel->find($id);
        $trxdetails         = $TrxdetailModel->where('transactionid', $id)->find();
        $trxpayments        = $TrxpaymentModel->where('transactionid', $id)->find();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->where('id', $id)->find();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $users              = $UserModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $member             = $MemberModel->where('id', $transactions['memberid'])->first();
        $debt               = $DebtModel->where('transactionid', $id)->find();
        $user               = $UserModel->where('id', $transactions['userid'])->first();
        $Gconfig            = $GconfigModel->first();

        $bundleBuilder      = $db->table('bundledetail');
        $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants     = $bundleBuilder->get();

        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['cash']           = $Cash;
        $data['transactions']   = $transactions;
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $TrxdetailModel->where('transactionid', $id)->find();
        $data['trxpayments']    = $trxpayments;
        $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
        $data['bookings']       = $BookingModel->findAll();
        $data['bundleVariants'] = $bundleVariants->getResult();

        $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['links']          =  urlencode($actual_link);

        $data['discount'] = "0";
        if ((!empty($transactions['discvalue'])) && ($transactions['disctype'] === '0')) {
            $data['discount'] += $transactions['discvalue'];
        } elseif ((isset($transactions['discvalue'])) && ($transactions['disctype'] === '1')) {
            foreach ($trxdetails as $trxdetail) {
                if ($trxdetail['transactionid'] === $transactions['id']) {
                    $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
                    $data['discount'] += (int)$sub * ((int)$transactions['discvalue'] / 100);
                }
            }
        } else {
            $data['discount'] += 0;
        }

        $prices = array();
        foreach ($trxdetails as $trxdet) {
            if ($trxdet['transactionid'] === $id) {
                $total      = (int)$trxdet['qty'] * (int)$trxdet['value'];
                $prices[]   = $total;
            }
        }
        $sum = array_sum($prices);

        $total = (int)$sum - (int)$data['discount'] - (int)$transactions['pointused'] - (int)$Gconfig['memberdisc'] + (int)$Gconfig['ppn'];

        // Gconfig poin setup
        $minimTrx       = $Gconfig['poinorder'];
        $poinval        = $Gconfig['poinvalue'];

        $poinresult = "";
        if ($total >= $minimTrx) {
            if ($minimTrx != "0") {
                $value          = (int)$total / (int)$minimTrx;
            } else {
                $value          = 0;
            }
            $result         = floor($value);
            $poinresult     = (int)$result * (int)$poinval;
        }

        if (!empty($transactions['memberid'])) {
            $data['cust']           = $MemberModel->where('id', $transactions['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            if (empty($poinresult)) {
                $data['poinearn']   = "0";
            } else {
                $data['poinearn']       = $poinresult;
            }
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinearn']       = "0";
        }

        if (!empty($transactions['pointused'])) {
            $data['poinused']       = $transactions['pointused'];
        } else {
            $data['poinused']       = "0";
        }

        foreach ($trxdetails as $trxdetail) {
            $trxdetval = $trxdetail['value'];
        }

        if (!empty($transactions['amountpaid'])) {
            $change             = (int)$transactions['amountpaid'] - (int)$transactions['value'];

            if ($change > '0') {
                $data['change']     = $change;
            } else {
                $data['change']     = '0';
            }
        } else {
            $data['change']     = "0";
        }

        if (!empty($trxdetails['discvar'])) {
            $data['vardiscval']     = $trxdetails['discvar']['variantid'];
        } else {
            $data['vardiscval']     = "0";
        }

        if (!empty($transactions['amountpaid'])) {
            $data['pay']            = $transactions['amountpaid'];
        } elseif (empty($transactions['amountpaid'])) {
            foreach ($trxdetails as $trxdetail) {
                if ($trxdetail['transactionid'] == $id) {
                    $data['pay']    = $trxdetail['value'];
                }
            }
        } else {
            $data['pay']            = '0';
        }

        if (!empty($debt)) {
            foreach ($debt as $deb) {
                $data['debt']       = $deb['value'];
                $data['totaldebt']  = $deb['value'];
            }
        } else {
            $data['debt']       = "0";
            $data['totaldebt']  = "0";
        }

        $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['link']           =  urlencode($actual_link);

        $data['user']           = $user->username;
        $data['date']           = $transactions['date'];
        $data['transactionid']  = $id;
        $data['subtotal']       = $sum;
        $data['members']        = $MemberModel->findall();
        $data['total']          = $transactions['value'];

        if ($this->data['uid'] != null) {
            $uid    = $this->data['uid'];
        } else {
            $uid    = '0';
        }
        $data['logedin']        = $UserModel->find($uid);

        return view('Views/print', $data);
    }

    public function bookprint($id)
    {
        // Calling Models
        $BundleModel            = new BundleModel();
        $BundledetModel         = new BundledetailModel();
        $CashModel              = new CashModel();
        $DebtModel              = new DebtModel();
        $GconfigModel           = new GconfigModel();
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $MemberModel            = new MemberModel();
        $PaymentModel           = new PaymentModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $StockModel             = new StockModel();
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $MemberModel            = new MemberModel();

        $db                 = \Config\Database::connect();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->findAll();
        $booking            = $BookingModel->find($id);
        $bookingdetails     = $BookingdetailModel->where('bookingid', $id)->find();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $users              = $UserModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $member             = $MemberModel->where('id', $booking['memberid'])->first();
        $debt               = $DebtModel->where('memberid', $booking['memberid'])->first();
        $user               = $UserModel->where('id', $booking['userid'])->first();
        $Gconfig            = $GconfigModel->first();

        $bundleBuilder      = $db->table('bundledetail');
        $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants     = $bundleBuilder->get();

        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['cash']           = $Cash;
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $TrxdetailModel->findAll();
        $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
        $data['bookings']       = $booking;
        $data['bookingdetails'] = $bookingdetails;
        $data['bundleVariants'] = $bundleVariants->getResult();

        $data['discount'] = "0";
        if ((!empty($booking['discvalue'])) && ($booking['disctype'] === '0')) {
            $data['discount'] += $booking['discvalue'];
        } elseif ((isset($booking['discvalue'])) && ($booking['disctype'] === '1')) {
            foreach ($bookingdetails as $trxdetail) {
                if ($trxdetail['bookingid'] === $booking['id']) {
                    $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
                    $data['discount'] += (int)$sub * ((int)$booking['discvalue'] / 100);
                }
            }
        } else {
            $data['discount'] += 0;
        }

        $prices = array();
        foreach ($bookingdetails as $trxdet) {
            if ($trxdet['bookingid'] === $id) {
                $total      = (int)$trxdet['qty'] * (int)$trxdet['value'];
                $prices[]   = $total;
            }

            if (!empty($trxdet['discvar']) && $trxdet['discvar'] !== "0") {
                $data['vardiscval']     = $trxdet['discvar'];
            } else {
                $data['vardiscval']     = "0";
            }
        }
        $sum = array_sum($prices);

        $total = (int)$sum - (int)$data['discount'] - (int)$Gconfig['memberdisc'] + (int)$Gconfig['ppn'];

        // Gconfig poin setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['poinvalue'];

        if ($total >= $minimTrx) {
            if ($minimTrx != "0") {
                $value          = (int)$total / (int)$minimTrx;
            } else {
                $value          = 0;
            }
            $result         = floor($value);
            $poinresult     = (int)$result * (int)$poinval;
        }

        if (!empty($bookings['memberid'])) {
            $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
            $data['mempoin']        = (int)$member['poin'];
            $data['poinearn']       = 'Tidak menggunakan Poin';
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinearn']       = "0";
        }

        if (!empty($member)) {
            $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
            $data['mempoin']        = (int)$member['poin'];
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinused']       = "0";
        }

        if (!empty($booking['value']) && $booking['value'] != "0") {
            $data['change']     = (int)$booking['value'] - (int)$total;
        } else {
            $data['change']     = "0";
        }

        if ((!empty($booking['discvalue'])) && ($booking['disctype'] === '0')) {
            $data['discount']   = $booking['discvalue'];
            $data['memberdisc'] = $booking['discvalue'];
        } elseif ((!empty($booking['discvalue'])) && ($booking['disctype'] === '1')) {
            $data['discount']   = ((int)$booking['discvalue'] / 100) * (int)$bookingdetails['value'];
            $data['memberdisc'] = ((int)$booking['discvalue'] / 100) * (int)$bookingdetails['value'];
        } else {
            $data['discount'] = 0;
            $data['memberdisc'] = 0;
        }

        $data['debt']       = "0";
        $data['totaldebt']  = "0";

        if (!empty($bookingdetails)) {
            $data['total']          = $booking['value'];
        } else {
            $data['total']          = "0";
        }

        $sub = [];
        foreach ($bookingdetails as $bookingdetail) {
            $sub[] = (int)$bookingdetail['value'] + (int)$bookingdetail['discvar'];
        }
        $subtotal = array_sum($sub);

        $data['pay']            = "UNPAID";
        $data['user']           = $user->username;
        $data['date']           = $booking['created_at'];
        $data['bookingid']      = $booking['id'];
        $data['subtotal']       = $subtotal;
        $data['member']         = $MemberModel->where('id', $booking['memberid'])->first();

        return view('Views/print', $data);
    }

    public function bookingdelete($id)
    {
        // Calling Model
        $BookingModel       = new BookingModel();
        $BookingdetailModel = new BookingdetailModel();
        $StockModel         = new StockModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();

        // Populating & Removing Booking Detail Data
        $bookingdetails = $BookingdetailModel->where('bookingid', $id)->find();
        foreach ($bookingdetails as $bookdet) {
            // Restore Stock
            if ($bookdet['variantid'] != '0') {
                $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $bookdet['variantid'])->first();
                $stockdata = [
                    'id'    => $stock['id'],
                    'qty'   => (int)$stock['qty'] + (int)$bookdet['qty'],
                ];
                $StockModel->save($stockdata);
            } else {
                $bundles = $BundledetailModel->where('bundleid', $bookdet['bundleid'])->find();
                foreach ($bundles as $bundle) {
                    $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $bundle['variantid'])->first();
                    $stockdata = [
                        'id'    => $stock['id'],
                        'qty'   => (int)$stock['qty'] + (int)$bookdet['qty'],
                    ];
                    $StockModel->save($stockdata);
                }
            }

            // Removing Booking Detail
            $BookingdetailModel->delete($bookdet['id']);
        }

        // Removing Booking Data
        $BookingModel->delete($id);

        return redirect()->back()->with('error', lang('Global.deleted'));
    }

    public function topup()
    {
        // Declaration Model
        $MemberModel            = new MemberModel;
        $TrxotherModel          = new TrxotherModel;
        $CashModel              = new CashModel;
        $DailyReportModel       = new DailyReportModel;
        $PaymentModel           = new PaymentModel;
        $OutletModel            = new OutletModel();

        // Get Data
        $cashinout              = $TrxotherModel->findAll();
        $input                  = $this->request->getPost();
        $payments               = $PaymentModel->where('id', $input['payment'])->first();
        $date                   = date_create();
        $tanggal                = date_format($date, 'Y-m-d H:i:s');
        $member                 = $MemberModel->where('id', $input['customerid'])->first();
        $cash                   = $CashModel->where('id', $payments['cashid'])->first();
        $outlet                 = $OutletModel->find($this->data['outletPick']);
        $pettycash              = $CashModel->where('name', 'Petty Cash ' . $outlet['name'])->first();

        // // Image Capture
        // $img                    = $input['image'];
        // $folderPath             = "img/tfproof/";
        // $image_parts            = explode(";base64,", $img);
        // $image_type_aux         = explode("image/", $image_parts[0]);
        // $image_type             = $image_type_aux[1];
        // $image_base64           = base64_decode($image_parts[1]);
        // $fileName               = uniqid() . '.png';
        // $file                   = $folderPath . $fileName;
        // file_put_contents($file, $image_base64);

        // Cash In 
        $cashin = [
            'userid'            => $this->data['uid'],
            'outletid'          => $this->data['outletPick'],
            'cashid'            => $payments['cashid'],
            'description'       => "Top Up - " . $member['name'] . "/" . $member['phone'],
            'type'              => "0",
            'date'              => $tanggal,
            'qty'               => $input['value'],
            // 'photo'             => $fileName,
        ];
        $TrxotherModel->save($cashin);

        // plus member poin
        $poin                   = (int)$member['poin'] + (int)$input['value'];
        $data = [
            'id'                => $input['customerid'],
            'poin'              => $poin,
        ];
        $MemberModel->save($data);

        $cas = (int)$cash['qty'] + (int)$input['value'];
        $wallet = [
            'id'                => $payments['cashid'],
            'qty'               => $cas,
        ];
        $CashModel->save($wallet);

        // // Find Data for Daily Report
        // $today                  = date('Y-m-d') . ' 00:00:01';
        // $dailyreports           = $DailyReportModel->where('outletid', $this->data['outletPick'])->where('dateopen >', $today)->find();
        // if ($payments['cashid'] === $pettycash) {
        //     foreach ($dailyreports as $dayrep) {
        //         $tcashin = [
        //             'id'            => $dayrep['id'],
        //             'totalcashin'   => (int)$dayrep['totalcashin'] + (int)$input['value'],
        //         ];
        //         $DailyReportModel->save($tcashin);
        //     }
        // }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    // public function invoice($id)
    // {
    //     // Calling Models
    //     $BundleModel            = new BundleModel();
    //     $BundledetModel         = new BundledetailModel();
    //     $CashModel              = new CashModel();
    //     $DebtModel              = new DebtModel();
    //     $GconfigModel           = new GconfigModel();
    //     $OutletModel            = new OutletModel();
    //     $UserModel              = new UserModel();
    //     $MemberModel            = new MemberModel();
    //     $PaymentModel           = new PaymentModel();
    //     $ProductModel           = new ProductModel();
    //     $VariantModel           = new VariantModel();
    //     $StockModel             = new StockModel();
    //     $BookingModel           = new BookingModel();
    //     $BookingdetailModel     = new BookingdetailModel();
    //     $TransactionModel       = new TransactionModel();
    //     $TrxdetailModel         = new TrxdetailModel();
    //     $TrxpaymentModel        = new TrxpaymentModel();
    //     $MemberModel            = new MemberModel();
    //     $GconfigModel           = new GconfigModel();

    //     $db                 = \Config\Database::connect();
    //     $transactions       = $TransactionModel->find($id);
    //     $trxdetails         = $TrxdetailModel->where('transactionid', $id)->find();
    //     $trxpayments        = $TrxpaymentModel->where('transactionid', $id)->find();
    //     $bundles            = $BundleModel->findAll();
    //     $bundets            = $BundledetModel->where('id', $id)->find();
    //     $Cash               = $CashModel->findAll();
    //     $outlets            = $OutletModel->findAll();
    //     $users              = $UserModel->findAll();
    //     $customers          = $MemberModel->findAll();
    //     $payments           = $PaymentModel->findAll();
    //     $products           = $ProductModel->findAll();
    //     $variants           = $VariantModel->findAll();
    //     $stocks             = $StockModel->findAll();
    //     $members            = $MemberModel->where('id', $transactions['memberid'])->first();
    //     $debt               = $DebtModel->where('transactionid', $id)->find();
    //     $user               = $UserModel->where('id', $transactions['userid'])->first();
    //     $Gconfig            = $GconfigModel->first();

    //     $bundleBuilder      = $db->table('bundledetail');
    //     $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
    //     $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
    //     $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
    //     $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
    //     $bundleVariants     = $bundleBuilder->get();

    //     $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    //     $data                   = $this->data;

    //     $data['discount'] = "0";
    //     if ((!empty($transactions['discvalue'])) && ($transactions['disctype'] === '0')) {
    //         $data['discount'] += $transactions['discvalue'];
    //     } elseif ((isset($transactions['discvalue'])) && ($transactions['disctype'] === '1')) {
    //         foreach ($trxdetails as $trxdetail) {
    //             if ($trxdetail['transactionid'] === $transactions['id']) {
    //                 $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
    //                 $data['discount'] += (int)$sub * ((int)$transactions['discvalue'] / 100);
    //             }
    //         }
    //     } else {
    //         $data['discount'] += 0;
    //     }

    //     $prices = array();
    //     foreach ($trxdetails as $trxdet) {
    //         if ($trxdet['transactionid'] === $id) {
    //             $total = (int)$trxdet['qty'] * (int)$trxdet['value'];
    //             $prices[] = $total;
    //         }
    //     }
    //     $sum = array_sum($prices);

    //     $total = (int)$sum - (int)$data['discount'] - (int)$transactions['pointused'] - (int)$Gconfig['memberdisc'] + (int)$Gconfig['ppn'];

    //     // Gconfig poin setup
    //     $minimTrx       = $Gconfig['poinorder'];
    //     $poinval        = $Gconfig['poinvalue'];

    //     if ($total >= $minimTrx) {
    //         if ($minimTrx != "0") {
    //             $value      = (int)$total / (int)$minimTrx;
    //         } else {
    //             $value      = 0;
    //         }
    //         $result         = floor($value);
    //         $poinresult     = (int)$result * (int)$poinval;
    //     }

    //     if (!empty($transactions['memberid'])) {
    //         $data['cust']           = $MemberModel->where('id', $transactions['memberid'])->first();
    //         $data['mempoin']        = $members['poin'];
    //         $data['poinearn']       = $poinresult;
    //     } else {
    //         $data['cust']           = "0";
    //         $data['mempoin']        = "0";
    //         $data['poinearn']       = "0";
    //     }


    //     if (!empty($transactions['pointused'])) {
    //         $data['poinused']       = $transactions['pointused'];
    //     } else {
    //         $data['poinused']       = "0";
    //     }

    //     $sub = [];
    //     foreach ($trxdetails as $trxdetail) {
    //         $trxdetval  = $trxdetail['value'];
    //         $sub[]     = (int)$trxdetail['value'] + (int)$trxdetail['discvar'] * (int)$trxdetail['qty'];
    //     }
    //     $subtotal = (array_sum($sub));

    //     if (!empty($transactions['amountpaid'])) {
    //         $data['change']     = (int)$transactions['amountpaid'] - (int)$transactions['value'];
    //     } else {
    //         $data['change']     = "0";
    //     }

    //     if (!empty($trxdetails['discvar'])) {
    //         $data['vardiscval']     = $trxdetails['discvar']['variantid'];
    //     } else {
    //         $data['vardiscval']     = "0";
    //     }

    //     if (!empty($transactions['amountpaid'])) {
    //         $data['pay'] = $transactions['amountpaid'];
    //     } elseif (empty($transactions['amountpaid'])) {
    //         foreach ($trxdetails as $trxdetail) {
    //             if ($trxdetail['transactionid'] == $id) {
    //                 $data['pay'] = $trxdetail['value'];
    //             }
    //         }
    //     } else {
    //         $data['pay'] = '0';
    //     }

    //     if (!empty($debt['value'])) {
    //         $data['debt']       = $debt['debt'];
    //         $data['totaldebt']  = $debt['value'];
    //     } else {
    //         $data['debt']       = "0";
    //         $data['totaldebt']  = "0";
    //     }

    //     $data['title']          = lang('Global.transaction');
    //     $data['description']    = lang('Global.transactionListDesc');
    //     $data['links']          = $actual_link;
    //     $data['bundles']        = $bundles;
    //     $data['bundets']        = $bundets;
    //     $data['cash']           = $Cash;
    //     $data['transactions']   = $transactions;
    //     $data['outlets']        = $outlets;
    //     $data['payments']       = $payments;
    //     $data['customers']      = $customers;
    //     $data['products']       = $products;
    //     $data['variants']       = $variants;
    //     $data['stocks']         = $stocks;
    //     $data['trxdetails']     = $TrxdetailModel->where('transactionid', $id)->find();
    //     $data['trxpayments']    = $trxpayments;
    //     $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
    //     $data['bundleVariants'] = $bundleVariants->getResult();
    //     $data['members']        = $MemberModel->findAll();
    //     $data['user']           = $user->username;
    //     $data['date']           = $transactions['date'];
    //     $data['transactionid']  = $id;
    //     $data['subtotal']       = $subtotal;
    //     $data['member']         = $MemberModel->where('id', $transactions['memberid'])->first();
    //     $data['total']          = $total;

    //     return view('Views/invoice', $data);
    // }

    // public function invoicebook($id)
    // {
    //     // Calling Models
    //     $BundleModel            = new BundleModel();
    //     $BundledetModel         = new BundledetailModel();
    //     $CashModel              = new CashModel();
    //     $DebtModel              = new DebtModel();
    //     $GconfigModel           = new GconfigModel();
    //     $OutletModel            = new OutletModel();
    //     $UserModel              = new UserModel();
    //     $MemberModel            = new MemberModel();
    //     $PaymentModel           = new PaymentModel();
    //     $ProductModel           = new ProductModel();
    //     $VariantModel           = new VariantModel();
    //     $StockModel             = new StockModel();
    //     $BookingModel           = new BookingModel();
    //     $BookingdetailModel     = new BookingdetailModel();
    //     $TransactionModel       = new TransactionModel();
    //     $TrxdetailModel         = new TrxdetailModel();
    //     $TrxpaymentModel        = new TrxpaymentModel();
    //     $MemberModel            = new MemberModel();
    //     $GconfigModel           = new GconfigModel();

    //     $db                 = \Config\Database::connect();
    //     $bundles            = $BundleModel->findAll();
    //     $bundets            = $BundledetModel->findAll();
    //     $booking            = $BookingModel->find($id);
    //     $bookingdetails     = $BookingdetailModel->where('bookingid', $id)->find();
    //     $Cash               = $CashModel->findAll();
    //     $outlets            = $OutletModel->findAll();
    //     $users              = $UserModel->findAll();
    //     $customers          = $MemberModel->findAll();
    //     $payments           = $PaymentModel->findAll();
    //     $products           = $ProductModel->findAll();
    //     $variants           = $VariantModel->findAll();
    //     $stocks             = $StockModel->findAll();
    //     $member             = $MemberModel->where('id', $booking['memberid'])->first();
    //     $debt               = $DebtModel->where('memberid', $booking['memberid'])->first();
    //     $user               = $UserModel->where('id', $booking['userid'])->first();
    //     $Gconfig            = $GconfigModel->first();

    //     $bundleBuilder      = $db->table('bundledetail');
    //     $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
    //     $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
    //     $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
    //     $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
    //     $bundleVariants     = $bundleBuilder->get();
    //     $data               = $this->data;
    //     if (!empty($member)) {
    //         $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
    //         $data['mempoin']        = $member['poin'];
    //     } else {
    //         $data['cust']           = "0";
    //         $data['mempoin']        = "0";
    //         $data['poinused']       = "0";
    //     }

    //     if (!empty($input['value']) && $input['value'] <= "0") {
    //         $data['change']     = (int)$input['value'] - (int)$total;
    //     } else {
    //         $data['change']     = "0";
    //     }

    //     if (!empty($booking['discvar']) && $booking['discvar'] !== "0") {
    //         $data['vardiscval']     = $bookingdetails['value']['variantid'];
    //     } else {
    //         $data['vardiscval']     = "0";
    //     }

    //     foreach ($trxdetails as $trxdetail) {
    //         if ($trxdetail['transactionid'] === $transactions['id']) {
    //             $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
    //             $data['discount'] += (int)$sub * ((int)$transactions['discvalue'] / 100);
    //         }
    //     }


    //     if ((!empty($booking['discvalue'])) && ($booking['disctype'] === '0')) {
    //         $data['discount']   = $booking['discvalue'];
    //         $data['memberdisc'] = $booking['discvalue'];
    //     } elseif ((!empty($booking['discvalue'])) && ($booking['disctype'] === '1')) {
    //         $sub = (int)$bookingdetails['value'] * (int)$bookingdetails['qty'];
    //         $data['discount']   = ((int)$booking['discvalue'] / 100) * (int)$sub;
    //         // $data['discount'] = ($booking['discvalue']/100) * $bookingdetails['value'];
    //         $data['memberdisc'] = ((int)$booking['discvalue'] / 100) * (int)$bookingdetails['value'];
    //     } else {
    //         $data['discount'] = 0;
    //         $data['memberdisc'] = 0;
    //     }

    //     if (!empty($input['debt'])) {
    //         $data['debt']       = $input['debt'];
    //         $data['totaldebt']  = $member['kasbon'];
    //     } else {
    //         $data['debt']       = "0";
    //         $data['totaldebt']  = "0";
    //     }

    //     $subtotal = 0;
    //     foreach ($bookingdetails as $bookingdetail) {
    //         $subtotal += $bookingdetail['value'];
    //     }

    //     // Gconfig poin setup
    //     $minimTrx    = $Gconfig['poinorder'];
    //     $poinval     = $Gconfig['poinvalue'];

    //     if (($minimTrx != "0") && ($subtotal  >= $minimTrx)) {
    //         $value  = (int)$subtotal / (int)$minimTrx;
    //         $result = floor($value);
    //         $poin   = (int)$result * $poinval;
    //     }

    //     if (!empty($booking['memberid'])) {
    //         $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
    //         $data['mempoin']        = $member['poin'];
    //         $data['poinearn']       = $poin;
    //     } else {
    //         $data['cust']           = "0";
    //         $data['mempoin']        = "0";
    //         $data['poinearn']       = "0";
    //     }

    //     $data['total']          = $booking['value'];
    //     $data['title']          = lang('Global.transaction');
    //     $data['description']    = lang('Global.transactionListDesc');
    //     $data['bundles']        = $bundles;
    //     $data['bundets']        = $bundets;
    //     $data['cash']           = $Cash;
    //     $data['outlets']        = $outlets;
    //     $data['payments']       = $payments;
    //     $data['customers']      = $customers;
    //     $data['products']       = $products;
    //     $data['variants']       = $variants;
    //     $data['stocks']         = $stocks;
    //     $data['trxdetails']     = $TrxdetailModel->findAll();
    //     $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
    //     $data['bookings']       = $booking;
    //     $data['bookingdetails'] = $bookingdetails;
    //     $data['bundleVariants'] = $bundleVariants->getResult();
    //     $data['members']        = $MemberModel->findAll();

    //     $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    //     $data['links']          = $actual_link;
    //     $data['pay']            = "UNPAID";
    //     $data['user']           = $user->username;
    //     $data['date']           = $booking['created_at'];
    //     $data['bookingid']      = $booking['id'];
    //     $data['subtotal']       = $subtotal;
    //     $data['member']         = $MemberModel->where('id', $booking['memberid'])->first();

    //     return view('Views/invoice', $data);
    // }
}
