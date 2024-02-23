<?php

declare(strict_types=1);

namespace App\lib;

class SessionManager
{
    private array $sessionData = [];

    /**
     * startSession
     *
     */
    public function startSession(): void
    {
        if (\PHP_SESSION_NONE === session_status()) {
            session_start();
            $this->sessionData = $_SESSION;
        }
    }

    /**
     * get
     *
     * @param  string $key
     *
     * @return array|null
     */
    public function get(string $key): ?array
    {
        return $this->sessionData[$key] ?? null;
    }

    /**
     * set
     *
     * @param  string $key
     * @param  array $value
     */
    public function set(string $key, array $value): void
    {
        $this->sessionData[$key] = $value;
    }

    public function unset(string $key): void
    {
        unset($this->sessionData[$key], $_SESSION[$key]);
    }

    public function destroy(): void
    {
        $this->sessionData = [];
        unset($_SESSION);
        session_destroy();
    }
}
