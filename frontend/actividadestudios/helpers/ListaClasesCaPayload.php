<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ListaClasesCaPayload
{
    /**
     * @param array<string, mixed> $payload
     * @return array{msg_err: string, nom_activ: string, nom_director_est: string, datos_asignatura: array<int|string, mixed>}
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'msg_err' => \frontend\shared\helpers\PayloadCoercion::string($payload['msg_err'] ?? ''),
            'nom_activ' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom_activ'] ?? ''),
            'nom_director_est' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom_director_est'] ?? ''),
            'datos_asignatura' => ActividadesListaSupport::datos($payload['datos_asignatura'] ?? []),
        ];
    }
}
