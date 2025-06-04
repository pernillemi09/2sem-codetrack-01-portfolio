<?php

declare(strict_types=1);

namespace App\Http;

use App\Template;

/**
 * Handles HTTP response data, including status codes, headers,
 * cookies, session management, and sending various response types
 * such as plain text, JSON, file downloads, and redirects.
 */
class Response
{
    /**
     * The HTTP status code for the response.
     * This determines the status sent to the client (e.g., 200, 404).
     */
    protected int $status = 200;

    /**
     * Associative array of response headers to be sent.
     * Headers are sent before the response body.
     */
    protected array $headers = [];

    /**
     * Whether headers have already been sent for this response.
     * This prevents duplicate header output.
     */
    protected bool $headersSent = false;

    /**
     * The response body content.
     * If set, this will be sent when send() is called without arguments.
     */
    protected ?string $body = null;

    /**
     * Set the HTTP status code for the response.
     * This should be called before sending the response body.
     *
     * @param int $status The HTTP status code to set.
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * Set a response header to be sent with the response.
     * If the header already exists, it will be overwritten.
     *
     * @param string $name The name of the header.
     * @param string $value The value of the header.
     */
    public function setHeader(string $name, string $value): void
    {
        $this->headers[strtolower($name)] = $value;
    }

    /**
     * Set a cookie to be sent with the response.
     * The cookie will be HTTP-only and have 'Lax' SameSite policy.
     *
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie.
     * @param int $minutes The number of minutes until the cookie expires.
     */
    public function setCookie(string $name, string $value, int $minutes = 60): void
    {
        setcookie($name, $value, [
            'expires' => time() + ($minutes * 60),
            'path' => '/',
            'httponly' => true,
            'secure' => isset($_SERVER['HTTPS']),
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Set a session value for the current user.
     * This will persist data across requests for the same session.
     *
     * @param string $key The session key.
     * @param mixed $value The value to store in the session.
     */
    public function setSession(string $key, mixed $value): void
    {
        if ($value === null) {
            unset($_SESSION[$key]);
            return;
        }
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     *
     * @param string $key The session key.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed          The session value, or the default value if not set.
     */
    public function getSession(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set a flash message that will persist for one request.
     *
     * @param string $key The flash message key.
     * @param mixed $value The value to store in the flash message.
     */
    public function setFlash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Get flash data and remove it from session.
     *
     * @param string $key The flash message key.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed          The flash message value, or the default value if not set.
     */
    public function pullFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * Get all flash data and clear it.
     *
     * @return array<string, mixed>
     */
    public function pullAllFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flash;
    }

    /**
     * Clear all flash data.
     */
    public function clearFlash(): void
    {
        unset($_SESSION['_flash']);
    }

    /**
     * Set the response body directly.
     * This can be used to store output for later sending.
     *
     * @param string $body The response body content.
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * Render a Template and set its output as the response body.
     * This allows controllers to pass a Template directly to the response.
     *
     * @param Template $template The template instance to render.
     * @param string $view The view file to render (without extension).
     * @param array $data The data to extract for the view.
     */
    public function setTemplate(Template $template, string $view, array $data = []): void
    {
        $template->build($view, $data);
        $this->body = $template->render();
    }

    /**
     * Send the response body to the client.
     * Also sends all headers and sets the HTTP status code.
     * Throws an exception if the response has already been sent.
     */
    public function send(): void
    {
        if ($this->headersSent) {
            throw new \RuntimeException('Response has already been sent.');
        }

        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        $this->headersSent = true;
        echo $this->body ?? '';
    }

    /**
     * Send a JSON response to the client.
     * Sets the Content-Type header to 'application/json'.
     *
     * @param array $data The data to encode as JSON and send.
     */
    public function json(array $data): void
    {
        $this->setHeader('content-type', 'application/json');
        $this->send(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * Send a plain text response to the client.
     * Sets the Content-Type header to 'text/plain; charset=utf-8'.
     *
     * @param string $text The plain text to send.
     */
    public function text(string $text): void
    {
        $this->setHeader('content-type', 'text/plain; charset=utf-8');
        $this->send($text);
    }

    /**
     * Redirect the client to a different URL.
     * Sets the Location header and sends an empty body.
     *
     * @param string $url The URL to redirect to.
     * @param int $status The HTTP status code for the redirect (default 302).
     */
    public function redirect(string $url, int $status = 302): void
    {
        $this->setStatus($status);
        $this->setHeader('Location', $url);
        $this->send('');
    }

    /**
     * Send a file to the client as a download.
     * Sets appropriate headers for file transfer and outputs the file content.
     * If the file does not exist, sends a 404 response.
     *
     * @param string $filepath The path to the file on disk.
     * @param string|null $filename Optional filename for the download.
     */
    public function download(string $filepath, ?string $filename = null): void
    {
        if (!file_exists($filepath)) {
            $this->setStatus(404);
            $this->text('File not found.');
            return;
        }
        $filename = $filename ?? basename($filepath);
        $this->setHeader('content-description', 'File Transfer');
        $this->setHeader('content-type', mime_content_type($filepath));
        $this->setHeader('content-disposition', "attachment; filename=\"{$filename}\"");
        $this->setHeader('content-length', (string)filesize($filepath));
        $this->setHeader('cache-control', 'must-revalidate');
        $this->setHeader('pragma', 'public');
        $this->setHeader('expires', '0');
        $this->send(file_get_contents($filepath));
    }

    /**
     * Check if headers have already been sent for this response.
     * Returns true if headers have been sent, false otherwise.
     */
    public function getHeadersSent(): bool
    {
        return $this->headersSent || headers_sent();
    }
}
