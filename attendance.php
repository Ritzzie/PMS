<?php
require_once 'header.php';
if (!$user) { header('Location: login.php'); exit; }

$appt_id = (int)($_GET['appt'] ?? 0);
$appt = null;
if ($appt_id) {
    $stmt = $pdo->prepare("SELECT a.*, u.full_name AS doctor_name FROM appointments a JOIN users u ON u.id=a.doctor_id WHERE a.id=?");
    $stmt->execute([$appt_id]);
    $appt = $stmt->fetch();
}

$done = false;
if ($appt && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // create or update attendance
    $stmt = $pdo->prepare("INSERT INTO attendance (appointment_id, checked_in_at, present) VALUES (?,?,1) ON DUPLICATE KEY UPDATE checked_in_at=VALUES(checked_in_at), present=VALUES(present)");
    // ensure unique constraint? If not, do simple update if exists:
    $exists = $pdo->prepare("SELECT id FROM attendance WHERE appointment_id=?");
    $exists->execute([$appt_id]);
    if ($exists->fetch()) {
        $pdo->prepare("UPDATE attendance SET checked_in_at=NOW(), present=1 WHERE appointment_id=?")->execute([$appt_id]);
    } else {
        $pdo->prepare("INSERT INTO attendance (appointment_id, checked_in_at, present) VALUES (?,NOW(),1)")->execute([$appt_id]);
    }
    $done = true;
}
?>
<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="app-card text-center">
      <?php if(!$appt): ?>
        <h4>Invalid appointment</h4>
        <p class="text-muted">No appointment found.</p>
      <?php elseif($done): ?>
        <h4>Checked in ✅</h4>
        <p class="text-muted">You checked in at <?= date('d M Y H:i') ?> for appointment with <?= e($appt['doctor_name']) ?>.</p>
        <a class="btn btn-primary" href="dashboard.php">Back to dashboard</a>
      <?php else: ?>
        <h4>Check-in for <?= e($appt['doctor_name']) ?></h4>
        <p><?= e(date('d M Y H:i', strtotime($appt['scheduled_at']))) ?></p>
        <form method="post"><button class="btn btn-primary">Check in now</button></form>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once 'footer.php'; ?>
