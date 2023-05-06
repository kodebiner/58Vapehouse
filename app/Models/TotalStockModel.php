<?php namespace App\Models;

use CodeIgniter\Model;

class TotalStockModel extends Model
{
    protected $allowedFields = [
        'variantid','hargadasar','hargamodal','qty',

    ];

    protected $table      = 'totalstock';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}