<?php namespace App\Models;

use CodeIgniter\Model;

class SopModel extends Model
{
    protected $allowedFields = [
        'name','shift',
    ];

    protected $table      = 'sop';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}