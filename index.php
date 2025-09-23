<?php
require_once 'header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-10">
    <div class="app-card p-4">
      <div class="row align-items-center">
        <div class="col-md-7">
          <h1 class="mb-2">Welcome to <span class="brand">Pakenham PMS</span></h1>
          <p class="text-muted">A minimal Patient Management System — register, book appointments, check-in, and get support.</p>
          <div class="mt-3">
            <?php if(!$user): ?>
              <a class="btn btn-primary me-2" href="register.php">Register as Patient</a>
              <a class="btn btn-outline-primary" href="login.php">Login</a>
            <?php else: ?>
              <a class="btn btn-primary" href="<?= $user['role'] === 'doctor' ? 'doctor_dashboard.php' : 'dashboard.php' ?>">Go to dashboard</a>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-md-5 text-center">
          <img src="https://images.unsplash.com/photo-1586773860411-5d9d8b2fa2c3?q=80&w=800&auto=format&fit=crop&crop=faces" alt="clinic" class="img-fluid rounded" style="max-height:200px;">
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once 'footer.php'; ?>
