<?php namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $allowedFields = [
        'outletid','variantid','restock','sale','qty', 'productid', 'supplierid', 'price', 'status',
    ];

    protected $table      = 'stock';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}