<?php

declare(strict_types=1);

namespace frontend\planning\helpers;

use frontend\shared\helpers\FuncTablasSupport;
use frontend\actividades\helpers\ActividadesPostInput;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\helpers\PayloadCoercion;
use src\configuracion\domain\value_objects\ConfigSnapshot;

final class PlanningPayload
{
public static function oConfig(): ?ConfigSnapshot
{
    $oConfig = $_SESSION['oConfig'] ?? null;

    return $oConfig instanceof ConfigSnapshot ? $oConfig : null;
}


public static function posicionString(mixed $value, string $default = ''): string
{
    return ActividadesPostInput::posicionString($value, $default);
}

public static function desplegableOpcionSel(int|string $value): string
{
    return PayloadCoercion::string($value);
}

public static function isJefeCalendario(): bool
{
    $oConfig = self::oConfig();

    return $oConfig !== null && $oConfig->is_jefeCalendario();
}

public static function mesFinStgr(): int
{
    $oConfig = self::oConfig();

    return $oConfig !== null ? $oConfig->getMesFinStgr() : (int) date('m');
}

/**
 * @return array{colorColumnaUno: string, colorColumnaDos: string, colorColumnaDomingo: string, table_border: string, css: string}
 */
public static function calendarioEstilos(bool $appendCalendarioCss = true): array
{
    $colorColumnaUno = '';
    $colorColumnaDos = '';
    $colorColumnaDomingo = '';
    $table_border = '';
    include_once OrbixRuntime::dirEstilos() . '/calendario_color_cols.css.php';
    $css = '';
    if ($appendCalendarioCss) {
        ob_start();
        include OrbixRuntime::dirEstilos() . '/calendario.css.php';
        $css = ob_get_clean() ?: '';
    }

    return [
        'colorColumnaUno' => PayloadCoercion::string($colorColumnaUno),
        'colorColumnaDos' => PayloadCoercion::string($colorColumnaDos),
        'colorColumnaDomingo' => PayloadCoercion::string($colorColumnaDomingo),
        'table_border' => PayloadCoercion::string($table_border),
        'css' => $css,
    ];
}

/**
 * @return array<string, mixed>
 */
public static function filtroCasas(mixed $raw): array
{
    if (!is_array($raw)) {
        return ['active' => true];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out !== [] ? $out : ['active' => true];
}

/**
 * @return array{id_nom: int, id_tabla: string, pref_apellidos_nombre: string, centro_o_dl: string}
 */
public static function personaRow(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_nom' => 0,
            'id_tabla' => '',
            'pref_apellidos_nombre' => '',
            'centro_o_dl' => '',
        ];
    }

    return [
        'id_nom' => PayloadCoercion::int($raw['id_nom'] ?? 0),
        'id_tabla' => PayloadCoercion::string($raw['id_tabla'] ?? ''),
        'pref_apellidos_nombre' => PayloadCoercion::string($raw['pref_apellidos_nombre'] ?? ''),
        'centro_o_dl' => PayloadCoercion::string($raw['centro_o_dl'] ?? ''),
    ];
}

/**
 * @return list<array{id_nom: int, id_tabla: string, pref_apellidos_nombre: string, centro_o_dl: string}>
 */
public static function personasFromPayload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        $out[] = self::personaRow($item);
    }

    return $out;
}

/**
 * @return array<int, array<int, array{iso_ini: string, iso_fin: string, sfsv: int}>>
 */
public static function casaPeriodosPorUbi(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $idUbi => $periodos) {
        $idUbiInt = PayloadCoercion::int($idUbi);
        if (!is_array($periodos)) {
            continue;
        }
        $parsed = [];
        foreach ($periodos as $per) {
            if (!is_array($per)) {
                continue;
            }
            $parsed[] = [
                'iso_ini' => PayloadCoercion::string($per['iso_ini'] ?? ''),
                'iso_fin' => PayloadCoercion::string($per['iso_fin'] ?? ''),
                'sfsv' => PayloadCoercion::int($per['sfsv'] ?? 0),
            ];
        }
        $out[$idUbiInt] = $parsed;
    }

    return $out;
}

/**
 * @param array<mixed, mixed> $raw
 * @return array<string, mixed>
 */
public static function stringKeyRow(array $raw): array
{
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out;
}

/**
 * Mapa persona/casa: claves `p#…`, `u#…`, `##`, etc.
 *
 * @param array<int|string, mixed> $items
 */
public static function isPersonaCasaMap(array $items): bool
{
    if ($items === []) {
        // Lista vacía de actividades bajo una clave `p#…` / `u#…`; no es un mapa anidado.
        return false;
    }
    foreach (array_keys($items) as $k) {
        if (!is_string($k) || !str_contains($k, '#')) {
            return false;
        }
    }

    return true;
}

/**
 * @return list<array<string, mixed>>
 */
public static function parseActividadList(mixed $items): array
{
    if (!is_array($items)) {
        return [];
    }
    if (array_is_list($items)) {
        $parsedItems = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                $parsedItems[] = self::stringKeyRow($item);
            }
        }

        return $parsedItems;
    }

    return [self::stringKeyRow($items)];
}

/**
 * @param array<int|string, mixed> $items
 * @return array<int|string, list<array<string, mixed>>>
 */
public static function parsePersonaCasaMap(array $items): array
{
    $out = [];
    foreach ($items as $k => $acts) {
        $out[$k] = self::parseActividadList($acts);
    }

    return $out;
}

