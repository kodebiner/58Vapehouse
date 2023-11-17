<?php namespace App\Models;

use CodeIgniter\Model;

class CashExpModel extends Model
{
    protected $allowedFields = [
        'description','cashid','qty','date',
    ];

    protected $table      = 'cashexp';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}