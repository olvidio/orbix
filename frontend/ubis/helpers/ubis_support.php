<?php

/**
 * Helpers compartidos del módulo frontend/ubis.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';
require_once __DIR__ . '/../../dossiers/controller/lista_dossiers.php';

use src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaDlRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaExRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroDlRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroExRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroRepository;

/**
 * @return array<string, mixed>
 */
function ubis_post_data(mixed $data): array
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

function ubis_id_from_sel_item(mixed $sel0): int
{
    if (!is_string($sel0) || $sel0 === '') {
        return 0;
    }
    $part = strtok($sel0, '#');

    return is_numeric($part) ? (int) $part : 0;
}

/**
 * @return array<string, mixed>
 */
function ubis_hash_campos_hidden(mixed $raw): array
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
 * @return array<string, int>
 */
function ubis_perm_bit_map(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_string($key)) {
            $out[$key] = tessera_imprimir_int($value);
        }
    }

    return $out;
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     cabeceras: list<array<string, mixed>|string>,
 *     botones: list<array<string, mixed>>,
 *     valores: array<int|string, mixed>,
 * }
 */
function ubis_lista_from_payload(array $payload): array
{
    return [
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'botones' => actividades_lista_botones($payload['a_botones'] ?? []),
        'valores' => actividades_lista_datos($payload['a_valores'] ?? []),
    ];
}

/**
 * @return list<string>
 */
function ubis_lista_cabecera_strings(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        $out[] = tessera_imprimir_string($item);
    }

    return $out;
}

/**
 * @return list<array<int|string, mixed>>
 */
