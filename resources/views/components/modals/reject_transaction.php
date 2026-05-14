<?php
// Usage: include 'components/modals/reject_transaction.php';
// This modal is shown via JS, form POSTs to /finance/{id}/reject
?>
<div id="reject-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeRejectModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-xl p-6">
        <h3 class="text-lg font-display font-semibold text-ink">Reject Transaction</h3>
        <p class="text-sm text-ink-muted mt-1">Please provide a reason for rejection.</p>

        <form id="reject-form" method="POST" action="">
            <?php echo \App\Core\CSRF::field(); ?>
            <input type="hidden" name="_method" value="POST">
            <textarea name="rejection_note" rows="3" required
                      class="form-input mt-4"
                      placeholder="Enter rejection reason..."></textarea>
            <div class="flex gap-3 mt-5 justify-end">
                <button type="button" onclick="closeRejectModal()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject Transaction</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id) {
    document.getElementById('reject-form').action = '/finance/' + id + '/reject';
    document.getElementById('reject-modal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>