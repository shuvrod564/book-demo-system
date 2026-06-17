<?php
/**
 * Validator Class - OOP PHP Form Validation
 * Handles all form input validation for the booking system
 */

class Validator
{
    private array $data;
    private array $errors = [];
    private array $rules = [];

    /**
     * Constructor
     * @param array $data Input data to validate (e.g., $_POST)
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Add a validation rule
     * @param string $field Field name
     * @param string $rule Rule type (required, email, url, maxLength, inList)
     * @param mixed $param Additional parameter for the rule
     * @return self
     */
    public function addRule(string $field, string $rule, mixed $param = null): self
    {
        $this->rules[] = [
            'field' => $field,
            'rule' => $rule,
            'param' => $param,
        ];
        return $this;
    }

    /**
     * Run all validation rules
     * @return bool True if all validations pass
     */
    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $ruleInfo) {
            $field = $ruleInfo['field'];
            $rule = $ruleInfo['rule'];
            $param = $ruleInfo['param'];
            $value = $this->data[$field] ?? '';

            switch ($rule) {
                case 'required':
                    if (empty(trim((string) $value))) {
                        $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . ' is required.');
                    }
                    break;

                case 'email':
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->addError($field, 'Please enter a valid email address.');
                    }
                    break;

                case 'url':
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                        $this->addError($field, 'Please enter a valid URL.');
                    }
                    break;

                case 'maxLength':
                    if (strlen((string) $value) > $param) {
                        $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$param} characters.");
                    }
                    break;

                case 'inList':
                    if (!empty($value) && !in_array($value, $param)) {
                        $this->addError($field, 'Please select a valid option.');
                    }
                    break;

                case 'guestEmails':
                    if (!empty($value)) {
                        $emails = array_filter(explode(',', $value));
                        foreach ($emails as $email) {
                            $email = trim($email);
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $this->addError($field, "\"{$email}\" is not a valid email address.");
                                break;
                            }
                        }
                    }
                    break;
            }
        }

        return empty($this->errors);
    }

    /**
     * Add an error message
     * @param string $field Field name
     * @param string $message Error message
     */
    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Get all validation errors
     * @return array Associative array of field => error messages
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get errors for a specific field
     * @param string $field Field name
     * @return array Array of error messages
     */
    public function getFieldErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * Get the first error for a specific field
     * @param string $field Field name
     * @return string|null Error message or null
     */
    public function getFirstError(string $field): ?string
    {
        $errors = $this->getFieldErrors($field);
        return !empty($errors) ? $errors[0] : null;
    }

    /**
     * Check if a field has errors
     * @param string $field Field name
     * @return bool
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Sanitize a string input
     * @param string $input Raw input
     * @return string Sanitized input
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize all data in an array
     * @param array $data Raw data array
     * @return array Sanitized data array
     */
    public static function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = self::sanitize((string) $value);
            }
        }
        return $sanitized;
    }

    /**
     * Get validated and sanitized data
     * @return array
     */
    public function getValidatedData(): array
    {
        return self::sanitizeArray($this->data);
    }
}
