<?php namespace App\Models;

use CodeIgniter\Model;

class TrxotherModel extends Model
{
    protected $allowedFields = [
        'id','userid','outletid','cashid','description','type','date','qty','photo',

    ];

    protected $table      = 'trxother';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}