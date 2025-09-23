<?php
require_once 'header.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $dob = $_POST['dob'] ?? null;

    if (!$name) $errors[] = "Full name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 chars.";

    if (!$errors) {
        // ensure unique email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (role,email,password_hash,full_name,phone,dob) VALUES ('patient',?,?,?,?,?)");
            $stmt->execute([$email, $hash, $name, $phone, $dob]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="app-card">
      <h3>Patient Registration</h3>
      <?php if($errors): ?>
        <div class="alert alert-danger">
          <?php foreach($errors as $e) echo "<div>".e($e)."</div>"; ?>
        </div>
      <?php endif; ?>
      <form method="post" novalidate>
        <div class="mb-3">
          <label class="form-label">Full name</label>
          <input name="full_name" class="form-control" value="<?= e($_POST['full_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input name="password" type="password" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Date of birth</label>
          <input name="dob" type="date" class="form-control" value="<?= e($_POST['dob'] ?? '') ?>">
        </div>
        <button class="btn btn-primary">Register</button>
      </form>
    </div>
  </div>
</div>
<?php require_once 'footer.php'; ?>