/**
 * @return array<int|string, array<int|string, mixed>>
 */
public static function actividadesMap(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $group) {
        if (!is_array($group)) {
            continue;
        }
        $parsedGroup = [];
        foreach ($group as $gKey => $items) {
            if (!is_array($items)) {
                continue;
            }
            if (self::isPersonaCasaMap($items)) {
                foreach (self::parsePersonaCasaMap($items) as $pKey => $actsList) {
                    // planning_ctr_select: cada persona va en un índice numérico
                    // (`[0 => ['p#…' => actividades]]`); aplanar rompe PlanningRenderer.
                    if (is_int($gKey) || ctype_digit((string) $gKey)) {
                        $parsedGroup[] = [$pKey => $actsList];
                    } else {
                        $parsedGroup[$pKey] = $actsList;
                    }
                }
            } elseif (array_is_list($items)) {
                $parsedGroup[$gKey] = self::parseActividadList($items);
            } else {
                $parsedGroup[$gKey] = [self::stringKeyRow($items)];
            }
        }
        $out[$key] = $parsedGroup;
    }

    return $out;
}

/**
 * @return array<int|string, string>
 */
public static function periodoAnysOpciones(): array
{
    $any = (int) date('Y');
    $out = [];
    for ($y = $any - 4; $y <= $any + 1; $y++) {
        $out[$y] = PayloadCoercion::string($y);
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{filtro: array<string, mixed>, modo_casas: string}
 */
public static function casaQueFromPayload(array $payload): array
{
    $modo = PayloadCoercion::string($payload['modo_casas'] ?? 'all', 'all');

    return [
        'filtro' => self::filtroCasas($payload['filtro'] ?? null),
        'modo_casas' => $modo !== '' ? $modo : 'all',
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{msg_txt: string, cabecera_title: string, a_actividades2: array<int|string, array<int|string, mixed>>}
 */
public static function ctrSelectFromPayload(array $payload): array
{
    return [
        'msg_txt' => PayloadCoercion::string($payload['msg_txt'] ?? ''),
        'cabecera_title' => PayloadCoercion::string($payload['cabecera_title'] ?? ''),
        'a_actividades2' => self::actividadesMap($payload['a_actividades2'] ?? null),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     planning_ini_iso: string,
 *     planning_fin_iso: string,
 *     titulo: string,
 *     zonas: int,
 *     actividades_por_zona: array<int|string, mixed>,
 *     cabeceras_por_zona: array<int|string, mixed>,
 * }
 */
public static function zonesSelectFromPayload(array $payload): array
{
    return [
        'planning_ini_iso' => PayloadCoercion::string($payload['planning_ini_iso'] ?? ''),
        'planning_fin_iso' => PayloadCoercion::string($payload['planning_fin_iso'] ?? ''),
        'titulo' => PayloadCoercion::string($payload['titulo'] ?? ''),
        'zonas' => PayloadCoercion::int($payload['zonas'] ?? 0),
        'actividades_por_zona' => is_array($payload['actividades_por_zona'] ?? null) ? $payload['actividades_por_zona'] : [],
        'cabeceras_por_zona' => is_array($payload['cabeceras_por_zona'] ?? null) ? $payload['cabeceras_por_zona'] : [],
    ];
}

/**
 * @param array<int|string, mixed> $decoded
 */
public static function whereString(array $decoded, string $key, string $default = ''): string
{
    return PayloadCoercion::string($decoded[$key] ?? $default);
}

/**
 * Restaura nombre/apellidos/centro/na a partir de los filtros codificados en la pila Posicion.
 *
 * @return array{nombre: string, apellido1: string, apellido2: string, centro: string, na: string}
 */
public static function filtrosPersonaDesdeSaWhereEncoded(
    string $saWhere,
    string $saWhereCtr,
    string $nombre = '',
    string $apellido1 = '',
    string $apellido2 = '',
    string $centro = '',
    string $na = '',
): array {
    if ($saWhere === '' && $saWhereCtr === '') {
        return compact('nombre', 'apellido1', 'apellido2', 'centro', 'na');
    }

    $aWhereDecoded = json_decode(FuncTablasSupport::urlsafeB64decode($saWhere), true);
    $aWhere = is_array($aWhereDecoded) ? $aWhereDecoded : [];
    $aWhereCtrDecoded = json_decode(FuncTablasSupport::urlsafeB64decode($saWhereCtr), true);
    $aWhereCtr = is_array($aWhereCtrDecoded) ? $aWhereCtrDecoded : [];

    $apellido1 = self::whereString($aWhere, 'apellido1', $apellido1);
    if (str_starts_with($apellido1, '^')) {
        $apellido1 = substr($apellido1, 1);
    }
    $apellido2 = self::whereString($aWhere, 'apellido2', $apellido2);
    if (str_starts_with($apellido2, '^')) {
        $apellido2 = substr($apellido2, 1);
    }
    $nombre = self::whereString($aWhere, 'nom', $nombre);
    if (str_starts_with($nombre, '^')) {
        $nombre = substr($nombre, 1);
    }
    $centro = self::whereString($aWhereCtr, 'nombre_ubi', $centro);
    $idTablaWhere = self::whereString($aWhere, 'id_tabla');
    if (str_starts_with($idTablaWhere, 'p')) {
        $na = substr($idTablaWhere, 1);
    }

    return compact('nombre', 'apellido1', 'apellido2', 'centro', 'na');
}
}
