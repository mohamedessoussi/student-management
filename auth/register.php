<?php
// ============================================================
// auth/register.php
// New-user registration form
// ============================================================

session_start();

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
    $result = $user->register(
        $_POST['username'] ?? '',
        $_POST['email']    ?? '',
        $_POST['password'] ?? ''
    );

    $message = $result['message'];
    $type    = $result['success'] ? 'success' : 'danger';

    if ($result['success']) {
        // Redirect to login after a brief moment
        header('refresh:2;url=login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register · Student MS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
  <div class="auth-card">

    <div class="auth-logo"><i class="bi bi-mortarboard-fill"></i></div>
    <h2>Create account</h2>
    <p class="subtitle">Fill in the details below to get started</p>

    <?php if ($message): ?>
      <div class="alert alert-<?= $type ?> auto-dismiss d-flex align-items-center gap-2">
        <i class="bi bi-<?= $type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="register.php" novalidate>

      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0">
            <i class="bi bi-person text-muted"></i>
          </span>
          <input type="text" id="username" name="username" class="form-control border-start-0"
                 placeholder="johndoe" required
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
      </div>

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
        <label for="password" class="form-label">Password
          <small class="text-muted fw-normal">(min. 6 characters)</small>
        </label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0">
            <i class="bi bi-lock text-muted"></i>
          </span>
          <input type="password" id="password" name="password"
                 class="form-control border-start-0"
                 placeholder="••••••••" required minlength="6">
        </div>
      </div>

      <button type="submit" class="btn btn-accent w-100 py-2">
        <i class="bi bi-person-plus me-1"></i> Create Account
      </button>

    </form>

    <p class="text-center mt-3 mb-0" style="font-size:.875rem;color:var(--text-muted);">
      Already have an account?
      <a href="login.php" class="fw-semibold">Sign in</a>
    </p>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
