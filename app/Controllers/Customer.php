<?php

namespace App\Controllers;

use App\Models\MemberModel;

class Customer extends BaseController
{
    public function index()
    {
        // Calling Models
        $MemberModel             = new MemberModel();

        // Populating Data
        $customers              = $MemberModel->findAll();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.customer');
        $data['description']    = lang('Global.customerListDesc');
        $data['customers']      = $customers;

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
            'name'      => "required|max_length[255]',",
            'phone'     => 'required',
            'email'     => 'required|max_length[255]|valid_email',
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
        ];

        // Validasi
        if (! $this->validate([
            'name'      => "max_length[255]',",
            'phone'     => "max_length[255]',",
            'email'     => "max_length[255]',",
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