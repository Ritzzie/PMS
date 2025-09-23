<?php
require_once 'header.php';
if (!$user) { header('Location: login.php'); exit; }
$appt_id = (int)($_GET['appt'] ?? 0);
$stmt = $pdo->prepare("SELECT a.*, p.full_name as patient_name, d.full_name as doctor_name FROM appointments a JOIN users p ON p.id=a.patient_id JOIN users d ON d.id=a.doctor_id WHERE a.id=?");
$stmt->execute([$appt_id]);
$appt = $stmt->fetch();
if (!$appt) { header('Location: dashboard.php'); exit; }
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="app-card">
      <h4>Appointment Detail</h4>
      <p><strong>Patient:</strong> <?= e($appt['patient_name']) ?></p>
      <p><strong>Doctor:</strong> <?= e($appt['doctor_name']) ?></p>
      <p><strong>When:</strong> <?= e($appt['scheduled_at']) ?></p>
      <p><strong>Status:</strong> <?= e($appt['status']) ?></p>

      <hr>
      <h5>Past Records</h5>
      <?php
        $r = $pdo->prepare("SELECT * FROM patient_records WHERE patient_id = ? ORDER BY created_at DESC");
        $r->execute([$appt['patient_id']]);
        $records = $r->fetchAll();
      ?>
      <?php if($records): ?>
        <?php foreach($records as $rec): ?>
          <div class="mb-2">
            <strong><?= e(ucfirst($rec['record_type'])) ?></strong>
            <div class="small text-muted"><?= e($rec['created_at']) ?></div>
            <div><?= nl2br(e($rec['notes'])) ?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="small text-muted">No records for this patient.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once 'footer.php'; ?>
