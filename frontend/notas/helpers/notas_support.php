<?php

/**
 * Helpers compartidos del módulo frontend/notas.
 */

require_once __DIR__ . '/tessera_imprimir_support.php';

function notas_form_scalar(mixed $value): int|string
{
    if (is_int($value)) {
        return $value;
    }
    if (is_string($value)) {
        return $value;
    }
    if (is_bool($value)) {
        return $value ? '1' : '';
    }
    if (is_float($value)) {
        return (string) $value;
    }

    return '';
}

function notas_form_bool_or_string(mixed $value): bool|string
{
    if (is_bool($value)) {
        return $value;
    }

    return tessera_imprimir_string($value);
}

/**
 * @return array<int|string, string>
 */
function notas_desplegable_opciones(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_int($key)) {
            $out[$key] = tessera_imprimir_string($value);
        } elseif (is_string($key)) {
            $out[$key] = tessera_imprimir_string($value);
        }
    }

    return $out;
}

/**
 * @return list<int|string>
 */
function notas_checked_ids_from_post(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $id) {
        if (is_int($id) || is_string($id)) {
            $out[] = $id;
        }
    }

    return $out;
}

/**
 * @return array{id_nom: int, id_tabla: string}
 */
function notas_persona_from_sel_post(): array
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);
            $idNomRaw = $parts[0];
            $idTabla = $parts[1] ?? '';

            return [
                'id_nom' => is_numeric($idNomRaw) ? (int) $idNomRaw : 0,
                'id_tabla' => $idTabla,
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
 * @return array{id_nom: int, id_tabla: string, nom: string, nombre_ubi: string, stgr: string, asig_txt: string, telfs: string, mails: string}
 */
function notas_asig_faltan_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_nom' => 0,
            'id_tabla' => '',
            'nom' => '',
            'nombre_ubi' => '',
            'stgr' => '',
            'asig_txt' => '',
            'telfs' => '',
            'mails' => '',
        ];
    }
    $asigTxt = $raw['asig_txt'] ?? '';
    if (is_int($asigTxt)) {
        $asigTxtStr = (string) $asigTxt;
    } else {
        $asigTxtStr = tessera_imprimir_string($asigTxt);
    }

    return [
        'id_nom' => tessera_imprimir_int($raw['id_nom'] ?? 0),
        'id_tabla' => tessera_imprimir_string($raw['id_tabla'] ?? ''),
        'nom' => tessera_imprimir_string($raw['nom'] ?? ''),
        'nombre_ubi' => tessera_imprimir_string($raw['nombre_ubi'] ?? ''),
        'stgr' => tessera_imprimir_string($raw['stgr'] ?? ''),
        'asig_txt' => $asigTxtStr,
        'telfs' => tessera_imprimir_string($raw['telfs'] ?? ''),
        'mails' => tessera_imprimir_string($raw['mails'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{titulo: string, obj_pau: string, rows: list<array{id_nom: int, id_tabla: string, nom: string, nombre_ubi: string, stgr: string, asig_txt: string, telfs: string, mails: string}>}
 */
function notas_asig_faltan_tabla_from_payload(array $payload): array
{
    $rawRows = $payload['rows'] ?? [];
    $rows = [];
    if (is_array($rawRows)) {
        foreach ($rawRows as $row) {
            $rows[] = notas_asig_faltan_row($row);
        }
    }

    return [
        'titulo' => tessera_imprimir_string($payload['titulo'] ?? ''),
        'obj_pau' => tessera_imprimir_string($payload['obj_pau'] ?? ''),
        'rows' => $rows,
    ];
}

/**
 * @return list<array{txt: string, click: string}>
 */
function notas_botones_modificar_tessera(): array
{
    return [
        ['txt' => _('modificar stgr'), 'click' => 'fnjs_modificar("#seleccionados")'],
        ['txt' => _('ver tessera'), 'click' => 'fnjs_tesera("#seleccionados")'],
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     notas: string,
 *     permiso: int,
 *     mod: string,
 *     acta_actual: string,
 *     acta_new: string,
 *     ult_acta: string,
 *     f_acta: string,
 *     libro: string,
 *     ult_lib: string,
 *     pagina: string,
 *     ult_pag: string,
 *     linea: string,
 *     ult_lin: string,
 *     lugar: string,
 *     observ: string,
 *     id_activ: int,
 *     id_asignatura_actual: string,
 *     nombre_asignatura: string,
 *     examinadores: list<string>,
 *     a_actas: list<string>,
 *     has_pdf: bool,
 *     warn_no_id_activ: bool,
 * }
 */
function notas_acta_ver_form_from_payload(array $payload): array
{
    $examinadoresRaw = $payload['examinadores'] ?? [];
    $examinadores = [];
    if (is_array($examinadoresRaw)) {
        foreach ($examinadoresRaw as $item) {
            $examinadores[] = tessera_imprimir_string($item);
        }
    }
    $aActasRaw = $payload['a_actas'] ?? [];
    $aActas = [];
    if (is_array($aActasRaw)) {
        foreach ($aActasRaw as $item) {
            $aActas[] = tessera_imprimir_string($item);
        }
    }

    return [
        'notas' => tessera_imprimir_string($payload['notas'] ?? ''),
        'permiso' => tessera_imprimir_int($payload['permiso'] ?? 0),
        'mod' => tessera_imprimir_string($payload['mod'] ?? ''),
        'acta_actual' => tessera_imprimir_string($payload['acta_actual'] ?? ''),
        'acta_new' => tessera_imprimir_string($payload['acta_new'] ?? ''),
        'ult_acta' => tessera_imprimir_string($payload['ult_acta'] ?? ''),
        'f_acta' => tessera_imprimir_string($payload['f_acta'] ?? ''),
        'libro' => tessera_imprimir_string($payload['libro'] ?? ''),
        'ult_lib' => tessera_imprimir_string($payload['ult_lib'] ?? ''),
        'pagina' => tessera_imprimir_string($payload['pagina'] ?? ''),
        'ult_pag' => tessera_imprimir_string($payload['ult_pag'] ?? ''),
        'linea' => tessera_imprimir_string($payload['linea'] ?? ''),
        'ult_lin' => tessera_imprimir_string($payload['ult_lin'] ?? ''),
        'lugar' => tessera_imprimir_string($payload['lugar'] ?? ''),
        'observ' => tessera_imprimir_string($payload['observ'] ?? ''),
        'id_activ' => tessera_imprimir_int($payload['id_activ'] ?? 0),
        'id_asignatura_actual' => tessera_imprimir_string($payload['id_asignatura_actual'] ?? ''),
        'nombre_asignatura' => tessera_imprimir_string($payload['nombre_asignatura'] ?? ''),
        'examinadores' => $examinadores,
        'a_actas' => $aActas,
        'has_pdf' => !empty($payload['has_pdf']),
        'warn_no_id_activ' => !empty($payload['warn_no_id_activ']),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     titulo: string,
 *     a_asignaturas: array<int, string>,
 *     actas: list<array{acta: string, f_acta: string, id_asignatura: int, has_pdf: bool}>,
 * }
 */
function notas_acta_select_from_payload(array $payload): array
{
    $aAsigRaw = $payload['a_asignaturas'] ?? [];
    $aAsignaturas = [];
    if (is_array($aAsigRaw)) {
        foreach ($aAsigRaw as $key => $value) {
            if (is_int($key)) {
                $aAsignaturas[$key] = tessera_imprimir_string($value);
            } elseif (is_numeric($key)) {
                $aAsignaturas[(int) $key] = tessera_imprimir_string($value);
            }
        }
    }
    $actasRaw = $payload['actas'] ?? [];
    $actas = [];
    if (is_array($actasRaw)) {
        foreach ($actasRaw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $actas[] = [
                'acta' => tessera_imprimir_string($row['acta'] ?? ''),
                'f_acta' => tessera_imprimir_string($row['f_acta'] ?? ''),
                'id_asignatura' => tessera_imprimir_int($row['id_asignatura'] ?? 0),
                'has_pdf' => !empty($row['has_pdf']),
            ];
        }
    }

    return [
        'titulo' => tessera_imprimir_string($payload['titulo'] ?? ''),
        'a_asignaturas' => $aAsignaturas,
        'actas' => $actas,
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     mod: string,
 *     id_asignatura_real: string,
 *     id_nivel: int|string,
 *     nombre_corto: string,
 *     id_situacion: int|string,
 *     nota_num: string,
 *     nota_max: string|int|float,
 *     acta: string,
 *     tipo_acta: int|string,
 *     f_acta: string,
 *     f_acta_iso: string,
 *     preceptor: bool|string,
 *     id_preceptor: int|string,
 *     detalle: string,
 *     epoca: int|string,
 *     id_activ: int|string,
 *     nom_activ: string,
 *     profesores: array<int|string, string>,
 *     asignaturas_faltan: array<int|string, string>,
 *     lista_situacion_no_acta: string,
 *     aOpcionesSituacion: array<int|string, string>,
 *     vo: array{
 *         NotaSituacion: array<string, int>,
 *         TipoActa: array<string, int>,
 *         NotaEpoca: array<string, int>,
 *     },
 *     helpers: array{condicion_js: string, op_genericas_json: string},
 * }
 */
function notas_persona_form_from_payload(array $payload): array
{
    $voRaw = $payload['vo'] ?? [];
    $vo = is_array($voRaw) ? $voRaw : [];
    $nsRaw = $vo['NotaSituacion'] ?? [];
    $taRaw = $vo['TipoActa'] ?? [];
    $neRaw = $vo['NotaEpoca'] ?? [];
    $notaSituacion = is_array($nsRaw) ? $nsRaw : [];
    $tipoActa = is_array($taRaw) ? $taRaw : [];
    $notaEpoca = is_array($neRaw) ? $neRaw : [];
    $helpersRaw = $payload['helpers'] ?? [];
    $helpers = is_array($helpersRaw) ? $helpersRaw : [];

    return [
        'mod' => tessera_imprimir_string($payload['mod'] ?? ''),
        'id_asignatura_real' => tessera_imprimir_string($payload['id_asignatura_real'] ?? ''),
        'id_nivel' => notas_form_scalar($payload['id_nivel'] ?? ''),
        'nombre_corto' => tessera_imprimir_string($payload['nombre_corto'] ?? ''),
        'id_situacion' => notas_form_scalar($payload['id_situacion'] ?? ''),
        'nota_num' => tessera_imprimir_string($payload['nota_num'] ?? ''),
        'nota_max' => notas_form_scalar($payload['nota_max'] ?? ''),
        'acta' => tessera_imprimir_string($payload['acta'] ?? ''),
        'tipo_acta' => notas_form_scalar($payload['tipo_acta'] ?? ''),
        'f_acta' => tessera_imprimir_string($payload['f_acta'] ?? ''),
        'f_acta_iso' => tessera_imprimir_string($payload['f_acta_iso'] ?? ''),
        'preceptor' => notas_form_bool_or_string($payload['preceptor'] ?? ''),
        'id_preceptor' => notas_form_scalar($payload['id_preceptor'] ?? ''),
        'detalle' => tessera_imprimir_string($payload['detalle'] ?? ''),
        'epoca' => notas_form_scalar($payload['epoca'] ?? ''),
        'id_activ' => notas_form_scalar($payload['id_activ'] ?? ''),
        'nom_activ' => tessera_imprimir_string($payload['nom_activ'] ?? ''),
        'profesores' => notas_desplegable_opciones($payload['profesores'] ?? []),
        'asignaturas_faltan' => notas_desplegable_opciones($payload['asignaturas_faltan'] ?? []),
        'lista_situacion_no_acta' => tessera_imprimir_string($payload['lista_situacion_no_acta'] ?? '"11"'),
        'aOpcionesSituacion' => notas_desplegable_opciones($payload['aOpcionesSituacion'] ?? []),
        'vo' => [
            'NotaSituacion' => array_map(
                static fn (mixed $v): int => tessera_imprimir_int($v),
                $notaSituacion
            ),
            'TipoActa' => array_map(
                static fn (mixed $v): int => tessera_imprimir_int($v),
                $tipoActa
            ),
            'NotaEpoca' => array_map(
                static fn (mixed $v): int => tessera_imprimir_int($v),
                $notaEpoca
            ),
        ],
        'helpers' => [
            'condicion_js' => tessera_imprimir_string($helpers['condicion_js'] ?? ''),
            'op_genericas_json' => tessera_imprimir_string($helpers['op_genericas_json'] ?? ''),
        ],
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{delegaciones: array<int|string, string>, actividades: array<int|string, string>, dl_org_sel: string, id_activ_sel: string}
 */
function notas_actividades_buscar_from_payload(array $payload): array
{
    return [
        'delegaciones' => notas_desplegable_opciones($payload['delegaciones'] ?? []),
        'actividades' => notas_desplegable_opciones($payload['actividades'] ?? []),
        'dl_org_sel' => tessera_imprimir_string($payload['dl_org_sel'] ?? ''),
        'id_activ_sel' => tessera_imprimir_string($payload['id_activ_sel'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{cabeceras: list<string>, filas: array<int, array<int, mixed>>, delegaciones: array<int|string, string>, ambito_rstgr: bool}
 */
function notas_asignaturas_pendientes_from_payload(array $payload): array
{
    $cabecerasRaw = $payload['cabeceras'] ?? [];
    $cabeceras = [];
    if (is_array($cabecerasRaw)) {
        foreach ($cabecerasRaw as $item) {
            $cabeceras[] = tessera_imprimir_string($item);
        }
    }
    $filasRaw = $payload['filas'] ?? [];
    $filas = [];
    if (is_array($filasRaw)) {
        foreach ($filasRaw as $key => $row) {
            if (is_int($key) && is_array($row)) {
                $filas[$key] = $row;
            }
        }
    }

    return [
        'cabeceras' => $cabeceras,
        'filas' => $filas,
        'delegaciones' => notas_desplegable_opciones($payload['delegaciones'] ?? []),
        'ambito_rstgr' => !empty($payload['ambito_rstgr']),
    ];
}
