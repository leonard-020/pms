<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Member;
use App\Models\FinanceTransaction;
use App\Models\Event;
use App\Models\Group;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Session::get('user');
        $role = $user['role_slug'];

        $data = ['title' => 'Dashboard', 'user' => $user, 'role' => $role];

        // Stats based on role permissions
        $canViewMembers = in_array($role, ['super_admin', 'parish_priest', 'parish_secretary', 'system_auditor', 'ministry_leader']);
        $canViewFinance = in_array($role, ['super_admin', 'parish_priest', 'finance_officer', 'system_auditor']);
        $canViewEvents  = true; // Almost all roles
        $canViewGroups  = in_array($role, ['super_admin', 'ministry_leader', 'parish_priest']);

        if ($canViewMembers) {
            $memberModel = new Member();
            $data['total_members'] = $memberModel->count(['status' => 'active']);
        }

        if ($canViewFinance) {
            $financeModel = new FinanceTransaction();
            $summary = $financeModel->getSummary();
            $data['total_income']  = $summary['total_income'];
            $data['total_expense'] = $summary['total_expense'];
            $data['net_balance']   = $summary['net'];
            $data['pending_txns']  = $summary['pending_count'];
        }

        if ($canViewEvents) {
            $eventModel = new Event();
            $data['upcoming_events'] = $eventModel->getUpcoming(5);
        }

        if ($canViewGroups) {
            $groupModel = new Group();
            $data['total_groups'] = $groupModel->count(['status' => 'active']);
        }

        $data['success'] = Session::flash('success');
        $data['error']   = Session::flash('error');

        $this->layout('main', 'dashboard/index', $data);
    }
}