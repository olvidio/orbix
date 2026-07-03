<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class PlanEstudiosCaPayload
{
    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     msg_err: string,
     *     nom_activ: string,
     *     nom_director_est: string,
     *     aPreceptores: array<int|string, mixed>,
     *     aProfesores: array<int|string, mixed>,
     *     aAlumnos: array<int|string, mixed>,
     * }
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'msg_err' => PayloadCoercion::string($payload['msg_err'] ?? ''),
            'nom_activ' => PayloadCoercion::string($payload['nom_activ'] ?? ''),
            'nom_director_est' => PayloadCoercion::string($payload['nom_director_est'] ?? ''),
            'aPreceptores' => ActividadesListaSupport::datos($payload['aPreceptores'] ?? []),
            'aProfesores' => ActividadesListaSupport::datos($payload['aProfesores'] ?? []),
            'aAlumnos' => ActividadesListaSupport::datos($payload['aAlumnos'] ?? []),
        ];
    }
}
