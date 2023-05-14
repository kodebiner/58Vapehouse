<?php namespace App\Models;

use CodeIgniter\Model;

class OldStockModel extends Model
{
    protected $allowedFields = [
        'variantid','hargadasar','hargamodal',

    ];

    protected $table      = 'oldstock';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}