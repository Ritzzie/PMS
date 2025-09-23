<?php
require_once 'header.php';
if (!$user || $user['role'] !== 'patient') { header('Location: login.php'); exit; }

// fetch doctors
$doctors = $pdo->query("SELECT id, full_name, email FROM users WHERE role='doctor'")->fetchAll();

// handle booking
$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $doctor_id = (int)$_POST['doctor_id'];
    $when = $_POST['scheduled_at'] ?? '';
    if ($doctor_id && $when) {
        // simple availability: check same time exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id=? AND scheduled_at = ? AND status = 'booked'");
        $stmt->execute([$doctor_id, $when]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            // add to waitlist
            $pdo->prepare("INSERT INTO waitlist (patient_id, doctor_id, reason) VALUES (?,?,?)")
                ->execute([$user['id'], $doctor_id, 'Auto waitlist (slot taken)']);
            $msg = "Slot taken — you have been added to the waitlist.";
        } else {
            $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, scheduled_at) VALUES (?,?,?)")
                ->execute([$user['id'], $doctor_id, $when]);
            $msg = "Appointment booked successfully.";
        }
    } else {
        $msg = "Select a doctor and time.";
    }
}

// fetch upcoming appointments
$appts = $pdo->prepare("SELECT a.*, u.full_name AS doctor_name FROM appointments a JOIN users u ON a.doctor_id=u.id WHERE a.patient_id=? ORDER BY a.scheduled_at DESC");
$appts->execute([$user['id']]);
$appts = $appts->fetchAll();
?>
<div class="row">
  <div class="col-md-7">
    <div class="app-card">
      <h4>Your Appointments</h4>
      <?php if($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>
      <?php if($appts): ?>
        <div class="list-group">
          <?php foreach($appts as $a): ?>
            <div class="list-group-item d-flex justify-content-between align-items-start">
              <div>
                <div><strong><?= e($a['doctor_name']) ?></strong> — <?= e(date('d M Y, H:i', strtotime($a['scheduled_at']))) ?></div>
                <div class="text-muted small">Status: <?= e($a['status']) ?></div>
              </div>
              <div>
                <?php if($a['status'] === 'booked'): ?>
                  <a href="attendance.php?appt=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary">Check in</a>
                <?php endif; ?>
                <a href="view_record.php?appt=<?= $a['id'] ?>" class="btn btn-sm btn-light">View</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-muted">No appointments yet.</p>
      <?php endif; ?>
    </div>

    <div class="app-card mt-4">
      <h4>Support & Tickets</h4>
      <p class="text-muted">Report issues or ask general questions.</p>
      <a class="btn btn-outline-primary" href="support.php">Open Support Ticket</a>
    </div>

  </div>

  <div class="col-md-5">
    <div class="app-card">
      <h4>Book Appointment</h4>
      <form method="post">
        <div class="mb-3">
          <label>Doctor</label>
          <select name="doctor_id" class="form-select">
            <option value="">Choose doctor</option>
            <?php foreach($doctors as $d): ?>
              <option value="<?= $d['id'] ?>"><?= e($d['full_name']) ?> — <?= e($d['email']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Date & time</label>
          <input type="datetime-local" name="scheduled_at" class="form-control">
          <div class="form-text">Use local date & time.</div>
        </div>
        <button name="book" class="btn btn-primary">Book</button>
      </form>
    </div>

    <div class="app-card mt-4">
      <h5>Treatments & Records</h5>
      <p class="text-muted">View your treatment and disciplinary records below.</p>
      <?php
        $rec = $pdo->prepare("SELECT * FROM patient_records WHERE patient_id=? ORDER BY created_at DESC LIMIT 6");
        $rec->execute([$user['id']]);
        $records = $rec->fetchAll();
      ?>
      <?php if($records): ?>
        <ul class="list-group">
          <?php foreach($records as $r): ?>
            <li class="list-group-item small">
              <strong><?= e(ucfirst($r['record_type'])) ?>:</strong> <?= e(substr($r['notes'],0,100)) ?>
              <div class="small text-muted"><?= e($r['created_at']) ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="small text-muted">No records found.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
