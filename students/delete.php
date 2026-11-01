<?php
// ============================================================
// students/delete.php
// Deletes a student record and redirects back to the list
// ============================================================

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Student.php';

use Classes\User;
use Classes\Student;

User::requireLogin();

// Validate the ID — only accept GET requests with an integer ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['flash_message'] = 'Invalid request.';
    $_SESSION['flash_type']    = 'danger';
    header('Location: read.php');
    exit;
}

$student = new Student();
$result  = $student->delete((int)$id);

$_SESSION['flash_message'] = $result['message'];
$_SESSION['flash_type']    = $result['success'] ? 'success' : 'danger';

header('Location: read.php');
exit;
