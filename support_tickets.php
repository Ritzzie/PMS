<?php
require_once 'header.php';
if (!$user || $user['role'] !== 'admin') { header('Location: login.php'); exit; }

// fetch tickets (assuming you have a support table)
$tickets = $pdo->query("SELECT id, user_id, subject, status, created_at FROM support ORDER BY created_at DESC")->fetchAll();
?>
<div class="app-card">
  <h3>Support Tickets</h3>
  <table class="table table-hover">
    <thead><tr><th>ID</th><th>User</th><th>Subject</th><th>Status</th><th>Created</th></tr></thead>
    <tbody>
      <?php foreach($tickets as $t): ?>
        <tr>
          <td><?= e($t['id']) ?></td>
          <td><?= e($t['user_id']) ?></td>
          <td><?= e($t['subject']) ?></td>
          <td><?= e($t['status']) ?></td>
          <td><?= e($t['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once 'footer.php'; ?>
