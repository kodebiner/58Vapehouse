<?php

namespace App\Controllers;

use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\VariantModel;
use App\Models\StockModel;
use App\Models\OldStockModel;
use App\Models\StockmovementModel;
use App\Models\StockMoveDetailModel;
use App\Models\UserModel;

class StockMovement extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function index()
    {
        // Calling Model
        $ProductModel               = new ProductModel();
        $VariantModel               = new VariantModel();
        $OutletModel                = new OutletModel();
        $StockModel                 = new StockModel();
        $StockmovementModel         = new StockmovementModel();
        $StockMoveDetailModel       = new StockMoveDetailModel();
        $UserModel                  = new UserModel();

        // Populating Data
        $data                       = $this->data;

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-m' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        if ($this->data['outletPick'] === null) {
            $stockmovements         = $StockmovementModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->orderBy('date', 'DESC')->paginate(20, 'stockmovement');
        } else {
            $stockmovements         = $StockmovementModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('origin', $this->data['outletPick'])->orWhere('destination', $this->data['outletPick'])->orderBy('date', 'DESC')->paginate(20, 'stockmovement');
        }

        $outlets                    = $OutletModel->findAll();

        $productlist                = $ProductModel->where('status', '1')->find();

        $stockmovedata              = array();
        if (!empty($stockmovements)) {
            foreach ($stockmovements as $stockmove) {
                $stockmovedetails   = $StockMoveDetailModel->where('stockmoveid', $stockmove['id'])->find();
                $dataorigin         = $OutletModel->find($stockmove['origin']);
                $datadestination    = $OutletModel->find($stockmove['destination']);
                $creator            = $UserModel->find($stockmove['creator']);
                $sender             = $UserModel->find($stockmove['sender']);
                $receiver           = $UserModel->find($stockmove['receiver']);

                if (!empty($dataorigin)) {
                    $origin         = $dataorigin['name'];
                    $originid       = $dataorigin['id'];
                } else {
                    $origin         = '';
                    $originid       = '';
                }

                if (!empty($datadestination)) {
                    $destination    = $datadestination['name'];
                    $destinationid  = $datadestination['id'];
                } else {
                    $destination = '';
                    $destinationid  = '';
                }

                if (!empty($creator)) {
                    $creator         = $creator->firstname.' '.$creator->lastname;
                } else {
                    $creator         = '-';
                }

                if (!empty($sender)) {
                    $sender         = $sender->firstname.' '.$sender->lastname;
                } else {
                    $sender         = '-';
                }

                if (!empty($receiver)) {
                    $receiver       = $receiver->firstname.' '.$receiver->lastname;
                } else {
                    $receiver       = '-';
                }

                $stockmovedata[$stockmove['id']]['id']              = $stockmove['id'];
                $stockmovedata[$stockmove['id']]['origin']          = $origin;
                $stockmovedata[$stockmove['id']]['originid']        = $originid;
                $stockmovedata[$stockmove['id']]['destination']     = $destination;
                $stockmovedata[$stockmove['id']]['destinationid']   = $destinationid;
                $stockmovedata[$stockmove['id']]['creator']         = $creator;
                $stockmovedata[$stockmove['id']]['sender']          = $sender;
                $stockmovedata[$stockmove['id']]['receiver']        = $receiver;
                $stockmovedata[$stockmove['id']]['date']            = $stockmove['date'];
                $stockmovedata[$stockmove['id']]['status']          = $stockmove['status'];

                $arrayqty       = array();
                $arrayprice     = array();
                if (!empty($stockmovedetails)) {
                    foreach ($stockmovedetails as $movedet) {
                        $movementvariants           = $VariantModel->find($movedet['variantid']);
    
                        if (!empty($movementvariants)) {
                            $movementproducts       = $ProductModel->find($movementvariants['productid']);
                            $stocks                 = $StockModel->where('variantid', $movementvariants['id'])->where('outletid', $stockmove['origin'])->first();
    
                            if (!empty($movementproducts)) {
                                $product = $movementproducts['name'];
                            } else {
                                $product = '';
                            }
    
                            if (!empty($stocks)) {
                                $qty = $stocks['qty'];
                            } else {
                                $qty = '';
                            }
    
                            $varid      = $movementvariants['id'];
                            $variants   = $movementvariants['name'];
                            $sku        = $movementvariants['sku'];
                            $wholesale  = $movementvariants['hargamodal'];
                        } else {
                            $varid      = '';
                            $variants   = '';
                            $sku        = '';
                            $product    = '';
                            $qty        = '';
                            $wholesale  = '';
                        }
                        
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['name']          = $product.' - '.$variants;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['productname']   = $product;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['variantname']   = $variants;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['sku']           = $sku;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['varid']         = $varid;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['qty']           = $qty;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['wholesale']     = $wholesale;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['inputqty']      = $movedet['qty'];
                        
                        $arrayqty[]     = $movedet['qty'];
                        $arrayprice[]   = (Int)$wholesale * (Int)$movedet['qty'];
                    }
                } else {
                    $stockmovedata[$stockmove['id']]['detail']      = array();
                }
                    
                $stockmovedata[$stockmove['id']]['totalqty']        = array_sum($arrayqty);
                $stockmovedata[$stockmove['id']]['totalwholesale']  = array_sum($arrayprice);
            }
        }

        // Parsing data to view
        $data['title']              = lang('Global.stockmoveList');
        $data['description']        = lang('Global.stockmoveListDesc');
        $data['stockmovements']     = $stockmovements;
        $data['stockmovedata']      = $stockmovedata;
        $data['productlist']        = $productlist;
        $data['outlets']            = $outlets;
        $data['pager']              = $StockmovementModel->pager;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);

        return view ('Views/stockmove', $data);
    }

    public function product()
    {
        // Calling Model
        $VariantModel   = new VariantModel();
        $StockModel     = new StockModel();
        $ProductModel   = new ProductModel();

        // initialize
        $input      = $this->request->getPost();

        $product    = $ProductModel->find($input['id']);

        $variants   = $VariantModel->where('productid', $input['id'])->find();

        $variantid = array();
        foreach ($variants as $var) {
            $variantid[]    = $var['id'];
        }
        
        if (isset($input['outletid'])) {
            $stocks     = $StockModel->whereIn('variantid', $variantid)->where('outletid', $input['outletid'])->find();
        } else {
            $stocks     = $StockModel->whereIn('variantid', $variantid)->where('outletid', $this->data['outletPick'])->find();
        }

        $return = array();
        foreach ($stocks as $stock) {
            foreach ($variants as $variant) {
                if ($stock['variantid'] === $variant['id']) {
                    $return[] = [
                        'id'        => $variant['id'],
                        'product'   => $product['name'],
                        'variant'   => $variant['name'],
                        'sku'       => $variant['sku'],
                        'wholesale' => $variant['hargamodal'],
                        'name'      => $product['name'].' - '.$variant['name'],
                        'qty'       => $stock['qty'],
                        'price'     => $variant['hargadasar'],
                        'sellprice' => (int)$variant['hargamodal'] + (int)$variant['hargajual'],
                        'msrp'      => $variant['hargarekomendasi']
                    ];
                }
            }
        }
        
        die(json_encode($return));
    }

    public function create()
    {
        // Validate Data
        $validation = \Config\Services::validation();

        // Calling Model
        $StockmovementModel         = new StockmovementModel();
        $StockMoveDetailModel       = new StockMoveDetailModel();

        // initialize
        $input                      = $this->request->getPost();

        // date time stamp
        $date                       = date_create();
        $tanggal                    = date_format($date,'Y-m-d H:i:s');

        $data = [
            'origin'                => $input['origin'],
            'destination'           => $input['destination'],
            'creator'               => $this->data['uid'],
            'date'                  => $tanggal,
            'status'                => "0",
        ];

        // Save Data Stock Movement
        $StockmovementModel->insert($data);

        // Get Stock Movement ID
        $stockmoveid            = $StockmovementModel->getInsertID();

        // Stock Movement Detail
        foreach ($input['totalpcs'] as $varid => $value) {
            $datadetail   = [
                'stockmoveid'   => $stockmoveid,
                'variantid'     => $varid,
                'qty'           => $value,
            ];

            // Save Data Stock Movement Detail
            $StockMoveDetailModel->save($datadetail);
        }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id)
    {
        // Validate Data
        $validation = \Config\Services::validation();

        // Calling Model
        $StockmovementModel         = new StockmovementModel();
        $StockMoveDetailModel       = new StockMoveDetailModel();

        // Find Data
        $stockmovements             = $StockmovementModel->find($id);

        // initialize
        $input                      = $this->request->getPost();

        // date time stamp
        $date                       = date_create();
        $tanggal                    = date_format($date,'Y-m-d H:i:s');

        if ($input['origin'] != $stockmovements['origin']) {
            $origin                 = $input['origin'];
        } else {
            $origin                 = $stockmovements['origin'];
        }

        if ($input['destination'] != $stockmovements['destination']) {
            $destination            = $input['destination'];
        } else {
            $destination            = $stockmovements['destination'];
        }

        if ($this->data['outletPick'] == $stockmovements['origin']) {
            $status                 = 0;
        } else {
            $status                 = 1;
        }

        $data = [
            'id'                    => $id,
            'origin'                => $origin,
            'destination'           => $destination,
            'date'                  => $tanggal,
            'status'                => $status,
        ];

        // Save Data Stock Movement
        $StockmovementModel->save($data);

        // Stock Movement Detail
        foreach ($input['totalpcs'] as $smdetid => $value) {
            $datadetail     = [
                'id'            => $smdetid,
                'qty'           => $value,
            ];

            if ($datadetail['qty'] == "0") {
                $StockMoveDetailModel->delete($smdetid);
            }

            // Save Data Stock Movement Detail
            $StockMoveDetailModel->save($datadetail);
        }

        if (isset($input['addtotalpcs'])) {
            foreach ($input['addtotalpcs'] as $var => $val) {
                $adddata = [
                    'stockmoveid'   => $id,
                    'variantid'     => $var,
                    'qty'           => $val,
                ];

                // Save Data Stock Movement Detail
                $StockMoveDetailModel->save($adddata);
            }
        }

        // return
        return redirect()->back()->withInput()->with('message', lang('Global.saved'));
    }

    public function confirm($id)
    {
        // Calling Model
        $StockmovementModel         = new StockmovementModel();
        $StockMoveDetailModel       = new StockMoveDetailModel();
        $StockModel                 = new StockModel();

        // initialize
        $input                      = $this->request->getPost();

        // date time stamp
        $date                       = date_create();
        $tanggal                    = date_format($date,'Y-m-d H:i:s');

        // Find Data
        $stockmovements             = $StockmovementModel->find($id);

        if ($input['outletpick'] == $stockmovements['origin']) {
            // $status                 = 1;

            $data = [
                'id'                    => $id,
                'date'                  => $tanggal,
                'sender'                => $this->data['uid'],
                'status'                => 1,
            ];
    
            $StockmovementModel->save($data);
        } else {
            // $status                 = 3;

            $data = [
                'id'                    => $id,
                'date'                  => $tanggal,
                'receiver'              => $this->data['uid'],
                'status'                => 3,
            ];
    
            $StockmovementModel->save($data);
        }

        foreach ($input['ctotalpcs'][$id] as $key => $value) {
            // Update Movement Detail
            $movedet                = $StockMoveDetailModel->where('stockmoveid', $id)->where('variantid', $key)->first();
            $movedetdata            = [
                'id'                => $movedet['id'],
                'qty'               => $value,
            ];
    
            if ($movedetdata['qty'] == "0") {
                $StockMoveDetailModel->delete($movedet['id']);
            }
            
            $StockMoveDetailModel->save($movedetdata);

            if ($input['outletpick'] == $stockmovements['destination']) {
                // Minus Stock
                $originstock        = $StockModel->where('variantid', $key)->where('outletid', $stockmovements['origin'])->first();
                $origindata         = [
                    'id'    => $originstock['id'],
                    'qty'   => (Int)$originstock['qty'] -= (Int)$value,
                ];
                $StockModel->save($origindata);
    
                // Plus Stock
                $destinationstock   = $StockModel->where('variantid', $key)->where('outletid', $stockmovements['destination'])->first();
                $destinationdata    = [
                    'id'        => $destinationstock['id'],
                    'qty'       => (Int)$destinationstock['qty'] += (Int)$value,
                    // 'restock'   => $tanggal,
                ];
                $StockModel->save($destinationdata);
            }
        }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function cancel($id)
    {
        // calling Model
        $StockmovementModel         = new StockmovementModel();
        
        // search id
        // $data['purchases']          = $StockmovementModel->find($id);

        // date time stamp
        $date                       = date_create();
        $tanggal                    = date_format($date,'Y-m-d H:i:s');

        // validation
        $data = [
            'id'                    => $id,
            'date'                  => $tanggal,
            'status'                => "2",
        ];
        $StockmovementModel->save($data);

        return redirect()->back()->with('massage', lang('global.saved'));
    }

    public function print($id)
    {
        // Calling Model
        $ProductModel               = new ProductModel();
        $VariantModel               = new VariantModel();
        $OutletModel                = new OutletModel();
        $StockModel                 = new StockModel();
        $StockmovementModel         = new StockmovementModel();
        $StockMoveDetailModel       = new StockMoveDetailModel();
        $UserModel                  = new UserModel();

        // Populating Data
        $data                       = $this->data;

        $stockmovements             = $StockmovementModel->find($id);

        $stockmovedata              = array();
        if (!empty($stockmovements)) {
            $stockmovedetails   = $StockMoveDetailModel->where('stockmoveid', $stockmovements['id'])->find();
            $dataorigin         = $OutletModel->find($stockmovements['origin']);
            $datadestination    = $OutletModel->find($stockmovements['destination']);
            $creator            = $UserModel->find($stockmovements['creator']);
            $sender             = $UserModel->find($stockmovements['sender']);
            $receiver           = $UserModel->find($stockmovements['receiver']);

            if (!empty($dataorigin)) {
                $origin         = $dataorigin['name'];
                $originaddress  = $dataorigin['address'];
                $originphone    = $dataorigin['phone'];
            } else {
                $origin         = '';
                $originaddress  = '';
                $originphone    = '';
            }

            if (!empty($datadestination)) {
                $destination        = $datadestination['name'];
                $destinationaddress = $datadestination['address'];
                $destinationphone   = $datadestination['phone'];
            } else {
                $destination        = '';
                $destinationaddress = '';
                $destinationphone   = '';
            }

            if (!empty($creator)) {
                $creator         = $creator->firstname.' '.$creator->lastname;
            } else {
                $creator         = '-';
            }

            if (!empty($sender)) {
                $sender         = $sender->firstname.' '.$sender->lastname;
            } else {
                $sender         = '-';
            }

            if (!empty($receiver)) {
                $receiver       = $receiver->firstname.' '.$receiver->lastname;
            } else {
                $receiver       = '-';
            }

            $stockmovedata['id']                    = $stockmovements['id'];
            $stockmovedata['origin']                = $origin;
            $stockmovedata['originaddress']         = $originaddress;
            $stockmovedata['originphone']           = $originphone;
            $stockmovedata['destination']           = $destination;
            $stockmovedata['destinationaddress']    = $destinationaddress;
            $stockmovedata['destinationphone']      = $destinationphone;
            $stockmovedata['creator']               = $creator;
            $stockmovedata['sender']                = $sender;
            $stockmovedata['receiver']              = $receiver;
            $stockmovedata['date']                  = $stockmovements['date'];
            $stockmovedata['status']                = $stockmovements['status'];

            $arrayqty       = array();
            $arrayprice     = array();
            if (!empty($stockmovedetails)) {
                foreach ($stockmovedetails as $movedet) {
                    $movementvariants           = $VariantModel->find($movedet['variantid']);

                    if (!empty($movementvariants)) {
                        $movementproducts       = $ProductModel->find($movementvariants['productid']);

                        if (!empty($movementproducts)) {
                            $product = $movementproducts['name'];
                        } else {
                            $product = '';
                        }

                        $variants   = $movementvariants['name'];
                        $sku        = $movementvariants['sku'];
                        $wholesale  = $movementvariants['hargamodal'];
                    } else {
                        $variants   = '';
                        $sku        = '';
                        $product    = '';
                        $wholesale  = '';
                    }
                    
                    $stockmovedata['detail'][$movedet['id']]['name']            = $product.' - '.$variants;
                    $stockmovedata['detail'][$movedet['id']]['productname']     = $product;
                    $stockmovedata['detail'][$movedet['id']]['variantname']     = $variants;
                    $stockmovedata['detail'][$movedet['id']]['sku']             = $sku;
                    $stockmovedata['detail'][$movedet['id']]['wholesale']       = $wholesale;
                    $stockmovedata['detail'][$movedet['id']]['qty']             = $movedet['qty'];
                    
                    $arrayqty[]     = $movedet['qty'];
                    $arrayprice[]   = (Int)$wholesale * (Int)$movedet['qty'];
                }
            } else {
                $stockmovedata['detail']      = array();
            }
                
            $stockmovedata['totalqty']        = array_sum($arrayqty);
            $stockmovedata['totalwholesale']  = array_sum($arrayprice);
        }

        // Parsing data to view
        $data['stockmovedata']  = $stockmovedata;
        
        $mpdf   = new \Mpdf\Mpdf([
            'default_font_size' => 7,
        ]);
        $mpdf->Image('./img/logo.png', 80, 0, 210, 297, 'png', '', true, false);
        $mpdf->showImageErrors = true;
        $mpdf->AddPage("P", "", "", "", "", "15", "15", "2", "15", "", "", "", "", "", "", "", "", "", "", "", "A4-P");

        $date       = date_create($stockmovedata['date']);
        $filename   = "SM" . date_format($date, 'Ymd') . $stockmovedata['id'] . ".pdf";
        $html       = view('Views/stockmovementprint', $data);
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'D');
    }
}