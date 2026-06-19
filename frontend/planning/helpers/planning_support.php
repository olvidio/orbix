<?php

/**
 * Helpers compartidos del módulo frontend/planning.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use frontend\shared\config\OrbixRuntime;
use src\configuracion\domain\value_objects\ConfigSnapshot;

use function frontend\shared\helpers\urlsafe_b64decode;

function planning_o_config(): ?ConfigSnapshot
{
    $oConfig = $_SESSION['oConfig'] ?? null;

    return $oConfig instanceof ConfigSnapshot ? $oConfig : null;
}

function planning_post_string(string $name, string $default = ''): string
{
    return tessera_imprimir_string(filter_input(INPUT_POST, $name), $default);
}

function planning_post_int(string $name, int $default = 0): int
{
    $raw = filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT);

    return is_int($raw) ? $raw : $default;
}

/**
 * @return list<string>
 */
function planning_post_string_list(string $name): array
{
    if (!isset($_POST[$name]) || !is_array($_POST[$name])) {
        return [];
    }
    $out = [];
    foreach ($_POST[$name] as $item) {
        if (is_string($item) && $item !== '') {
            $out[] = $item;
        } elseif (is_int($item) || is_float($item)) {
            $out[] = (string) $item;
        }
    }

    return $out;
}

/**
 * Personas seleccionadas en listas SlickGrid (planning persona, etc.).
 * Prioriza `sSeleccionados` (csv) porque PostRequest/HashFront pierden arrays `sel[]`.
 *
 * @return list<string>
 */
function planning_collect_sel_from_post(): array
{
    $csv = planning_post_string('sSeleccionados');
    if ($csv !== '') {
        return array_values(array_filter(
            array_map('trim', explode(',', $csv)),
            static fn (string $v): bool => $v !== ''
        ));
    }

    return planning_post_string_list('sel');
}

function planning_posicion_string(mixed $value, string $default = ''): string
{
    return actividades_posicion_string($value, $default);
}

function planning_desplegable_opcion_sel(int|string $value): string
{
    return tessera_imprimir_string($value);
}

function planning_is_jefe_calendario(): bool
{
    $oConfig = planning_o_config();

    return $oConfig !== null && $oConfig->is_jefeCalendario();
}

function planning_mes_fin_stgr(): int
{
    $oConfig = planning_o_config();

    return $oConfig !== null ? $oConfig->getMesFinStgr() : (int) date('m');
}

/**
 * @return array{colorColumnaUno: string, colorColumnaDos: string, colorColumnaDomingo: string, table_border: string, css: string}
 */
function planning_calendario_estilos(bool $appendCalendarioCss = true): array
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
        'colorColumnaUno' => tessera_imprimir_string($colorColumnaUno),
        'colorColumnaDos' => tessera_imprimir_string($colorColumnaDos),
        'colorColumnaDomingo' => tessera_imprimir_string($colorColumnaDomingo),
        'table_border' => tessera_imprimir_string($table_border),
        'css' => $css,
    ];
}

/**
 * @return array<string, mixed>
 */
function planning_filtro_casas(mixed $raw): array
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
function planning_persona_row(mixed $raw): array
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
        'id_nom' => tessera_imprimir_int($raw['id_nom'] ?? 0),
        'id_tabla' => tessera_imprimir_string($raw['id_tabla'] ?? ''),
        'pref_apellidos_nombre' => tessera_imprimir_string($raw['pref_apellidos_nombre'] ?? ''),
        'centro_o_dl' => tessera_imprimir_string($raw['centro_o_dl'] ?? ''),
    ];
}

/**
 * @return list<array{id_nom: int, id_tabla: string, pref_apellidos_nombre: string, centro_o_dl: string}>
 */
function planning_personas_from_payload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        $out[] = planning_persona_row($item);
    }

    return $out;
}

/**
 * @return array<int, array<int, array{iso_ini: string, iso_fin: string, sfsv: int}>>
 */
