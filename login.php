<?php
require_once 'header.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Provide a valid email.";
    if (!$password) $errors[] = "Password required.";

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id, password_hash, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        if ($u && password_verify($password, $u['password_hash'])) {
            $_SESSION['user_id'] = $u['id'];
            if ($u['role'] === 'doctor') header('Location: doctor_dashboard.php');
            else header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = "Invalid credentials.";
        }
    }
}
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="app-card">
      <h3>Login</h3>
      <?php if($errors): ?>
        <div class="alert alert-danger"><?php foreach($errors as $er) echo "<div>".e($er)."</div>"; ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3"><label>Email</label><input name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>"></div>
        <div class="mb-3"><label>Password</label><input name="password" type="password" class="form-control"></div>
        <button class="btn btn-primary">Login</button>
      </form>
    </div>
  </div>
</div>
<?php require_once 'footer.php'; ?>
