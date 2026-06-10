<?php

/**
 * Helpers compartidos del módulo frontend/inventario.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

/**
 * @return array<int|string, mixed>
 */
function inventario_post_payload(mixed $data): array
{
    return is_array($data) ? $data : [];
}

function inventario_periodo_sel_string(int|string $value): string
{
    return tessera_imprimir_string($value);
}

function inventario_desplegable_opcion_sel(int|string $value): string
{
    return tessera_imprimir_string($value);
}

/**
 * @return array<int|string, string>
 */
function inventario_desplegable_opciones(mixed $raw): array
{
    return notas_desplegable_opciones($raw);
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     a_cabeceras: list<array<string, mixed>|string>,
 *     a_botones: list<array<string, mixed>>,
 *     a_valores: array<int|string, mixed>,
 *     nombreDoc: string,
 * }
 */
function inventario_lista_docs_from_payload(array $payload): array
{
    return [
        'a_cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'a_botones' => actividades_lista_botones($payload['a_botones'] ?? []),
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'nombreDoc' => tessera_imprimir_string($payload['nombreDoc'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     a_valores: array<int|string, mixed>,
 *     nombreDoc: string,
 *     isNumerado: bool,
 *     sCamposForm: string,
 * }
 */
function inventario_doc_asignar_from_payload(array $payload): array
{
    return [
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'nombreDoc' => tessera_imprimir_string($payload['nombreDoc'] ?? ''),
        'isNumerado' => !empty($payload['isNumerado']),
        'sCamposForm' => tessera_imprimir_string($payload['sCamposForm'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{a_valores: array<int|string, mixed>, a_grupos: array<int|string, string>}
 */
function inventario_doc_de_dlb_from_payload(array $payload): array
{
    $gruposRaw = $payload['a_grupos'] ?? $payload['aGrupos'] ?? [];
    $grupos = [];
    if (is_array($gruposRaw)) {
        foreach ($gruposRaw as $key => $value) {
            $grupos[$key] = tessera_imprimir_string($value);
        }
    }

    return [
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'a_grupos' => $grupos,
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     a_valores: array<int|string, mixed>,
 *     nombre_valija: string,
 * }
 */
function inventario_lista_docs_grupo_from_payload(array $payload): array
{
    return [
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'nombre_valija' => tessera_imprimir_string($payload['nombre_valija'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     cabecera: string,
 *     cabeceraB: string,
 *     firma: string,
 *     pie: string,
 * }
 */
function inventario_cabecera_pie_from_payload(array $payload): array
{
    return [
        'cabecera' => tessera_imprimir_string($payload['cabecera'] ?? ''),
        'cabeceraB' => tessera_imprimir_string($payload['cabeceraB'] ?? ''),
        'firma' => tessera_imprimir_string($payload['firma'] ?? ''),
        'pie' => tessera_imprimir_string($payload['pie'] ?? ''),
    ];
}

/**
 * @return list<string>
 */
function inventario_actividades_nombres(mixed $raw): array
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
 * @param array<int|string, mixed> $payload
 * @return array{a_valores: array<int|string, mixed>, nombre_ubi: string, id_ubi: int}
 */
function inventario_equipajes_doc_casa_from_payload(array $payload): array
{
    return [
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'nombre_ubi' => tessera_imprimir_string($payload['nombre_ubi'] ?? ''),
        'id_ubi' => tessera_imprimir_int($payload['id_ubi'] ?? 0),
    ];
}

/**
 * @return array{
 *     id_grupo: int,
 *     id_lugar: int,
 *     nom_lugar: string,
 *     id_item_egm: int,
 *     texto: string,
 *     a_valores: array<int|string, mixed>,
 * }
 */
function inventario_egm_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_grupo' => 0,
            'id_lugar' => 0,
            'nom_lugar' => '',
            'id_item_egm' => 0,
            'texto' => '',
            'a_valores' => [],
        ];
    }

    return [
        'id_grupo' => tessera_imprimir_int($raw['id_grupo'] ?? 0),
        'id_lugar' => tessera_imprimir_int($raw['id_lugar'] ?? 0),
        'nom_lugar' => tessera_imprimir_string($raw['nom_lugar'] ?? ''),
        'id_item_egm' => tessera_imprimir_int($raw['id_item_egm'] ?? 0),
        'texto' => tessera_imprimir_string($raw['texto'] ?? ''),
        'a_valores' => actividades_lista_datos($raw['a_valores'] ?? []),
    ];
}

/**
 * @return list<array{
 *     id_grupo: int,
 *     id_lugar: int,
 *     nom_lugar: string,
 *     id_item_egm: int,
 *     texto: string,
 *     a_valores: array<int|string, mixed>,
 * }>
 */
function inventario_egm_rows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = inventario_egm_row($row);
    }

    return $out;
}

/**
 * @return array{sigla: string, identificador: string, etiqueta: string}
 */
function inventario_doc_libre_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return ['sigla' => '', 'identificador' => '', 'etiqueta' => ''];
    }

    return [
        'sigla' => tessera_imprimir_string($raw[0] ?? ''),
        'identificador' => tessera_imprimir_string($raw[1] ?? ''),
        'etiqueta' => tessera_imprimir_string($raw[2] ?? ''),
    ];
}

/**
 * @return list<array{sigla: string, identificador: string, etiqueta: string}>
 */
function inventario_docs_libres_rows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = inventario_doc_libre_row($row);
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{a_opciones: array<int|string, string>, new_id_grupo: int}
 */
function inventario_posibles_maletas_from_payload(array $payload): array
{
    return [
        'a_opciones' => inventario_desplegable_opciones($payload['a_opciones'] ?? []),
        'new_id_grupo' => tessera_imprimir_int($payload['new_id_grupo'] ?? 0),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{nombre_ubi: string, ini: string, fin: string, ids_activ: string}
 */
function inventario_equipajes_form_nuevo_from_payload(array $payload): array
{
    return [
        'nombre_ubi' => tessera_imprimir_string($payload['nombre_ubi'] ?? ''),
        'ini' => tessera_imprimir_string($payload['ini'] ?? ''),
        'fin' => tessera_imprimir_string($payload['fin'] ?? ''),
        'ids_activ' => tessera_imprimir_string($payload['ids_activ'] ?? ''),
    ];
}

/**
 * @return array<string, array<int|string, mixed>>
 */
function inventario_ubi_valores_map(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        $out[(string) $key] = actividades_lista_datos($value);
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     aCambios: array<int|string, array<int|string, array{in: list<int|string>, out: list<int|string>}>>,
 *     aLugaresPorEgm: array<int|string, string>,
 *     aNomEquipajes: array<int|string, string>,
 * }
 */
function inventario_movimientos_from_payload(array $payload): array
{
    $cambiosRaw = $payload['aCambios'] ?? [];
    $cambios = [];
    if (is_array($cambiosRaw)) {
        foreach ($cambiosRaw as $idEquipaje => $gruposRaw) {
            if (!is_array($gruposRaw)) {
                continue;
            }
            $grupos = [];
            foreach ($gruposRaw as $idItem => $inOutRaw) {
                if (!is_array($inOutRaw)) {
                    continue;
                }
                $inRaw = $inOutRaw['in'] ?? [];
                $outRaw = $inOutRaw['out'] ?? [];
                $in = [];
                if (is_array($inRaw)) {
                    foreach ($inRaw as $idDoc) {
                        if (is_int($idDoc) || is_string($idDoc)) {
                            $in[] = $idDoc;
                        }
                    }
                }
                $out = [];
                if (is_array($outRaw)) {
                    foreach ($outRaw as $idDoc) {
                        if (is_int($idDoc) || is_string($idDoc)) {
                            $out[] = $idDoc;
                        }
                    }
                }
                $grupos[$idItem] = ['in' => $in, 'out' => $out];
            }
            $cambios[$idEquipaje] = $grupos;
        }
    }

    $lugaresRaw = $payload['aLugaresPorEgm'] ?? [];
    $lugares = [];
    if (is_array($lugaresRaw)) {
        foreach ($lugaresRaw as $key => $value) {
            $lugares[$key] = tessera_imprimir_string($value);
        }
    }

    $nombresRaw = $payload['aNomEquipajes'] ?? [];
    $nombres = [];
    if (is_array($nombresRaw)) {
        foreach ($nombresRaw as $key => $value) {
            $nombres[$key] = tessera_imprimir_string($value);
        }
    }

    return [
        'aCambios' => $cambios,
        'aLugaresPorEgm' => $lugares,
        'aNomEquipajes' => $nombres,
    ];
}

/**
 * @return list<array<int|string, string>>
 */
function inventario_movimientos_que_rows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        if (!is_array($row)) {
            continue;
        }
        $parsed = [];
        foreach ($row as $key => $value) {
            $parsed[$key] = tessera_imprimir_string($value);
        }
        $out[] = $parsed;
    }

    return $out;
}

function inventario_grupo_id_from_loc(string $loc): string
{
    if (preg_match('/docs_grupo_(.*)/', $loc, $matches) === 1) {
        return $matches[1];
    }

    return '';
}

/**
 * @return array{
 *     nombre: string,
 *     identificador: string,
 *     carta: string,
 *     coleccion: string,
 *     ejemplares: string,
 *     lugar: string,
 * }
 */
function inventario_valor_agrupar_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'nombre' => '',
            'identificador' => '',
            'carta' => '',
            'coleccion' => '',
            'ejemplares' => '',
            'lugar' => '',
        ];
    }

    return [
        'nombre' => tessera_imprimir_string($raw['nombre'] ?? ''),
        'identificador' => tessera_imprimir_string($raw['identificador'] ?? ''),
        'carta' => tessera_imprimir_string($raw['carta'] ?? ''),
        'coleccion' => tessera_imprimir_string($raw['coleccion'] ?? ''),
        'ejemplares' => tessera_imprimir_string($raw['ejemplares'] ?? ''),
        'lugar' => tessera_imprimir_string($raw['lugar'] ?? ''),
    ];
}

/**
 * @return list<array{
 *     nombre: string,
 *     identificador: string,
 *     carta: string,
 *     coleccion: string,
 *     ejemplares: string,
 *     lugar: string,
 * }>
 */
function inventario_valor_agrupar_rows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = inventario_valor_agrupar_row($row);
    }

    return $out;
}

/**
 * @return array<int|string, string>
 */
function inventario_colecciones_opciones(mixed $raw): array
{
    return inventario_desplegable_opciones($raw);
}
