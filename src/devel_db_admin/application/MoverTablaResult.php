<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

final class MoverTablaResult
{
    /**
     * @param list<string> $lines Fragmentos HTML a mostrar si `fatalError` está vacío.
     */
    public function __construct(
        public readonly string $fatalError = '',
        public readonly array $lines = [],
    ) {
    }
}
