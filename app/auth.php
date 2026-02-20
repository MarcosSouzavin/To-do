<?php
declare(strict_types=1);

function start_session(): void {
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
}

function is_logged_in(): bool {
  start_session();
  return isset($_SESSION['user_id']);
}

function require_login(): void {
  if (!is_logged_in()) {
    header('Location: /To-do/public/login.php');
    exit;
  }
}

function current_user_id(): int {
  start_session();
  return (int)($_SESSION['user_id'] ?? 0);
}