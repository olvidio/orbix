<?php

/**
 * Helpers compartidos del módulo frontend/pasarela.
 */

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';
require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

/**
 * @return array{id_tipo_activ: string, etiqueta: string, valor: string}|null
 */
function pasarela_excepcion_row(mixed $raw): ?array
{
    if (!is_array($raw)) {
        return null;
    }

    return [
        'id_tipo_activ' => tessera_imprimir_string($raw['id_tipo_activ'] ?? ''),
        'etiqueta' => tessera_imprimir_string($raw['etiqueta'] ?? ''),
        'valor' => tessera_imprimir_string($raw['valor'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $raw
 * @return array{default: string, excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>}
 */
function pasarela_excepcion_lista_con_default_from_payload(array $raw): array
{
    $excepcionesRaw = $raw['excepciones'] ?? [];
    $excepciones = [];
    if (is_array($excepcionesRaw)) {
        foreach ($excepcionesRaw as $row) {
            $parsed = pasarela_excepcion_row($row);
            if ($parsed !== null) {
                $excepciones[] = $parsed;
            }
        }
    }

    return [
        'default' => tessera_imprimir_string($raw['default'] ?? ''),
        'excepciones' => $excepciones,
    ];
}

/**
 * @param array<int|string, mixed> $raw
 * @return array{excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>}
 */
function pasarela_excepcion_lista_from_payload(array $raw): array
{
    $parsed = pasarela_excepcion_lista_con_default_from_payload($raw);

    return ['excepciones' => $parsed['excepciones']];
}

/**
 * @param array{default: string, excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>} $data
 */
function pasarela_render_excepcion_lista_con_default_html(array $data, string $defaultOnclick, string $rowOnclickFn): string
{
    $default = htmlspecialchars($data['default'], ENT_QUOTES, 'UTF-8');
    $html = '<table>';
    $html .= '<tr><td>' . _('por defecto') . '</td><td>';
    $html .= '<span class="link" onclick="' . $defaultOnclick . '">' . $default . '</span></td></tr>';
    $html .= '</table><table>';
    foreach ($data['excepciones'] as $row) {
        $idTipoActiv = tessera_imprimir_int($row['id_tipo_activ']);
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
function pasarela_render_excepcion_lista_html(array $data, string $rowOnclickFn): string
{
    $html = '<table>';
    foreach ($data['excepciones'] as $row) {
        $idTipoActiv = tessera_imprimir_int($row['id_tipo_activ']);
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

function pasarela_tipo_txt_from_payload(mixed $raw): string
{
    if (!is_array($raw)) {
        return '';
    }

    return tessera_imprimir_string($raw['tipo_txt'] ?? '');
}

function pasarela_exportar_errores_from_payload(mixed $raw): string
{
    if (!is_array($raw)) {
        return '';
    }

    return tessera_imprimir_string($raw['errores'] ?? '');
}

/**
 * @param array<int|string, mixed> $raw
 * @return array{
 *     a_cabeceras: list<array<string, mixed>|string>,
 *     a_botones: list<array<string, mixed>>,
 *     a_valores: array<int|string, mixed>,
 * }
 */
function pasarela_exportar_lista_from_payload(array $raw): array
{
    return [
        'a_cabeceras' => actividades_lista_cabeceras($raw['a_cabeceras'] ?? []),
        'a_botones' => actividades_lista_botones($raw['a_botones'] ?? []),
        'a_valores' => actividades_lista_datos($raw['a_valores'] ?? []),
    ];
}
