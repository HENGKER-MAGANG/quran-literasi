<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  if ($_SESSION['user_id'] != $id) {
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
  }
}
header("Location: users.php");
exit;
?>
