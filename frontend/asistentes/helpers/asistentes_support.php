<?php

/**
 * Helpers compartidos del módulo frontend/asistentes.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\security\HashFrontSignedLink;

/**
 * @return array<string, mixed>
 */
function asistentes_post_data(mixed $data): array
{
    if (!is_array($data)) {
        return [];
    }
    $out = [];
    foreach ($data as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out;
}

/**
 * @return array<string, mixed>
 */
function asistentes_hash_campos_hidden(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $k => $v) {
        if (is_string($k)) {
            $out[$k] = $v;
        }
    }

    return $out;
}

/**
 * @return array<string, array{path: string, query?: array<string, mixed>}>
 */
function asistentes_link_specs_by_label(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $label => $spec) {
        if (!is_string($label) || !is_array($spec)) {
            continue;
        }
        $path = $spec['path'] ?? null;
        if (!is_string($path) || $path === '') {
            continue;
        }
        $entry = ['path' => $path];
        $query = $spec['query'] ?? null;
        if (is_array($query)) {
            $q = [];
            foreach ($query as $k => $v) {
                $q[(string) $k] = $v;
            }
            $entry['query'] = $q;
        }
        $out[$label] = $entry;
    }

    return $out;
}

/**
 * @return array<string, string>
 */
function asistentes_sign_link_map(mixed $raw): array
{
    return DossierTipoFormLinkSpecsSigning::signLinkMap(asistentes_link_specs_by_label($raw));
}

function asistentes_id_from_sel_post(string $fallbackField = 'id_activ_old'): int
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);

            return is_numeric($parts[0]) ? (int) $parts[0] : 0;
        }
    }
    $idRaw = filter_input(INPUT_POST, $fallbackField, FILTER_VALIDATE_INT);

    return is_int($idRaw) ? $idRaw : 0;
}

/**
 * @return array{id_nom: int, id_tabla: string}
 */
function asistentes_persona_from_sel_post(): array
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);

            return [
                'id_nom' => is_numeric($parts[0]) ? (int) $parts[0] : 0,
                'id_tabla' => $parts[1] ?? '',
            ];
        }
    }
    $idNomRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

    return [
        'id_nom' => is_int($idNomRaw) ? $idNomRaw : 0,
        'id_tabla' => tessera_imprimir_string(filter_input(INPUT_POST, 'id_tabla')),
    ];
}

/**
 * @return array{form_name: string, titulo: string, opciones_periodos: array<int|string, string>, periodo_sel: string, year_sel: string}|null
 */
function asistentes_periodo_form_config(mixed $raw): ?array
{
    if (!is_array($raw)) {
        return null;
    }

    return [
        'form_name' => tessera_imprimir_string($raw['form_name'] ?? 'modifica'),
        'titulo' => tessera_imprimir_string($raw['titulo'] ?? ''),
        'opciones_periodos' => notas_desplegable_opciones($raw['opciones_periodos'] ?? []),
        'periodo_sel' => tessera_imprimir_string($raw['periodo_sel'] ?? 'tot_any'),
        'year_sel' => tessera_imprimir_string($raw['year_sel'] ?? (string) date('Y')),
    ];
}

/**
 * @return array{t: string, s: string, h: array<string, mixed>}
 */
function asistentes_peticion_part(mixed $raw): array
{
    if (!is_array($raw)) {
        return ['t' => '', 's' => '', 'h' => []];
    }

    return [
        't' => tessera_imprimir_string($raw['t'] ?? ''),
        's' => tessera_imprimir_string($raw['s'] ?? ''),
        'h' => asistentes_hash_campos_hidden($raw['h'] ?? []),
    ];
}

/**
 * @return list<array{t: string, s: string, h: array<string, mixed>}>
 */
function asistentes_peticiones_parts(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $part) {
        $out[] = asistentes_peticion_part($part);
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{api_save_url: string, cabeceras: list<array<string, mixed>|string>, botones: list<array<string, mixed>>, valores: array<int|string, mixed>}
 */
function asistentes_tabla_peticiones_from_payload(array $payload): array
{
    $paths = is_array($payload['paths'] ?? null) ? $payload['paths'] : [];
    $apiPath = tessera_imprimir_string($paths['asistente_guardar'] ?? '');
    $apiSaveUrl = $apiPath !== ''
        ? rtrim(\frontend\shared\config\AppUrlConfig::getPublicAppBaseUrl(), '/') . '/' . ltrim($apiPath, '/')
        : '';

    return [
        'api_save_url' => $apiSaveUrl,
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'botones' => actividades_lista_botones($payload['a_botones'] ?? []),
        'valores' => actividades_lista_datos($payload['a_valores'] ?? []),
    ];
}

/**
 * @param array<int|string, mixed> $fila
 */
/**
 * @param array<int|string, mixed> $fila
 * @return array<int|string, mixed>
 */
function asistentes_tabla_peticiones_resolve_cell(array $fila, string $apiSaveUrl): array
{
    if (!array_key_exists(2, $fila) || !is_array($fila[2])) {
        return $fila;
    }
    $col2 = $fila[2];
    $parts = asistentes_peticiones_parts($col2['peticiones_parts'] ?? []);
    $out = '';
    foreach ($parts as $p) {
        if ($p['t'] === 'p') {
            $out .= $p['s'];
        } elseif ($p['t'] === 'm' && $p['h'] !== []) {
            $oHash = new \frontend\shared\security\HashFront();
            $oHash->setUrl($apiSaveUrl);
            $oHash->setArrayCamposHidden($p['h']);
            $param = $oHash->getParamAjax();
            $out .= '<span class="link" onClick="fnjs_cambiar_actividad(\'' . $param . '\')">'
                . htmlspecialchars($p['s'], ENT_QUOTES, 'UTF-8') . '</span>';
        }
    }
    $fila[2] = $out;

    return $fila;
}

/**
 * @return list<array<string, mixed>>
 */
function asistentes_sign_lista_valores(mixed $raw): array
{
    return actividades_lista_valores_from_payload($raw);
}
