<?php

declare(strict_types=1);

namespace frontend\shared\web;

use frontend\shared\security\HashFront;

/**
 * Pila de navegación v2 en la clave de sesión `nav`.
 */
final class NavStack
{
    private const MAX_STACK = 20;

    /** Campos UI que HashFront excluye del hash pero deben ir en el POST de vuelta. */
    private const BACK_QUERY_UI_FIELDS = ['id_sel', 'scroll_id'];

    /** @var array<string, mixed> */
    private array $requestVars;

    /**
     * @param array<string, mixed> $requestVars POST (o GET) del request actual.
     */
    public function __construct(array $requestVars = [])
    {
        $this->requestVars = $requestVars;
    }

    /**
     * @param array<string, mixed> $identity
     * @param array<string, mixed> $state
     */
    public function enter(string $url, string $bloque, array $identity, array $state): void
    {
        $this->mutateSession(function () use ($url, $bloque, $identity, $state): void {
            if ($this->requestNavReset()) {
                $this->resetStackInSession();
            }

            $identity = NavEphemeralFields::strip($identity);
            $state = NavEphemeralFields::strip($state);
            $bloque = self::normalizeBloque($bloque);
            $key = self::pageKey($url, $identity);

            $stack = $this->loadStack();
            $foundIndex = null;
            for ($i = count($stack) - 1; $i >= 0; $i--) {
                if (($stack[$i]['key'] ?? '') === $key) {
                    $foundIndex = $i;
                    break;
                }
            }

            /** @var array<string, mixed> $entry */
            $entry = [
                'key' => $key,
                'url' => $url,
                'bloque' => $bloque,
                'identity' => $identity,
                'state' => $state,
                'ts' => time(),
            ];

            if ($foundIndex !== null) {
                $above = count($stack) - $foundIndex - 1;
                if ($above > 0) {
                    array_splice($stack, $foundIndex + 1, $above);
                }
                $stack[$foundIndex] = $entry;
            } else {
                $stack[] = $entry;
            }

            $this->saveStack($this->trimStack($stack));
        });
    }

    /**
     * @param array<string, mixed> $patch
     */
    public function updateState(array $patch): void
    {
        $this->updateStateAt(0, $patch);
    }

    /**
     * @param array<string, mixed> $patch
     */
    public function updateStateAt(int $n, array $patch): void
    {
        $patch = NavEphemeralFields::strip($patch);
        if ($patch === []) {
            return;
        }

        $this->mutateSession(function () use ($n, $patch): void {
            $stack = $this->loadStack();
            $idx = count($stack) - 1 - $n;
            if ($idx < 0 || !isset($stack[$idx])) {
                return;
            }

            /** @var array<string, mixed> $currentState */
            $currentState = is_array($stack[$idx]['state'] ?? null) ? $stack[$idx]['state'] : [];
            $stack[$idx]['state'] = array_merge($currentState, $patch);
            $stack[$idx]['ts'] = time();
            $this->saveStack($stack);
        });
    }

    /**
     * Elimina capas duplicadas consecutivas de dossiers_ver con el mismo segmento (p. ej. refresh tras matricular).
     *
     * @param callable(array<string, mixed>): string $segmentKeyFn
     */
    public function collapseDuplicateDossiersSegments(callable $segmentKeyFn, string $segmentKey): void
    {
        if ($segmentKey === '') {
            return;
        }

        $this->mutateSession(function () use ($segmentKeyFn, $segmentKey): void {
            $stack = $this->loadStack();
            $len = count($stack);
            if ($len < 2) {
                return;
            }

            $run = 0;
            for ($i = $len - 1; $i >= 0; $i--) {
                $url = is_string($stack[$i]['url'] ?? null) ? $stack[$i]['url'] : '';
                if (!str_contains($url, 'dossiers_ver.php')) {
                    break;
                }
                if ($segmentKeyFn($stack[$i]) !== $segmentKey) {
                    break;
                }
                $run++;
            }

            if ($run <= 1) {
                return;
            }

            array_splice($stack, $len - $run, $run - 1);
            $this->saveStack($stack);
        });
    }

    /**
     * @return array<string, mixed>|null
     */
    public function peek(int $n = 0): ?array
    {
        return $this->readSession(function () use ($n): ?array {
            $stack = $this->loadStack();
            $idx = count($stack) - 1 - $n;
            if ($idx < 0 || !isset($stack[$idx])) {
                return null;
            }

            /** @var array<string, mixed> $entry */
            $entry = $stack[$idx];

            return $entry;
        });
    }

    /**
     * @return array{url: string, parametros: string, bloque: string}|null
     */
    public function backTarget(int $n = 1): ?array
    {
        if ($n < 1) {
            $n = 1;
        }

        return $this->readSession(function () use ($n): ?array {
            $stack = $this->loadStack();
            $idx = count($stack) - 1 - $n;
            if ($idx < 0 || !isset($stack[$idx])) {
                return null;
            }

            /** @var array<string, mixed> $entry */
            $entry = $stack[$idx];
            $url = $entry['url'] ?? '';
            $bloque = $entry['bloque'] ?? '#main';
            if (!is_string($url) || $url === '') {
                return null;
            }
            if (!is_string($bloque)) {
                $bloque = '#main';
            }
            // dossiers_ver siempre recarga #main; entradas antiguas con #fichaNNNN rompen fnjs_nav_atras.
            if (str_contains($url, 'dossiers_ver.php')) {
                $bloque = '#main';
            }
            /** @var array<string, mixed> $params */
            $params = array_merge(
                is_array($entry['identity'] ?? null) ? $entry['identity'] : [],
                is_array($entry['state'] ?? null) ? $entry['state'] : [],
            );

            return [
                'url' => $url,
                'parametros' => self::buildBackQuery($params, $url),
                'bloque' => $bloque,
            ];
        });
    }

