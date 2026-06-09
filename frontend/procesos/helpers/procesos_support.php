<?php

/**
 * Helpers compartidos del módulo frontend/procesos.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use src\permisos\domain\XPermisos;

function procesos_o_perm(): ?XPermisos
{
    $oPerm = $_SESSION['oPerm'] ?? null;

    return $oPerm instanceof XPermisos ? $oPerm : null;
}

function procesos_have_perm_calendario(): bool
{
    $oPerm = procesos_o_perm();
    if ($oPerm === null) {
        return false;
    }

    return $oPerm->have_perm_oficina('calendario')
        || $oPerm->have_perm_oficina('vcsd')
        || $oPerm->have_perm_oficina('des');
}

function procesos_post_string(string $name, string $default = ''): string
{
    return tessera_imprimir_string(filter_input(INPUT_POST, $name), $default);
}

function procesos_post_int(string $name, int $default = 0): int
{
    $raw = filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT);

    return is_int($raw) ? $raw : $default;
}

/**
 * @return array{id: int, id_item: string, id_usuario: int, id_tipo_activ_txt: string, dl_propia: string}
 */
function procesos_sel_tokens_from_post(): array
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!is_array($a_sel_raw) || $a_sel_raw === []) {
        return [
            'id' => procesos_post_int('id_activ'),
            'id_item' => '',
            'id_usuario' => procesos_post_int('id_usuario'),
            'id_tipo_activ_txt' => procesos_post_string('id_tipo_activ_txt'),
            'dl_propia' => procesos_post_string('dl_propia'),
        ];
    }
    $sel0 = $a_sel_raw[0];
    if (!is_string($sel0) || $sel0 === '') {
        return [
            'id' => procesos_post_int('id_activ'),
            'id_item' => '',
            'id_usuario' => procesos_post_int('id_usuario'),
            'id_tipo_activ_txt' => procesos_post_string('id_tipo_activ_txt'),
            'dl_propia' => procesos_post_string('dl_propia'),
        ];
    }
    $parts = explode('#', $sel0, 4);
    $id0 = $parts[0];

    return [
        'id' => is_numeric($id0) ? (int) $id0 : 0,
        'id_item' => tessera_imprimir_string($parts[1] ?? ''),
        'id_usuario' => is_numeric($id0) ? (int) $id0 : 0,
        'id_tipo_activ_txt' => tessera_imprimir_string($parts[2] ?? ''),
        'dl_propia' => tessera_imprimir_string($parts[3] ?? ''),
    ];
}

/**
 * @return array<string, mixed>
 */
function procesos_payload_data(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     a_oficinas: array<int|string, string>,
 *     a_status: array<int|string, string>,
 *     a_fases: array<int|string, string>,
 *     a_tareas: array<int|string, string>,
 *     a_fases_previas: list<array<string, mixed>>,
 *     status: string,
 *     id_of_responsable: string,
 *     id_fase: string,
 *     id_tarea: string,
 * }
 */
function procesos_ver_from_payload(array $payload): array
{
    $fasesPrevias = [];
    $rawPrevias = $payload['a_fases_previas'] ?? [];
    if (is_array($rawPrevias)) {
        foreach ($rawPrevias as $fila) {
            if (is_array($fila)) {
                $fasesPrevias[] = $fila;
            }
        }
    }

    return [
        'a_oficinas' => notas_desplegable_opciones($payload['a_oficinas'] ?? []),
        'a_status' => notas_desplegable_opciones($payload['a_status'] ?? []),
        'a_fases' => notas_desplegable_opciones($payload['a_fases'] ?? []),
        'a_tareas' => notas_desplegable_opciones($payload['a_tareas'] ?? []),
        'a_fases_previas' => $fasesPrevias,
        'status' => tessera_imprimir_string($payload['status'] ?? ''),
        'id_of_responsable' => tessera_imprimir_string($payload['id_of_responsable'] ?? ''),
        'id_fase' => tessera_imprimir_string($payload['id_fase'] ?? ''),
        'id_tarea' => tessera_imprimir_string($payload['id_tarea'] ?? ''),
    ];
}

/**
 * @return array{
 *     id_fase_previa: string,
 *     id_tarea_previa: string,
 *     mensaje_requisito: string,
 *     a_tareas_previa: array<int|string, string>,
 * }
 */
function procesos_fase_previa_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_fase_previa' => '',
            'id_tarea_previa' => '',
            'mensaje_requisito' => '',
            'a_tareas_previa' => [],
        ];
    }

    return [
        'id_fase_previa' => tessera_imprimir_string($raw['id_fase_previa'] ?? ''),
        'id_tarea_previa' => tessera_imprimir_string($raw['id_tarea_previa'] ?? ''),
        'mensaje_requisito' => tessera_imprimir_string($raw['mensaje_requisito'] ?? ''),
        'a_tareas_previa' => notas_desplegable_opciones($raw['a_tareas_previa'] ?? []),
    ];
}

/**
 * @return array{
 *     id_item: int,
 *     fase: string,
 *     tarea: string,
 *     of_responsable_txt: string,
 *     completado: bool,
 *     observ: string,
 *     puede_editar: bool,
 * }
 */
