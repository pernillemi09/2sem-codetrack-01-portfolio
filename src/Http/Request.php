<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Handles HTTP request data and provides convenient access to
 * query parameters, POST data, cookies, headers, JSON payloads,
 * and session data. Also includes helpers for request type and
 * authentication token extraction.
 */
class Request
{
    /**
     * The HTTP method used for the request (GET, POST, etc.).
     * This is typically set by the client and determines the action to be performed.
     */
    protected string $method;

    /**
     * The URI path of the request.
     * This is the part of the URL after the domain, not including query parameters.
     */
    protected string $uri;

    /**
     * The query parameters from the URL (?foo=bar).
     * These are typically used for GET requests.
     */
    protected array $get;

    /**
     * The POST parameters from the request body.
     * These are typically used for form submissions or API requests.
     */
    protected array $post;

    /**
     * The cookies sent with the request.
     * Cookies are key-value pairs stored on the client and sent with each request.
     */
    protected array $cookies;

    /**
     * The server and environment variables.
     * This includes information such as headers, protocol, and script locations.
     */
    protected array $server;

    /**
     * The decoded JSON body, if the request is JSON.
     * This is only populated if the Content-Type is application/json.
     * Null if not a JSON request.
     */
    protected ?array $jsonBody = null;

    private const CSRF_TOKEN_NAME = '_token';

    /**
     * Construct a new Request instance with the given data.
     * This allows for custom construction, such as in testing or for internal requests.
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $uri Request URI path
     * @param array $get Query parameters
     * @param array $post POST parameters
     * @param array $cookies Cookie values
     * @param array $server Server/environment variables
     * @param array|null $jsonBody Decoded JSON body, if present
     */
    public function __construct(
        string $method = 'GET',
        string $uri = '/',
        array $get = [],
        array $post = [],
        array $cookies = [],
        array $server = [],
        ?array $jsonBody = null
    ) {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->get = $get;
        $this->post = $post;
        $this->cookies = $cookies;
        $this->server = $server;
        $this->jsonBody = $jsonBody;

        // Always refresh CSRF token when creating a new request
        if ($this->isGet()) {
            $this->refreshCsrfToken();
        }
    }

    /**
     * Create a Request instance from PHP superglobals.
     * This is the typical entry point for handling an incoming HTTP request.
     * It will automatically detect JSON requests and decode the body if needed.
     */
    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $json = null;

        if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true) ?? [];
        }

        return new self(
            $method,
            $uri,
            $_GET,
            $_POST,
            $_COOKIE,
            $_SERVER,
            $json
        );
    }

    /**
     * Get the HTTP method (GET, POST, etc.) for this request.
     * This is useful for routing and handling different request types.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get the request URI path.
     * This does not include query parameters.
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Retrieve a value from JSON body, POST, or GET (in that order).
     * Returns the first match found, or the default value if not present.
     * This is useful for handling input regardless of how it was sent.
     *
     * @param string $key The input key to retrieve.
     * @param mixed $default The default value if the key is not found.
     */
    public function getInput(string $key, mixed $default = null): mixed
    {
        return $this->jsonBody[$key] ?? $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    /**
     * Retrieve a value from the query string (?foo=bar).
     * Returns the default value if the key is not present.
     * This is useful for accessing GET parameters directly.
     *
     * @param string $key The query parameter key.
     * @param mixed $default The default value if the key is not found.
     */
    public function getQuery(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Retrieve a value from POST data.
     * Returns the default value if the key is not present.
     * This is useful for accessing form or API POST parameters.
     *
     * @param string $key The POST parameter key.
     * @param mixed $default The default value if the key is not found.
     */
    public function getForm(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Retrieve a value from the JSON request body.
     * Returns the default value if the key is not present or if not a JSON request.
     * This is useful for APIs that accept JSON payloads.
     *
     * @param string $key The JSON key.
     * @param mixed $default The default value if the key is not found.
     */
    public function getJson(string $key, mixed $default = null): mixed
    {
        return $this->jsonBody[$key] ?? $default;
    }

    /**
     * Get all input data merged from GET, POST, and JSON.
     * Useful for mass assignment, validation, or logging all input data.
     */
    public function getAll(): array
    {
        return array_merge($this->get, $this->post, $this->jsonBody ?? []);
    }

    /**
     * Retrieve a cookie value by key.
     * Returns the default value if the cookie is not present.
     * This is useful for authentication, preferences, or tracking.
     *
     * @param string $key The cookie name.
     * @param mixed $default The default value if the cookie is not found.
     */
    public function getCookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Retrieve a session value by key.
     * Returns the default value if the session key is not present.
     * This is useful for user authentication and storing temporary data.
     *
     * @param string $key The session key.
     * @param mixed $default The default value if the session key is not found.
     */
    public function getSession(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Determine if the request method is POST.
     * Useful for routing, form handling, or API endpoints.
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Determine if the request method is GET.
     * Useful for routing or query handling.
     */
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    /**
     * Determine if the request has a JSON content type.
     * Checks the Content-Type header for 'application/json'.
     * Useful for API endpoints that expect JSON payloads.
     */
    public function isJson(): bool
    {
        return str_contains($this->server['CONTENT_TYPE'] ?? '', 'application/json');
    }

    /**
     * Get all HTTP headers as an associative array.
     * Uses getallheaders() if available, otherwise builds headers from $_SERVER.
     * This is useful for authentication, content negotiation, and debugging.
     */
    public function getHeaders(): array
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            // Normalize all header keys to lowercase
            return array_change_key_case($headers, CASE_LOWER);
        }

        // Fallback for non-Apache
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    /**
     * Get the Bearer token from the Authorization header, if present.
     * Returns null if no Bearer token is found.
     * Useful for API authentication using OAuth2 or JWT.
     */
    public function getBearerToken(): ?string
    {
        $header = $this->getHeaders()['authorization'] ?? null;
        if ($header && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function validateCsrfToken(): bool
    {
        $tokenFromSession = $_SESSION[self::CSRF_TOKEN_NAME] ?? null;
        $tokenFromRequest = $this->getInput(self::CSRF_TOKEN_NAME);

        return $tokenFromSession && $tokenFromRequest && hash_equals($tokenFromSession, $tokenFromRequest);
    }

    /**
     * Get the current CSRF token. If no token exists, one will be created.
     * Use this method in views to get the token for forms.
     */
    public function getCsrfToken(): string
    {
        if (!isset($_SESSION[self::CSRF_TOKEN_NAME])) {
            $this->refreshCsrfToken();
        }

        return $_SESSION[self::CSRF_TOKEN_NAME];
    }

    /**
     * Create a new CSRF token and store it in the session.
     * This is called automatically when creating a new request.
     */
    private function refreshCsrfToken(): void
    {
        $_SESSION[self::CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
}
