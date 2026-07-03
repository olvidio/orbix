<?php

declare(strict_types=1);

namespace frontend\dossiers\helpers;

use frontend\shared\security\HashFrontSignedLink;

final class DossiersListaSupport
{
    /**
     * @param list<string> $cols
     * @return list<array<string, mixed>>
     */
    public static function signFilas(mixed $raw, array $cols): array
    {
        return HashFrontSignedLink::signRowLinkSpecs(DossiersPayload::listRows($raw), $cols);
    }
}