    /**
     * @param array<string, mixed> $params
     */
    private static function buildBackQuery(array $params, string $url): string
    {
        $query = HashFront::add_hash($params, $url);

        foreach (self::BACK_QUERY_UI_FIELDS as $key) {
            if (!array_key_exists($key, $params)) {
                continue;
            }
            $val = $params[$key];
            if (is_array($val)) {
                $val = $val[0] ?? '';
            }
            if (!is_scalar($val)) {
                continue;
            }
            $s = (string) $val;
            if ($s === '' || ($key === 'scroll_id' && $s === '0')) {
                continue;
            }
            $query .= '&' . rawurlencode($key) . '=' . rawurlencode($s);
        }

        return $query;
    }

    public function reset(): void
    {
        $this->mutateSession(function (): void {
            $this->resetStackInSession();
        });
    }

    /**
     * Pasos hacia atrás hasta la primera entrada cuya URL contiene $needle.
     */
    public function backStepsUntilUrlContains(string $needle, int $max = 10): int
    {
        if ($needle === '') {
            return 1;
        }

        return $this->readSession(function () use ($needle, $max): int {
            $stack = $this->loadStack();
            $count = count($stack);
            for ($n = 1; $n < $count && $n <= $max; $n++) {
                $idx = $count - 1 - $n;
                $url = is_string($stack[$idx]['url'] ?? null) ? $stack[$idx]['url'] : '';
                if (str_contains($url, $needle)) {
                    return $n;
                }
            }

            return 1;
        });
    }

    /**
     * Pasos hacia atrás saltando entradas cuya URL cumple $shouldSkip.
     *
     * @param callable(string $url): bool $shouldSkip
     */
    public function backStepsSkippingUrls(callable $shouldSkip, int $max = 15): int
    {
        return $this->readSession(function () use ($shouldSkip, $max): int {
            $stack = $this->loadStack();
            $count = count($stack);
            for ($n = 1; $n < $count && $n <= $max; $n++) {
                $idx = $count - 1 - $n;
                $url = is_string($stack[$idx]['url'] ?? null) ? $stack[$idx]['url'] : '';
                if (!$shouldSkip($url)) {
                    return $n;
                }
            }

            return 1;
        });
    }

    /**
     * @param array<string, mixed> $identity
     */
    public static function pageKey(string $url, array $identity): string
    {
        $identity = NavEphemeralFields::strip($identity);
        ksort($identity);

        return sha1($url . '|' . json_encode($identity, JSON_THROW_ON_ERROR));
    }

    public static function normalizeBloque(string $bloque): string
    {
        $bloque = ltrim(trim($bloque), '#');

        return $bloque === '' ? '#main' : '#' . $bloque;
    }

    private function requestNavReset(): bool
    {
        $nav = $this->requestVars['nav'] ?? '';

        return is_string($nav) && $nav === 'reset';
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function loadStack(): array
    {
        if (!isset($_SESSION['nav']) || !is_array($_SESSION['nav'])) {
            $_SESSION['nav'] = ['stack' => []];
        }

        $stack = $_SESSION['nav']['stack'] ?? [];
        if (!is_array($stack)) {
            $stack = [];
            $_SESSION['nav']['stack'] = $stack;
        }

        /** @var list<array<string, mixed>> $stack */
        return $stack;
    }

    /**
     * @param list<array<string, mixed>> $stack
     */
    private function saveStack(array $stack): void
    {
        if (!isset($_SESSION['nav']) || !is_array($_SESSION['nav'])) {
            $_SESSION['nav'] = [];
        }
        $_SESSION['nav']['stack'] = $stack;
    }

    private function resetStackInSession(): void
    {
        $_SESSION['nav'] = ['stack' => []];
    }

    /**
     * @param list<array<string, mixed>> $stack
     * @return list<array<string, mixed>>
     */
    private function trimStack(array $stack): array
    {
        $count = count($stack);
        if ($count > self::MAX_STACK) {
            /** @var list<array<string, mixed>> $trimmed */
            $trimmed = array_slice($stack, $count - self::MAX_STACK);

            return $trimmed;
        }

        return $stack;
    }

    /**
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    private function mutateSession(callable $callback): mixed
    {
        $wasActive = session_status() === PHP_SESSION_ACTIVE;
        if (!$wasActive) {
            session_start();
        }

        try {
            return $callback();
        } finally {
            if (!$wasActive) {
                session_write_close();
            }
        }
    }

    /**
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    private function readSession(callable $callback): mixed
    {
        $wasActive = session_status() === PHP_SESSION_ACTIVE;
        if (!$wasActive) {
            session_start();
        }

        try {
            return $callback();
        } finally {
            if (!$wasActive) {
                session_write_close();
            }
        }
    }
}
