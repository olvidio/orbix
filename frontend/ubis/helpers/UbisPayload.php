<?php

declare(strict_types=1);

namespace frontend\ubis\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaDlRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaExRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroDlRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroExRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroRepository;

final class UbisPayload
{
public static function postData(mixed $data): array
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

public static function idFromSelItem(mixed $sel0): int
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
public static function hashCamposHidden(mixed $raw): array
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
public static function permBitMap(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_string($key)) {
            $out[$key] = \frontend\shared\helpers\PayloadCoercion::int($value);
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
public static function listaFromPayload(array $payload): array
{
    return [
        'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
        'botones' => ActividadesListaSupport::botones($payload['a_botones'] ?? []),
        'valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
    ];
}

/**
 * @return list<string>
 */
public static function listaCabeceraStrings(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        $out[] = \frontend\shared\helpers\PayloadCoercion::string($item);
    }

    return $out;
}

/**
 * @return list<array<int|string, mixed>>
 */
public static function listaFilas(mixed $raw): array
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
public static function signListaValores(array $valores): array
{
    $rows = [];
    foreach ($valores as $item) {
        if (is_array($item)) {
            $rows[] = $item;
        }
    }
    $signed = ActividadesListaSupport::signNestedLinkSpecs($rows);
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
public static function tablaFromPayload(array $payload): array
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
        'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
        'botones' => ActividadesListaSupport::botones($payload['a_botones'] ?? []),
        'valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        'titulo' => \frontend\shared\helpers\PayloadCoercion::string($payload['titulo'] ?? ''),
        'nueva_ficha' => NotasFormSupport::formBoolOrString($payload['nueva_ficha'] ?? ''),
        'hash_hidden' => self::hashCamposHidden($payload['hash_hidden'] ?? []),
        'pagina_link_spec' => $paginaLinkSpec,
        'go_back' => self::postData($payload['go_back'] ?? []),
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
public static function homeFromPayload(array $payload): array
{
    return [
        'nombre_ubi' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombre_ubi'] ?? ''),
        'dl' => \frontend\shared\helpers\PayloadCoercion::string($payload['dl'] ?? ''),
        'region' => \frontend\shared\helpers\PayloadCoercion::string($payload['region'] ?? ''),
        'direccion' => \frontend\shared\helpers\PayloadCoercion::string($payload['direccion'] ?? ''),
        'poblacion' => \frontend\shared\helpers\PayloadCoercion::string($payload['poblacion'] ?? ''),
        'c_p' => \frontend\shared\helpers\PayloadCoercion::string($payload['c_p'] ?? ''),
        'id_direccion' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_direccion'] ?? ''),
        'id_pau' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_pau'] ?? 0),
        'pau' => \frontend\shared\helpers\PayloadCoercion::string($payload['pau'] ?? ''),
        'obj_pau' => \frontend\shared\helpers\PayloadCoercion::string($payload['obj_pau'] ?? ''),
        'obj_dir' => \frontend\shared\helpers\PayloadCoercion::string($payload['obj_dir'] ?? ''),
        'ubi' => \frontend\shared\helpers\PayloadCoercion::string($payload['ubi'] ?? ''),
        'telfs' => \frontend\shared\helpers\PayloadCoercion::string($payload['telfs'] ?? ''),
        'fax' => \frontend\shared\helpers\PayloadCoercion::string($payload['fax'] ?? ''),
        'mails' => \frontend\shared\helpers\PayloadCoercion::string($payload['mails'] ?? ''),
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
public static function editarLoadFromPayload(array $load): array
{
    return [
        'tipo_ubi' => \frontend\shared\helpers\PayloadCoercion::string($load['tipo_ubi'] ?? ''),
        'obj_pau' => \frontend\shared\helpers\PayloadCoercion::string($load['obj_pau'] ?? ''),
        'id_ubi' => \frontend\shared\helpers\PayloadCoercion::int($load['id_ubi'] ?? 0),
        'id_direccion' => \frontend\shared\helpers\PayloadCoercion::string($load['id_direccion'] ?? ''),
        'dl' => \frontend\shared\helpers\PayloadCoercion::string($load['dl'] ?? ''),
        'botones' => NotasFormSupport::formScalar($load['botones'] ?? 0),
        'region' => \frontend\shared\helpers\PayloadCoercion::string($load['region'] ?? ''),
        'nombre_ubi' => \frontend\shared\helpers\PayloadCoercion::string($load['nombre_ubi'] ?? ''),
        'tipo_labor_bit_map' => self::permBitMap($load['tipo_labor_bit_map'] ?? []),
        'tipo_labor' => \frontend\shared\helpers\PayloadCoercion::int($load['tipo_labor'] ?? 0),
        'chk' => \frontend\shared\helpers\PayloadCoercion::string($load['chk'] ?? ''),
        'chk_cdc' => \frontend\shared\helpers\PayloadCoercion::string($load['chk_cdc'] ?? ''),
        'tipo_labor_val' => NotasFormSupport::formScalar($load['tipo_labor'] ?? ''),
        'id_ctr_padre' => NotasFormSupport::formScalar($load['id_ctr_padre'] ?? ''),
        'tipo_ctr' => NotasFormSupport::formScalar($load['tipo_ctr'] ?? ''),
        'num_pi' => NotasFormSupport::formScalar($load['num_pi'] ?? ''),
        'num_cartas' => NotasFormSupport::formScalar($load['num_cartas'] ?? ''),
        'num_cartas_mensuales' => NotasFormSupport::formScalar($load['num_cartas_mensuales'] ?? ''),
        'num_habit_indiv' => NotasFormSupport::formScalar($load['num_habit_indiv'] ?? ''),
        'plazas' => NotasFormSupport::formScalar($load['plazas'] ?? ''),
        'n_buzon' => NotasFormSupport::formScalar($load['n_buzon'] ?? ''),
        'observ' => \frontend\shared\helpers\PayloadCoercion::string($load['observ'] ?? ''),
        'tipo_casa' => NotasFormSupport::formScalar($load['tipo_casa'] ?? ''),
        'plazas_min' => NotasFormSupport::formScalar($load['plazas_min'] ?? ''),
        'num_sacd' => NotasFormSupport::formScalar($load['num_sacd'] ?? ''),
        'sv_chk' => \frontend\shared\helpers\PayloadCoercion::string($load['sv_chk'] ?? ''),
        'sf_chk' => \frontend\shared\helpers\PayloadCoercion::string($load['sf_chk'] ?? ''),
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
public static function editarOpcionesFromPayload(array $payload): array
{
    return [
        'opciones_dl' => NotasFormSupport::desplegableOpciones($payload['opciones_dl'] ?? []),
        'opciones_region' => NotasFormSupport::desplegableOpciones($payload['opciones_region'] ?? []),
        'opciones_tipo_ctr' => NotasFormSupport::desplegableOpciones($payload['opciones_tipo_ctr'] ?? []),
        'opciones_id_ctr_padre' => NotasFormSupport::desplegableOpciones($payload['opciones_id_ctr_padre'] ?? []),
        'opciones_tipo_casa' => NotasFormSupport::desplegableOpciones($payload['opciones_tipo_casa'] ?? []),
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
public static function calendarioPeriodoRows(mixed $rows): array
{
    if (!is_array($rows)) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $sfsv = \frontend\shared\helpers\PayloadCoercion::int($row['sfsv'] ?? 0);
        $out[] = [
            'id_item' => \frontend\shared\helpers\PayloadCoercion::int($row['id_item'] ?? 0),
            'id_ubi' => \frontend\shared\helpers\PayloadCoercion::int($row['id_ubi'] ?? 0),
            'f_ini' => \frontend\shared\helpers\PayloadCoercion::string($row['f_ini'] ?? ''),
            'f_fin' => \frontend\shared\helpers\PayloadCoercion::string($row['f_fin'] ?? ''),
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
public static function centroLaborRow(array $payload): array
{
    return [
        'id_ubi' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_ubi'] ?? 0),
        'nombre_ubi' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombre_ubi'] ?? ''),
        'tipo_ctr' => \frontend\shared\helpers\PayloadCoercion::string($payload['tipo_ctr'] ?? ''),
        'tipo_labor' => \frontend\shared\helpers\PayloadCoercion::int($payload['tipo_labor'] ?? 0),
    ];
}

/**
 * @return list<array{id_ubi: int, nombre_ubi: string, tipo_ctr: string, tipo_labor: int}>
 */
public static function centrosLaborRows(mixed $rows, mixed $bitMap): array
{
    if (!is_array($rows)) {
        return [];
    }
    $map = self::permBitMap($bitMap);
    $out = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $parsed = self::centroLaborRow(self::postData($row));
        $out[] = $parsed;
    }

    return $out;
}

/**
 * @return array{plano_nom: string, plano_extension: string, plano_doc: mixed}
 */
public static function planoDownload(string $obj_dir, int $id_direccion): array
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
        'plano_nom' => \frontend\shared\helpers\PayloadCoercion::string($raw['plano_nom'] ?? ''),
        'plano_extension' => \frontend\shared\helpers\PayloadCoercion::string($raw['plano_extension'] ?? ''),
        'plano_doc' => $raw['plano_doc'] ?? null,
    ];
}

public static function planoUpload(string $obj_dir, int $id_direccion, string $nom, string $extension, mixed $fichero): void
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

public static function planoBorrar(string $obj_dir, int $id_direccion): void
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
public static function uploadFileFromPost(mixed $files): array
{
    if (!is_array($files)) {
        return ['error' => UPLOAD_ERR_NO_FILE, 'name' => '', 'tmp_name' => '', 'filename' => '', 'extension' => ''];
    }
    $error = \frontend\shared\helpers\PayloadCoercion::int($files['error'] ?? UPLOAD_ERR_NO_FILE);
    $name = \frontend\shared\helpers\PayloadCoercion::string($files['name'] ?? '');
    $tmpName = \frontend\shared\helpers\PayloadCoercion::string($files['tmp_name'] ?? '');
    $pathParts = pathinfo($name);

    return [
        'error' => $error,
        'name' => $name,
        'tmp_name' => $tmpName,
        'filename' => \frontend\shared\helpers\PayloadCoercion::string($pathParts['filename']),
        'extension' => \frontend\shared\helpers\PayloadCoercion::string($pathParts['extension'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{f_ini: string, f_fin: string, sel_sv: string, sel_sf: string, sel_res: string, f_next: string, sv_chk: string, sf_chk: string, overlap_error: string, show_nuevo: bool}
 */
public static function calendarioPeriodoFields(array $payload): array
{
    return [
        'f_ini' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_ini'] ?? ''),
        'f_fin' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_fin'] ?? ''),
        'sel_sv' => \frontend\shared\helpers\PayloadCoercion::string($payload['sel_sv'] ?? ''),
        'sel_sf' => \frontend\shared\helpers\PayloadCoercion::string($payload['sel_sf'] ?? ''),
        'sel_res' => \frontend\shared\helpers\PayloadCoercion::string($payload['sel_res'] ?? ''),
        'f_next' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_next'] ?? ''),
        'sv_chk' => \frontend\shared\helpers\PayloadCoercion::string($payload['sv_chk'] ?? ''),
        'sf_chk' => \frontend\shared\helpers\PayloadCoercion::string($payload['sf_chk'] ?? ''),
        'overlap_error' => \frontend\shared\helpers\PayloadCoercion::string($payload['overlap_error'] ?? ''),
        'show_nuevo' => !empty($payload['show_nuevo']),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{nombre_ubi: string, tipo_ctr: string, tipo_labor: int, tipo_labor_bit_map: array<string, int>}
 */
public static function centroLaborFormFromPayload(array $payload): array
{
    return [
        'nombre_ubi' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombre_ubi'] ?? ''),
        'tipo_ctr' => \frontend\shared\helpers\PayloadCoercion::string($payload['tipo_ctr'] ?? ''),
        'tipo_labor' => \frontend\shared\helpers\PayloadCoercion::int($payload['tipo_labor'] ?? 0),
        'tipo_labor_bit_map' => self::permBitMap($payload['tipo_labor_bit_map'] ?? []),
    ];
}

public static function jsonEcho(mixed $payload): void
{
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo is_string($json) ? $json : '';
}

public static function apiError(mixed $data): string
{
    if (!is_array($data)) {
        return '';
    }

    return \frontend\shared\helpers\PayloadCoercion::string($data['error'] ?? '');
}

/**
 * @param array<string, mixed> $base
 * @param array<string, mixed> $extra
 * @return array<string, mixed>
 */
public static function viewVars(array $base, array $extra): array
{
    return array_merge($base, $extra);
}

/**
 * @param array<string, mixed> $payload
 * @return array{nombre_ubi: string, n_buzon: int|float|string, num_pi: int|float|string, num_cartas: int|float|string}
 */
public static function centroNumFormFromPayload(array $payload): array
{
    return [
        'nombre_ubi' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombre_ubi'] ?? ''),
        'n_buzon' => NotasFormSupport::formScalar($payload['n_buzon'] ?? ''),
        'num_pi' => NotasFormSupport::formScalar($payload['num_pi'] ?? ''),
        'num_cartas' => NotasFormSupport::formScalar($payload['num_cartas'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{nombre_ubi: string, num_habit_indiv: int|float|string, plazas: int|float|string, sede: bool|string}
 */
public static function centroPlazasFormFromPayload(array $payload): array
{
    return [
        'nombre_ubi' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombre_ubi'] ?? ''),
        'num_habit_indiv' => NotasFormSupport::formScalar($payload['num_habit_indiv'] ?? ''),
        'plazas' => NotasFormSupport::formScalar($payload['plazas'] ?? ''),
        'sede' => NotasFormSupport::formBoolOrString($payload['sede'] ?? false),
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
public static function listCtrFromPayload(array $payload): array
{
    $lista = self::listaFromPayload($payload);

    return [
        'cabeceras' => $lista['cabeceras'],
        'botones' => $lista['botones'],
        'valores' => self::signListaValores($lista['valores']),
        'opciones_loc' => NotasFormSupport::desplegableOpciones($payload['opciones_loc'] ?? []),
        'opciones_que_lista' => NotasFormSupport::desplegableOpciones($payload['opciones_que_lista'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $tabla
 */
public static function paginaLinkFromTabla(array $tabla): string
{
    $spec = $tabla['pagina_link_spec'] ?? null;
    if (!is_array($spec)) {
        return '';
    }
    $path = \frontend\shared\helpers\PayloadCoercion::string($spec['path'] ?? '');
    if ($path === '') {
        return '';
    }
    $baseUrl = AppUrlConfig::getPublicAppBaseUrl();
    $queryRaw = $spec['query'] ?? null;
    $query = is_array($queryRaw) ? $queryRaw : [];
    $url = $baseUrl . '/' . ltrim($path, '/') . '?' . http_build_query($query);

    return HashFront::link($url);
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
public static function telecoFromPayload(array $payload): array
{
    return [
        'obj' => \frontend\shared\helpers\PayloadCoercion::string($payload['obj'] ?? ''),
        'a_tipos' => NotasFormSupport::desplegableOpciones($payload['a_tipos'] ?? []),
        'a_desc' => NotasFormSupport::desplegableOpciones($payload['a_desc'] ?? []),
        'id_tipo_teleco' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_tipo_teleco'] ?? ''),
        'id_desc_teleco' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_desc_teleco'] ?? ''),
        'num_teleco' => \frontend\shared\helpers\PayloadCoercion::string($payload['num_teleco'] ?? ''),
        'observ' => \frontend\shared\helpers\PayloadCoercion::string($payload['observ'] ?? ''),
        'botones' => NotasFormSupport::formScalar($payload['botones'] ?? 0),
        'ficha' => \frontend\shared\helpers\PayloadCoercion::string($payload['ficha'] ?? ''),
        'tit_txt' => \frontend\shared\helpers\PayloadCoercion::string($payload['tit_txt'] ?? ''),
    ];
}

public static function buscarNomUbi(string $tipo): string
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
}
