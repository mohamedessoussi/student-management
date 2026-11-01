<?php
// ============================================================
// classes/User.php
// Handles user registration, login, and session management
// ============================================================

namespace Classes;

use Config\Database;
use PDO;

class User
{
    // ── Properties ───────────────────────────────────────────
    private ?int    $id       = null;
    private string  $username = '';
    private string  $email    = '';
    private string  $password = '';

    private PDO $db;

    public function __construct()
    {
        // Obtain the shared PDO instance from the singleton
        $this->db = Database::getInstance()->getConnection();
    }

    // ── Registration ─────────────────────────────────────────

    /**
     * Registers a new user after validating inputs.
     *
     * @param  string $username
     * @param  string $email
     * @param  string $password
     * @return array  ['success' => bool, 'message' => string]
     */
    public function register(string $username, string $email, string $password): array
    {
        // --- Validate inputs ---
        if (!Validator::required($username) || !Validator::required($email) || !Validator::required($password)) {
            return ['success' => false, 'message' => 'All fields are required.'];
        }
        if (!Validator::email($email)) {
            return ['success' => false, 'message' => 'Invalid email address.'];
        }
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
        }

        // --- Check for duplicate email ---
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'This email is already registered.'];
        }

        // --- Hash the password before storing ---
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        // --- Insert with a prepared statement (SQL-injection safe) ---
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
        );
        $stmt->execute([
            ':username' => htmlspecialchars(trim($username)),
            ':email'    => trim($email),
            ':password' => $hashed,
        ]);

        return ['success' => true, 'message' => 'Registration successful. You can now log in.'];
    }

    // ── Login ────────────────────────────────────────────────

    /**
     * Verifies credentials and starts a session on success.
     *
     * @param  string $email
     * @param  string $password
     * @return array  ['success' => bool, 'message' => string]
     */
    public function login(string $email, string $password): array
    {
        if (!Validator::required($email) || !Validator::required($password)) {
            return ['success' => false, 'message' => 'Email and password are required.'];
        }

        // Fetch user by email using a prepared statement
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => trim($email)]);
        $user = $stmt->fetch();

        // Verify password hash — never compare plain text passwords
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        // --- Start authenticated session ---
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true); // Prevent session fixation

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['logged_in'] = true;

        return ['success' => true, 'message' => 'Login successful.'];
    }

    // ── Logout ───────────────────────────────────────────────

    /**
     * Destroys the session and redirects to the login page.
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header('Location: ../auth/login.php');
        exit;
    }

    // ── Helpers ──────────────────────────────────────────────

    /**
     * Checks whether an email is already in the users table.
     */
    private function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => trim($email)]);
        return (bool)$stmt->fetch();
    }

    // ── Static auth guard ────────────────────────────────────

    /**
     * Call at the top of every protected page.
     * Redirects to login if the user is not authenticated.
     *
     * @param  string $loginPath  Relative path to login.php
     */
    public static function requireLogin(string $loginPath = '../auth/login.php'): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['logged_in'])) {
            header("Location: {$loginPath}");
            exit;
        }
    }
}
