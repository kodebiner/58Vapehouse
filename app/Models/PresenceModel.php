<?php namespace App\Models;

use CodeIgniter\Model;

class PresenceModel extends Model
{
    protected $allowedFields = [
        'userid','datetime','geoloc','status','photo',

    ];

    protected $table      = 'presence';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}