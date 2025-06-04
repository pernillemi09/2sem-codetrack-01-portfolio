<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config;
use App\Controller;
use App\Dto\CredentialsDto;
use App\Http\Request;
use App\Http\Response;
use App\Security\RateLimiter;

/**
 * Handles admin login functionality.
 */
class LoginController extends Controller
{
    private RateLimiter $limiter;

    public function __construct()
    {
        $this->limiter = new RateLimiter(
            maxAttempts: 5, // 3 attempts allowed
            decayMinutes: 5, // within 10 minutes
            sessionKey: 'login_attempts'
        );
    }

    /**
     * Show the login form page with any flash messages from previous attempts.
     */
    public function index(Request $request): Response
    {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            $response = new Response();
            $response->redirect('/admin/dashboard');
            return $response;
        }

        $response = new Response();
        $response->setTemplate($this->template, 'login', [
            ...$this->pullFlash($response),
            'request' => $request
        ]);
        return $response;
    }

    /**
     * Handle login form submission.
     * Validates the input, checks rate limiting, and handles authentication.
     */
    public function login(Request $request): Response
    {
        $response = new Response();

        // Check CSRF token
        if (!$request->validateCsrfToken()) {
            return $this->handleInvalidRequest(
                $response, 
                'Invalid security token',
                $request->getAll(),
            );
        }

        // Check rate limiting
        if ($this->isRateLimited()) {
            return $this->handleInvalidRequest(
                $response,
                'Too many login attempts. Please try again later.',
                $request->getAll(),
            );
        }

        // Validate form data
        $credentials = CredentialsDto::fromRequest($request);
        $errors = $credentials->validate();

        if (!empty($errors)) {
            $this->flashErrors($response, $errors);
            $this->flashOldInput($response, $credentials->toArray());
            $response->redirect('/login');
            return $response;
        }

        // Check credentials against environment variables
        if (
            $credentials->email !== Config::get('ADMIN_EMAIL') ||
            $credentials->password !== Config::get('ADMIN_PASSWORD')
        ) {
            // Invalid credentials
            $this->flashErrors($response, ['general' => ['Invalid credentials']]);
            $this->flashOldInput($response, $credentials->toArray());
            $response->redirect('/login');
            return $response;
        }

        // Login successful
        $_SESSION['user_id'] = 1;
        $_SESSION['user_email'] = $credentials->email;

        $this->flashSuccess($response, $credentials->email);
        $response->redirect('/admin/dashboard');
        return $response;
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request): Response
    {
        $response = new Response();

        // Check CSRF token
        if (!$request->validateCsrfToken()) {
            return $this->handleInvalidRequest(
                $response, 
                'Invalid security token',
                $request->getAll(),
            );
        }

        // Clear session
        session_destroy();
        session_start();

        $response->setFlash('success', 'You have been logged out successfully.');
        $response->redirect('/login');
        return $response;
    }

    /**
     * Flash success message to session.
     */
    private function flashSuccess(Response $response, string $email): void
    {
        $response->setFlash('success', "Welcome back, {$email}!");
    }

    /**
     * Check if the request is rate limited.
     */
    private function isRateLimited(): bool
    {
        if ($this->limiter->tooManyAttempts('login')) {
            return true;
        }

        $this->limiter->hit('login');
        return false;
    }
}
