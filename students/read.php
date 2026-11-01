<?php
// ============================================================
// students/read.php
// Lists all students with search and pagination
// ============================================================

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Student.php';

use Classes\User;
use Classes\Student;

User::requireLogin();

$student = new Student();

// Sanitize inputs from the query string
$search  = trim($_GET['search'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));

// Fetch paginated results
$result  = $student->paginate($page, $search);
$rows    = $result['data'];
$total   = $result['total'];
$pages   = $result['pages'];

// Flash messages from create/update/delete redirects
$message = $_SESSION['flash_message'] ?? '';
$msgType = $_SESSION['flash_type']    ?? 'success';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

$pageTitle = 'Students';
$activeNav = 'students';
$depth     = '../';

require_once __DIR__ . '/../includes/layout.php';
?>

<!-- ── Flash message ─────────────────────────────────────── -->
<?php if ($message): ?>
  <div class="alert alert-<?= $msgType ?> auto-dismiss d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-<?= $msgType === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
    <?= htmlspecialchars($message) ?>
  </div>
<?php endif; ?>

<!-- ── Toolbar ────────────────────────────────────────────── -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">

  <!-- Search -->
  <form method="GET" action="read.php" class="d-flex gap-2">
    <div class="search-wrapper">
      <span class="search-icon"><i class="bi bi-search"></i></span>
      <input type="text" id="searchInput" name="search" class="form-control"
             placeholder="Search by name or email…"
             value="<?= htmlspecialchars($search) ?>">
    </div>
    <button type="submit" class="btn btn-accent">Search</button>
    <?php if ($search): ?>
      <a href="read.php" class="btn btn-outline-secondary">Clear</a>
    <?php endif; ?>
  </form>

  <a href="create.php" class="btn btn-accent">
    <i class="bi bi-person-plus me-1"></i> Add Student
  </a>
</div>

<!-- ── Results count ─────────────────────────────────────── -->
<p class="text-muted mb-2" style="font-size:.825rem;">
  <?php if ($search): ?>
    <?= $total ?> result<?= $total !== 1 ? 's' : '' ?> for
    "<strong><?= htmlspecialchars($search) ?></strong>"
  <?php else: ?>
    Showing <?= count($rows) ?> of <?= $total ?> student<?= $total !== 1 ? 's' : '' ?>
  <?php endif; ?>
</p>

<!-- ── Table ─────────────────────────────────────────────── -->
<div class="panel p-0">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Photo</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Age</th>
          <th>Phone</th>
          <th>Registered</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="bi bi-inbox display-5 d-block mb-2 opacity-50"></i>
              <?= $search ? 'No students match your search.' : 'No students yet.' ?>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($rows as $i => $s): ?>
          <tr>
            <td class="text-muted"><?= ($page - 1) * $student->perPage + $i + 1 ?></td>
            <td>
              <?php if ($s['image']): ?>
                <img src="../uploads/<?= htmlspecialchars($s['image']) ?>"
                     class="student-thumb" alt="">
              <?php else: ?>
                <span class="student-initials">
                  <?= strtoupper(substr($s['fullname'], 0, 2)) ?>
                </span>
              <?php endif; ?>
            </td>
            <td class="fw-medium"><?= htmlspecialchars($s['fullname']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= $s['age'] ?></td>
            <td><?= htmlspecialchars($s['phone'] ?: '—') ?></td>
            <td class="text-muted" style="font-size:.8rem;">
              <?= date('M j, Y', strtotime($s['created_at'])) ?>
            </td>
            <td class="text-center">
              <a href="update.php?id=<?= $s['id'] ?>"
                 class="btn-icon btn bg-primary bg-opacity-10 text-primary me-1" title="Edit">
                <i class="bi bi-pencil-fill"></i>
              </a>
              <a href="delete.php?id=<?= $s['id'] ?>"
                 class="btn-icon btn bg-danger bg-opacity-10 text-danger confirm-delete"
                 title="Delete">
                <i class="bi bi-trash-fill"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ── Pagination ─────────────────────────────────────────── -->
<?php if ($pages > 1): ?>
<nav class="mt-3 d-flex justify-content-center">
  <ul class="pagination">

    <!-- Previous -->
    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
      <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
        <i class="bi bi-chevron-left"></i>
      </a>
    </li>

    <!-- Page numbers -->
    <?php for ($p = 1; $p <= $pages; $p++): ?>
      <li class="page-item <?= $p === $page ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>">
          <?= $p ?>
        </a>
      </li>
    <?php endfor; ?>

    <!-- Next -->
    <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
      <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
        <i class="bi bi-chevron-right"></i>
      </a>
    </li>

  </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
