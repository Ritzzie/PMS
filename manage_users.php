<?php
require_once 'header.php';
if (!$user || $user['role'] !== 'admin') { header('Location: login.php'); exit; }

// fetch all users
$users = $pdo->query("SELECT id, full_name, email, role, dob, phone FROM users ORDER BY id DESC")->fetchAll();
?>
<div class="app-card">
  <h3>Manage Users</h3>
  <table class="table table-bordered">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>DOB</th></tr></thead>
    <tbody>
      <?php foreach($users as $u): ?>
      <tr>
        <td><?= e($u['id']) ?></td>
        <td><?= e($u['full_name']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><?= e($u['role']) ?></td>
        <td><?= e($u['phone']) ?></td>
        <td><?= e($u['dob']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once 'footer.php'; ?>
