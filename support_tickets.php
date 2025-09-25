<?php
require_once 'header.php';

// Restrict access: only logged-in doctors/admins should see this
if (!$user || $user['role'] !== 'doctor') {
    header("Location: index.php");
    exit;
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = (int) $_POST['id'];
    $status = $_POST['status'];
    $allowed = ['new', 'in-progress', 'closed'];
    if (in_array($status, $allowed)) {
        $stmt = $pdo->prepare("UPDATE counselling_requests SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
    header("Location: support_tickets.php");
    exit;
}

// Fetch all requests
$stmt = $pdo->query("SELECT * FROM counselling_requests ORDER BY created_at DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row justify-content-center">
  <div class="col-md-11">
    <div class="app-card">
      <h2 class="mb-3">Support / Counselling Requests</h2>

      <?php if (count($requests) === 0): ?>
        <p class="text-muted">No support requests yet.</p>
      <?php else: ?>
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Contact</th>
              <th>Message</th>
              <th>Status</th>
              <th>Submitted</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($requests as $r): ?>
              <tr>
                <td><?= e($r['id']) ?></td>
                <td><?= e($r['name']) ?></td>
                <td><?= e($r['contact']) ?></td>
                <td><?= nl2br(e($r['message'])) ?></td>
                <td>
                  <?php
                    $badgeClass = match($r['status']) {
                      'new' => 'primary',
                      'in-progress' => 'warning text-dark',
                      'closed' => 'success',
                      default => 'secondary'
                    };
                  ?>
                  <span class="badge bg-<?= $badgeClass ?>"><?= e($r['status']) ?></span>
                </td>
                <td><?= date("M d, Y H:i", strtotime($r['created_at'])) ?></td>
                <td>
                  <form method="post" class="d-inline">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <input type="hidden" name="status" value="in-progress">
                    <button class="btn btn-sm btn-outline-warning"
                      <?= $r['status'] !== 'new' ? 'disabled' : '' ?>>
                      In Progress
                    </button>
                  </form>
                  <form method="post" class="d-inline">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <input type="hidden" name="status" value="closed">
                    <button class="btn btn-sm btn-outline-success"
                      <?= $r['status'] === 'closed' ? 'disabled' : '' ?>>
                      Close
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