function procesos_actividad_proceso_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_item' => 0,
            'fase' => '',
            'tarea' => '',
            'of_responsable_txt' => '',
            'completado' => false,
            'observ' => '',
            'puede_editar' => false,
        ];
    }

    return [
        'id_item' => tessera_imprimir_int($raw['id_item'] ?? 0),
        'fase' => tessera_imprimir_string($raw['fase'] ?? ''),
        'tarea' => tessera_imprimir_string($raw['tarea'] ?? ''),
        'of_responsable_txt' => tessera_imprimir_string($raw['of_responsable_txt'] ?? ''),
        'completado' => !empty($raw['completado']),
        'observ' => tessera_imprimir_string($raw['observ'] ?? ''),
        'puede_editar' => !empty($raw['puede_editar']),
    ];
}

/**
 * @return list<array{id_item: int, fase: string, tarea: string, of_responsable_txt: string, completado: bool, observ: string, puede_editar: bool}>
 */
function procesos_actividad_proceso_rows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = procesos_actividad_proceso_row($row);
    }

    return $out;
}

/**
 * @return array{
 *     id_item: int,
 *     status_txt: string,
 *     responsable: string,
 *     fase: string,
 *     tarea: string,
 *     fase_previa: string,
 * }
 */
function procesos_listado_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_item' => 0,
            'status_txt' => '',
            'responsable' => '',
            'fase' => '',
            'tarea' => '',
            'fase_previa' => '',
        ];
    }

    return [
        'id_item' => tessera_imprimir_int($raw['id_item'] ?? 0),
        'status_txt' => tessera_imprimir_string($raw['status_txt'] ?? ''),
        'responsable' => tessera_imprimir_string($raw['responsable'] ?? ''),
        'fase' => tessera_imprimir_string($raw['fase'] ?? ''),
        'tarea' => tessera_imprimir_string($raw['tarea'] ?? ''),
        'fase_previa' => tessera_imprimir_string($raw['fase_previa'] ?? ''),
    ];
}

/**
 * @return list<array{id_item: int, status_txt: string, responsable: string, fase: string, tarea: string, fase_previa: string}>
 */
function procesos_listado_rows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = procesos_listado_row($row);
    }

    return $out;
}

/**
 * @return array{id_tipo_activ: string, nom: string, nom_proceso_propio: string, nom_proceso_no_propio: string}
 */
function procesos_tipo_activ_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_tipo_activ' => '',
            'nom' => '',
            'nom_proceso_propio' => '',
            'nom_proceso_no_propio' => '',
        ];
    }

    return [
        'id_tipo_activ' => tessera_imprimir_string($raw['id_tipo_activ'] ?? ''),
        'nom' => tessera_imprimir_string($raw['nom'] ?? ''),
        'nom_proceso_propio' => tessera_imprimir_string($raw['nom_proceso_propio'] ?? ''),
        'nom_proceso_no_propio' => tessera_imprimir_string($raw['nom_proceso_no_propio'] ?? ''),
    ];
}

/**
 * @return array{id_tipo_proceso: int, nom_proceso: string}
 */
function procesos_tipo_proceso_posible_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return ['id_tipo_proceso' => 0, 'nom_proceso' => ''];
    }

    return [
        'id_tipo_proceso' => tessera_imprimir_int($raw['id_tipo_proceso'] ?? 0),
        'nom_proceso' => tessera_imprimir_string($raw['nom_proceso'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $requestPayload
 * @return array<string, string>
 */
function procesos_fases_activ_cambio_goback(array $requestPayload): array
{
    return [
        'refresh' => '1',
        'hnov' => '0',
        'dl_propia' => tessera_imprimir_string($requestPayload['dl_propia'] ?? ''),
        'id_fase_nueva' => tessera_imprimir_string($requestPayload['id_fase_nueva'] ?? ''),
        'id_tipo_activ' => tessera_imprimir_string($requestPayload['id_tipo_activ'] ?? ''),
        'periodo' => tessera_imprimir_string($requestPayload['periodo'] ?? ''),
        'year' => tessera_imprimir_string($requestPayload['year'] ?? ''),
        'empiezamin' => tessera_imprimir_string($requestPayload['empiezamin'] ?? ''),
        'empiezamax' => tessera_imprimir_string($requestPayload['empiezamax'] ?? ''),
        'accion' => tessera_imprimir_string($requestPayload['accion'] ?? ''),
    ];
}

/**
 * @return array{
 *     fase_ref: string,
 *     perm_on: string,
 *     perm_off: string,
 *     afecta_a: mixed,
 *     num: mixed,
 *     marcado: bool,
 * }
 */
function procesos_usuario_perm_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'fase_ref' => '',
            'perm_on' => '',
            'perm_off' => '',
            'afecta_a' => '',
            'num' => '',
            'marcado' => false,
        ];
    }

    return [
        'fase_ref' => tessera_imprimir_string($raw['fase_ref'] ?? ''),
        'perm_on' => tessera_imprimir_string($raw['perm_on'] ?? ''),
        'perm_off' => tessera_imprimir_string($raw['perm_off'] ?? ''),
        'afecta_a' => $raw['afecta_a'] ?? '',
        'num' => $raw['num'] ?? '',
        'marcado' => !empty($raw['marcado']),
    ];
}

/**
 * @return list<array{fase_ref: string, perm_on: string, perm_off: string, afecta_a: mixed, num: mixed, marcado: bool}>
 */
function procesos_usuario_perm_rows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = procesos_usuario_perm_row($row);
    }

    return $out;
}

/**
 * @return array<int, array<int, array<string, mixed>>>
 */
function procesos_tree_padres(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $items) {
        $keyInt = tessera_imprimir_int($key);
        if (!is_array($items)) {
            continue;
        }
        $parsed = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                $parsed[] = $item;
            }
        }
        $out[$keyInt] = $parsed;
    }

    return $out;
}
