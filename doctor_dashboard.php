<?php
require_once 'header.php';
if (!$user || $user['role'] !== 'doctor') { header('Location: login.php'); exit; }

// fetch doctor's appointments
$stmt = $pdo->prepare("SELECT a.*, u.full_name AS patient_name, u.email AS patient_email FROM appointments a JOIN users u ON a.patient_id = u.id WHERE a.doctor_id = ? ORDER BY a.scheduled_at DESC");
$stmt->execute([$user['id']]);
$appts = $stmt->fetchAll();

// simple endpoint to mark as completed or to add notes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_completed'])) {
        $id = (int)$_POST['appt_id'];
        $pdo->prepare("UPDATE appointments SET status='completed' WHERE id=? AND doctor_id=?")->execute([$id, $user['id']]);
    } elseif (isset($_POST['add_record'])) {
        $pid = (int)$_POST['patient_id'];
        $type = $_POST['record_type'];
        $notes = $_POST['notes'];
        $pdo->prepare("INSERT INTO patient_records (patient_id, record_type, notes) VALUES (?,?,?)")->execute([$pid, $type, $notes]);
    }
    header('Location: doctor_dashboard.php');
    exit;
}
?>
<div class="row">
  <div class="col-md-8">
    <div class="app-card">
      <h4>Your Schedule</h4>
      <?php if($appts): ?>
        <div class="list-group">
          <?php foreach($appts as $a): ?>
            <div class="list-group-item d-flex justify-content-between">
              <div>
                <div><strong><?= e($a['patient_name']) ?></strong> — <?= e(date('d M Y H:i', strtotime($a['scheduled_at']))) ?></div>
                <div class="text-muted small"><?= e($a['patient_email']) ?> · Status: <?= e($a['status']) ?></div>
              </div>
              <div>
                <form method="post" style="display:inline">
                  <input type="hidden" name="appt_id" value="<?= $a['id'] ?>">
                  <button name="mark_completed" class="btn btn-sm btn-success">Mark Completed</button>
                </form>
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#recordModal" data-pid="<?= $a['patient_id'] ?>">Add Record</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-muted">No appointments scheduled.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-md-4">
    <div class="app-card">
      <h5>Quick Stats</h5>
      <?php
        $total = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id=?");
        $total->execute([$user['id']]);
        $total = $total->fetchColumn();
      ?>
      <p>Total appointments: <strong><?= e($total) ?></strong></p>
      <p><a href="view_waitlist.php" class="btn btn-outline-primary btn-sm">View Waitlist</a></p>
    </div>
  </div>
</div>

<!-- Modal for adding record -->
<div class="modal fade" id="recordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add Patient Record</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="patient_id" id="patient_id">
        <div class="mb-3">
          <label>Type</label>
          <select name="record_type" class="form-select"><option value="treatment">Treatment</option><option value="disciplinary">Disciplinary</option></select>
        </div>
        <div class="mb-3"><label>Notes</label><textarea name="notes" class="form-control" rows="4"></textarea></div>
      </div>
      <div class="modal-footer"><button type="submit" name="add_record" class="btn btn-primary">Save</button></div>
    </form>
  </div>
</div>

<script>
var recordModal = document.getElementById('recordModal');
recordModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var pid = button.getAttribute('data-pid');
  document.getElementById('patient_id').value = pid;
});
</script>

<?php require_once 'footer.php'; ?>
