<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ActaNotasPayload
{
    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     permiso: int,
     *     nom_activ: string,
     *     matriculados: int,
     *     matriculas_rows: array<int|string, mixed>,
     *     notas: string,
     *     acta_principal: string,
     *     acta_notas_a_actas: array<int|string, mixed>,
     *     acta_txt_cursada: string,
     *     despl_actas_opciones: array<int|string, string>,
     *     msg_err: string,
     * }
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'permiso' => \frontend\shared\helpers\PayloadCoercion::int($payload['permiso'] ?? 1),
            'nom_activ' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom_activ'] ?? ''),
            'matriculados' => \frontend\shared\helpers\PayloadCoercion::int($payload['matriculados'] ?? 0),
            'matriculas_rows' => ActividadesListaSupport::datos($payload['matriculas_rows'] ?? []),
            'notas' => \frontend\shared\helpers\PayloadCoercion::string($payload['notas'] ?? 'nuevo'),
            'acta_principal' => \frontend\shared\helpers\PayloadCoercion::string($payload['acta_principal'] ?? ''),
            'acta_notas_a_actas' => ActividadesListaSupport::datos($payload['acta_notas_a_actas'] ?? []),
            'acta_txt_cursada' => \frontend\shared\helpers\PayloadCoercion::string($payload['acta_txt_cursada'] ?? ''),
            'despl_actas_opciones' => NotasFormSupport::desplegableOpciones($payload['despl_actas_opciones'] ?? []),
            'msg_err' => \frontend\shared\helpers\PayloadCoercion::string($payload['msg_err'] ?? ''),
        ];
    }
}
