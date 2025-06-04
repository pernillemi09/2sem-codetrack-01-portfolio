<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * Data Transfer Object for login credentials.
 * Encapsulates the data and validation logic for the login form.
 * Readonly for immutability.
 */
readonly class CredentialsDto
{
    /**
     * Create a new CredentialsDto instance with constructor promotion.
     */
    public function __construct(
        public string $email = '',
        public string $password = '',
        public string $token = ''
    ) {
    }

    /**
     * Create a CredentialsDto from a Request object.
     */
    public static function fromRequest(\App\Http\Request $request): self
    {
        return new self(
            strtolower(trim($request->getInput('email', ''))),
            $request->getInput('password', ''),
            $request->getInput('_token', '')
        );
    }

    /**
     * Validate the credentials and return any validation errors.
     *
     * @return array<string, array<string>> Validation errors grouped by field name
     */
    public function validate(): array
    {
        $errors = [];

        // Validate email
        if ($this->email === '') {
            $errors['email'][] = 'Email is required.';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Please enter a valid email address.';
        }

        // Validate password
        if ($this->password === '') {
            $errors['password'][] = 'Password is required.';
        } elseif (strlen($this->password) < 8) {
            $errors['password'][] = 'Password must be at least 8 characters.';
        }

        // Validate CSRF token
        if ($this->token === '') {
            $errors['general'][] = 'Invalid form submission.';
        }

        return $errors;
    }

    /**
     * Convert the DTO to an array for template rendering or form repopulation.
     *
     * @return array{email: string, password: string, _token: string}
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => '',  // Never include password in form repopulation
            '_token' => $this->token,
        ];
    }
}
