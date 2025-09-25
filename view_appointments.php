<?php
require_once 'header.php';

// Redirect if not logged in
if (!$user) {
    header("Location: login.php");
    exit;
}

// Fetch appointments
$stmt = $pdo->query("
  SELECT a.id, a.appointment_time, a.practitioner, a.reason, a.status,
         p.full_name AS patient_name
  FROM appointments a
  JOIN patients p ON p.id = a.patient_id
  ORDER BY a.appointment_time ASC
");
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row justify-content-center">
  <div class="col-md-11">
    <div class="app-card">
      <h2 class="mb-3">All Appointments</h2>
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Patient</th>
            <th>Time</th>
            <th>Practitioner</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($appointments as $a): ?>
          <tr>
            <td><?= e($a['patient_name']) ?></td>
            <td><?= date("M d, Y H:i", strtotime($a['appointment_time'])) ?></td>
            <td><?= e($a['practitioner']) ?></td>
            <td><?= e($a['reason']) ?></td>
            <td>
              <?php
                $status = $a['status'];
                $badgeClass = match ($status) {
                  'booked' => 'primary',
                  'checked-in' => 'warning text-dark',
                  'complete' => 'success',
                  'cancelled' => 'danger',
                  default => 'secondary',
                };
              ?>
              <span class="badge bg-<?= $badgeClass ?>"><?= e($status) ?></span>
            </td>
            <td>
              <form method="post" action="update_status.php" class="d-inline">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <input type="hidden" name="status" value="checked-in">
                <button class="btn btn-sm btn-outline-warning"
                  <?= $status !== 'booked' ? 'disabled' : '' ?>>
                  Check-in
                </button>
              </form>
              <form method="post" action="update_status.php" class="d-inline">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <input type="hidden" name="status" value="complete">
                <button class="btn btn-sm btn-outline-success"
                  <?= $status === 'complete' || $status === 'cancelled' ? 'disabled' : '' ?>>
                  Complete
                </button>
              </form>
              <form method="post" action="update_status.php" class="d-inline">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <input type="hidden" name="status" value="cancelled">
                <button class="btn btn-sm btn-outline-danger"
                  <?= $status === 'cancelled' ? 'disabled' : '' ?>>
                  Cancel
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
