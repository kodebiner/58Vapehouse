<?php namespace App\Models;

use CodeIgniter\Model;

class TrxdetailModel extends Model
{
    protected $allowedFields = [
        'transactionid','variantid','bundleid','qty','description','value',

    ];

    protected $table      = 'trxdetail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}