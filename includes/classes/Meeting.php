<?php
/**
 * Meeting Class - OOP PHP Meeting Management
 * Handles CRUD operations, filtering, and statistics for bookings/meetings
 */

require_once __DIR__ . '/Database.php';

class Meeting
{
    private ?Database $db = null;

    // Meeting properties
    private ?int $id = null;
    private string $firstName = '';
    private string $lastName = '';
    private string $email = '';
    private string $role = '';
    private string $employees = '';
    private string $websiteUrl = '';
    private string $aiSearchExperience = '';
    private string $referralSource = '';
    private string $notes = '';
    private string $guestEmails = '';
    private string $selectedDate = '';
    private string $selectedTime = '';
    private string $status = 'pending';
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    /**
     * Constructor
     * @param int|null $id Load meeting by ID
     */
    public function __construct(?int $id = null)
    {
        if ($id !== null) {
            $this->loadById($id);
        }
    }

    private function getDb(): Database
    {
        if ($this->db === null) {
            $this->db = Database::getInstance();
        }
        return $this->db;
    }

    // ==================== Getters ====================

    public function getId(): ?int { return $this->id; }
    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string { return $this->role; }
    public function getEmployees(): string { return $this->employees; }
    public function getWebsiteUrl(): string { return $this->websiteUrl; }
    public function getAiSearchExperience(): string { return $this->aiSearchExperience; }
    public function getReferralSource(): string { return $this->referralSource; }
    public function getNotes(): string { return $this->notes; }
    public function getGuestEmails(): string { return $this->guestEmails; }
    public function getSelectedDate(): string { return $this->selectedDate; }
    public function getSelectedTime(): string { return $this->selectedTime; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // ==================== Setters ====================

    public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }
    public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function setRole(string $role): self { $this->role = $role; return $this; }
    public function setEmployees(string $employees): self { $this->employees = $employees; return $this; }
    public function setWebsiteUrl(string $websiteUrl): self { $this->websiteUrl = $websiteUrl; return $this; }
    public function setAiSearchExperience(string $aiSearchExperience): self { $this->aiSearchExperience = $aiSearchExperience; return $this; }
    public function setReferralSource(string $referralSource): self { $this->referralSource = $referralSource; return $this; }
    public function setGuestEmails(string $guestEmails): self { $this->guestEmails = $guestEmails; return $this; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function setNotes(string $notes): self { $this->notes = $notes; return $this; }
    public function setSelectedDate(string $date): self { $this->selectedDate = $date; return $this; }
    public function setSelectedTime(string $time): self { $this->selectedTime = $time; return $this; }

    // public function setStatus(string $status): self { $this->status = $status; return $this; }
    // public function setNotes(string $notes): self { $this->notes = $notes; return $this; }
    // public function setSelectedDate(string $date): self { $this->selectedDate = $date; return $this; }
    // public function setSelectedTime(string $time): self { $this->selectedTime = $time; return $this; }

    // ==================== Computed Properties ====================

    /**
     * Get full name
     */
    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    /**
     * Get formatted date and time for display
     */
    public function getFormattedDateTime(): string
    {
        if (empty($this->selectedDate) || empty($this->selectedTime)) return '';
        $timestamp = strtotime($this->selectedDate . ' ' . $this->selectedTime);
        return date('l, F j, Y g:i A', $timestamp);
    }

    /**
     * Get formatted date only
     */
    public function getFormattedDate(): string
    {
        if (empty($this->selectedDate)) return '';
        return date('M j, Y', strtotime($this->selectedDate));
    }

    /**
     * Get formatted time only
     */
    public function getFormattedTime(): string
    {
        if (empty($this->selectedTime)) return '';
        return date('g:i A', strtotime($this->selectedTime));
    }

    /**
     * Get guest emails as array
     */
    public function getGuestEmailList(): array
    {
        if (empty($this->guestEmails)) return [];
        return array_filter(array_map('trim', explode(',', $this->guestEmails)));
    }

    /**
     * Get guest count
     */
    public function getGuestCount(): int
    {
        return count($this->getGuestEmailList());
    }

    /**
     * Get status badge CSS class
     */
    public function getStatusBadge(): string
    {
        return match ($this->status) {
            'confirmed' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'cancelled' => 'bg-danger',
            'completed' => 'bg-info',
            default => 'bg-secondary',
        };
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'confirmed' => 'Confirmed',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed',
            default => ucfirst($this->status),
        };
    }

    /**
     * Check if meeting is upcoming (future date)
     */
    public function isUpcoming(): bool
    {
        if (empty($this->selectedDate)) return false;
        $meetingTimestamp = strtotime($this->selectedDate . ' ' . $this->selectedTime);
        return $meetingTimestamp > time();
    }

