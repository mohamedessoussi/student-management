<?php
// ============================================================
// index.php
// Entry point: redirect to dashboard (or login if not authed)
// ============================================================

session_start();

if (!empty($_SESSION['logged_in'])) {
    header('Location: dashboard.php');
} else {
    header('Location: auth/login.php');
}
exit;
