<?php
require_once 'header.php';
if (!$user || $user['role'] !== 'doctor') { header('Location: login.php'); exit; }

$wl = $pdo->prepare("SELECT w.*, u.full_name AS patient_name, u.email FROM waitlist w JOIN users u ON u.id = w.patient_id WHERE w.doctor_id = ? ORDER BY w.requested_at DESC");
$wl->execute([$user['id']]);
$waitlist = $wl->fetchAll();
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="app-card">
      <h4>Waitlist</h4>
      <?php if($waitlist): ?>
        <ul class="list-group">
          <?php foreach($waitlist as $w): ?>
            <li class="list-group-item">
              <strong><?= e($w['patient_name']) ?></strong> — <?= e($w['email']) ?>
              <div class="small text-muted"><?= e($w['requested_at']) ?></div>
              <div class="mt-2"><?= e($w['reason']) ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">No waitlist entries.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once 'footer.php'; ?>
