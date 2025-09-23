<?php
require_once 'header.php';
if (!$user || $user['role'] !== 'patient') { header('Location: login.php'); exit; }
$errors = [];
$msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_ticket'])) {
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if (!$subject || !$message) $errors[] = 'Both subject and message required.';
        if (!$errors) {
            $pdo->prepare("INSERT INTO tickets (patient_id, subject, message) VALUES (?,?,?)")
                ->execute([$user['id'], $subject, $message]);
            $msg = "Ticket submitted. Our team will respond.";
        }
    } elseif (isset($_POST['chat'])) {
        // Tiny rule-based chatbot
        $q = strtolower(trim($_POST['query'] ?? ''));
        if (strpos($q, 'appointment') !== false) $bot = "You can book appointments from your Dashboard → 'Book Appointment'. If you need urgent help, contact reception.";
        elseif (strpos($q, 'counsel') !== false || strpos($q, 'counselling') !== false) $bot = "Counselling slots are available—please book via 'Book Appointment' and choose a mental health counsellor (tagged as doctor).";
        elseif (strpos($q, 'attendance') !== false) $bot = "Use 'Check in' on your upcoming appointment card to mark attendance. Low attendance alerts appear on your dashboard.";
        else $bot = "Thanks — a human will respond to your ticket. For simple appointment help ask: 'How to book appointment?'.";
    }
}

// fetch tickets
$t = $pdo->prepare("SELECT * FROM tickets WHERE patient_id = ? ORDER BY created_at DESC LIMIT 10");
$t->execute([$user['id']]);
$tickets = $t->fetchAll();
?>
<div class="row">
  <div class="col-md-6">
    <div class="app-card">
      <h5>AI Chatbot (mini)</h5>
      <form method="post" class="mb-3">
        <div class="input-group">
          <input name="query" class="form-control" placeholder="Ask about appointments, counselling, attendance...">
          <button name="chat" class="btn btn-outline-primary">Ask</button>
        </div>
      </form>
      <?php if(isset($bot)): ?>
        <div class="border rounded p-3"><strong>Bot:</strong> <?= e($bot) ?></div>
      <?php else: ?>
        <div class="text-muted small">Try: "How to book appointment?" or "I need counselling".</div>
      <?php endif; ?>
    </div>

    <div class="app-card mt-3">
      <h5>Open a Ticket</h5>
      <?php if($errors): ?><div class="alert alert-danger"><?php foreach($errors as $er) echo "<div>".e($er)."</div>"; ?></div><?php endif; ?>
      <?php if($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-2"><input name="subject" class="form-control" placeholder="Subject"></div>
        <div class="mb-2"><textarea name="message" class="form-control" rows="4" placeholder="Message"></textarea></div>
        <button name="create_ticket" class="btn btn-primary">Send Ticket</button>
      </form>
    </div>
  </div>

  <div class="col-md-6">
    <div class="app-card">
      <h5>Your Tickets</h5>
      <?php if($tickets): ?>
        <ul class="list-group">
          <?php foreach($tickets as $tk): ?>
            <li class="list-group-item">
              <strong><?= e($tk['subject']) ?></strong>
              <div class="small text-muted"><?= e($tk['created_at']) ?> · <?= e($tk['status']) ?></div>
              <div class="mt-2"><?= e(substr($tk['message'],0,200)) ?></div>
              <?php if($tk['response']): ?><div class="mt-2 alert alert-light">Response: <?= e($tk['response']) ?></div><?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted small">No tickets yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
