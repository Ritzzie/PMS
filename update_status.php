<?php
require_once 'config.php';
session_start();

// Ensure user is logged in
if (!$user) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['id'], $_POST['status'])) {
    die("Invalid request");
}

$id = (int) $_POST['id'];
$status = $_POST['status'];

$allowed = ['booked','checked-in','complete','cancelled'];
if (!in_array($status, $allowed)) {
    die("Invalid status");
}

// Update the appointment
$stmt = $pdo->prepare("UPDATE appointments SET status = ?, updated_at = NOW() WHERE id = ?");
$stmt->execute([$status, $id]);

// After update, redirect back
header("Location: view_appointments.php");
exit;
