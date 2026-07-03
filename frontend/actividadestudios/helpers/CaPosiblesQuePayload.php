<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;

final class CaPosiblesQuePayload
{
    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     grupo_estudios: string,
     *     mi_grupo: mixed,
     *     aCentrosNExt: array<int|string, string>,
     *     aCentrosAgdExt: array<int|string, string>,
     * }
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'grupo_estudios' => PayloadCoercion::string($payload['grupo_estudios'] ?? ''),
            'mi_grupo' => $payload['mi_grupo'] ?? '',
            'aCentrosNExt' => NotasFormSupport::desplegableOpciones($payload['aCentrosNExt'] ?? []),
            'aCentrosAgdExt' => NotasFormSupport::desplegableOpciones($payload['aCentrosAgdExt'] ?? []),
        ];
    }
}