function ubis_lista_filas(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        if (is_array($row)) {
            $out[] = $row;
        }
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $valores
 * @return array<int|string, mixed>
 */
function ubis_sign_lista_valores(array $valores): array
{
    $rows = [];
    foreach ($valores as $item) {
        if (is_array($item)) {
            $rows[] = $item;
        }
    }
    $signed = actividades_sign_nested_link_specs($rows);
    $out = $valores;
    $i = 0;
    foreach ($out as $idx => $fila) {
        if (!is_array($fila)) {
            continue;
        }
        if (isset($signed[$i])) {
            $out[$idx] = $signed[$i];
        }
        $i++;
    }

    return $out;
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     cabeceras: list<array<string, mixed>|string>,
 *     botones: list<array<string, mixed>>,
 *     valores: array<int|string, mixed>,
 *     titulo: string,
 *     nueva_ficha: bool|string,
 *     hash_hidden: array<string, mixed>,
 *     pagina_link_spec: array<string, mixed>|null,
 *     go_back: array<string, mixed>,
 * }
 */
function ubis_tabla_from_payload(array $payload): array
{
    $specRaw = $payload['pagina_link_spec'] ?? null;
    $paginaLinkSpec = null;
    if (is_array($specRaw)) {
        $path = $specRaw['path'] ?? null;
        if (is_string($path) && $path !== '') {
            $paginaLinkSpec = ['path' => $path];
            $query = $specRaw['query'] ?? null;
            if (is_array($query)) {
                $q = [];
                foreach ($query as $k => $v) {
                    $q[(string) $k] = $v;
                }
                if ($q !== []) {
                    $paginaLinkSpec['query'] = $q;
                }
            }
        }
    }

    return [
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'botones' => actividades_lista_botones($payload['a_botones'] ?? []),
        'valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'titulo' => tessera_imprimir_string($payload['titulo'] ?? ''),
        'nueva_ficha' => notas_form_bool_or_string($payload['nueva_ficha'] ?? ''),
        'hash_hidden' => ubis_hash_campos_hidden($payload['hash_hidden'] ?? []),
        'pagina_link_spec' => $paginaLinkSpec,
        'go_back' => ubis_post_data($payload['go_back'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     nombre_ubi: string,
 *     dl: string,
 *     region: string,
 *     direccion: string,
 *     poblacion: string,
 *     c_p: string,
 *     id_direccion: string,
 *     id_pau: int,
 *     pau: string,
 *     obj_pau: string,
 *     obj_dir: string,
 *     ubi: string,
 *     telfs: string,
 *     fax: string,
 *     mails: string,
 * }
 */
function ubis_home_from_payload(array $payload): array
{
    return [
        'nombre_ubi' => tessera_imprimir_string($payload['nombre_ubi'] ?? ''),
        'dl' => tessera_imprimir_string($payload['dl'] ?? ''),
        'region' => tessera_imprimir_string($payload['region'] ?? ''),
        'direccion' => tessera_imprimir_string($payload['direccion'] ?? ''),
        'poblacion' => tessera_imprimir_string($payload['poblacion'] ?? ''),
        'c_p' => tessera_imprimir_string($payload['c_p'] ?? ''),
        'id_direccion' => tessera_imprimir_string($payload['id_direccion'] ?? ''),
        'id_pau' => tessera_imprimir_int($payload['id_pau'] ?? 0),
        'pau' => tessera_imprimir_string($payload['pau'] ?? ''),
        'obj_pau' => tessera_imprimir_string($payload['obj_pau'] ?? ''),
        'obj_dir' => tessera_imprimir_string($payload['obj_dir'] ?? ''),
        'ubi' => tessera_imprimir_string($payload['ubi'] ?? ''),
        'telfs' => tessera_imprimir_string($payload['telfs'] ?? ''),
        'fax' => tessera_imprimir_string($payload['fax'] ?? ''),
        'mails' => tessera_imprimir_string($payload['mails'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $load
 * @return array{
 *     tipo_ubi: string,
 *     obj_pau: string,
 *     id_ubi: int,
 *     id_direccion: string,
 *     dl: string,
 *     botones: int|float|string,
 *     region: string,
 *     nombre_ubi: string,
 *     tipo_labor_bit_map: array<string, int>,
 *     tipo_labor: int,
 *     chk: string,
 *     chk_cdc: string,
 *     tipo_labor_val: int|float|string,
 *     id_ctr_padre: int|float|string,
 *     tipo_ctr: int|float|string,
 *     num_pi: int|float|string,
 *     num_cartas: int|float|string,
 *     num_cartas_mensuales: int|float|string,
 *     num_habit_indiv: int|float|string,
 *     plazas: int|float|string,
 *     n_buzon: int|float|string,
 *     observ: string,
 *     tipo_casa: int|float|string,
 *     plazas_min: int|float|string,
 *     num_sacd: int|float|string,
 *     sv_chk: string,
 *     sf_chk: string,
 * }
 */
function ubis_editar_load_from_payload(array $load): array
{
    return [
        'tipo_ubi' => tessera_imprimir_string($load['tipo_ubi'] ?? ''),
        'obj_pau' => tessera_imprimir_string($load['obj_pau'] ?? ''),
        'id_ubi' => tessera_imprimir_int($load['id_ubi'] ?? 0),
        'id_direccion' => tessera_imprimir_string($load['id_direccion'] ?? ''),
        'dl' => tessera_imprimir_string($load['dl'] ?? ''),
        'botones' => notas_form_scalar($load['botones'] ?? 0),
        'region' => tessera_imprimir_string($load['region'] ?? ''),
        'nombre_ubi' => tessera_imprimir_string($load['nombre_ubi'] ?? ''),
        'tipo_labor_bit_map' => ubis_perm_bit_map($load['tipo_labor_bit_map'] ?? []),
        'tipo_labor' => tessera_imprimir_int($load['tipo_labor'] ?? 0),
        'chk' => tessera_imprimir_string($load['chk'] ?? ''),
        'chk_cdc' => tessera_imprimir_string($load['chk_cdc'] ?? ''),
        'tipo_labor_val' => notas_form_scalar($load['tipo_labor'] ?? ''),
        'id_ctr_padre' => notas_form_scalar($load['id_ctr_padre'] ?? ''),
        'tipo_ctr' => notas_form_scalar($load['tipo_ctr'] ?? ''),
        'num_pi' => notas_form_scalar($load['num_pi'] ?? ''),
        'num_cartas' => notas_form_scalar($load['num_cartas'] ?? ''),
        'num_cartas_mensuales' => notas_form_scalar($load['num_cartas_mensuales'] ?? ''),
        'num_habit_indiv' => notas_form_scalar($load['num_habit_indiv'] ?? ''),
        'plazas' => notas_form_scalar($load['plazas'] ?? ''),
        'n_buzon' => notas_form_scalar($load['n_buzon'] ?? ''),
        'observ' => tessera_imprimir_string($load['observ'] ?? ''),
        'tipo_casa' => notas_form_scalar($load['tipo_casa'] ?? ''),
        'plazas_min' => notas_form_scalar($load['plazas_min'] ?? ''),
        'num_sacd' => notas_form_scalar($load['num_sacd'] ?? ''),
        'sv_chk' => tessera_imprimir_string($load['sv_chk'] ?? ''),
        'sf_chk' => tessera_imprimir_string($load['sf_chk'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     opciones_dl: array<int|string, string>,
 *     opciones_region: array<int|string, string>,
 *     opciones_tipo_ctr: array<int|string, string>,
 *     opciones_id_ctr_padre: array<int|string, string>,
 *     opciones_tipo_casa: array<int|string, string>,
 * }
 */
function ubis_editar_opciones_from_payload(array $payload): array
{
    return [
        'opciones_dl' => notas_desplegable_opciones($payload['opciones_dl'] ?? []),
        'opciones_region' => notas_desplegable_opciones($payload['opciones_region'] ?? []),
        'opciones_tipo_ctr' => notas_desplegable_opciones($payload['opciones_tipo_ctr'] ?? []),
        'opciones_id_ctr_padre' => notas_desplegable_opciones($payload['opciones_id_ctr_padre'] ?? []),
        'opciones_tipo_casa' => notas_desplegable_opciones($payload['opciones_tipo_casa'] ?? []),
    ];
}

/**
 * @return list<array{
 *     id_item: int,
 *     id_ubi: int,
 *     f_ini: string,
 *     f_fin: string,
 *     sfsv: int,
 *     sel_sv: string,
 *     sel_sf: string,
 *     sel_res: string,
 * }>
 */
function ubis_calendario_periodo_rows(mixed $rows): array
{
    if (!is_array($rows)) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $sfsv = tessera_imprimir_int($row['sfsv'] ?? 0);
        $out[] = [
            'id_item' => tessera_imprimir_int($row['id_item'] ?? 0),
            'id_ubi' => tessera_imprimir_int($row['id_ubi'] ?? 0),
            'f_ini' => tessera_imprimir_string($row['f_ini'] ?? ''),
            'f_fin' => tessera_imprimir_string($row['f_fin'] ?? ''),
            'sfsv' => $sfsv,
            'sel_sv' => $sfsv === 1 ? 'selected' : '',
            'sel_sf' => $sfsv === 2 ? 'selected' : '',
            'sel_res' => $sfsv === 3 ? 'selected' : '',
        ];
    }

    return $out;
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     id_ubi: int,
 *     nombre_ubi: string,
 *     tipo_ctr: string,
 *     tipo_labor: int,
 * }
 */
function ubis_centro_labor_row(array $payload): array
{
    return [
        'id_ubi' => tessera_imprimir_int($payload['id_ubi'] ?? 0),
        'nombre_ubi' => tessera_imprimir_string($payload['nombre_ubi'] ?? ''),
        'tipo_ctr' => tessera_imprimir_string($payload['tipo_ctr'] ?? ''),
        'tipo_labor' => tessera_imprimir_int($payload['tipo_labor'] ?? 0),
    ];
}

/**
 * @return list<array{id_ubi: int, nombre_ubi: string, tipo_ctr: string, tipo_labor: int}>
 */
function ubis_centros_labor_rows(mixed $rows, mixed $bitMap): array
{
    if (!is_array($rows)) {
        return [];
    }
    $map = ubis_perm_bit_map($bitMap);
    $out = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $parsed = ubis_centro_labor_row(ubis_post_data($row));
        $out[] = $parsed;
    }

    return $out;
}

/**
 * @return array{plano_nom: string, plano_extension: string, plano_doc: mixed}
 */
function ubis_plano_download(string $obj_dir, int $id_direccion): array
{
    $raw = match ($obj_dir) {
        'DireccionCentro' => (new PgDireccionCentroRepository())->planoDownload($id_direccion),
        'DireccionCentroDl' => (new PgDireccionCentroDlRepository())->planoDownload($id_direccion),
        'DireccionCentroEx' => (new PgDireccionCentroExRepository())->planoDownload($id_direccion),
        'DireccionCdc' => (new PgDireccionCasaRepository())->planoDownload($id_direccion),
        'DireccionCdcDl' => (new PgDireccionCasaDlRepository())->planoDownload($id_direccion),
        'DireccionCdcEx' => (new PgDireccionCasaExRepository())->planoDownload($id_direccion),
        default => throw new InvalidArgumentException("obj_dir desconocido: $obj_dir"),
    };

    return [
        'plano_nom' => tessera_imprimir_string($raw['plano_nom'] ?? ''),
        'plano_extension' => tessera_imprimir_string($raw['plano_extension'] ?? ''),
        'plano_doc' => $raw['plano_doc'] ?? null,
    ];
}

function ubis_plano_upload(string $obj_dir, int $id_direccion, string $nom, string $extension, mixed $fichero): void
{
    $payload = is_string($fichero) || is_resource($fichero) ? $fichero : null;
    match ($obj_dir) {
        'DireccionCentro' => (new PgDireccionCentroRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
        'DireccionCentroDl' => (new PgDireccionCentroDlRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
        'DireccionCentroEx' => (new PgDireccionCentroExRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
        'DireccionCdc' => (new PgDireccionCasaRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
        'DireccionCdcDl' => (new PgDireccionCasaDlRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
        'DireccionCdcEx' => (new PgDireccionCasaExRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
        default => throw new InvalidArgumentException("obj_dir desconocido: $obj_dir"),
    };
}

function ubis_plano_borrar(string $obj_dir, int $id_direccion): void
{
    match ($obj_dir) {
        'DireccionCentro' => (new PgDireccionCentroRepository())->planoBorrar($id_direccion),
        'DireccionCentroDl' => (new PgDireccionCentroDlRepository())->planoBorrar($id_direccion),
        'DireccionCentroEx' => (new PgDireccionCentroExRepository())->planoBorrar($id_direccion),
        'DireccionCdc' => (new PgDireccionCasaRepository())->planoBorrar($id_direccion),
        'DireccionCdcDl' => (new PgDireccionCasaDlRepository())->planoBorrar($id_direccion),
        'DireccionCdcEx' => (new PgDireccionCasaExRepository())->planoBorrar($id_direccion),
        default => throw new InvalidArgumentException("obj_dir desconocido: $obj_dir"),
    };
}

/**
 * @return array{error: int, name: string, tmp_name: string, filename: string, extension: string}
 */
function ubis_upload_file_from_post(mixed $files): array
{
    if (!is_array($files)) {
        return ['error' => UPLOAD_ERR_NO_FILE, 'name' => '', 'tmp_name' => '', 'filename' => '', 'extension' => ''];
    }
    $error = tessera_imprimir_int($files['error'] ?? UPLOAD_ERR_NO_FILE);
    $name = tessera_imprimir_string($files['name'] ?? '');
    $tmpName = tessera_imprimir_string($files['tmp_name'] ?? '');
    $pathParts = pathinfo($name);

    return [
        'error' => $error,
        'name' => $name,
        'tmp_name' => $tmpName,
        'filename' => tessera_imprimir_string($pathParts['filename']),
        'extension' => tessera_imprimir_string($pathParts['extension'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{f_ini: string, f_fin: string, sel_sv: string, sel_sf: string, sel_res: string, f_next: string, sv_chk: string, sf_chk: string, overlap_error: string, show_nuevo: bool}
 */
function ubis_calendario_periodo_fields(array $payload): array
{
    return [
        'f_ini' => tessera_imprimir_string($payload['f_ini'] ?? ''),
        'f_fin' => tessera_imprimir_string($payload['f_fin'] ?? ''),
        'sel_sv' => tessera_imprimir_string($payload['sel_sv'] ?? ''),
        'sel_sf' => tessera_imprimir_string($payload['sel_sf'] ?? ''),
        'sel_res' => tessera_imprimir_string($payload['sel_res'] ?? ''),
        'f_next' => tessera_imprimir_string($payload['f_next'] ?? ''),
        'sv_chk' => tessera_imprimir_string($payload['sv_chk'] ?? ''),
        'sf_chk' => tessera_imprimir_string($payload['sf_chk'] ?? ''),
        'overlap_error' => tessera_imprimir_string($payload['overlap_error'] ?? ''),
        'show_nuevo' => !empty($payload['show_nuevo']),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{nombre_ubi: string, tipo_ctr: string, tipo_labor: int, tipo_labor_bit_map: array<string, int>}
 */
function ubis_centro_labor_form_from_payload(array $payload): array
{
    return [
        'nombre_ubi' => tessera_imprimir_string($payload['nombre_ubi'] ?? ''),
        'tipo_ctr' => tessera_imprimir_string($payload['tipo_ctr'] ?? ''),
        'tipo_labor' => tessera_imprimir_int($payload['tipo_labor'] ?? 0),
        'tipo_labor_bit_map' => ubis_perm_bit_map($payload['tipo_labor_bit_map'] ?? []),
    ];
}

function ubis_json_echo(mixed $payload): void
{
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo is_string($json) ? $json : '';
}

function ubis_api_error(mixed $data): string
{
    if (!is_array($data)) {
        return '';
    }

    return tessera_imprimir_string($data['error'] ?? '');
}

/**
 * @param array<string, mixed> $base
 * @param array<string, mixed> $extra
 * @return array<string, mixed>
 */
function ubis_view_vars(array $base, array $extra): array
{
    return array_merge($base, $extra);
}

/**
 * @param array<string, mixed> $payload
 * @return array{nombre_ubi: string, n_buzon: int|float|string, num_pi: int|float|string, num_cartas: int|float|string}
 */
function ubis_centro_num_form_from_payload(array $payload): array
{
    return [
        'nombre_ubi' => tessera_imprimir_string($payload['nombre_ubi'] ?? ''),
        'n_buzon' => notas_form_scalar($payload['n_buzon'] ?? ''),
        'num_pi' => notas_form_scalar($payload['num_pi'] ?? ''),
        'num_cartas' => notas_form_scalar($payload['num_cartas'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{nombre_ubi: string, num_habit_indiv: int|float|string, plazas: int|float|string, sede: bool|string}
 */
function ubis_centro_plazas_form_from_payload(array $payload): array
{
    return [
        'nombre_ubi' => tessera_imprimir_string($payload['nombre_ubi'] ?? ''),
        'num_habit_indiv' => notas_form_scalar($payload['num_habit_indiv'] ?? ''),
        'plazas' => notas_form_scalar($payload['plazas'] ?? ''),
        'sede' => notas_form_bool_or_string($payload['sede'] ?? false),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     cabeceras: list<array<string, mixed>|string>,
 *     botones: list<array<string, mixed>>,
 *     valores: array<int|string, mixed>,
 *     opciones_loc: array<int|string, string>,
 *     opciones_que_lista: array<int|string, string>,
 * }
 */
function ubis_list_ctr_from_payload(array $payload): array
{
    $lista = ubis_lista_from_payload($payload);

    return [
        'cabeceras' => $lista['cabeceras'],
        'botones' => $lista['botones'],
        'valores' => ubis_sign_lista_valores($lista['valores']),
        'opciones_loc' => notas_desplegable_opciones($payload['opciones_loc'] ?? []),
        'opciones_que_lista' => notas_desplegable_opciones($payload['opciones_que_lista'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $tabla
 */
function ubis_pagina_link_from_tabla(array $tabla): string
{
    $spec = $tabla['pagina_link_spec'] ?? null;
    if (!is_array($spec)) {
        return '';
    }
    $path = tessera_imprimir_string($spec['path'] ?? '');
    if ($path === '') {
        return '';
    }
    $baseUrl = \frontend\shared\config\AppUrlConfig::getPublicAppBaseUrl();
    $queryRaw = $spec['query'] ?? null;
    $query = is_array($queryRaw) ? $queryRaw : [];
    $url = $baseUrl . '/' . ltrim($path, '/') . '?' . http_build_query($query);

    return \frontend\shared\security\HashFront::link($url);
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     obj: string,
 *     a_tipos: array<int|string, string>,
 *     a_desc: array<int|string, string>,
 *     id_tipo_teleco: string,
 *     id_desc_teleco: string,
 *     num_teleco: string,
 *     observ: string,
 *     botones: int|float|string,
 *     ficha: string,
 *     tit_txt: string,
 * }
 */
function ubis_teleco_from_payload(array $payload): array
{
    return [
        'obj' => tessera_imprimir_string($payload['obj'] ?? ''),
        'a_tipos' => notas_desplegable_opciones($payload['a_tipos'] ?? []),
        'a_desc' => notas_desplegable_opciones($payload['a_desc'] ?? []),
        'id_tipo_teleco' => tessera_imprimir_string($payload['id_tipo_teleco'] ?? ''),
        'id_desc_teleco' => tessera_imprimir_string($payload['id_desc_teleco'] ?? ''),
        'num_teleco' => tessera_imprimir_string($payload['num_teleco'] ?? ''),
        'observ' => tessera_imprimir_string($payload['observ'] ?? ''),
        'botones' => notas_form_scalar($payload['botones'] ?? 0),
        'ficha' => tessera_imprimir_string($payload['ficha'] ?? ''),
        'tit_txt' => tessera_imprimir_string($payload['tit_txt'] ?? ''),
    ];
}

function ubis_buscar_nom_ubi(string $tipo): string
{
    return match ($tipo) {
        'ctrdl' => ucfirst(_('nombre del centro')),
        'vu_ex' => ucfirst(_('nombre del centro o casa')),
        'ctrex' => ucfirst(_('nombre del centro')),
        'cdcdl' => ucfirst(_('nombre de la casa')),
        'cdcex' => ucfirst(_('nombre de la casa')),
        'mail' => ucfirst(_('nombre del centro')),
        'ctrsf' => ucfirst(_('nombre del centro')),
        'ctr' => ucfirst(_('nombre del centro')),
        'cdc' => ucfirst(_('nombre de la casa')),
        default => ucfirst(_('nombre de la casa o centro')),
    };
}
