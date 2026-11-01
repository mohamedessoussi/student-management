<?php
// ============================================================
// classes/Student.php
// CRUD operations, search, and pagination for the students table
// ============================================================

namespace Classes;

use Config\Database;
use PDO;

class Student
{
    // ── Properties ───────────────────────────────────────────
    private ?int    $id       = null;
    private string  $fullname = '';
    private string  $email    = '';
    private int     $age      = 0;
    private string  $phone    = '';
    private ?string $image    = null;

    private PDO $db;

    /** Number of records displayed per page */
    public int $perPage = 5;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── Create ───────────────────────────────────────────────

    /**
     * Inserts a new student record.
     *
     * @param  array $data  Associative array of student fields
     * @param  array $file  $_FILES['image'] element (may be empty)
     * @return array ['success' => bool, 'message' => string]
     */
    public function create(array $data, array $file = []): array
    {
        // --- Validation ---
        $errors = $this->validate($data, $file);
        if ($errors) {
            return ['success' => false, 'message' => implode('<br>', $errors)];
        }

        // --- Handle optional image upload ---
        $imageName = null;
        if (!empty($file['name'])) {
            $upload = $this->uploadImage($file);
            if (!$upload['success']) {
                return $upload;
            }
            $imageName = $upload['filename'];
        }

        // --- Prepared INSERT ---
        $stmt = $this->db->prepare(
            'INSERT INTO students (fullname, email, age, phone, image)
             VALUES (:fullname, :email, :age, :phone, :image)'
        );
        $stmt->execute([
            ':fullname' => $this->sanitize($data['fullname']),
            ':email'    => trim($data['email']),
            ':age'      => (int)$data['age'],
            ':phone'    => $this->sanitize($data['phone'] ?? ''),
            ':image'    => $imageName,
        ]);

        return ['success' => true, 'message' => 'Student added successfully.'];
    }

    // ── Read all (paginated) ─────────────────────────────────

    /**
     * Returns a page of student records (optionally filtered by search term).
     *
     * @param  int    $page    Current page number (1-based)
     * @param  string $search  Optional search keyword
     * @return array  ['data' => [...], 'total' => int, 'pages' => int]
     */
    public function paginate(int $page = 1, string $search = ''): array
    {
        $offset = ($page - 1) * $this->perPage;
        $like   = '%' . $search . '%';

        // Total count for pagination controls
        $countStmt = $this->db->prepare(
            'SELECT COUNT(*) FROM students
             WHERE fullname LIKE :s1 OR email LIKE :s2'
        );
        $countStmt->execute([':s1' => $like, ':s2' => $like]);
        $total = (int)$countStmt->fetchColumn();

        // Fetch the current page
        $stmt = $this->db->prepare(
            'SELECT * FROM students
             WHERE fullname LIKE :s1 OR email LIKE :s2
             ORDER BY created_at DESC
             LIMIT :limit OFFSET :offset'
        );
        // LIMIT and OFFSET must be bound as integers
        $stmt->bindValue(':s1',     $like,           PDO::PARAM_STR);
        $stmt->bindValue(':s2',     $like,           PDO::PARAM_STR);
        $stmt->bindValue(':limit',  $this->perPage,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,         PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'  => $stmt->fetchAll(),
            'total' => $total,
            'pages' => (int)ceil($total / $this->perPage),
        ];
    }

    // ── Read all (no pagination, for dashboard) ──────────────

    /**
     * Returns the N most recently added students.
     */
    public function latest(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM students ORDER BY created_at DESC LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ── Total count ──────────────────────────────────────────

    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM students')->fetchColumn();
    }

    // ── Find by ID ───────────────────────────────────────────

    /**
     * Fetches a single student record by primary key.
     */
    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM students WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ── Update ───────────────────────────────────────────────

