<?php namespace App\Models;

use CodeIgniter\Model;

class VariantModel extends Model
{
    protected $allowedFields = [
        'productid','name','hargdasar','hargamodal','hargajual',

    ];

    protected $table      = 'variant';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}