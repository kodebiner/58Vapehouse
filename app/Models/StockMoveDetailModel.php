<?php namespace App\Models;

use CodeIgniter\Model;

class StockMoveDetailModel extends Model
{
    protected $allowedFields = [
        'stockmoveid','variantid','qty',

    ];

    protected $table      = 'stockmovedetail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}