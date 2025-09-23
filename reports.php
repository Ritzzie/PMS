<?php
require_once 'header.php';
if (!$user || $user['role'] !== 'admin') { header('Location: login.php'); exit; }

// quick stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_doctors = $pdo->query("SELECT COUNT(*) FROM users WHERE role='doctor'")->fetchColumn();
$total_patients = $pdo->query("SELECT COUNT(*) FROM users WHERE role='patient'")->fetchColumn();
$total_appts = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$completed_appts = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='completed'")->fetchColumn();
?>
<div class="app-card">
  <h3>Reports & Analytics</h3>
  <ul class="list-group">
    <li class="list-group-item">Total Users: <strong><?= e($total_users) ?></strong></li>
    <li class="list-group-item">Doctors: <strong><?= e($total_doctors) ?></strong></li>
    <li class="list-group-item">Patients: <strong><?= e($total_patients) ?></strong></li>
    <li class="list-group-item">Appointments (All): <strong><?= e($total_appts) ?></strong></li>
    <li class="list-group-item">Appointments Completed: <strong><?= e($completed_appts) ?></strong></li>
  </ul>
</div>
<?php require_once 'footer.php'; ?>

