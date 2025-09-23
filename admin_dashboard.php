<?php
require_once 'header.php';

// only allow admins
if (!$user || $user['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>
<div class="row">
  <div class="col-md-8">
    <div class="app-card">
      <h3>Admin Dashboard</h3>
      <p class="text-muted">Welcome, <?= e($user['full_name']) ?> (Admin)</p>

      <div class="list-group">
        <a href="manage_users.php" class="list-group-item list-group-item-action">
          👥 Manage Users
        </a>
        <a href="view_appointments.php" class="list-group-item list-group-item-action">
          📅 View All Appointments
        </a>
        <a href="support_tickets.php" class="list-group-item list-group-item-action">
          🛠 Support Tickets
        </a>
        <a href="reports.php" class="list-group-item list-group-item-action">
          📊 Reports
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="app-card">
      <h5>Quick Stats</h5>
      <?php
        // total users
        $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        // total doctors
        $total_doctors = $pdo->query("SELECT COUNT(*) FROM users WHERE role='doctor'")->fetchColumn();
        // total patients
        $total_patients = $pdo->query("SELECT COUNT(*) FROM users WHERE role='patient'")->fetchColumn();
      ?>
      <p>Total users: <strong><?= e($total_users) ?></strong></p>
      <p>Doctors: <strong><?= e($total_doctors) ?></strong></p>
      <p>Patients: <strong><?= e($total_patients) ?></strong></p>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
