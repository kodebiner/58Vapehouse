<?php namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $allowedFields = [
        'outletid','userid','memberid','memberdisc','value','disctype','discvalue','status','created_at','updated_at','deleted_at',

    ];

    protected $table            = 'booking';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
}