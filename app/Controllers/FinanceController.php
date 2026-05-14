<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Models\FinanceTransaction;
use App\Services\FinanceService;

class FinanceController extends Controller
{
    private FinanceService $financeService;

    public function __construct()
    {
        parent::__construct();
        $this->financeService = new FinanceService();
    }

    public function index(): void
    {
        $page = (int) ($this->request->query('page', 1) ?: 1);
        $search = $this->request->query('search', '');
        $filters = [
            'type'     => $this->request->query('type', ''),
            'status'   => $this->request->query('status', ''),
            'category' => $this->request->query('category', ''),
            'date_from'=> $this->request->query('date_from', ''),
            'date_to'  => $this->request->query('date_to', ''),
        ];

        $model = new FinanceTransaction();
        $transactions = $model->paginateWithDetails($page, 15, $filters, $search);
        $summary = $model->getSummary($filters);

        $this->layout('main', 'finance/index', [
            'title'        => 'Finance',
            'transactions' => $transactions,
            'summary'      => $summary,
            'filters'      => array_map(fn($v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8'), $filters),
            'search'       => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
            'success'      => Session::flash('success'),
            'error'        => Session::flash('error'),
        ]);
    }

    public function create(): void
    {
        $this->layout('main', 'finance/create', [
            'title'  => 'Record Transaction',
            '_token' => CSRF::field(),
            'old'    => Session::flash('old') ?: [],
            'errors' => Session::flash('errors') ?: [],
        ]);
    }

    public function store(): void
    {
        $result = $this->financeService->create($this->request->all());

        if (!$result['success']) {
            Session::flash('errors', $result['errors']);
            Session::flash('old', $this->request->all());
            $this->redirect('/finance/create');
        }

        Session::flash('success', 'Transaction recorded. Awaiting approval.');
        $this->redirect('/finance');
    }

    public function approve(int $id): void
    {
        $result = $this->financeService->approve($id);

        if (!$result['success']) {
            Session::flash('error', $result['message']);
        } else {
            Session::flash('success', $result['message']);
        }

        $this->back();
    }

    public function reject(int $id): void
    {
        $note = $this->request->input('rejection_note', '');
        if (trim($note) === '') {
            Session::flash('error', 'Please provide a reason for rejection.');
            $this->back();
            return;
        }

        $result = $this->financeService->reject($id, $note);

        if (!$result['success']) {
            Session::flash('error', $result['message']);
        } else {
            Session::flash('success', $result['message']);
        }

        $this->back();
    }
}