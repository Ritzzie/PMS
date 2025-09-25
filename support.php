<?php
require_once 'header.php';

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $message === '') {
        $error = "Name and message are required.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO counselling_requests (name, contact, message)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$name, $contact, $message]);
            $success = "Your request has been submitted successfully!";
        } catch (Exception $e) {
            $error = "Error saving request: " . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="app-card">
      <h2 class="mb-3">Support / Counselling Request</h2>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
      <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" class="mt-3">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Contact (email or phone)</label>
          <input type="text" name="contact" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Request</button>
      </form>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
