<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Provides rate limiting functionality using session storage.
 * Allows configuring attempts, decay time, and unique keys for different rate limits.
 */
class RateLimiter
{
    /**
     * @param int $maxAttempts Maximum number of attempts allowed within the window
     * @param int $decayMinutes Number of minutes before attempts expire
     * @param string $sessionKey Key used to store attempts in session
     */
    public function __construct(
        private readonly int $maxAttempts = 10,
        private readonly int $decayMinutes = 60,
        private readonly string $sessionKey = 'rate_limit'
    ) {
    }

    /**
     * Check if too many attempts have been made for the given key.
     */
    public function tooManyAttempts(string $key): bool
    {
        return count($this->getAttempts($key)) >= $this->maxAttempts;
    }

    /**
     * Record a new attempt for the given key.
     */
    public function hit(string $key): void
    {
        $attempts = $this->getAttempts($key);
        $attempts[] = time();
        $_SESSION[$this->sessionKey][$key] = $attempts;
    }

    /**
     * Get the remaining number of attempts allowed.
     */
    public function remaining(string $key): int
    {
        return max(0, $this->maxAttempts - count($this->getAttempts($key)));
    }

    /**
     * Get the timestamp when the rate limit will reset.
     */
    public function getResetTime(string $key): int
    {
        $attempts = $this->getAttempts($key);
        if (empty($attempts)) {
            return time();
        }

        return min($attempts) + ($this->decayMinutes * 60);
    }

    /**
     * Get active attempts for the given key, removing expired ones.
     *
     * @return array<int> Array of timestamps for each attempt
     */
    private function getAttempts(string $key): array
    {
        $now = time();
        $cutoff = $now - ($this->decayMinutes * 60);

        return array_filter(
            $_SESSION[$this->sessionKey][$key] ?? [],
            fn(int $time): bool => $time > $cutoff
        );
    }
}
