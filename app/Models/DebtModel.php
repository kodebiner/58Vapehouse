<?php namespace App\Models;

use CodeIgniter\Model;

class DebtModel extends Model
{
    protected $allowedFields = [
        'memberid','transactionid','deadline',

    ];

    protected $table      = 'debt';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}