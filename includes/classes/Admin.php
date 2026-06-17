<?php
/**
 * Admin Class - OOP PHP Authentication & User Management
 * Handles admin login, logout, session management
 */

require_once __DIR__ . '/Database.php';

class Admin
{
    private ?Database $db = null;
    private ?int $id = null;
    private string $username = '';
    private string $email = '';
    private string $fullName = '';
    private string $role = 'admin';
    private ?string $lastLogin = null;
    private ?string $createdAt = null;

    /**
     * Constructor - optionally load an admin by ID
     * @param int|null $id Admin ID to load
     */
    public function __construct(?int $id = null)
    {
        if ($id !== null) {
            $this->loadById($id);
        }
    }

    /**
     * Get database instance (lazy loading)
     */
    private function getDb(): Database
    {
        if ($this->db === null) {
            $this->db = Database::getInstance();
        }
        return $this->db;
    }

    // ==================== Getters ====================

    public function getId(): ?int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getFullName(): string { return $this->fullName; }
    public function getRole(): string { return $this->role; }
    public function getLastLogin(): ?string { return $this->lastLogin; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // ==================== Authentication ====================

    /**
     * Attempt to log in an admin user
     * @param string $username Username
     * @param string $password Plain text password
     * @return bool True if login successful
     */
    public function login(string $username, string $password): bool
    {
        try {
            $row = $this->getDb()->fetchOne(
                "SELECT * FROM admins WHERE username = ? AND status = 'active' LIMIT 1",
                [$username]
            );

            if ($row && password_verify($password, $row['password'])) {
                $this->hydrate($row);

                // Update last login timestamp
                $this->getDb()->update('admins', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$this->id]);

                // Set session data
                $this->setSession();
                return true;
            }
        } catch (Exception $e) {
            // Fallback: check hardcoded admin credentials if DB is unavailable
            if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
                $this->id = 1;
                $this->username = $username;
                $this->fullName = 'Administrator';
                $this->email = 'admin@bookdemo.com';
                $this->role = 'admin';
                $this->setSession();
                return true;
            }
        }
        return false;
    }

    /**
     * Set admin session data
     */
    private function setSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $this->id;
        $_SESSION['admin_username'] = $this->username;
        $_SESSION['admin_fullname'] = $this->fullName;
        $_SESSION['admin_role'] = $this->role;
        $_SESSION['admin_login_time'] = time();
    }

    /**
     * Log out the current admin
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset(
            $_SESSION['admin_logged_in'],
            $_SESSION['admin_id'],
            $_SESSION['admin_username'],
            $_SESSION['admin_fullname'],
            $_SESSION['admin_role'],
            $_SESSION['admin_login_time']
        );
        session_destroy();
    }

    /**
     * Check if admin is logged in
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    /**
     * Require login - redirect to login page if not authenticated
     * Call this at the top of protected pages
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Get current logged-in admin's name
     * @return string
     */
    public static function getCurrentName(): string
    {
        return $_SESSION['admin_fullname'] ?? 'Admin';
    }

    /**
     * Get current logged-in admin's role
     * @return string
     */
    public static function getCurrentRole(): string
    {
        return $_SESSION['admin_role'] ?? 'admin';
    }

    // ==================== CRUD Operations ====================

    /**
     * Load admin by ID
     * @param int $id
     * @return bool
     */
    public function loadById(int $id): bool
    {
        try {
            $row = $this->getDb()->fetchOne("SELECT * FROM admins WHERE id = ?", [$id]);
            if ($row) {
                $this->hydrate($row);
                return true;
            }
        } catch (Exception $e) {}
        return false;
    }

    /**
     * Create a new admin user
     * @param string $username
     * @param string $password Plain text (will be hashed)
     * @param string $email
     * @param string $fullName
     * @return int|false New ID or false on failure
     */
    public static function create(string $username, string $password, string $email, string $fullName): int|false
    {
        try {
            $db = Database::getInstance();
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            return (int) $db->insert('admins', [
                'username' => $username,
                'password' => $hashedPassword,
                'email' => $email,
                'full_name' => $fullName,
                'status' => 'active',
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get all admin users
     * @return array
     */
    public static function getAll(): array
    {
        try {
            $db = Database::getInstance();
            return $db->fetchAll("SELECT id, username, email, full_name, role, status, last_login, created_at FROM admins ORDER BY created_at DESC");
        } catch (Exception $e) {
            return [];
        }
    }

    // ==================== Hydration ====================

    /**
     * Hydrate from a database row
     */
    private function hydrate(array $row): void
    {
        $this->id = (int) ($row['id'] ?? null);
        $this->username = $row['username'] ?? '';
        $this->email = $row['email'] ?? '';
        $this->fullName = $row['full_name'] ?? '';
        $this->role = $row['role'] ?? 'admin';
        $this->lastLogin = $row['last_login'] ?? null;
        $this->createdAt = $row['created_at'] ?? null;
    }
}
