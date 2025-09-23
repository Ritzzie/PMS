<?php
require_once 'header.php';
if (!$user || $user['role'] !== 'admin') { header('Location: login.php'); exit; }

$appts = $pdo->query("SELECT a.id, a.scheduled_at, a.status, 
                             p.full_name AS patient_name, d.full_name AS doctor_name
                      FROM appointments a
                      JOIN users p ON a.patient_id=p.id
                      JOIN users d ON a.doctor_id=d.id
                      ORDER BY a.scheduled_at DESC")->fetchAll();
?>
<div class="app-card">
  <h3>All Appointments</h3>
  <table class="table table-bordered">
    <thead><tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Time</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach($appts as $a): ?>
      <tr>
        <td><?= e($a['id']) ?></td>
        <td><?= e($a['patient_name']) ?></td>
        <td><?= e($a['doctor_name']) ?></td>
        <td><?= e(date('d M Y H:i', strtotime($a['scheduled_at']))) ?></td>
        <td><?= e($a['status']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once 'footer.php'; ?>
