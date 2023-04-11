<?php namespace App\Models;

use CodeIgniter\Model;

class StockadjusmentModel extends Model
{
    protected $allowedFields = [
        'outletid','variantid','restock','sale','qty',

    ];

    protected $table      = 'stockadjusment';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}