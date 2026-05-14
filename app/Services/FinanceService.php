<?php

namespace App\Services;

use App\Core\Validator;
use App\Core\CSRF;
use App\Core\Database;
use App\Models\FinanceTransaction;
use App\Models\User;

class FinanceService
{
    private FinanceTransaction $transactionModel;
    private AuditService $audit;

    public function __construct()
    {
        $this->transactionModel = new FinanceTransaction();
        $this->audit = new AuditService();
    }

    public function create(array $data): array
    {
        if (!CSRF::check()) {
            return ['success' => false, 'errors' => ['_token' => ['Invalid security token.']]];
        }

        $validator = new Validator($data, [
            'type'             => 'required|in:income,expense',
            'category'         => 'required|max:100',
            'amount'           => 'required|numeric|min:0.01',
            'description'      => 'nullable|max:500',
            'transaction_date' => 'required|date',
            'notes'            => 'nullable|max:1000',
        ]);

        if (!$validator->validate()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        $userId = \App\Core\Session::get('user.id');
        $data['status'] = 'pending';
        $data['recorded_by'] = $userId;

        $id = $this->transactionModel->create($data);

        $this->audit->log(
            'create', 'finance',
            "Recorded {$data['type']} transaction: {$data['category']} - " . number_format($data['amount'], 2),
            null,
            $data
        );

        return ['success' => true, 'id' => $id];
    }

    public function approve(int $id, string $note = ''): array
    {
        $userId = \App\Core\Session::get('user.id');

        $transaction = $this->transactionModel->find($id);
        if (!$transaction) {
            return ['success' => false, 'message' => 'Transaction not found.'];
        }

        if ($transaction['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Only pending transactions can be approved.'];
        }

        // Dual control: cannot approve own transaction
        if ($transaction['recorded_by'] == $userId) {
            return ['success' => false, 'message' => 'You cannot approve your own transaction. Dual control required.'];
        }

        $this->transactionModel->update($id, [
            'status'      => 'approved',
            'approved_by' => $userId,
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        $this->audit->log(
            'approve', 'finance',
            "Approved transaction #{$id}: {$transaction['category']} - " . number_format($transaction['amount'], 2),
            $transaction,
            ['status' => 'approved', 'approved_by' => $userId]
        );

        return ['success' => true, 'message' => 'Transaction approved successfully.'];
    }

    public function reject(int $id, string $note): array
    {
        $userId = \App\Core\Session::get('user.id');

        $transaction = $this->transactionModel->find($id);
        if (!$transaction) {
            return ['success' => false, 'message' => 'Transaction not found.'];
        }

        if ($transaction['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Only pending transactions can be rejected.'];
        }

        if ($transaction['recorded_by'] == $userId) {
            return ['success' => false, 'message' => 'You cannot reject your own transaction.'];
        }

        $this->transactionModel->update($id, [
            'status'         => 'rejected',
            'approved_by'    => $userId,
            'approved_at'    => date('Y-m-d H:i:s'),
            'rejection_note' => $note,
        ]);

        $this->audit->log(
            'reject', 'finance',
            "Rejected transaction #{$id}: {$transaction['category']}. Reason: {$note}",
            $transaction,
            ['status' => 'rejected', 'rejection_note' => $note]
        );

        return ['success' => true, 'message' => 'Transaction rejected.'];
    }
}