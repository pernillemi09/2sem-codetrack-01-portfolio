<?php

declare(strict_types=1);

namespace App;

use App\Http\Response;

/**
 * Base controller class for the application.
 *
 * Provides access to the Template engine for rendering views.
 * All application controllers should extend this class and return a Response from actions.
 */
class Controller
{
    /**
     * The Template instance used for rendering views.
     * This allows controllers to easily render HTML templates.
     */
    protected Template $template;

    /**
     * Set the template instance for rendering views.
     */
    public function setTemplate(Template $template): void
    {
        $this->template = $template;
    }

    /**
     * Sanitize input to prevent XSS.
     */
    protected function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Pull success, error messages and old input from flash data.
     *
     * @return array{success: string, errors: array<string>, old: array<string, mixed>}
     */
    protected function pullFlash(Response $response): array
    {
        return [
            'success' => $response->pullFlash('success', ''),
            'errors' => $response->pullFlash('errors', []),
            'old' => $response->pullFlash('old', [])
        ];
    }

    /**
     * Flash old input data to session.
     *
     * @param array<string, mixed> $oldInput
     */
    protected function flashOldInput(Response $response, array $oldInput): void
    {
        $response->setFlash('old', $oldInput);
    }

    /**
     * Flash error messages to session.
     *
     * @param array<string, array<string>> $errors
     */
    protected function flashErrors(Response $response, array $errors): void
    {
        $response->setFlash('errors', $errors);
    }

    /**
     * Handle an invalid request with error message.
     */
    protected function handleInvalidRequest(Response $response, string $error, array $oldInput = []): Response
    {
        $response->setStatus(429);
        $this->flashOldInput($response, $oldInput);
        $this->flashErrors($response, ['general' => [$error]]);
        $response->redirect($this->getRefererPath());
        return $response;
    }

    /**
     * Get the referer path or fallback to home.
     */
    protected function getRefererPath(): string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $path = parse_url($referer, PHP_URL_PATH);
        return $path ?: '/';
    }

    /**
     * Check if a user is logged in.
     */
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Create a redirect response to the login page with an unauthorized error.
     */
    protected function redirectToLoginWithError(): Response
    {
        $response = new Response();
        $this->flashErrors($response, ['general' => ['Please login to access this page.']]);
        $response->redirect('/login');

        return $response;
    }
}
