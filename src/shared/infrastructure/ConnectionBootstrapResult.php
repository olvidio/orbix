<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

use PDO;

/**
 * Resultado del bootstrap de conexiones PDO + contexto de esquema para vistas materializadas.
 */
final class ConnectionBootstrapResult
{
    /**
     * @param array<string, PDO|string> $connections
     */
    public function __construct(
        public int|string $userSfsv,
        public ?string $esquema,
        public ?string $esquemav,
        public ?string $esquemaf,
        public array $connections,
    ) {
    }
}
