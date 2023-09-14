<?php namespace App\Models;

use CodeIgniter\Model;

class DailyReportModel extends Model
{
    protected $table      = 'dailyreport';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $allowedFields = [
        'dateopen','dateclose','useridopen','useridclose','outletid','initialcash','totalcashin','totalcashout','cashclose','noncashclose',
    ];
}