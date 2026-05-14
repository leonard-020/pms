<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(): void
    {
        $page   = (int) ($this->request->query('page', 1) ?: 1);
        $search = $this->request->query('search', '');
        $filters = [
            'action'    => $this->request->query('action', ''),
            'module'    => $this->request->query('module', ''),
            'date_from' => $this->request->query('date_from', ''),
            'date_to'   => $this->request->query('date_to', ''),
        ];

        $model = new AuditLog();
        $logs  = $model->paginateWithUser($page, 25, $filters, $search);

        $this->layout('main', 'audit/index', [
            'title'   => 'Audit Logs',
            'logs'    => $logs,
            'filters' => array_map(fn($v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8'), $filters),
            'search'  => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
        ]);
    }
}