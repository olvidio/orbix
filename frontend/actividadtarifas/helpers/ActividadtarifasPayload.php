<?php

declare(strict_types=1);

namespace frontend\actividadtarifas\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ActividadtarifasPayload
{
    /**
     * @param array<int|string, mixed> $payload
     * @return array{
     *     id_tarifa: string,
     *     es_nuevo: bool,
     *     letra: string,
     *     modo: int,
     *     observ: string,
     *     opciones_modo: array<int|string, string>,
     *     id_item: string,
     *     id_tipo_activ: int,
     *     id_tarifa_sel: int,
     *     isfsv: int,
     *     nom_tipo_activ: string,
     *     opciones_tarifa: array<int|string, string>,
     *     id_ubi: int,
     *     year: int,
     *     cantidad: string,
     *     opciones_serie: array<int|string, string>,
     *     id_serie_sel: int,
     *     token_update: string,
     *     token_eliminar: string,
     *     a_cabeceras: list<array<string, mixed>|string>,
     *     a_valores: array<int|string, mixed>,
     *     puede_anadir: bool,
     *     any_anterior: int,
     *     any_actual: int,
     *     token_copiar: string,
     * }
     */
    public static function fields(array $payload): array
    {
        return [
            'id_tarifa' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_tarifa'] ?? 'nuevo'),
            'es_nuevo' => ($payload['es_nuevo'] ?? true) === true,
            'letra' => \frontend\shared\helpers\PayloadCoercion::string($payload['letra'] ?? ''),
            'modo' => \frontend\shared\helpers\PayloadCoercion::int($payload['modo'] ?? 0),
            'observ' => \frontend\shared\helpers\PayloadCoercion::string($payload['observ'] ?? ''),
            'opciones_modo' => NotasFormSupport::desplegableOpciones($payload['opciones_modo'] ?? []),
            'id_item' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_item'] ?? ''),
            'id_tipo_activ' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_tipo_activ'] ?? 0),
            'id_tarifa_sel' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_tarifa_sel'] ?? 0),
            'isfsv' => \frontend\shared\helpers\PayloadCoercion::int($payload['isfsv'] ?? 0),
            'nom_tipo_activ' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom_tipo_activ'] ?? ''),
            'opciones_tarifa' => NotasFormSupport::desplegableOpciones($payload['opciones_tarifa'] ?? []),
            'id_ubi' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_ubi'] ?? 0),
            'year' => \frontend\shared\helpers\PayloadCoercion::int($payload['year'] ?? 0),
            'cantidad' => \frontend\shared\helpers\PayloadCoercion::string($payload['cantidad'] ?? ''),
            'opciones_serie' => NotasFormSupport::desplegableOpciones($payload['opciones_serie'] ?? []),
            'id_serie_sel' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_serie_sel'] ?? 1),
            'token_update' => \frontend\shared\helpers\PayloadCoercion::string($payload['token_update'] ?? ''),
            'token_eliminar' => \frontend\shared\helpers\PayloadCoercion::string($payload['token_eliminar'] ?? ''),
            'a_cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
            'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
            'puede_anadir' => ($payload['puede_anadir'] ?? false) === true,
            'any_anterior' => \frontend\shared\helpers\PayloadCoercion::int($payload['any_anterior'] ?? 0),
            'any_actual' => \frontend\shared\helpers\PayloadCoercion::int($payload['any_actual'] ?? 0),
            'token_copiar' => \frontend\shared\helpers\PayloadCoercion::string($payload['token_copiar'] ?? ''),
        ];
    }

    public static function jsonForHtml(string $value): string
    {
        $encoded = json_encode($value, JSON_UNESCAPED_SLASHES);

        return htmlspecialchars($encoded !== false ? $encoded : '""', ENT_QUOTES, 'UTF-8');
    }
}
