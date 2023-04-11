<?php namespace App\Models;

use CodeIgniter\Model;

class TrxpaymentModel extends Model
{
    protected $allowedFields = [
        'paymentid','transactionid','value',

    ];

    protected $table      = 'trxpayment';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}