<?php namespace App\Models;

use Myth\Auth\Models\UserModel as MythModel;

class UserModel extends MythModel
{
    protected $allowedFields = [
        'email', 'username', 'password_hash', 'reset_hash', 'reset_at', 'reset_expires', 'activate_hash',
        'status', 'status_message', 'active', 'force_pass_reset', 'permissions', 'deleted_at',
        'firstname', 'lastname', 'phone', 'photo','address',
    ];

    protected $returnType = 'App\Entities\User';

    protected function createContact(array $data)
    {
        $contactModel = new \App\Models\AccountancyContactModel();

        $userId = $data['id'];
        $user   = $this->find($userId);

        $contactModel->insert([
            'name'        => $user->firstname . ' ' . $user->lastname,
            'email'       => $user->email,
            'phone'       => $user->phone,
            'address'     => $user->address ?? '',
            'source_type' => 'user',
            'source_id'   => $userId,
        ]);
    }

    protected function updateContact(array $data)
    {
        $contactModel = new \App\Models\AccountancyContactModel();

        $userId = $data['id'];
        $user   = $this->find($userId);

        $contactModel
            ->where('source_type', 'user')
            ->where('source_id', $userId)
            ->set([
                'name'    => $user->firstname . ' ' . $user->lastname,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'address' => $user->address ?? '',
            ])
            ->update();
    }
}