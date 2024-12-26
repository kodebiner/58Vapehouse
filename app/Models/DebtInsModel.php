<?php namespace App\Models;

use CodeIgniter\Model;

class DebtInsModel extends Model
{
    protected $allowedFields = [
        'debtid','transactionid', 'paymentid', 'outletid', 'userid', 'date', 'qty',

    ];

    protected $table      = 'debtins';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

}