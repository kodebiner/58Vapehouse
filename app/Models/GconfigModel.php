<?php namespace App\Models;

use CodeIgniter\Model;

class GconfigModel extends Model
{
    protected $allowedFields = [
        'poinorder','poinvalue','memberdisc','memberdisctype','logo','bizname','ppn',

    ];

    protected $table      = 'gconfig';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}