<?php

declare(strict_types=1);

namespace frontend\dossiers\helpers;

use frontend\shared\security\HashFSignedLink;

final class DossiersListaSupport
{
    /**
     * @param list<string> $cols
     * @return list<array<string, mixed>>
     */
    public static function signFilas(mixed $raw, array $cols): array
    {
        return HashFSignedLink::signRowLinkSpecs(DossiersPayload::listRows($raw), $cols);
    }
}
