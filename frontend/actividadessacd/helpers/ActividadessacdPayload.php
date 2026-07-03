<?php

declare(strict_types=1);

namespace frontend\actividadessacd\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ActividadessacdPayload
{
    /**
     * @param array<int|string, mixed> $payload
     * @return array<int|string, string>
     */
    public static function localesFromPayload(array $payload): array
    {
        return NotasFormSupport::desplegableOpciones($payload['a_locales'] ?? []);
    }

    /**
     * @param array<int|string, mixed> $payload
     */
    public static function textoFromPayload(array $payload): string
    {
        return \frontend\shared\helpers\PayloadCoercion::string($payload['texto'] ?? '');
    }
}