    /**
     * Updates an existing student record.
     *
     * @param  int   $id
     * @param  array $data  New values
     * @param  array $file  Optional new image via $_FILES
     * @return array ['success' => bool, 'message' => string]
     */
    public function update(int $id, array $data, array $file = []): array
    {
        // Validate fields (image optional on update)
        $errors = $this->validate($data, $file, isUpdate: true);
        if ($errors) {
            return ['success' => false, 'message' => implode('<br>', $errors)];
        }

        // Fetch existing record so we can keep the old image if none supplied
        $existing = $this->find($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Student not found.'];
        }

        $imageName = $existing['image']; // default: keep current image

        // New image uploaded?
        if (!empty($file['name'])) {
            $upload = $this->uploadImage($file);
            if (!$upload['success']) {
                return $upload;
            }
            // Delete the old image file from disk
            $this->deleteImageFile($existing['image']);
            $imageName = $upload['filename'];
        }

        // Prepared UPDATE
        $stmt = $this->db->prepare(
            'UPDATE students
             SET fullname = :fullname,
                 email    = :email,
                 age      = :age,
                 phone    = :phone,
                 image    = :image
             WHERE id = :id'
        );
        $stmt->execute([
            ':fullname' => $this->sanitize($data['fullname']),
            ':email'    => trim($data['email']),
            ':age'      => (int)$data['age'],
            ':phone'    => $this->sanitize($data['phone'] ?? ''),
            ':image'    => $imageName,
            ':id'       => $id,
        ]);

        return ['success' => true, 'message' => 'Student updated successfully.'];
    }

    // ── Delete ───────────────────────────────────────────────

    /**
     * Deletes a student and their uploaded image.
     */
    public function delete(int $id): array
    {
        $student = $this->find($id);
        if (!$student) {
            return ['success' => false, 'message' => 'Student not found.'];
        }

        // Remove image from disk first
        $this->deleteImageFile($student['image']);

        $stmt = $this->db->prepare('DELETE FROM students WHERE id = :id');
        $stmt->execute([':id' => $id]);

        return ['success' => true, 'message' => 'Student deleted successfully.'];
    }

    // ── Internal helpers ─────────────────────────────────────

    /**
     * Validates student form data.
     * When $isUpdate = true the image field is not required.
     *
     * @return string[]  Array of error messages (empty = valid)
     */
    private function validate(array $data, array $file, bool $isUpdate = false): array
    {
        $errors = [];

        if (!Validator::required($data['fullname'] ?? '')) {
            $errors[] = 'Full name is required.';
        }
        if (!Validator::required($data['email'] ?? '')) {
            $errors[] = 'Email is required.';
        } elseif (!Validator::email($data['email'])) {
            $errors[] = 'Invalid email format.';
        }
        if (!Validator::required($data['age'] ?? '')) {
            $errors[] = 'Age is required.';
        } elseif (!Validator::numeric($data['age'])) {
            $errors[] = 'Age must be a positive number.';
        }

        // Image is only mandatory on create
        if (!$isUpdate && empty($file['name'])) {
            // No image provided on create — allowed (image is optional)
        }
        if (!empty($file['name'])) {
            if (!Validator::imageType($file)) {
                $errors[] = 'Image must be JPG, JPEG, or PNG.';
            }
            if (!Validator::imageSize($file)) {
                $errors[] = 'Image must not exceed 2 MB.';
            }
        }

        return $errors;
    }

    /**
     * Moves an uploaded image to /uploads/ with a unique filename.
     *
     * @return array ['success' => bool, 'filename' => string]
     */
    private function uploadImage(array $file): array
    {
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('img_', true) . '.' . $ext;
        $dest     = __DIR__ . '/../uploads/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'message' => 'Failed to save the uploaded image.'];
        }
        return ['success' => true, 'filename' => $filename];
    }

    /**
     * Deletes an image file from the uploads directory.
     */
    private function deleteImageFile(?string $filename): void
    {
        if ($filename) {
            $path = __DIR__ . '/../uploads/' . $filename;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Strips tags and trims a string (XSS guard for display).
     */
    private function sanitize(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)));
    }
}
