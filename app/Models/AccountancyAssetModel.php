<?php namespace App\Models;

use CodeIgniter\Model;

class AccountancyAssetModel extends Model
{
    protected $allowedFields = [
        'date','code_asset','name','description','cat_asset_tetap','value_asset_tetap','cat_tax','value_tax','cat_asset_credit','image_asset','depreciation_status','depreciation_method','depreciation_residu','depreciation_benefit_era','depreciation_cat_penyusutan','depreciation_sum_cat_penyusutan'

    ];

    protected $table      = 'accountancy_asset';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}