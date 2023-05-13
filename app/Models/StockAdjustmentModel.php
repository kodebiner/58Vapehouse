<?php namespace App\Models;

use CodeIgniter\Model;

class StockAdjustmentModel extends Model
{
    protected $allowedFields = [
        'outletid','variantid','restock','sale','qty','note',

    ];

    protected $table      = 'stockadjustment';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}