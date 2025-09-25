<?php
// header.php
require_once 'config.php';
$user = current_user($pdo);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Pakenham PMS</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{--brand:#2563eb;--muted:#6b7280}
    body {background: linear-gradient(180deg,#f8fafc 0%, #ffffff 60%); min-height:100vh;}
    .app-card{border-radius:14px; box-shadow: 0 6px 20px rgba(16,24,40,0.08); padding:18px;}
    .brand {color:var(--brand); font-weight:700;}
    .nav-cta {background:linear-gradient(90deg,#2563eb,#7c3aed); color:white; padding:.35rem .8rem; border-radius:8px;}
    footer {font-size:.85rem; color:var(--muted); padding:18px 0;}
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white py-3 shadow-sm">
  <div class="container">
    <a class="navbar-brand brand" href="index.php">Pakenham PMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if($user): ?>
          <li class="nav-item me-2">Hello, <strong><?= e($user['full_name']) ?></strong></li>

          <?php if($user['role'] === 'doctor'): ?>
            <li class="nav-item"><a class="nav-link" href="doctor_dashboard.php">Doctor Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="view_appointments.php">Appointments</a></li>
            <li class="nav-item"><a class="nav-link" href="support_tickets.php">Support Tickets</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <?php endif; ?>

          <li class="nav-item"><a class="nav-link" href="support.php">Support</a></li>
          <li class="nav-item"><a class="nav-link nav-cta" href="logout.php">Logout</a></li>

        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
          <li class="nav-item"><a class="nav-link nav-cta" href="login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container my-5">
