<?php

declare(strict_types=1);

namespace frontend\actividadplazas\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ActividadplazasPayload
{
    /**
     * @param array<int|string, mixed> $payload
     * @return array{
     *     ap_nom: string,
     *     sid_activ: string,
     *     opciones: array<int|string, string>,
     *     sactividad: string,
     *     na: string,
     *     dlA: string,
     *     dlB: string,
     *     concedidasA2B: int,
     *     concedidasB2A: int,
     *     a_cabeceras: list<array<string, mixed>|string>,
     *     a_valores: array<int|string, mixed>,
     *     id_tipo_activ: string,
     *     year: string,
     *     periodo: string,
     *     empiezamin: string,
     *     empiezamax: string,
     *     extendida: bool,
     *     publicado: bool,
     *     otra_dl: bool,
     *     a_plazas: mixed,
     *     plazas_totales: int,
     *     tot_calendario: int,
     *     tot_cedidas: int,
     *     tot_conseguidas: int,
     *     tot_disponibles: int,
     *     tot_ocupadas: int,
     *     dl_opciones: array<int|string, string>,
     * }
     */
    public static function gestionPlazasFromPayload(array $payload): array
    {
        return [
            'ap_nom' => \frontend\shared\helpers\PayloadCoercion::string($payload['ap_nom'] ?? ''),
            'sid_activ' => \frontend\shared\helpers\PayloadCoercion::string($payload['sid_activ'] ?? ''),
            'opciones' => NotasFormSupport::desplegableOpciones($payload['opciones'] ?? []),
            'sactividad' => \frontend\shared\helpers\PayloadCoercion::string($payload['sactividad'] ?? ''),
            'na' => \frontend\shared\helpers\PayloadCoercion::string($payload['na'] ?? ''),
            'dlA' => \frontend\shared\helpers\PayloadCoercion::string($payload['dlA'] ?? ''),
            'dlB' => \frontend\shared\helpers\PayloadCoercion::string($payload['dlB'] ?? ''),
            'concedidasA2B' => \frontend\shared\helpers\PayloadCoercion::int($payload['concedidasA2B'] ?? 0),
            'concedidasB2A' => \frontend\shared\helpers\PayloadCoercion::int($payload['concedidasB2A'] ?? 0),
            'a_cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
            'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
            'id_tipo_activ' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_tipo_activ'] ?? ''),
            'year' => \frontend\shared\helpers\PayloadCoercion::string($payload['year'] ?? ''),
            'periodo' => \frontend\shared\helpers\PayloadCoercion::string($payload['periodo'] ?? ''),
            'empiezamin' => \frontend\shared\helpers\PayloadCoercion::string($payload['empiezamin'] ?? ''),
            'empiezamax' => \frontend\shared\helpers\PayloadCoercion::string($payload['empiezamax'] ?? ''),
            'extendida' => ($payload['extendida'] ?? false) === true,
            'publicado' => ($payload['publicado'] ?? false) === true,
            'otra_dl' => ($payload['otra_dl'] ?? false) === true,
            'a_plazas' => $payload['a_plazas'] ?? [],
            'plazas_totales' => \frontend\shared\helpers\PayloadCoercion::int($payload['plazas_totales'] ?? 0),
            'tot_calendario' => \frontend\shared\helpers\PayloadCoercion::int($payload['tot_calendario'] ?? 0),
            'tot_cedidas' => \frontend\shared\helpers\PayloadCoercion::int($payload['tot_cedidas'] ?? 0),
            'tot_conseguidas' => \frontend\shared\helpers\PayloadCoercion::int($payload['tot_conseguidas'] ?? 0),
            'tot_disponibles' => \frontend\shared\helpers\PayloadCoercion::int($payload['tot_disponibles'] ?? 0),
            'tot_ocupadas' => \frontend\shared\helpers\PayloadCoercion::int($payload['tot_ocupadas'] ?? 0),
            'dl_opciones' => NotasFormSupport::desplegableOpciones($payload['dl_opciones'] ?? []),
        ];
    }
}
