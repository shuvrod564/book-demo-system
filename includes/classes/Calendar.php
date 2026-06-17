<?php
/**
 * Calendar Class - OOP PHP Calendar Generator
 * Generates a dynamic monthly calendar for the booking interface
 */

class Calendar
{
    private int $year;
    private int $month;
    private ?int $selectedDay = null;
    private array $disabledDays = [];

    /**
     * Constructor
     * @param int $year Year (e.g., 2026)
     * @param int $month Month (1-12)
     */
    public function __construct(int $year = null, int $month = null)
    {
        $this->year = $year ?? (int) date('Y');
        $this->month = $month ?? (int) date('n');
    }

    /**
     * Set the selected day
     * @param int $day Day of the month
     * @return self
     */
    public function setSelectedDay(int $day): self
    {
        $this->selectedDay = $day;
        return $this;
    }

    /**
     * Set disabled days (e.g., past dates)
     * @param array $days Array of day numbers to disable
     * @return self
     */
    public function setDisabledDays(array $days): self
    {
        $this->disabledDays = $days;
        return $this;
    }

    /**
     * Get the month name
     * @return string Full month name (e.g., "June")
     */
    public function getMonthName(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1, $this->year));
    }

    /**
     * Get year
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Get month
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * Get the next month's year and month
     * @return array ['year' => int, 'month' => int]
     */
    public function getNextMonth(): array
    {
        $nextMonth = $this->month + 1;
        $nextYear = $this->year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }
        return ['year' => $nextYear, 'month' => $nextMonth];
    }

    /**
     * Get the previous month's year and month
     * @return array ['year' => int, 'month' => int]
     */
    public function getPrevMonth(): array
    {
        $prevMonth = $this->month - 1;
        $prevYear = $this->year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }
        return ['year' => $prevYear, 'month' => $prevMonth];
    }

    /**
     * Get the number of days in the current month
     * @return int
     */
    public function getDaysInMonth(): int
    {
        return (int) cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
    }

    /**
     * Get the day of the week the month starts on (0=Sunday, 6=Saturday)
     * @return int
     */
    public function getStartDayOfWeek(): int
    {
        return (int) date('w', mktime(0, 0, 0, $this->month, 1, $this->year));
    }

    /**
     * Get today's date
     * @return int Day of the month
     */
    public function getToday(): int
    {
        $todayYear = (int) date('Y');
        $todayMonth = (int) date('n');
        if ($todayYear === $this->year && $todayMonth === $this->month) {
            return (int) date('j');
        }
        return 0;
    }

    /**
     * Check if a day is disabled (past dates)
     * @param int $day Day of the month
     * @return bool
     */
    public function isDayDisabled(int $day): bool
    {
        if (in_array($day, $this->disabledDays)) {
            return true;
        }
        // Disable past dates
        $todayYear = (int) date('Y');
        $todayMonth = (int) date('n');
        $todayDay = (int) date('j');

        if ($this->year < $todayYear) return true;
        if ($this->year === $todayYear && $this->month < $todayMonth) return true;
        if ($this->year === $todayYear && $this->month === $todayMonth && $day < $todayDay) return true;

        return false;
    }

    /**
     * Check if a day is the selected day
     * @param int $day Day of the month
     * @return bool
     */
    public function isDaySelected(int $day): bool
    {
        return $this->selectedDay === $day;
    }

    /**
     * Get formatted date string for display
     * @param int $day Selected day
     * @return string Formatted date (e.g., "Thursday, June 18, 2026")
     */
    public function getFormattedDate(int $day): string
    {
        $timestamp = mktime(0, 0, 0, $this->month, $day, $this->year);
        return date('l, F j, Y', $timestamp);
    }

    /**
     * Generate the calendar grid data as an array
     * @return array Array of weeks, each containing day data
     */
    public function generateGrid(): array
    {
        $grid = [];
        $daysInMonth = $this->getDaysInMonth();
        $startDay = $this->getStartDayOfWeek();

        // Previous month days to fill the grid
        $prevMonthDays = (new self($this->getPrevMonth()['year'], $this->getPrevMonth()['month']))->getDaysInMonth();

        $week = [];
        $dayCount = 1;

        // Fill in previous month days
        for ($i = 0; $i < $startDay; $i++) {
            $week[] = [
                'day' => $prevMonthDays - $startDay + $i + 1,
                'currentMonth' => false,
                'disabled' => true,
                'selected' => false,
            ];
        }

        // Current month days
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $week[] = [
                'day' => $day,
                'currentMonth' => true,
                'disabled' => $this->isDayDisabled($day),
                'selected' => $this->isDaySelected($day),
            ];

            if (count($week) === 7) {
                $grid[] = $week;
                $week = [];
            }
        }

        // Next month days to fill the grid
        $nextDay = 1;
        while (count($week) < 7) {
            $week[] = [
                'day' => $nextDay++,
                'currentMonth' => false,
                'disabled' => true,
                'selected' => false,
            ];
        }
        if (!empty($week)) {
            $grid[] = $week;
        }

        return $grid;
    }
}
