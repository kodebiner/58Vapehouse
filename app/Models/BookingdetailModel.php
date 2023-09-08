<?php namespace App\Models;

use CodeIgniter\Model;

class BookingdetailModel extends Model
{
    protected $allowedFields = [
        'bookingid','variantid','bundleid','qty','description','value','discvar',

    ];

    protected $table      = 'bookingdetail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}