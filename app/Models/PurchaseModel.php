<?php namespace App\Models;

use CodeIgniter\Model;

class PurchaseModel extends Model
{
    protected $allowedFields = [
        'outletid','supplierid','purchasedetailid','date','status',

    ];

    protected $table      = 'purchase';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}