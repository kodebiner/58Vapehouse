<?php namespace App\Models;

use CodeIgniter\Model;

class PurchasedetailModel extends Model
{
    protected $allowedFields = [
        'variantid','purchaseid','qty','price',

    ];

    protected $table      = 'purchasedetail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}