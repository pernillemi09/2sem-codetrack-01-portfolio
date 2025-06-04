<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * Data Transfer Object for contact form data.
 * Encapsulates the data and validation logic for the contact form.
 * Readonly for immutability.
 */
readonly class ContactFormDto
{
    /**
     * Create a new ContactFormDto instance with constructor promotion.
     */
    public function __construct(
        public string $name = '',
        public string $email = '',
        public string $subject = '',
        public string $message = ''
    ) {
    }

    /**
     * Create a ContactFormDto from a Request object.
     */
    public static function fromRequest(\App\Http\Request $request): self
    {
        return new self(
            self::sanitizeInput($request->getInput('name', '')),
            strtolower(trim($request->getInput('email', ''))),
            self::sanitizeInput($request->getInput('subject', '')),
            self::sanitizeInput($request->getInput('message', ''))
        );
    }

    /**
     * Validate the form data and return any validation errors.
     *
     * @return array<string, array<string>> Validation errors grouped by field name where keys are field names and values are arrays of error messages
     */
    public function validate(): array
    {
        $errors = [];

        // Validate name
        if ($this->name === '') {
            $errors['name'][] = 'Name is required.';
        } elseif (strlen($this->name) > 100) {
            $errors['name'][] = 'Name is too long (maximum 100 characters).';
        }

        // Validate email
        if ($this->email === '') {
            $errors['email'][] = 'Email is required.';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Please enter a valid email address.';
        }

        // Validate subject
        if ($this->subject === '') {
            $errors['subject'][] = 'Subject is required.';
        } elseif (strlen($this->subject) > 200) {
            $errors['subject'][] = 'Subject is too long (maximum 200 characters).';
        }

        // Validate message
        if ($this->message === '') {
            $errors['message'][] = 'Message is required.';
        } elseif (strlen($this->message) > 3000) {
            $errors['message'][] = 'Message is too long (maximum 3000 characters).';
        }

        return $errors;
    }

    /**
     * Convert the DTO to an array for template rendering or form repopulation.
     *
     * @return array{name: string, email: string, subject: string, message: string}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ];
    }

    /**
     * Sanitize input to prevent XSS attacks.
     */
    private static function sanitizeInput(string $input): string
    {
        return trim(htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
