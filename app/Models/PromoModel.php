<?php namespace App\Models;

use CodeIgniter\Model;

class PromoModel extends Model
{
    protected $allowedFields = [
        'name','photo','status','description',

    ];

    protected $table      = 'promo';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}