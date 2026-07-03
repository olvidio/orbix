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
            'id_tipo_activ' => PayloadCoercion::string($raw['id_tipo_activ'] ?? ''),
            'etiqueta' => PayloadCoercion::string($raw['etiqueta'] ?? ''),
            'valor' => PayloadCoercion::string($raw['valor'] ?? ''),
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
            'default' => PayloadCoercion::string($raw['default'] ?? ''),
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

        return PayloadCoercion::string($raw['tipo_txt'] ?? '');
    }

    public static function exportarErroresFromPayload(mixed $raw): string
    {
        if (!is_array($raw)) {
            return '';
        }

        return PayloadCoercion::string($raw['errores'] ?? '');
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

final class PasarelaExcepcionRender
{
    /**
     * @param array{default: string, excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>} $data
     */
    public static function listaConDefaultHtml(array $data, string $defaultOnclick, string $rowOnclickFn): string
    {
        $default = htmlspecialchars($data['default'], ENT_QUOTES, 'UTF-8');
        $html = '<table>';
        $html .= '<tr><td>' . _('por defecto') . '</td><td>';
        $html .= '<span class="link" onclick="' . $defaultOnclick . '">' . $default . '</span></td></tr>';
        $html .= '</table><table>';
        foreach ($data['excepciones'] as $row) {
            $idTipoActiv = PayloadCoercion::int($row['id_tipo_activ']);
            $etiqueta = htmlspecialchars($row['etiqueta'], ENT_QUOTES, 'UTF-8');
            $valor = $row['valor'];
            $valorJs = htmlspecialchars(addslashes($valor), ENT_QUOTES, 'UTF-8');
            $valorHtml = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
            $html .= "<tr><td>$etiqueta</td><td>";
            $html .= "<span class=\"link\" onclick=\"$rowOnclickFn($idTipoActiv,'$valorJs')\">$valorHtml</span></td></tr>";
        }
        $html .= '</table>';

        return $html;
    }

    /**
     * @param array{excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>} $data
     */
    public static function listaHtml(array $data, string $rowOnclickFn): string
    {
        $html = '<table>';
        foreach ($data['excepciones'] as $row) {
            $idTipoActiv = PayloadCoercion::int($row['id_tipo_activ']);
            $etiqueta = htmlspecialchars($row['etiqueta'], ENT_QUOTES, 'UTF-8');
            $valor = $row['valor'];
            $valorJs = htmlspecialchars(addslashes($valor), ENT_QUOTES, 'UTF-8');
            $valorHtml = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
            $html .= "<tr><td>$etiqueta</td><td>";
            $html .= "<span class=\"link\" onclick=\"$rowOnclickFn($idTipoActiv,'$valorJs')\" size=\"200\">$valorHtml</span></td></tr>";
        }
        $html .= '</table>';

        return $html;
    }
}
