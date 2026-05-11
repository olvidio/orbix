<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

final class AbsorberEsquemaResult
{
    /**
     * @param list<string> $lines Texto a mostrar (sin envolver en HTML; el front añade saltos si hace falta).
     */
    public function __construct(
        public readonly array $lines = [],
    ) {
    }
}
