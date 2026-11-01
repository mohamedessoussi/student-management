<?php
// ============================================================
// includes/layout.php
// Shared HTML shell: <head>, sidebar, topbar
// Include at the top of every protected page.
//
// Expected variables set by the including page:
//   $pageTitle   string  – shown in <title> and topbar
//   $activeNav   string  – nav link to mark as active (e.g. 'dashboard')
// ============================================================

if (session_status() === PHP_SESSION_NONE) session_start();

// Depth from project root so asset paths work everywhere
$depth = $depth ?? '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'Student MS') ?> · Student MS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= $depth ?>assets/css/style.css">
</head>
<body>

<!-- ── Mobile toggle ─────────────────────────────────────── -->
<button class="btn mobile-toggle position-fixed top-0 start-0 m-2 z-3 d-md-none"
        style="background:#1e1b4b;color:#fff;border-radius:8px;width:38px;height:38px;">
  <i class="bi bi-list"></i>
</button>

<!-- ── Sidebar ───────────────────────────────────────────── -->
<aside class="sidebar">
  <a class="sidebar-brand" href="<?= $depth ?>dashboard.php">
    <span class="brand-icon"><i class="bi bi-mortarboard-fill"></i></span>
    Student&nbsp;MS
  </a>

  <nav>
    <a href="<?= $depth ?>dashboard.php"
       class="<?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
      <i class="bi bi-grid-1x2"></i> Dashboard
    </a>
    <a href="<?= $depth ?>students/read.php"
       class="<?= ($activeNav ?? '') === 'students' ? 'active' : '' ?>">
      <i class="bi bi-people"></i> Students
    </a>
    <a href="<?= $depth ?>students/create.php"
       class="<?= ($activeNav ?? '') === 'add' ? 'active' : '' ?>">
      <i class="bi bi-person-plus"></i> Add Student
    </a>
  </nav>

  <div class="sidebar-footer">
    <i class="bi bi-person-circle me-1"></i>
    <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
  </div>
</aside>

<!-- ── Main wrapper ──────────────────────────────────────── -->
<div class="main-content">

  <!-- Top bar -->
  <div class="topbar">
    <h1><?= htmlspecialchars($pageTitle ?? '') ?></h1>
    <div class="topbar-user">
      <span class="avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></span>
      <span><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
      <a href="<?= $depth ?>auth/logout.php" class="btn btn-sm btn-outline-secondary ms-2">
        <i class="bi bi-box-arrow-right"></i>
      </a>
    </div>
  </div>
