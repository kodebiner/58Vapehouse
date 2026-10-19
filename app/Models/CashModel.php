<?php namespace App\Models;

use CodeIgniter\Model;

class CashModel extends Model
{
    // Tipe dompet
    const TYPE_CASH     = 0; // Tunai
    const TYPE_NON_CASH = 1; // Non Tunai

    protected $allowedFields = [
        'outletid','type','name','qty',

    ];

    protected $table      = 'cash';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    
    /**
     * Ambil dompet berdasarkan outlet dan tipe (0=Tunai, 1=Non Tunai).
     */
    public function getByOutletAndType($outletid, $type)
    {
        return $this->where('outletid', $outletid)->where('type', $type)->first();
    }

    /**
     * Label tipe dompet.
     */
    public static function typeLabel($type)
    {
        return ((int) $type === self::TYPE_NON_CASH)
            ? lang('Global.nonCash')
            : lang('Global.cashType');
    }
}