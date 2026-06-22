<?php

/**
 * Helpers compartidos del módulo frontend/actividades.
 */

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';
require_once __DIR__ . '/../../notas/helpers/notas_support.php';

use frontend\shared\web\Desplegable;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XPermisos;

function actividades_o_perm(): ?XPermisos
{
    $oPerm = $_SESSION['oPerm'] ?? null;

    return $oPerm instanceof XPermisos ? $oPerm : null;
}

function actividades_o_perm_actividades(): ?PermisosActividades
{
    $oPerm = $_SESSION['oPermActividades'] ?? null;

    return $oPerm instanceof PermisosActividades ? $oPerm : null;
}

function actividades_perm_des(): bool
{
    $oPerm = actividades_o_perm();
    if ($oPerm === null) {
        return false;
    }

    return $oPerm->have_perm_oficina('vcsd') || $oPerm->have_perm_oficina('des');
}

function actividades_have_perm_oficina(string $oficina): bool
{
    $oPerm = actividades_o_perm();

    return $oPerm !== null && $oPerm->have_perm_oficina($oficina);
}

function actividades_is_jefe_calendario(): bool
{
    $oConfig = $_SESSION['oConfig'] ?? null;

    return $oConfig instanceof ConfigSnapshot && $oConfig->is_jefeCalendario();
}

function actividades_id_activ_from_post(): int
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);
            $idRaw = $parts[0];

            return is_numeric($idRaw) ? (int) $idRaw : 0;
        }
    }

    $idRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);

    return is_int($idRaw) ? $idRaw : 0;
}

/**
 * @param array<int|string, mixed> $labelsRow
 * @return array<int|string, string>
 */
