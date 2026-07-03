<?php

declare(strict_types=1);

namespace frontend\pasarela\helpers;

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class PasarelaPayload
{
    /**
     * @return array{id_tipo_activ: string, etiqueta: string, valor: string}|null
     */
    public static function excepcionRow(mixed $raw): ?array
    {
        if (!is_array($raw)) {
            return null;
        }

        return [
            'id_tipo_activ' => \frontend\shared\helpers\PayloadCoercion::string($raw['id_tipo_activ'] ?? ''),
            'etiqueta' => \frontend\shared\helpers\PayloadCoercion::string($raw['etiqueta'] ?? ''),
            'valor' => \frontend\shared\helpers\PayloadCoercion::string($raw['valor'] ?? ''),
        ];
    }

    /**
     * @param array<int|string, mixed> $raw
     * @return array{default: string, excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>}
     */
    public static function excepcionListaConDefaultFromPayload(array $raw): array
    {
        $excepcionesRaw = $raw['excepciones'] ?? [];
        $excepciones = [];
        if (is_array($excepcionesRaw)) {
            foreach ($excepcionesRaw as $row) {
                $parsed = self::excepcionRow($row);
                if ($parsed !== null) {
                    $excepciones[] = $parsed;
                }
            }
        }

        return [
            'default' => \frontend\shared\helpers\PayloadCoercion::string($raw['default'] ?? ''),
            'excepciones' => $excepciones,
        ];
    }

    /**
     * @param array<int|string, mixed> $raw
     * @return array{excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>}
     */
    public static function excepcionListaFromPayload(array $raw): array
    {
        $parsed = self::excepcionListaConDefaultFromPayload($raw);

        return ['excepciones' => $parsed['excepciones']];
    }

    public static function tipoTxtFromPayload(mixed $raw): string
    {
        if (!is_array($raw)) {
            return '';
        }

        return \frontend\shared\helpers\PayloadCoercion::string($raw['tipo_txt'] ?? '');
    }

    public static function exportarErroresFromPayload(mixed $raw): string
    {
        if (!is_array($raw)) {
            return '';
        }

        return \frontend\shared\helpers\PayloadCoercion::string($raw['errores'] ?? '');
    }

    /**
     * @param array<int|string, mixed> $raw
     * @return array{
     *     a_cabeceras: list<array<string, mixed>|string>,
     *     a_botones: list<array<string, mixed>>,
     *     a_valores: array<int|string, mixed>,
     * }
     */
    public static function exportarListaFromPayload(array $raw): array
    {
        return [
            'a_cabeceras' => ActividadesListaSupport::cabeceras($raw['a_cabeceras'] ?? []),
            'a_botones' => ActividadesListaSupport::botones($raw['a_botones'] ?? []),
            'a_valores' => ActividadesListaSupport::datos($raw['a_valores'] ?? []),
        ];
    }
}
