<?php

declare(strict_types=1);

namespace frontend\procesos\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;
use src\permisos\domain\XPermisos;

final class ProcesosPayload
{
public static function oPerm(): ?XPermisos
{
    $oPerm = $_SESSION['oPerm'] ?? null;

    return $oPerm instanceof XPermisos ? $oPerm : null;
}

public static function havePermCalendario(): bool
{
    $oPerm = self::oPerm();
    if ($oPerm === null) {
        return false;
    }

    return $oPerm->have_perm_oficina('calendario')
        || $oPerm->have_perm_oficina('vcsd')
        || $oPerm->have_perm_oficina('des');
}

/**
 * @return array<string, mixed>
 */
public static function payloadData(mixed $raw): array
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
public static function verFromPayload(array $payload): array
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
        'a_oficinas' => NotasFormSupport::desplegableOpciones($payload['a_oficinas'] ?? []),
        'a_status' => NotasFormSupport::desplegableOpciones($payload['a_status'] ?? []),
        'a_fases' => NotasFormSupport::desplegableOpciones($payload['a_fases'] ?? []),
        'a_tareas' => NotasFormSupport::desplegableOpciones($payload['a_tareas'] ?? []),
        'a_fases_previas' => $fasesPrevias,
        'status' => PayloadCoercion::string($payload['status'] ?? ''),
        'id_of_responsable' => PayloadCoercion::string($payload['id_of_responsable'] ?? ''),
        'id_fase' => PayloadCoercion::string($payload['id_fase'] ?? ''),
        'id_tarea' => PayloadCoercion::string($payload['id_tarea'] ?? ''),
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
public static function fasePreviaRow(mixed $raw): array
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
        'id_fase_previa' => PayloadCoercion::string($raw['id_fase_previa'] ?? ''),
        'id_tarea_previa' => PayloadCoercion::string($raw['id_tarea_previa'] ?? ''),
        'mensaje_requisito' => PayloadCoercion::string($raw['mensaje_requisito'] ?? ''),
        'a_tareas_previa' => NotasFormSupport::desplegableOpciones($raw['a_tareas_previa'] ?? []),
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
public static function actividadProcesoRow(mixed $raw): array
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
        'id_item' => PayloadCoercion::int($raw['id_item'] ?? 0),
        'fase' => PayloadCoercion::string($raw['fase'] ?? ''),
        'tarea' => PayloadCoercion::string($raw['tarea'] ?? ''),
        'of_responsable_txt' => PayloadCoercion::string($raw['of_responsable_txt'] ?? ''),
        'completado' => !empty($raw['completado']),
        'observ' => PayloadCoercion::string($raw['observ'] ?? ''),
        'puede_editar' => !empty($raw['puede_editar']),
    ];
}

/**
 * @return list<array{id_item: int, fase: string, tarea: string, of_responsable_txt: string, completado: bool, observ: string, puede_editar: bool}>
 */
public static function actividadProcesoRows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = self::actividadProcesoRow($row);
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
public static function listadoRow(mixed $raw): array
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
        'id_item' => PayloadCoercion::int($raw['id_item'] ?? 0),
        'status_txt' => PayloadCoercion::string($raw['status_txt'] ?? ''),
        'responsable' => PayloadCoercion::string($raw['responsable'] ?? ''),
        'fase' => PayloadCoercion::string($raw['fase'] ?? ''),
        'tarea' => PayloadCoercion::string($raw['tarea'] ?? ''),
        'fase_previa' => PayloadCoercion::string($raw['fase_previa'] ?? ''),
    ];
}

/**
 * @return list<array{id_item: int, status_txt: string, responsable: string, fase: string, tarea: string, fase_previa: string}>
 */
public static function listadoRows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = self::listadoRow($row);
    }

    return $out;
}

/**
 * @return array{id_tipo_activ: string, nom: string, nom_proceso_propio: string, nom_proceso_no_propio: string}
 */
public static function tipoActivRow(mixed $raw): array
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
        'id_tipo_activ' => PayloadCoercion::string($raw['id_tipo_activ'] ?? ''),
        'nom' => PayloadCoercion::string($raw['nom'] ?? ''),
        'nom_proceso_propio' => PayloadCoercion::string($raw['nom_proceso_propio'] ?? ''),
        'nom_proceso_no_propio' => PayloadCoercion::string($raw['nom_proceso_no_propio'] ?? ''),
    ];
}

/**
 * @return array{id_tipo_proceso: int, nom_proceso: string}
 */
public static function tipoProcesoPosibleRow(mixed $raw): array
{
    if (!is_array($raw)) {
        return ['id_tipo_proceso' => 0, 'nom_proceso' => ''];
    }

    return [
        'id_tipo_proceso' => PayloadCoercion::int($raw['id_tipo_proceso'] ?? 0),
        'nom_proceso' => PayloadCoercion::string($raw['nom_proceso'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $requestPayload
 * @return array<string, string>
 */
public static function fasesActivCambioGoback(array $requestPayload): array
{
    return [
        'refresh' => '1',
        'hnov' => '0',
        'dl_propia' => PayloadCoercion::string($requestPayload['dl_propia'] ?? ''),
        'id_fase_nueva' => PayloadCoercion::string($requestPayload['id_fase_nueva'] ?? ''),
        'id_tipo_activ' => PayloadCoercion::string($requestPayload['id_tipo_activ'] ?? ''),
        'periodo' => PayloadCoercion::string($requestPayload['periodo'] ?? ''),
        'year' => PayloadCoercion::string($requestPayload['year'] ?? ''),
        'empiezamin' => PayloadCoercion::string($requestPayload['empiezamin'] ?? ''),
        'empiezamax' => PayloadCoercion::string($requestPayload['empiezamax'] ?? ''),
        'accion' => PayloadCoercion::string($requestPayload['accion'] ?? ''),
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
public static function usuarioPermRow(mixed $raw): array
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
        'fase_ref' => PayloadCoercion::string($raw['fase_ref'] ?? ''),
        'perm_on' => PayloadCoercion::string($raw['perm_on'] ?? ''),
        'perm_off' => PayloadCoercion::string($raw['perm_off'] ?? ''),
        'afecta_a' => $raw['afecta_a'] ?? '',
        'num' => $raw['num'] ?? '',
        'marcado' => !empty($raw['marcado']),
    ];
}

/**
 * @return list<array{fase_ref: string, perm_on: string, perm_off: string, afecta_a: mixed, num: mixed, marcado: bool}>
 */
public static function usuarioPermRows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = self::usuarioPermRow($row);
    }

    return $out;
}

/**
 * @return array<int, array<int, array<string, mixed>>>
 */
public static function treePadres(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $items) {
        $keyInt = PayloadCoercion::int($key);
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
}
