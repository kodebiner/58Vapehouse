<?php namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $allowedFields = [
        'outletid','userid','memberid','paymentid','value','disctype','discvalue','date',

    ];

    protected $table            = 'transaction';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    

}