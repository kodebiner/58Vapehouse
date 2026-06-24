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
        $data           = $this->data;
        $input          = $this->request->getGet();
        $daterange      = $input['daterange'] ?? date('Y-m-01') . ' - ' . date('Y-m-d');
        
        [$startdate, $enddate] = explode(' - ', $daterange);
        $startdate = date('Y-m-d', strtotime($startdate));
        $enddate   = date('Y-m-d', strtotime($enddate));

        if ($this->data['outletPick'] === null) {
            $stockmovements         = $StockmovementModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->orderBy('date', 'DESC')->paginate(20, 'stockmovement');
        } else {
            $stockmovements         = $StockmovementModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')
                                        ->groupStart()
                                            ->where('origin', $this->data['outletPick'])
                                            ->orWhere('destination', $this->data['outletPick'])
                                        ->groupEnd()
                                        ->orderBy('date', 'DESC')
                                        ->paginate(20, 'stockmovement');
        }

        $productlist    = $ProductModel->where('status', '1')->findAll();
        $stockmoveIds   = array_column($stockmovements, 'id');
        $originIds      = [];
        $destinationIds = [];
        $userIds        = [];
        foreach ($stockmovements as $move) {
            $originIds[]        = $move['origin'];
            $destinationIds[]   = $move['destination'];
            if (!empty($move['creator'])) {
                $userIds[] = $move['creator'];
            }
            if (!empty($move['sender'])) {
                $userIds[] = $move['sender'];
            }
            if (!empty($move['receiver'])) {
                $userIds[] = $move['receiver'];
            }
        }

        $outletIds      = array_unique(array_merge($originIds, $destinationIds));
        $originIds      = array_unique($originIds);
        $destinationIds = array_unique($destinationIds);
        $userIds        = array_unique($userIds);
        $outletData     = [];
        if (!empty($outletIds)) {
            foreach ($OutletModel->whereIn('id', $outletIds)->findAll() as $outlet) {
                $outletData[$outlet['id']] = $outlet;
            }
        }
        $userData = [];
        if (!empty($userIds)) {
            foreach ($UserModel->whereIn('id', $userIds)->findAll() as $user) {
                $userData[$user->id] = $user;
            }
        }
        $detailData = [];
        $variantIds = [];
        if (!empty($stockmoveIds)) {
            $details = $StockMoveDetailModel
                ->whereIn('stockmoveid', $stockmoveIds)
                ->findAll();
            foreach ($details as $detail) {
                $detailData[$detail['stockmoveid']][] = $detail;
                $variantIds[] = $detail['variantid'];
            }
        }
        $variantIds     = array_unique($variantIds);
        $variantData    = [];
        $productIds     = [];
        if (!empty($variantIds)) {
            foreach (
                $VariantModel
                ->whereIn('id', $variantIds)
                ->findAll()
                as $variant
            ) {
                $variantData[$variant['id']] = $variant;
                $productIds[] = $variant['productid'];
            }
        }
        $productIds     = array_unique($productIds);
        $productData    = [];
        if (!empty($productIds)) {
            foreach (
                $ProductModel
                ->whereIn('id', $productIds)
                ->findAll()
                as $product
            ) {
                $productData[$product['id']] = $product;
            }
        }
        $stockData = [];
        if (!empty($variantIds) && !empty($originIds)) {
            $stocks = $StockModel
                ->whereIn('variantid', $variantIds)
                ->whereIn('outletid', $originIds)
                ->findAll();
            foreach ($stocks as $stock) {
                $key = $stock['variantid'] . '_' . $stock['outletid'];
                $stockData[$key] = $stock;
            }
        }
        $stockmovedata = [];
        foreach ($stockmovements as $stockmove) {
            $originData        = $outletData[$stockmove['origin']] ?? null;
            $destinationData   = $outletData[$stockmove['destination']] ?? null;
            $creatorData       = $userData[$stockmove['creator']] ?? null;
            $senderData        = $userData[$stockmove['sender']] ?? null;
            $receiverData      = $userData[$stockmove['receiver']] ?? null;

            $stockmovedata[$stockmove['id']] = [
                'id'            => $stockmove['id'],
                'origin'        => $originData['name'] ?? '',
                'originid'      => $originData['id'] ?? '',
                'destination'   => $destinationData['name'] ?? '',
                'destinationid' => $destinationData['id'] ?? '',
                'creator'       => $creatorData ? $creatorData->firstname . ' ' . $creatorData->lastname : '-',
                'sender'        => $senderData ? $senderData->firstname . ' ' . $senderData->lastname : '-',
                'receiver'      => $receiverData ? $receiverData->firstname . ' ' . $receiverData->lastname : '-',
                'date'          => $stockmove['date'],
                'status'        => $stockmove['status'],
                'detail'        => []
            ];
            $totalQty           = 0;
            $totalWholesale     = 0;
            $totalBuyPrice      = 0;
            $moveDetails        = $detailData[$stockmove['id']] ?? [];

            foreach ($moveDetails as $movedet) {
                $variant = $variantData[$movedet['variantid']] ?? null;
                if ($variant) {
                    $product        = $productData[$variant['productid']] ?? null;
                    $stockKey       = $variant['id'] . '_' . $stockmove['origin'];
                    $stock          = $stockData[$stockKey] ?? null;
                    $productName    = $product['name'] ?? '';
                    $variantName    = $variant['name'];
                    $sku            = $variant['sku'];
                    $varid          = $variant['id'];
                    $qty            = $stock['qty'] ?? '';
                    $wholesale      = $variant['hargamodal'];
                    $hargabeli      = $variant['hargadasar'];
                } else {
                    $productName = '';
                    $variantName = '';
                    $sku         = '';
                    $varid       = '';
                    $qty         = '';
                    $wholesale   = '';
                    $hargabeli   = '';
                }
                $stockmovedata[$stockmove['id']]['detail'][$movedet['id']] = [
                    'name'          => $productName . ' - ' . $variantName,
                    'productname'   => $productName,
                    'variantname'   => $variantName,
                    'sku'           => $sku,
                    'varid'         => $varid,
                    'qty'           => $qty,
                    'wholesale'     => $wholesale,
                    'hargabeli'     => $hargabeli,
                    'inputqty'      => $movedet['qty'],
                ];
                $totalQty += (int) $movedet['qty'];
                $totalWholesale += (int) $wholesale * (int) $movedet['qty'];
                $totalBuyPrice += (int) $hargabeli * (int) $movedet['qty'];
            }
            $stockmovedata[$stockmove['id']]['totalqty'] = $totalQty;
            $stockmovedata[$stockmove['id']]['totalwholesale'] = $totalWholesale;
            $stockmovedata[$stockmove['id']]['totalhargabeli'] = $totalBuyPrice;
        }

        // Parsing data to view
        $data['title']              = lang('Global.stockmoveList');
        $data['description']        = lang('Global.stockmoveListDesc');
        $data['stockmovements']     = $stockmovements;
        $data['stockmovedata']      = $stockmovedata;
        $data['productlist']        = $productlist;
        $data['outlets']            = $OutletModel->findAll();
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
            'origin'                => $input['origin'] ?? '',
            'destination'           => $input['destination'] ?? '',
            'creator'               => $this->data['uid'],
            'date'                  => $tanggal,
            'status'                => "0",
        ];

        // Save Data Stock Movement
        $StockmovementModel->insert($data);

        // Get Stock Movement ID
        $stockmoveid            = $StockmovementModel->getInsertID();

        // Stock Movement Detail
        if (isset($input['totalpcs'])) {
            foreach ($input['totalpcs'] as $varid => $value) {
                $datadetail   = [
                    'stockmoveid'   => $stockmoveid,
                    'variantid'     => $varid,
                    'qty'           => $value,
                ];

                $StockMoveDetailModel->save($datadetail);
            }
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

        $origin                 = $input['origin'] ?? $stockmovements['origin'];
        $destination            = $input['destination'] ?? $stockmovements['destination'];

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

        // Get existing detail IDs for cleanup
        $existingDetails = $StockMoveDetailModel->where('stockmoveid', $id)->findAll();
        $existingIds = array_column($existingDetails, 'id');

        // Stock Movement Detail
        $submittedIds = [];
        if (isset($input['totalpcs'])) {
            foreach ($input['totalpcs'] as $smdetid => $value) {
                $submittedIds[] = $smdetid;
                if ($value == "0") {
                    $StockMoveDetailModel->delete($smdetid);
                } else {
                    $datadetail = [
                        'id'  => $smdetid,
                        'qty' => $value,
                    ];
                    $StockMoveDetailModel->save($datadetail);
                }
            }
        }

        // Delete details that were removed from the DOM (value set to 0 and row removed)
        $toDelete = array_diff($existingIds, $submittedIds);
        if (!empty($toDelete)) {
            $StockMoveDetailModel->delete($toDelete);
        }

        if (isset($input['addtotalpcs'])) {
            foreach ($input['addtotalpcs'] as $var => $val) {
                $adddata = [
                    'stockmoveid'   => $id,
                    'variantid'     => $var,
                    'qty'           => $val,
                ];

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

        foreach ($input['ctotalpcs'][$id] ?? [] as $key => $value) {
            // Update Movement Detail
            $movedet = $StockMoveDetailModel->where('stockmoveid', $id)->where('variantid', $key)->first();
            if (!$movedet) continue;

            $movedetdata = [
                'id'  => $movedet['id'],
                'qty' => $value,
            ];

            if ($movedetdata['qty'] == "0") {
                $StockMoveDetailModel->delete($movedet['id']);
            } else {
                $StockMoveDetailModel->save($movedetdata);
            }

            if ($input['outletpick'] == $stockmovements['destination']) {
                // Minus Stock (origin)
                $originstock = $StockModel->where('variantid', $key)->where('outletid', $stockmovements['origin'])->first();
                if ($originstock) {
                    $origindata = [
                        'id'  => $originstock['id'],
                        'qty' => (int)$originstock['qty'] - (int)$value,
                    ];
                    $StockModel->save($origindata);
                }

                // Plus Stock (destination)
                $destinationstock = $StockModel->where('variantid', $key)->where('outletid', $stockmovements['destination'])->first();
                if ($destinationstock) {
                    $destinationdata = [
                        'id'      => $destinationstock['id'],
                        'qty'     => (int)$destinationstock['qty'] + (int)$value,
                        'restock' => $tanggal,
                    ];
                    $StockModel->save($destinationdata);
                }
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

        return redirect()->back()->with('message', lang('global.saved'));
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
        // $mpdf->Output($filename, 'D');
        
        ob_clean();
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->setBody($mpdf->Output($filename, 'S'));
    }
}