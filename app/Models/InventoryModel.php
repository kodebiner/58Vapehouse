<?php namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $allowedFields = [
        'outletid','name','qty',

    ];

    protected $table      = 'inventory';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}