    /**
     * Check if meeting is today
     */
    public function isToday(): bool
    {
        if (empty($this->selectedDate)) return false;
        return $this->selectedDate === date('Y-m-d');
    }

    /**
     * Get days until meeting
     */
    public function getDaysUntil(): int
    {
        if (empty($this->selectedDate)) return 0;
        $meetingDate = new DateTime($this->selectedDate);
        $today = new DateTime('today');
        return (int) $today->diff($meetingDate)->days;
    }

    // ==================== Database Operations ====================

    /**
     * Load meeting by ID
     */
    public function loadById(int $id): bool
    {
        try {
            $row = $this->getDb()->fetchOne("SELECT * FROM bookings WHERE id = ?", [$id]);
            if ($row) {
                $this->hydrate($row);
                return true;
            }
        } catch (Exception $e) {}
        return false;
    }

    /**
     * Save meeting (insert or update)
     */
    public function save(): bool
    {
        try {
            $data = [
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'role' => $this->role,
                'employees' => $this->employees,
                'website_url' => $this->websiteUrl,
                'ai_search_experience' => $this->aiSearchExperience,
                'referral_source' => $this->referralSource,
                'notes' => $this->notes,
                'guest_emails' => $this->guestEmails,
                'selected_date' => $this->selectedDate,
                'selected_time' => $this->selectedTime,
                'status' => $this->status,
            ];

            if ($this->id === null) {
                $this->id = (int) $this->getDb()->insert('bookings', $data);
                return $this->id > 0;
            } else {
                $affected = $this->getDb()->update('bookings', $data, 'id = ?', [$this->id]);
                return $affected >= 0;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete meeting
     */
    public function delete(): bool
    {
        try {
            if ($this->id === null) return false;
            return $this->getDb()->delete('bookings', 'id = ?', [$this->id]) > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    // ==================== Static Query Methods ====================

    /**
     * Get all meetings with optional filters
     * @param array $filters Associative array of filters (status, date_from, date_to, search)
     * @param string $orderBy Sort column
     * @param string $orderDir Sort direction (ASC/DESC)
     * @return array Array of Meeting objects
     */
    public static function getAll(array $filters = [], string $orderBy = 'selected_date', string $orderDir = 'ASC'): array
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT * FROM bookings WHERE 1=1";
            $params = [];

            // Apply filters
            if (!empty($filters['status'])) {
                $sql .= " AND status = ?";
                $params[] = $filters['status'];
            }
            if (!empty($filters['date_from'])) {
                $sql .= " AND selected_date >= ?";
                $params[] = $filters['date_from'];
            }
            if (!empty($filters['date_to'])) {
                $sql .= " AND selected_date <= ?";
                $params[] = $filters['date_to'];
            }
            if (!empty($filters['search'])) {
                $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            // Validate order direction
            $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $allowedOrders = ['selected_date', 'created_at', 'first_name', 'email', 'status'];
            if (!in_array($orderBy, $allowedOrders)) $orderBy = 'selected_date';

            $sql .= " ORDER BY {$orderBy} {$orderDir}";

            $rows = $db->fetchAll($sql, $params);
            return array_map(function ($row) {
                $meeting = new self();
                $meeting->hydrate($row);
                return $meeting;
            }, $rows);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get upcoming meetings (future date, not cancelled)
     * @param int $limit Max results
     * @return array
     */
    public static function getUpcoming(int $limit = 10): array
    {
        try {
            $db = Database::getInstance();
            $today = date('Y-m-d');
            $rows = $db->fetchAll(
                "SELECT * FROM bookings WHERE selected_date >= ? AND status != 'cancelled' ORDER BY selected_date ASC, selected_time ASC LIMIT ?",
                [$today, $limit]
            );
            return array_map(function ($row) {
                $meeting = new self();
                $meeting->hydrate($row);
                return $meeting;
            }, $rows);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get today's meetings
     * @return array
     */
    public static function getToday(): array
    {
        try {
            $db = Database::getInstance();
            $today = date('Y-m-d');
            $rows = $db->fetchAll(
                "SELECT * FROM bookings WHERE selected_date = ? ORDER BY selected_time ASC",
                [$today]
            );
            return array_map(function ($row) {
                $meeting = new self();
                $meeting->hydrate($row);
                return $meeting;
            }, $rows);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get a single meeting by ID
     * @param int $id
     * @return self|null
     */
    public static function getById(int $id): ?self
    {
        $meeting = new self($id);
        return $meeting->getId() !== null ? $meeting : null;
    }

    /**
     * Update meeting status
     * @param int $id Meeting ID
     * @param string $status New status
     * @return bool
     */
    public static function updateStatus(int $id, string $status): bool
    {
        $allowed = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array($status, $allowed)) return false;
        try {
            $db = Database::getInstance();
            return $db->update('bookings', ['status' => $status], 'id = ?', [$id]) >= 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete meeting by ID
     * @param int $id
     * @return bool
     */
    public static function deleteById(int $id): bool
    {
        $meeting = new self($id);
        if ($meeting->getId() === null) return false;
        return $meeting->delete();
    }

    // ==================== Statistics ====================

    /**
     * Get dashboard statistics
     * @return array Associative array of stats
     */
    public static function getStats(): array
    {
        try {
            $db = Database::getInstance();
            $today = date('Y-m-d');
            $firstOfMonth = date('Y-m-01');
            $lastOfMonth = date('Y-m-t');

            $totalBookings = (int) $db->fetchOne("SELECT COUNT(*) as cnt FROM bookings")['cnt'];
            $todayBookings = (int) $db->fetchOne("SELECT COUNT(*) as cnt FROM bookings WHERE selected_date = ?", [$today])['cnt'];
            $upcomingBookings = (int) $db->fetchOne("SELECT COUNT(*) as cnt FROM bookings WHERE selected_date >= ? AND status != 'cancelled'", [$today])['cnt'];
            $pendingBookings = (int) $db->fetchOne("SELECT COUNT(*) as cnt FROM bookings WHERE status = 'pending'")['cnt'];
            $confirmedBookings = (int) $db->fetchOne("SELECT COUNT(*) as cnt FROM bookings WHERE status = 'confirmed'")['cnt'];
            $cancelledBookings = (int) $db->fetchOne("SELECT COUNT(*) as cnt FROM bookings WHERE status = 'cancelled'")['cnt'];
            $completedBookings = (int) $db->fetchOne("SELECT COUNT(*) as cnt FROM bookings WHERE status = 'completed'")['cnt'];
            $monthBookings = (int) $db->fetchOne("SELECT COUNT(*) as cnt FROM bookings WHERE selected_date BETWEEN ? AND ?", [$firstOfMonth, $lastOfMonth])['cnt'];

            return [
                'total' => $totalBookings,
                'today' => $todayBookings,
                'upcoming' => $upcomingBookings,
                'pending' => $pendingBookings,
                'confirmed' => $confirmedBookings,
                'cancelled' => $cancelledBookings,
                'completed' => $completedBookings,
                'this_month' => $monthBookings,
            ];
        } catch (Exception $e) {
            return [
                'total' => 0,
                'today' => 0,
                'upcoming' => 0,
                'pending' => 0,
                'confirmed' => 0,
                'cancelled' => 0,
                'completed' => 0,
                'this_month' => 0,
            ];
        }
    }

    /**
     * Get bookings count by status for chart data
     * @return array
     */
    public static function getStatusBreakdown(): array
    {
        try {
            $db = Database::getInstance();
            $rows = $db->fetchAll("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
            $breakdown = [];
            foreach ($rows as $row) {
                $breakdown[$row['status']] = (int) $row['count'];
            }
            return $breakdown;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get daily bookings for the current month (for chart)
     * @return array
     */
    public static function getDailyBookingsThisMonth(): array
    {
        try {
            $db = Database::getInstance();
            $firstOfMonth = date('Y-m-01');
            $lastOfMonth = date('Y-m-t');
            $rows = $db->fetchAll(
                "SELECT selected_date, COUNT(*) as count FROM bookings WHERE selected_date BETWEEN ? AND ? GROUP BY selected_date ORDER BY selected_date ASC",
                [$firstOfMonth, $lastOfMonth]
            );
            return $rows;
        } catch (Exception $e) {
            return [];
        }
    }

    // ==================== Hydration ====================

    private function hydrate(array $row): void
    {
        $this->id = (int) ($row['id'] ?? null);
        $this->firstName = $row['first_name'] ?? '';
        $this->lastName = $row['last_name'] ?? '';
        $this->email = $row['email'] ?? '';
        $this->role = $row['role'] ?? '';
        $this->employees = $row['employees'] ?? '';
        $this->websiteUrl = $row['website_url'] ?? '';
        $this->aiSearchExperience = $row['ai_search_experience'] ?? '';
        $this->referralSource = $row['referral_source'] ?? '';
        $this->notes = $row['notes'] ?? '';
        $this->guestEmails = $row['guest_emails'] ?? '';
        $this->selectedDate = $row['selected_date'] ?? '';
        $this->selectedTime = $row['selected_time'] ?? '';
        $this->status = $row['status'] ?? 'pending';
        $this->createdAt = $row['created_at'] ?? null;
        $this->updatedAt = $row['updated_at'] ?? null;
    }
}