function planning_casa_periodos_por_ubi(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $idUbi => $periodos) {
        $idUbiInt = tessera_imprimir_int($idUbi);
        if (!is_array($periodos)) {
            continue;
        }
        $parsed = [];
        foreach ($periodos as $per) {
            if (!is_array($per)) {
                continue;
            }
            $parsed[] = [
                'iso_ini' => tessera_imprimir_string($per['iso_ini'] ?? ''),
                'iso_fin' => tessera_imprimir_string($per['iso_fin'] ?? ''),
                'sfsv' => tessera_imprimir_int($per['sfsv'] ?? 0),
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
function planning_string_key_row(array $raw): array
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
function planning_is_persona_casa_map(array $items): bool
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
function planning_parse_actividad_list(mixed $items): array
{
    if (!is_array($items)) {
        return [];
    }
    if (array_is_list($items)) {
        $parsedItems = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                $parsedItems[] = planning_string_key_row($item);
            }
        }

        return $parsedItems;
    }

    return [planning_string_key_row($items)];
}

/**
 * @param array<int|string, mixed> $items
 * @return array<int|string, list<array<string, mixed>>>
 */
function planning_parse_persona_casa_map(array $items): array
{
    $out = [];
    foreach ($items as $k => $acts) {
        $out[$k] = planning_parse_actividad_list($acts);
    }

    return $out;
}

/**
 * @return array<int|string, array<int|string, mixed>>
 */
function planning_actividades_map(mixed $raw): array
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
            if (planning_is_persona_casa_map($items)) {
                foreach (planning_parse_persona_casa_map($items) as $pKey => $actsList) {
                    // planning_ctr_select: cada persona va en un índice numérico
                    // (`[0 => ['p#…' => actividades]]`); aplanar rompe PlanningRenderer.
                    if (is_int($gKey) || ctype_digit((string) $gKey)) {
                        $parsedGroup[] = [$pKey => $actsList];
                    } else {
                        $parsedGroup[$pKey] = $actsList;
                    }
                }
            } elseif (array_is_list($items)) {
                $parsedGroup[$gKey] = planning_parse_actividad_list($items);
            } else {
                $parsedGroup[$gKey] = [planning_string_key_row($items)];
            }
        }
        $out[$key] = $parsedGroup;
    }

    return $out;
}

/**
 * @return array<int|string, string>
 */
function planning_periodo_anys_opciones(): array
{
    $any = (int) date('Y');
    $out = [];
    for ($y = $any - 4; $y <= $any + 1; $y++) {
        $out[$y] = tessera_imprimir_string($y);
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{filtro: array<string, mixed>, modo_casas: string}
 */
function planning_casa_que_from_payload(array $payload): array
{
    $modo = tessera_imprimir_string($payload['modo_casas'] ?? 'all', 'all');

    return [
        'filtro' => planning_filtro_casas($payload['filtro'] ?? null),
        'modo_casas' => $modo !== '' ? $modo : 'all',
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{msg_txt: string, cabecera_title: string, a_actividades2: array<int|string, array<int|string, mixed>>}
 */
function planning_ctr_select_from_payload(array $payload): array
{
    return [
        'msg_txt' => tessera_imprimir_string($payload['msg_txt'] ?? ''),
        'cabecera_title' => tessera_imprimir_string($payload['cabecera_title'] ?? ''),
        'a_actividades2' => planning_actividades_map($payload['a_actividades2'] ?? null),
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
function planning_zones_select_from_payload(array $payload): array
{
    return [
        'planning_ini_iso' => tessera_imprimir_string($payload['planning_ini_iso'] ?? ''),
        'planning_fin_iso' => tessera_imprimir_string($payload['planning_fin_iso'] ?? ''),
        'titulo' => tessera_imprimir_string($payload['titulo'] ?? ''),
        'zonas' => tessera_imprimir_int($payload['zonas'] ?? 0),
        'actividades_por_zona' => is_array($payload['actividades_por_zona'] ?? null) ? $payload['actividades_por_zona'] : [],
        'cabeceras_por_zona' => is_array($payload['cabeceras_por_zona'] ?? null) ? $payload['cabeceras_por_zona'] : [],
    ];
}

/**
 * @param array<int|string, mixed> $decoded
 */
function planning_where_string(array $decoded, string $key, string $default = ''): string
{
    return tessera_imprimir_string($decoded[$key] ?? $default);
}

/**
 * Restaura nombre/apellidos/centro/na a partir de los filtros codificados en la pila Posicion.
 *
 * @return array{nombre: string, apellido1: string, apellido2: string, centro: string, na: string}
 */
function planning_filtros_persona_desde_sa_where_encoded(
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

    $aWhereDecoded = json_decode(urlsafe_b64decode($saWhere), true);
    $aWhere = is_array($aWhereDecoded) ? $aWhereDecoded : [];
    $aWhereCtrDecoded = json_decode(urlsafe_b64decode($saWhereCtr), true);
    $aWhereCtr = is_array($aWhereCtrDecoded) ? $aWhereCtrDecoded : [];

    $apellido1 = planning_where_string($aWhere, 'apellido1', $apellido1);
    if (str_starts_with($apellido1, '^')) {
        $apellido1 = substr($apellido1, 1);
    }
    $apellido2 = planning_where_string($aWhere, 'apellido2', $apellido2);
    if (str_starts_with($apellido2, '^')) {
        $apellido2 = substr($apellido2, 1);
    }
    $nombre = planning_where_string($aWhere, 'nom', $nombre);
    if (str_starts_with($nombre, '^')) {
        $nombre = substr($nombre, 1);
    }
    $centro = planning_where_string($aWhereCtr, 'nombre_ubi', $centro);
    $idTablaWhere = planning_where_string($aWhere, 'id_tabla');
    if (str_starts_with($idTablaWhere, 'p')) {
        $na = substr($idTablaWhere, 1);
    }

    return compact('nombre', 'apellido1', 'apellido2', 'centro', 'na');
}
