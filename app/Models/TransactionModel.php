<?php namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $allowedFields = [
        'outletid','userid','memberid','memberdisc','paymentid','value','disctype','discvalue','date','photo','pointused','amountpaid',

    ];

    protected $table            = 'transaction';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    

}