<?php
// ============================================================
// dashboard.php
// Shows total student count and the latest 5 registrations
// ============================================================

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/classes/Validator.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Student.php';

use Classes\User;
use Classes\Student;

// Auth guard – redirects to login if not authenticated
User::requireLogin('auth/login.php');

$student = new Student();
$total   = $student->count();
$latest  = $student->latest(5);

// Layout variables
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
$depth     = './';

require_once __DIR__ . '/includes/layout.php';
?>

<!-- ── Stat cards ─────────────────────────────────────────── -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
      <div>
        <div class="stat-value"><?= number_format($total) ?></div>
        <div class="stat-label">Total Students</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#ecfdf5;color:#16a34a;">
        <i class="bi bi-person-check-fill"></i>
      </div>
      <div>
        <div class="stat-value"><?= count($latest) ?></div>
        <div class="stat-label">Recent Entries</div>
      </div>
    </div>
  </div>
</div>

<!-- ── Latest students panel ─────────────────────────────── -->
<div class="panel">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <span class="panel-title mb-0">Latest Registered Students</span>
    <a href="students/read.php" class="btn btn-accent btn-sm">
      <i class="bi bi-arrow-right me-1"></i> View All
    </a>
  </div>

  <?php if (empty($latest)): ?>
    <div class="text-center py-4 text-muted">
      <i class="bi bi-inbox display-5 d-block mb-2 opacity-50"></i>
      No students registered yet.
      <a href="students/create.php" class="d-block mt-2">Add the first one &rarr;</a>
    </div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>Student</th>
          <th>Email</th>
          <th>Age</th>
          <th>Phone</th>
          <th>Registered</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($latest as $i => $s): ?>
        <tr>
          <td class="text-muted"><?= $i + 1 ?></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <?php if ($s['image']): ?>
                <img src="uploads/<?= htmlspecialchars($s['image']) ?>"
                     class="student-thumb" alt="">
              <?php else: ?>
                <span class="student-initials">
                  <?= strtoupper(substr($s['fullname'], 0, 2)) ?>
                </span>
              <?php endif; ?>
              <span class="fw-medium"><?= htmlspecialchars($s['fullname']) ?></span>
            </div>
          </td>
          <td><?= htmlspecialchars($s['email']) ?></td>
          <td><?= $s['age'] ?></td>
          <td><?= htmlspecialchars($s['phone'] ?: '—') ?></td>
          <td class="text-muted" style="font-size:.8rem;">
            <?= date('M j, Y', strtotime($s['created_at'])) ?>
          </td>
          <td>
            <a href="students/update.php?id=<?= $s['id'] ?>"
               class="btn-icon btn bg-light text-primary" title="Edit">
              <i class="bi bi-pencil-fill"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
