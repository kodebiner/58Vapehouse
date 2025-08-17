<?php

namespace App\Controllers;

use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\VariantModel;
use App\Models\StockModel;
use App\Models\StockOpnameModel;
use App\Models\UserModel;

class StockOpname extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function index()
    {
        // Calling Model
        $OutletModel                = new OutletModel();
        $StockOpnameModel           = new StockOpnameModel();
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
            $stockopnames         = $StockOpnameModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->orderBy('date', 'DESC')->paginate(20, 'stockopname');
        } else {
            $stockopnames         = $StockOpnameModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('origin', $this->data['outletPick'])->orWhere('destination', $this->data['outletPick'])->orderBy('date', 'DESC')->paginate(20, 'stockopname');
        }

        $stockopnamedata              = array();
        if (!empty($stockopnames)) {
            foreach ($stockopnames as $stockopname) {
                $employee   = $UserModel->find($stockopname['userid']);
                $outlet     = $OutletModel->find($stockopname['outletid']);

                if (!empty($employee)) {
                    $employeeName = $employee->firstname . ' ' . $employee->lastname;
                } else {
                    $employeeName = '-';
                }

                if (!empty($outlet)) {
                    $outletName = $outlet->name;
                } else {
                    $outletName = 'All Outlets';
                }

                $stockopnamedata[$stockopname['id']]['id']              = $stockopname['id'];
                $stockopnamedata[$stockopname['id']]['outlet']          = $outletName;
                $stockopnamedata[$stockopname['id']]['employee']        = $employeeName;
                $stockopnamedata[$stockopname['id']]['date']            = $stockopname['date'];
            }
        }

        // Parsing data to view
        $data['title']              = 'Daftar Stok Opname';
        $data['description']        = 'Daftar semua Stok Opname';
        $data['stockopnames']       = $stockopnamedata;
        $data['pager']              = $StockOpnameModel->pager;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);

        return view ('Views/stockopname', $data);
    }

    public function print()
    {
        // Calling Model
        $ProductModel               = new ProductModel();
        $VariantModel               = new VariantModel();
        $OutletModel                = new OutletModel();
        $StockModel                 = new StockModel();
        $StockOpnameModel           = new StockOpnameModel();
        $UserModel                  = new UserModel();

        // Insert Stock Opname Data
        $inputstockopname = [
            'outletid'      => $this->data['outletPick'],
            'userid'        => $this->data['uid'],
            'date'          => date('Y-m-d H:i:s'),
        ];
        $StockOpnameModel->insert($inputstockopname);

        // Populating Data
        $data                       = $this->data;
        $

        // $stockmovements             = $StockmovementModel->find($id);

        // $stockmovedata              = array();
        // if (!empty($stockmovements)) {
        //     $stockmovedetails   = $StockMoveDetailModel->where('stockmoveid', $stockmovements['id'])->find();
        //     $dataorigin         = $OutletModel->find($stockmovements['origin']);
        //     $datadestination    = $OutletModel->find($stockmovements['destination']);
        //     $creator            = $UserModel->find($stockmovements['creator']);
        //     $sender             = $UserModel->find($stockmovements['sender']);
        //     $receiver           = $UserModel->find($stockmovements['receiver']);

        //     if (!empty($dataorigin)) {
        //         $origin         = $dataorigin['name'];
        //         $originaddress  = $dataorigin['address'];
        //         $originphone    = $dataorigin['phone'];
        //     } else {
        //         $origin         = '';
        //         $originaddress  = '';
        //         $originphone    = '';
        //     }

        //     if (!empty($datadestination)) {
        //         $destination        = $datadestination['name'];
        //         $destinationaddress = $datadestination['address'];
        //         $destinationphone   = $datadestination['phone'];
        //     } else {
        //         $destination        = '';
        //         $destinationaddress = '';
        //         $destinationphone   = '';
        //     }

        //     if (!empty($creator)) {
        //         $creator         = $creator->firstname.' '.$creator->lastname;
        //     } else {
        //         $creator         = '-';
        //     }

        //     if (!empty($sender)) {
        //         $sender         = $sender->firstname.' '.$sender->lastname;
        //     } else {
        //         $sender         = '-';
        //     }

        //     if (!empty($receiver)) {
        //         $receiver       = $receiver->firstname.' '.$receiver->lastname;
        //     } else {
        //         $receiver       = '-';
        //     }

        //     $stockmovedata['id']                    = $stockmovements['id'];
        //     $stockmovedata['origin']                = $origin;
        //     $stockmovedata['originaddress']         = $originaddress;
        //     $stockmovedata['originphone']           = $originphone;
        //     $stockmovedata['destination']           = $destination;
        //     $stockmovedata['destinationaddress']    = $destinationaddress;
        //     $stockmovedata['destinationphone']      = $destinationphone;
        //     $stockmovedata['creator']               = $creator;
        //     $stockmovedata['sender']                = $sender;
        //     $stockmovedata['receiver']              = $receiver;
        //     $stockmovedata['date']                  = $stockmovements['date'];
        //     $stockmovedata['status']                = $stockmovements['status'];

        //     $arrayqty       = array();
        //     $arrayprice     = array();
        //     if (!empty($stockmovedetails)) {
        //         foreach ($stockmovedetails as $movedet) {
        //             $movementvariants           = $VariantModel->find($movedet['variantid']);

        //             if (!empty($movementvariants)) {
        //                 $movementproducts       = $ProductModel->find($movementvariants['productid']);

        //                 if (!empty($movementproducts)) {
        //                     $product = $movementproducts['name'];
        //                 } else {
        //                     $product = '';
        //                 }

        //                 $variants   = $movementvariants['name'];
        //                 $sku        = $movementvariants['sku'];
        //                 $wholesale  = $movementvariants['hargamodal'];
        //             } else {
        //                 $variants   = '';
        //                 $sku        = '';
        //                 $product    = '';
        //                 $wholesale  = '';
        //             }
                    
        //             $stockmovedata['detail'][$movedet['id']]['name']            = $product.' - '.$variants;
        //             $stockmovedata['detail'][$movedet['id']]['productname']     = $product;
        //             $stockmovedata['detail'][$movedet['id']]['variantname']     = $variants;
        //             $stockmovedata['detail'][$movedet['id']]['sku']             = $sku;
        //             $stockmovedata['detail'][$movedet['id']]['wholesale']       = $wholesale;
        //             $stockmovedata['detail'][$movedet['id']]['qty']             = $movedet['qty'];
                    
        //             $arrayqty[]     = $movedet['qty'];
        //             $arrayprice[]   = (Int)$wholesale * (Int)$movedet['qty'];
        //         }
        //     } else {
        //         $stockmovedata['detail']      = array();
        //     }
                
        //     $stockmovedata['totalqty']        = array_sum($arrayqty);
        //     $stockmovedata['totalwholesale']  = array_sum($arrayprice);
        // }

        // Parsing data to view
        // $data['stockmovedata']  = $stockmovedata;
        
        $mpdf   = new \Mpdf\Mpdf([
            'default_font_size' => 7,
        ]);
        
        // // --- Header ---
        // $mpdf->SetHTMLHeader('
        //     <div style="text-align: right; font-size: 10px; border-bottom: 1px solid #ccc; padding-bottom: 3px;">Data Stok Opname</div>
        // ');

        // // --- Footer --- (auto page number)
        // $mpdf->SetHTMLFooter('
        //     <div style="text-align: center; font-size: 10px; border-top: 1px solid #ccc; padding-top: 3px;">
        //         Page {PAGENO} of {nb}
        //     </div>
        // ');
        $mpdf->Image('./img/logo.png', 80, 0, 210, 297, 'png', '', true, false);
        $mpdf->showImageErrors = true;
        $mpdf->AddPage("P", "", "", "", "", "15", "15", "2", "15", "", "", "", "", "", "", "", "", "", "", "", "A4-P");

        // $date       = date_create($stockmovedata['date']);
        $filename   = "Data Stok Opname " . date('d-m-Y') . ".pdf";
        $html       = view('Views/stockopnameprint', $data);
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'D');
    }
}