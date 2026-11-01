<?php
// ============================================================
// students/update.php
// Edit form for an existing student record
// ============================================================

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Student.php';

use Classes\User;
use Classes\Student;

User::requireLogin();

$student = new Student();
$message = '';
$msgType = '';

// Validate the ID in the URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)
    ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: read.php');
    exit;
}

// Load existing data
$record = $student->find((int)$id);
if (!$record) {
    $_SESSION['flash_message'] = 'Student not found.';
    $_SESSION['flash_type']    = 'danger';
    header('Location: read.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $student->update((int)$id, $_POST, $_FILES['image'] ?? []);

    $message = $result['message'];
    $msgType = $result['success'] ? 'success' : 'danger';

    if ($result['success']) {
        // Re-fetch updated record
        $record = $student->find((int)$id);
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type']    = 'success';
        header('Location: read.php');
        exit;
    }

    // On error, merge POST values so the form retains edits
    $record = array_merge($record, $_POST);
}

$pageTitle = 'Edit Student';
$activeNav = 'students';
$depth     = '../';

require_once __DIR__ . '/../includes/layout.php';
?>

<!-- ── Error message ─────────────────────────────────────── -->
<?php if ($message && $msgType === 'danger'): ?>
  <div class="alert alert-danger auto-dismiss d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <?= $message ?>
  </div>
<?php endif; ?>

<!-- ── Form ──────────────────────────────────────────────── -->
<div class="panel" style="max-width:680px;">
  <p class="panel-title">Edit Student — #<?= $record['id'] ?></p>

  <form method="POST" action="update.php?id=<?= $record['id'] ?>"
        enctype="multipart/form-data" novalidate>
    <input type="hidden" name="id" value="<?= $record['id'] ?>">

    <!-- Current photo + upload new -->
    <div class="d-flex flex-column align-items-center mb-4">
      <div class="img-preview-wrap mb-2">
        <?php $imgSrc = $record['image']
              ? '../uploads/' . htmlspecialchars($record['image'])
              : 'https://ui-avatars.com/api/?name=' . urlencode($record['fullname']) . '&background=eef2ff&color=4f46e5&size=100'; ?>
        <img id="imagePreview" src="<?= $imgSrc ?>" alt="Preview">
      </div>
      <label for="imageInput" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-camera me-1"></i> Change Photo
      </label>
      <input type="file" id="imageInput" name="image" class="d-none"
             accept=".jpg,.jpeg,.png">
      <small class="text-muted mt-1">Leave empty to keep current photo · JPG / PNG · max 2 MB</small>
    </div>

    <div class="row g-3">

      <div class="col-12">
        <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" id="fullname" name="fullname" class="form-control" required
               value="<?= htmlspecialchars($record['fullname']) ?>">
      </div>

      <div class="col-md-6">
        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
        <input type="email" id="email" name="email" class="form-control" required
               value="<?= htmlspecialchars($record['email']) ?>">
      </div>

      <div class="col-md-6">
        <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
        <input type="number" id="age" name="age" class="form-control"
               min="1" max="120" required
               value="<?= htmlspecialchars($record['age']) ?>">
      </div>

      <div class="col-12">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="tel" id="phone" name="phone" class="form-control"
               value="<?= htmlspecialchars($record['phone'] ?? '') ?>">
      </div>

      <div class="col-12 d-flex gap-2 mt-2">
        <button type="submit" class="btn btn-accent">
          <i class="bi bi-save me-1"></i> Save Changes
        </button>
        <a href="read.php" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
