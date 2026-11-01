<?php
// ============================================================
// auth/login.php
// Handles the login form and authenticates the user
// ============================================================

session_start();

// Already logged in → go straight to dashboard
if (!empty($_SESSION['logged_in'])) {
    header('Location: ../dashboard.php');
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/User.php';

use Classes\User;

$message = '';
$type    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user   = new User();
    $result = $user->login(
        $_POST['email']    ?? '',
        $_POST['password'] ?? ''
    );

    if ($result['success']) {
        header('Location: ../dashboard.php');
        exit;
    }

    $message = $result['message'];
    $type    = 'danger';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login · Student MS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
  <div class="auth-card">

    <!-- Logo -->
    <div class="auth-logo"><i class="bi bi-mortarboard-fill"></i></div>
    <h2>Welcome back</h2>
    <p class="subtitle">Sign in to your account to continue</p>

    <!-- Flash message -->
    <?php if ($message): ?>
      <div class="alert alert-<?= $type ?> auto-dismiss d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <!-- Login form -->
    <form method="POST" action="login.php" novalidate>

      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0">
            <i class="bi bi-envelope text-muted"></i>
          </span>
          <input type="email" id="email" name="email" class="form-control border-start-0"
                 placeholder="you@example.com" required
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0">
            <i class="bi bi-lock text-muted"></i>
          </span>
          <input type="password" id="password" name="password"
                 class="form-control border-start-0"
                 placeholder="••••••••" required>
        </div>
      </div>

      <button type="submit" class="btn btn-accent w-100 py-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
      </button>

    </form>

    <p class="text-center mt-3 mb-0" style="font-size:.875rem;color:var(--text-muted);">
      Don't have an account?
      <a href="register.php" class="fw-semibold">Create one</a>
    </p>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
