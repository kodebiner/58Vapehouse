<?php

namespace App\Controllers;

use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\VariantModel;
use App\Models\StockModel;
use App\Models\StockOpnameModel;
use App\Models\UserModel;
use DateTime;
use Exception;

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
            $stockopnames         = $StockOpnameModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->orderBy('date', 'DESC')->paginate(20, 'stockopname');
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
                    $outletName = $outlet['name'];
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
        $CategoryModel              = new CategoryModel();
        $VariantModel               = new VariantModel();
        $OutletModel                = new OutletModel();
        $StockModel                 = new StockModel();
        $StockOpnameModel           = new StockOpnameModel();
        $UserModel                  = new UserModel();

        // Insert Stock Opname Data
        $inputstockopname = [
            'outletid'      => $this->data['outletPick'] === null ? 0 : $this->data['outletPick'],
            'userid'        => $this->data['uid'],
            'date'          => date('Y-m-d H:i:s'),
        ];
        $StockOpnameModel->insert($inputstockopname);

        // Populating Data
        if ($this->data['outletPick'] === null) {
            $stocks     = $StockModel->where('qty !=', '0')->find();
            $outletcode = 'AOT';
            $outletname = 'All Outlets';
        } else {
            $stocks     = $StockModel->where('qty !=', '0')->where('outletid', $this->data['outletPick'])->find();
            $outlets    = $OutletModel->find($this->data['outletPick']);
            $outletname = $outlets['name'];

            if ($this->data['outletPick'] === '1') {
                $outletcode = 'PST';
            }

            if ($this->data['outletPick'] === '2') {
                $outletcode = 'SLM';
            }

            if ($this->data['outletPick'] === '3') {
                $outletcode = 'UGM';
            }

            if ($this->data['outletPick'] === '4') {
                $outletcode = 'FEP';
            }
        }

        $stockopnamedata    = [];

        if (!empty($stocks)) {
            foreach ($stocks as $stock) {
                $variant = $VariantModel->find($stock['variantid']);

                if (!empty($variant)) {
                    $product = $ProductModel->find($variant['productid']);

                    if (!empty($product)) {
                        $category   = $CategoryModel->find($product['catid']);
                        if (!empty($category)) {
                            $categoryName = $category['name'];
                        } else {
                            $categoryName = 'Kategori Tidak Ditemukan';
                        }

                        $umurProduk = null;
                        if (!empty($stock['restock']) && $stock['restock'] !== '0000-00-00 00:00:00') {
                            try {
                                $origin   = new \DateTime($stock['restock']);
                                $target   = new \DateTime('now');
                                $interval = $origin->diff($target);
                                $umurProduk = (int) $interval->format('%a');
                            } catch (\Exception $e) {
                                $umurProduk = null;
                            }
                        }

                        $key = $product['id'].'-'.$variant['id'];

                        if (!isset($stockopnamedata[$key])) {
                            $stockopnamedata[$key] = [
                                'product'       => $product['name'].' - '.$variant['name'],
                                'category'      => $categoryName,
                                'sku'           => $variant['sku'],
                                'stock'         => $stock['qty'],
                                'productage'    => $umurProduk,
                            ];
                        } else {
                            $stockopnamedata[$key]['stock'] += $stock['qty'];
                            if ($umurProduk !== null) {
                                if ($stockopnamedata[$key]['productage'] === null) {
                                    $stockopnamedata[$key]['productage'] = $umurProduk;
                                } else {
                                    $stockopnamedata[$key]['productage'] = min($stockopnamedata[$key]['productage'], $umurProduk);
                                }
                            }
                        }
                    }
                }
            }
        }
        array_multisort(array_column($stockopnamedata, 'category'), SORT_ASC, $stockopnamedata);

        $dateexport     = date('d-m-Y');
        $timeapproval   = date('H:i');
        $dateapproval   = date('j F Y');

        // Parsing data to view
        $data                   = $this->data;
        $data['stockopnames']   = $stockopnamedata;
        $data['outletcode']     = $outletcode;
        $data['outlet']         = $outletname;
        $data['timeapproval']   = $timeapproval;
        $data['dateexport']     = $dateexport;
        $data['dateapproval']   = $dateapproval;

        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=Data Stok Opname - ".$outletcode." - ".$dateexport.".pdf");

        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);

        $headerHtml = '
        <table width="100%" style="font-size:10pt; padding-bottom:5px;">
        <tr>
            <td width="60%" style="text-align:center; font-weight:bold;">
            Data Stok Opname - '.$outletcode.' - '.$dateexport.'
            </td>
        </tr>
        </table>';

        $footerHtml = '
        <div style="text-align:center; font-size:9pt; padding-top:3px;">
        Page {PAGENO} of {nb}
        </div>';

        $mpdf->SetHTMLHeader($headerHtml);
        $mpdf->SetHTMLFooter($footerHtml);

        $html       = view('stockopnameprint', $data);
        $mpdf->WriteHTML($html);

        $filename   = "Data Stok Opname - " . $outletcode . ' - ' . date('d-m-Y') . ".pdf";
        $mpdf->Output($filename, 'D');

        // // Return
        // return redirect()->back()->with('message', 'Data Tersimpan dan Export Berhasil');
    }
}