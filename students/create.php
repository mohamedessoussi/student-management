<?php
// ============================================================
// students/create.php
// Form to add a new student record
// ============================================================

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Student.php';

use Classes\User;
use Classes\Student;

User::requireLogin();

$message = '';
$msgType = '';
// Keep form values on error
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student = new Student();
    $result  = $student->create($_POST, $_FILES['image'] ?? []);

    $message = $result['message'];
    $msgType = $result['success'] ? 'success' : 'danger';

    if ($result['success']) {
        // Store flash and redirect to list
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type']    = 'success';
        header('Location: read.php');
        exit;
    }

    // Re-populate form fields on failure
    $old = $_POST;
}

$pageTitle = 'Add Student';
$activeNav = 'add';
$depth     = '../';

require_once __DIR__ . '/../includes/layout.php';
?>

<!-- ── Error message ─────────────────────────────────────── -->
<?php if ($message && $msgType === 'danger'): ?>
  <div class="alert alert-danger auto-dismiss d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <?= $message /* already built from Validator, values are safe */ ?>
  </div>
<?php endif; ?>

<!-- ── Form card ─────────────────────────────────────────── -->
<div class="panel" style="max-width:680px;">
  <p class="panel-title">Student Details</p>

  <form method="POST" action="create.php" enctype="multipart/form-data" novalidate>

    <!-- Profile photo preview + upload -->
    <div class="d-flex flex-column align-items-center mb-4">
      <div class="img-preview-wrap mb-2">
        <img id="imagePreview"
             src="https://ui-avatars.com/api/?name=New+Student&background=eef2ff&color=4f46e5&size=100"
             alt="Preview">
      </div>
      <label for="imageInput" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-camera me-1"></i> Choose Photo
      </label>
      <input type="file" id="imageInput" name="image" class="d-none"
             accept=".jpg,.jpeg,.png">
      <small class="text-muted mt-1">JPG / PNG · max 2 MB</small>
    </div>

    <div class="row g-3">

      <!-- Full name -->
      <div class="col-12">
        <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" id="fullname" name="fullname" class="form-control"
               placeholder="e.g. Jane Doe" required
               value="<?= htmlspecialchars($old['fullname'] ?? '') ?>">
      </div>

      <!-- Email -->
      <div class="col-md-6">
        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
        <input type="email" id="email" name="email" class="form-control"
               placeholder="jane@example.com" required
               value="<?= htmlspecialchars($old['email'] ?? '') ?>">
      </div>

      <!-- Age -->
      <div class="col-md-6">
        <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
        <input type="number" id="age" name="age" class="form-control"
               placeholder="18" min="1" max="120" required
               value="<?= htmlspecialchars($old['age'] ?? '') ?>">
      </div>

      <!-- Phone -->
      <div class="col-12">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="tel" id="phone" name="phone" class="form-control"
               placeholder="+1 555 000 0000"
               value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
      </div>

      <!-- Actions -->
      <div class="col-12 d-flex gap-2 mt-2">
        <button type="submit" class="btn btn-accent">
          <i class="bi bi-person-plus me-1"></i> Add Student
        </button>
        <a href="read.php" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
