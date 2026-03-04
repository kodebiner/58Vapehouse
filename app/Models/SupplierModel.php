<?php namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $allowedFields = [
        'name','phone','email','address','city',

    ];

    protected $table      = 'supplier';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    
    protected function createContact(array $data)
    {
        $contactModel = new \App\Models\AccountancyContactModel();

        $supplierId = $data['id'];

        $supplier = $this->find($supplierId);

        $contactModel->insert([
            'name'        => $supplier['name'],
            'email'       => $supplier['email'],
            'phone'       => $supplier['phone'],
            'address'     => $supplier['address'],
            'source_type' => 'supplier',
            'source_id'   => $supplierId,
        ]);
    }

    protected function updateContact(array $data)
    {
        $contactModel = new \App\Models\AccountancyContactModel();

        $supplierId = $data['id'];

        $supplier = $this->find($supplierId);

        $contactModel
            ->where('source_type', 'supplier')
            ->where('source_id', $supplierId)
            ->set([
                'name'    => $supplier['name'],
                'email'   => $supplier['email'],
                'phone'   => $supplier['phone'],
                'address' => $supplier['address'],
            ])
            ->update();
    }
}