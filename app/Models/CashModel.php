<?php namespace App\Models;

use CodeIgniter\Model;

class CashModel extends Model
{
    protected $allowedFields = [
        'outletid','description','qty','userid','type','date',

    ];

    protected $table      = 'cash';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}