function actividades_status_labels_from_payload(array $labelsRow): array
{
    $raw = $labelsRow['id_to_label'] ?? [];
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
 * @param array<int|string, mixed> $dataEntidad
 * @return array{
 *     id_tipo_activ: string,
 *     dl_org: string,
 *     nom_activ: string,
 *     id_ubi: int,
 *     f_ini: string,
 *     h_ini: string,
 *     f_fin: string,
 *     h_fin: string,
 *     precio: int|float|string,
 *     status: int,
 *     observ: string,
 *     nivel_stgr: int|string,
 *     lugar_esp: string,
 *     tarifa: int|float|string,
 *     id_repeticion: int,
 *     publicado: bool|string,
 *     plazas: int|float|string,
 *     idioma: string,
 * }
 */
function actividades_entidad_from_ver_datos(array $dataEntidad): array
{
    $entidadRaw = $dataEntidad['entidad'] ?? null;
    if (!is_array($entidadRaw)) {
        die(_('No se encuentra la actividad'));
    }

    return [
        'id_tipo_activ' => tessera_imprimir_string($entidadRaw['id_tipo_activ'] ?? ''),
        'dl_org' => tessera_imprimir_string($entidadRaw['dl_org'] ?? ''),
        'nom_activ' => tessera_imprimir_string($entidadRaw['nom_activ'] ?? ''),
        'id_ubi' => tessera_imprimir_int($entidadRaw['id_ubi'] ?? 0),
        'f_ini' => tessera_imprimir_string($entidadRaw['f_ini'] ?? ''),
        'h_ini' => tessera_imprimir_string($entidadRaw['h_ini'] ?? ''),
        'f_fin' => tessera_imprimir_string($entidadRaw['f_fin'] ?? ''),
        'h_fin' => tessera_imprimir_string($entidadRaw['h_fin'] ?? ''),
        'precio' => notas_form_scalar($entidadRaw['precio'] ?? ''),
        'status' => tessera_imprimir_int($entidadRaw['status'] ?? 0),
        'observ' => tessera_imprimir_string($entidadRaw['observ'] ?? ''),
        'nivel_stgr' => notas_form_scalar($entidadRaw['nivel_stgr'] ?? ''),
        'lugar_esp' => tessera_imprimir_string($entidadRaw['lugar_esp'] ?? ''),
        'tarifa' => notas_form_scalar($entidadRaw['tarifa'] ?? ''),
        'id_repeticion' => tessera_imprimir_int($entidadRaw['id_repeticion'] ?? 0),
        'publicado' => notas_form_bool_or_string($entidadRaw['publicado'] ?? ''),
        'plazas' => notas_form_scalar($entidadRaw['plazas'] ?? ''),
        'idioma' => tessera_imprimir_string($entidadRaw['idioma'] ?? ''),
    ];
}

/**
 * Convierte payload estándar de desplegable (contrato refactor.md) en HTML `<select>`.
 *
 * @param array<string, mixed>|null $raw
 */
function actividades_desplegable_html(?array $raw): string
{
    if ($raw === null || $raw === []) {
        return '';
    }
    $id = tessera_imprimir_string($raw['id'] ?? '');
    if ($id === '') {
        return '';
    }
    $opciones = notas_desplegable_opciones($raw['opciones'] ?? []);
    $blanco = !array_key_exists('blanco', $raw) || (bool) $raw['blanco'];
    $d = Desplegable::desdeOpciones($opciones, $id, $blanco);
    $selected = tessera_imprimir_string($raw['selected'] ?? '');
    if ($selected !== '') {
        $d->setOpcion_sel($selected);
    }
    $action = tessera_imprimir_string($raw['action'] ?? '');
    if ($action !== '') {
        $d->setAction($action);
    }
    if (array_key_exists('val_blanco', $raw)) {
        $d->setBlanco(true);
        $d->setValBlanco(tessera_imprimir_string($raw['val_blanco']));
    }
    $opcionNo = $raw['opcion_no'] ?? null;
    if (is_array($opcionNo) && $opcionNo !== []) {
        $normalized = [];
        foreach ($opcionNo as $item) {
            $normalized[] = tessera_imprimir_string($item);
        }
        $d->setOpcion_no($normalized);
    }

    return $d->desplegable();
}

/**
 * @param array<int|string, mixed> $data
 * @return array{
 *     html_despl_dl_org: string,
 *     html_despl_tarifa: string,
 *     html_despl_nivel_stgr: string,
 *     html_despl_idioma: string,
 *     html_despl_repeticion: string,
 *     nombre_ubi: string,
 *     ssfsv: string,
 *     sasistentes: string,
 *     sactividad: string,
 *     snom_tipo: string,
 *     isfsv: int,
 *     tarifa_inicial: mixed,
 * }
 */
function actividades_ver_render_from_payload(array $data): array
{
    return [
        'html_despl_dl_org' => actividades_desplegable_html(is_array($data['select_dl_org'] ?? null) ? $data['select_dl_org'] : null),
        'html_despl_tarifa' => actividades_desplegable_html(is_array($data['select_tarifa'] ?? null) ? $data['select_tarifa'] : null),
        'html_despl_nivel_stgr' => actividades_desplegable_html(is_array($data['select_nivel_stgr'] ?? null) ? $data['select_nivel_stgr'] : null),
        'html_despl_idioma' => actividades_desplegable_html(is_array($data['select_idioma'] ?? null) ? $data['select_idioma'] : null),
        'html_despl_repeticion' => actividades_desplegable_html(is_array($data['select_repeticion'] ?? null) ? $data['select_repeticion'] : null),
        'nombre_ubi' => tessera_imprimir_string($data['nombre_ubi'] ?? ''),
        'ssfsv' => tessera_imprimir_string($data['ssfsv'] ?? ''),
        'sasistentes' => tessera_imprimir_string($data['sasistentes'] ?? ''),
        'sactividad' => tessera_imprimir_string($data['sactividad'] ?? ''),
        'snom_tipo' => tessera_imprimir_string($data['snom_tipo'] ?? ''),
        'isfsv' => tessera_imprimir_int($data['isfsv'] ?? 0),
        'tarifa_inicial' => $data['tarifa_inicial'] ?? null,
    ];
}

/**
 * @param array<int|string, mixed> $row
 * @return array{of_responsable_txt: string, status: int}|null
 */
function actividades_permiso_crear_from_row(array $row): ?array
{
    $crear = $row['permiso_crear'] ?? false;
    if ($crear === false || !is_array($crear)) {
        return null;
    }

    return [
        'of_responsable_txt' => tessera_imprimir_string($crear['of_responsable_txt'] ?? ''),
        'status' => tessera_imprimir_int($crear['status'] ?? 0),
    ];
}

function actividades_posicion_string(mixed $value, string $default = ''): string
{
    return is_string($value) || is_int($value) || is_float($value) ? tessera_imprimir_string($value) : $default;
}

function actividades_posicion_int(mixed $value, int $default = 0): int
{
    return is_int($value) || is_string($value) ? tessera_imprimir_int($value, $default) : $default;
}

/**
 * @param list<array<string, mixed>> $rows
 * @return list<array<string, mixed>>
 */
function actividades_sign_nested_link_specs(array $rows): array
{
    $out = [];
    foreach ($rows as $fila) {
        $row = $fila;
        foreach ($row as $colKey => $cell) {
            if (!is_array($cell) || !isset($cell['link_spec'])) {
                continue;
            }
            $signed = \frontend\shared\security\HashFrontSignedLink::tryFromSpec($cell['link_spec']);
            if ($signed !== '') {
                $cell['ira'] = $signed;
            }
            unset($cell['link_spec']);
            $row[$colKey] = $cell;
        }
        $out[] = $row;
    }

    return $out;
}

/**
 * @return list<array<string, mixed>|string>
 */
function actividades_lista_cabeceras(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        if (is_string($item)) {
            $out[] = $item;
        } elseif (is_array($item)) {
            $out[] = $item;
        }
    }

    return $out;
}

/**
 * @return list<array<string, mixed>>
 */
function actividades_lista_botones(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        if (is_array($item)) {
            $out[] = $item;
        }
    }

    return $out;
}

/**
 * @return array<int|string, mixed>
 */
function actividades_lista_datos(mixed $raw): array
{
    return is_array($raw) ? $raw : [];
}

/**
 * @return list<array<string, mixed>>
 */
function actividades_lista_valores_from_payload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $rows = [];
    foreach ($raw as $item) {
        if (is_array($item)) {
            $rows[] = $item;
        }
    }

    return actividades_sign_nested_link_specs($rows);
}

/**
 * @return list<int>
 */
function actividades_fases_completadas_from_payload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $id) {
        if (is_int($id) || is_string($id)) {
            $out[] = tessera_imprimir_int($id);
        }
    }

    return $out;
}
