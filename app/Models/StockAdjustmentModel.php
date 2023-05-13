<?php namespace App\Models;

use CodeIgniter\Model;

class StockAdjustmentModel extends Model
{
    protected $allowedFields = [
        'outletid','variantid','stockid','type','date','qty','note',

    ];

    protected $table      = 'stockadjustment';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}