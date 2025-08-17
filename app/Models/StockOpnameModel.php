<?php namespace App\Models;

use CodeIgniter\Model;

class StockOpnameModel extends Model
{
    protected $allowedFields = [
        'userid', 'outletid','date',

    ];

    protected $table      = 'stockopname';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}