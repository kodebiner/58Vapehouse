<?php namespace App\Models;

use CodeIgniter\Model;

class VariantModel extends Model
{
    protected $allowedFields = [
        'productid','name','hargadasar','hargamodal','hargajual','hargarekomendasi'

    ];

    protected $table      = 'variant';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}