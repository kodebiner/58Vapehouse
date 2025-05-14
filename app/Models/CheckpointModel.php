<?php namespace App\Models;

use CodeIgniter\Model;

class CheckpointModel extends Model
{
    protected $table      = 'checkpoint';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $allowedFields = [
        'userid','outletid','cash','noncash','date',
    ];
}