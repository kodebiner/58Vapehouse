<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\TransactionModel;
use App\Models\DebtModel;

class Customer extends BaseController
{
    public function index()
    {
        $pager      = \Config\Services::pager();
        
        // Calling Models
        $MemberModel            = new MemberModel();
        $DebtModel              = new DebtModel;
        $TransactionModel       = new TransactionModel;

        // Populating Data
        $input      = $this->request->getGet('search');
        if (!empty($input)) {
            $customers  = $MemberModel->like('name', $input)->orLike('phone', $input)->orderBy('id', 'DESC')->paginate(20, 'customer');
        } else {
            $customers  = $MemberModel->orderBy('id', 'DESC')->paginate(20, 'customer');
        }

        if ($this->data['outletPick'] != null) {
            // Date Range
            $inputdate = $this->request->getGet('daterange');
            if (!empty($inputdate)) {
                $daterange = explode(' - ', $inputdate);
                $startdate = $daterange[0];
                $enddate = $daterange[1];
            } else {
                $startdate = date('Y-m-1');
                $enddate = date('Y-m-t');
            }

            // Transactions
            $transactions   = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid',$this->data['outletPick'])->find();

            // Debts
            $debts          = $DebtModel->findAll();

            // Customer History
            $customer = array();
            foreach ($customers as $member) {
                $totaltrx   = array();
                $debtval    = array();
                foreach ($debts as $debt) {
                    if ($member['id'] === $debt['memberid']) {
                        $debtval[]  = $debt['value'];
                    }
                }
                foreach ($transactions as $trx) {
                    if ($member['id'] === $trx['memberid']) {
                        $totaltrx[] = $trx['memberid'];
                    }
                }
                
                $customer[] =[
                    'id'    => $member['id'],
                    'debt'  => array_sum($debtval),
                    'trx'   => count($totaltrx),
                ];
            }
        } else {
            return redirect()->to('');
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.customerList');
        $data['description']    = lang('Global.customerListDesc');
        $data['customers']      = $customers;
        $data['pager']          = $MemberModel->pager;
        $data['member']         = $customer;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/customer', $data);
    }

    public function create()
    {
        $validation = \Config\Services::validation();

        // Calling Models
        $MemberModel = new MemberModel;

        // Populating data
        $input          = $this->request->getPost();
        $customers      = $MemberModel->findAll();

        $data = [
            'name'      => $input['name'],
            'phone'     => $input['phone'],
            'email'     => $input['email'],
            'poin'      => '0',
        ];
        
        if (! $this->validate([
            'name'      => "required|max_length[255]|is_unique[member.name]",
            'phone'     => 'required|is_unique[member.phone]',
            'email'     => 'max_length[255]',
        ])) {
                
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
            
        // Inserting Customer
        $MemberModel->insert($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id)
    {
        // Calling Models
        $customers = new MemberModel();
        
        // Poulating Data
        $data['customer'] = $customers->where('id', $id)->first();
        $input = $this->request->getPost();
        
        
        $validation =  \Config\Services::validation();
        $data = [
            'id'        => $id,
            'name'      => $input['name'],
            'phone'     => $input['phone'],
            'email'     => $input['email'],
            'poin'      => $input['poin'],
        ];

        // Validasi
        if (! $this->validate([
            'name'      => "max_length[255]",
            'phone'     => "max_length[255]",
            'email'     => "max_length[255]",
            'poin'      => "max_length[255]",
        ])) {

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Save Data
        $customers->save($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }
    
    public function delete($id)
    {
        $customers = new MemberModel();

        $customers->delete($id);

        return redirect()->back()->with('error', lang('Global.deleted'));
    }
}