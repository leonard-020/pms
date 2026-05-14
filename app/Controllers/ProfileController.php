<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Core\Validator;
use App\Models\Member;
use App\Models\User;
use App\Services\AuditService;

class ProfileController extends Controller
{
    public function index(): void
    {
        $userId = Session::get('user.id');

        $userModel  = new User();
        $user       = $userModel->findWithRole($userId);

        $memberModel = new Member();
        $member      = $memberModel->findByUserId($userId);

        $this->layout('main', 'profile/index', [
            'title'  => 'My Profile',
            'user'   => $user,
            'member' => $member,
            'success'=> Session::flash('success'),
        ]);
    }

    public function updateProfile(): void
    {
        if (!CSRF::check()) {
            Session::flash('error', 'Invalid security token.');
            $this->back();
            return;
        }

        $userId = Session::get('user.id');

        $validator = new Validator($this->request->all(), [
            'phone'      => 'nullable|phone',
            'address'    => 'nullable|max:255',
            'city'       => 'nullable|max:100',
            'state'      => 'nullable|max:100',
            'occupation' => 'nullable|max:150',
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->errors());
            $this->back();
            return;
        }

        $memberModel = new Member();
        $member = $memberModel->findByUserId($userId);

        if ($member) {
            $old = $member;
            $memberModel->update($member['id'], $this->request->all());
            (new AuditService())->log('update', 'profile', 'Updated own profile', $old, $this->request->all());
        }

        Session::flash('success', 'Profile updated successfully.');
        $this->redirect('/profile');
    }
}