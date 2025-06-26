<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MemberModel;

class MemberApiController extends BaseController
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
    }

    // Endpoint untuk search autocomplete
    public function search()
    {
        $q = $this->request->getGet('q');
        $results = $this->memberModel
            ->select('id, name, phone')
            ->groupStart()
                ->like('name', $q)
                ->orLike('phone', $q)
            ->groupEnd()
            ->limit(10)
            ->find();

        $data = array_map(function ($member) {
            return [
                'id'    => $member['id'],
                'label' => $member['name'] . ' / ' . $member['phone'],
                'value' => $member['name'],
            ];
        }, $results);

        return $this->response->setJSON($data);
    }

    // Endpoint untuk ambil detail 1 member (poin & phone)
    public function detail()
    {
        $id = $this->request->getGet('id');
        $member = $this->memberModel->select('phone, poin')->find($id);

        if ($member) {
            return $this->response->setJSON([
                'phone' => $member['phone'],
                'poin'  => $member['poin']
            ]);
        }

        return $this->response->setJSON(['error' => 'Not found'], 404);
    }
}
