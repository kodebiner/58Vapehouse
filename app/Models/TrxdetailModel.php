<?php namespace App\Models;

use CodeIgniter\Model;

class TrxdetailModel extends Model
{
    protected $allowedFields = [
        'transactionid','variantid','bundleid','qty','description','value','discvar','globaldisc','margindasar','marginmodal',

    ];

    protected $table      = 'trxdetail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}