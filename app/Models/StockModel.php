<?php namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $allowedFields = [
        'outletid','variantid','restock','sale','qty'
    ];

    protected $table      = 'stock';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    public function getStockList($search = null, $outletPick = null)
    {
        $this->select('
            stock.id,
            stock.qty,
            variant.id as variantid,
            variant.name as variantname,
            variant.hargamodal,
            product.name as productname,
            outlet.name as outletname,
            CONCAT(
                variant.name,
                " - ",
                product.name
            ) AS name
        ');

        $this->join('variant', 'variant.id = stock.variantid');
        $this->join('product', 'product.id = variant.productid');
        $this->join('outlet', 'outlet.id = stock.outletid');

        if ($search) {
            $this->like('product.name', $search);
        }

        if ($outletPick !== null) {
            $this->where('stock.outletid', $outletPick);
        }

        $this->orderBy('variant.id', 'DESC');

        return $this->paginate(20);
    }

    public function getStockSummary($search = null, $outletPick = null)
    {
        $builder = $this->db->table('stock');

        $builder->select('
            SUM(stock.qty) as totalstock,
            SUM(stock.qty * variant.hargamodal) as capsum,
            COUNT(stock.id) as stockcount
        ');

        $builder->join('variant', 'variant.id = stock.variantid');
        $builder->join('product', 'product.id = variant.productid');

        if ($search) {
            $builder->like('product.name', $search);
        }

        if ($outletPick !== null) {
            $builder->where('stock.outletid', $outletPick);
        }

        return $builder->get()->getRowArray();
    }
}