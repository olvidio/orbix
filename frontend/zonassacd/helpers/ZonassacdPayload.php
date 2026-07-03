<?php

declare(strict_types=1);

namespace frontend\zonassacd\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ZonassacdPayload
{
    /**
     * @param array<int|string, mixed> $payload
     * @return array{a_opciones: array<int|string, string>, perm_des: bool}
     */
    public static function pageFromPayload(array $payload): array
    {
        return [
            'a_opciones' => NotasFormSupport::desplegableOpciones($payload['a_opciones'] ?? []),
            'perm_des' => !empty($payload['perm_des']),
        ];
    }

    /**
     * @param array<int|string, mixed> $payload
     * @return array{
     *     id_tabla: string,
     *     a_cabeceras: list<array<string, mixed>|string>,
     *     a_botones: list<array<string, mixed>>,
     *     con_sel: bool,
     *     a_valores: array<int|string, mixed>,
     * }
     */
    public static function listaFromPayload(array $payload): array
    {
        return [
            'id_tabla' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_tabla'] ?? ''),
            'a_cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? null),
            'a_botones' => ActividadesListaSupport::botones($payload['a_botones'] ?? null),
            'con_sel' => !empty($payload['con_sel']),
            'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? null),
        ];
    }

    /**
     * @param array<int|string, mixed> $payload
     * @return array{
     *     a_cabeceras: list<array<string, mixed>|string>,
     *     a_valores: array<int|string, mixed>,
     * }
     */
    public static function listaTotFromPayload(array $payload): array
    {
        return [
            'a_cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? null),
            'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? null),
        ];
    }
}
