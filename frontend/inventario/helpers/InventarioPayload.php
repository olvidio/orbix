<?php

declare(strict_types=1);

namespace frontend\inventario\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class InventarioPayload
{
/**
 * @return array<int|string, mixed>
 */
public static function postPayload(mixed $data): array
{
    return is_array($data) ? $data : [];
}

public static function periodoSelString(int|string $value): string
{
    return PayloadCoercion::string($value);
}

public static function desplegableOpcionSel(int|string $value): string
{
    return PayloadCoercion::string($value);
}

/**
 * @return array<int|string, string>
 */
public static function desplegableOpciones(mixed $raw): array
{
    return NotasFormSupport::desplegableOpciones($raw);
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
public static function listaDocsFromPayload(array $payload): array
{
    return [
        'a_cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
        'a_botones' => ActividadesListaSupport::botones($payload['a_botones'] ?? []),
        'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        'nombreDoc' => PayloadCoercion::string($payload['nombreDoc'] ?? ''),
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
public static function docAsignarFromPayload(array $payload): array
{
    return [
        'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        'nombreDoc' => PayloadCoercion::string($payload['nombreDoc'] ?? ''),
        'isNumerado' => !empty($payload['isNumerado']),
        'sCamposForm' => PayloadCoercion::string($payload['sCamposForm'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{a_valores: array<int|string, mixed>, a_grupos: array<int|string, string>}
 */
public static function docDeDlbFromPayload(array $payload): array
{
    $gruposRaw = $payload['a_grupos'] ?? $payload['aGrupos'] ?? [];
    $grupos = [];
    if (is_array($gruposRaw)) {
        foreach ($gruposRaw as $key => $value) {
            $grupos[$key] = PayloadCoercion::string($value);
        }
    }

    return [
        'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
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
public static function listaDocsGrupoFromPayload(array $payload): array
{
    return [
        'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        'nombre_valija' => PayloadCoercion::string($payload['nombre_valija'] ?? ''),
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
public static function cabeceraPieFromPayload(array $payload): array
{
    return [
        'cabecera' => PayloadCoercion::string($payload['cabecera'] ?? ''),
        'cabeceraB' => PayloadCoercion::string($payload['cabeceraB'] ?? ''),
        'firma' => PayloadCoercion::string($payload['firma'] ?? ''),
        'pie' => PayloadCoercion::string($payload['pie'] ?? ''),
    ];
}

/**
 * @return list<string>
 */
public static function actividadesNombres(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        $out[] = PayloadCoercion::string($item);
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{a_valores: array<int|string, mixed>, nombre_ubi: string, id_ubi: int}
 */
public static function equipajesDocCasaFromPayload(array $payload): array
{
    return [
        'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        'nombre_ubi' => PayloadCoercion::string($payload['nombre_ubi'] ?? ''),
        'id_ubi' => PayloadCoercion::int($payload['id_ubi'] ?? 0),
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
public static function egmRow(mixed $raw): array
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
        'id_grupo' => PayloadCoercion::int($raw['id_grupo'] ?? 0),
        'id_lugar' => PayloadCoercion::int($raw['id_lugar'] ?? 0),
        'nom_lugar' => PayloadCoercion::string($raw['nom_lugar'] ?? ''),
        'id_item_egm' => PayloadCoercion::int($raw['id_item_egm'] ?? 0),
        'texto' => PayloadCoercion::string($raw['texto'] ?? ''),
        'a_valores' => ActividadesListaSupport::datos($raw['a_valores'] ?? []),
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
public static function egmRows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = self::egmRow($row);
    }

    return $out;
}

/**
 * @return array{sigla: string, identificador: string, etiqueta: string}
 */
public static function docLibreRow(mixed $raw): array
{
    if (!is_array($raw)) {
        return ['sigla' => '', 'identificador' => '', 'etiqueta' => ''];
    }

    return [
        'sigla' => PayloadCoercion::string($raw[0] ?? ''),
        'identificador' => PayloadCoercion::string($raw[1] ?? ''),
        'etiqueta' => PayloadCoercion::string($raw[2] ?? ''),
    ];
}

/**
 * @return list<array{sigla: string, identificador: string, etiqueta: string}>
 */
public static function docsLibresRows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = self::docLibreRow($row);
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{a_opciones: array<int|string, string>, new_id_grupo: int}
 */
public static function posiblesMaletasFromPayload(array $payload): array
{
    return [
        'a_opciones' => self::desplegableOpciones($payload['a_opciones'] ?? []),
        'new_id_grupo' => PayloadCoercion::int($payload['new_id_grupo'] ?? 0),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{nombre_ubi: string, ini: string, fin: string, ids_activ: string}
 */
public static function equipajesFormNuevoFromPayload(array $payload): array
{
    return [
        'nombre_ubi' => PayloadCoercion::string($payload['nombre_ubi'] ?? ''),
        'ini' => PayloadCoercion::string($payload['ini'] ?? ''),
        'fin' => PayloadCoercion::string($payload['fin'] ?? ''),
        'ids_activ' => PayloadCoercion::string($payload['ids_activ'] ?? ''),
    ];
}

/**
 * @return array<string, array<int|string, mixed>>
 */
public static function ubiValoresMap(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        $out[(string) $key] = ActividadesListaSupport::datos($value);
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
public static function movimientosFromPayload(array $payload): array
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
            $lugares[$key] = PayloadCoercion::string($value);
        }
    }

    $nombresRaw = $payload['aNomEquipajes'] ?? [];
    $nombres = [];
    if (is_array($nombresRaw)) {
        foreach ($nombresRaw as $key => $value) {
            $nombres[$key] = PayloadCoercion::string($value);
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
public static function movimientosQueRows(mixed $raw): array
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
            $parsed[$key] = PayloadCoercion::string($value);
        }
        $out[] = $parsed;
    }

    return $out;
}

public static function grupoIdFromLoc(string $loc): string
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
public static function valorAgruparRow(mixed $raw): array
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
        'nombre' => PayloadCoercion::string($raw['nombre'] ?? ''),
        'identificador' => PayloadCoercion::string($raw['identificador'] ?? ''),
        'carta' => PayloadCoercion::string($raw['carta'] ?? ''),
        'coleccion' => PayloadCoercion::string($raw['coleccion'] ?? ''),
        'ejemplares' => PayloadCoercion::string($raw['ejemplares'] ?? ''),
        'lugar' => PayloadCoercion::string($raw['lugar'] ?? ''),
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
public static function valorAgruparRows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $out[] = self::valorAgruparRow($row);
    }

    return $out;
}

/**
 * @return array<int|string, string>
 */
public static function coleccionesOpciones(mixed $raw): array
{
    return self::desplegableOpciones($raw);
}
}
