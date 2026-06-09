<?php

/**
 * Helpers compartidos del módulo frontend/cambios.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use src\permisos\domain\XPermisos;

function cambios_o_perm(): ?XPermisos
{
    $oPerm = $_SESSION['oPerm'] ?? null;

    return $oPerm instanceof XPermisos ? $oPerm : null;
}

function cambios_is_admin(): bool
{
    $oPerm = cambios_o_perm();
    if ($oPerm === null) {
        return false;
    }

    return $oPerm->only_perm('admin_sf') || $oPerm->only_perm('admin_sv');
}

/**
 * @return array<string, mixed>
 */
function cambios_hash_campos_hidden(mixed $raw): array
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
 * @return array<string, mixed>
 */
function cambios_post_data(mixed $data): array
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

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     effective_id_usuario: int,
 *     effective_aviso_tipo: int,
 *     a_valores: array<int|string, mixed>,
 *     aOpcionesUsuarios: array<int|string, string>,
 *     aOpcionesAvisoTipo: array<int|string, string>,
 *     url_eliminar: string,
 *     url_eliminar_fecha: string,
 *     h_eliminar: string,
 *     h_eliminar_fecha: string,
 * }
 */
function cambios_avisos_generar_from_payload(array $payload): array
{
    return [
        'effective_id_usuario' => tessera_imprimir_int($payload['effective_id_usuario'] ?? 0),
        'effective_aviso_tipo' => tessera_imprimir_int($payload['effective_aviso_tipo'] ?? 0),
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'aOpcionesUsuarios' => notas_desplegable_opciones($payload['aOpcionesUsuarios'] ?? []),
        'aOpcionesAvisoTipo' => notas_desplegable_opciones($payload['aOpcionesAvisoTipo'] ?? []),
        'url_eliminar' => tessera_imprimir_string($payload['url_eliminar'] ?? ''),
        'url_eliminar_fecha' => tessera_imprimir_string($payload['url_eliminar_fecha'] ?? ''),
        'h_eliminar' => tessera_imprimir_string($payload['h_eliminar'] ?? ''),
        'h_eliminar_fecha' => tessera_imprimir_string($payload['h_eliminar_fecha'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     aTiposAviso: array<int|string, string>,
 *     aObjetos: array<int|string, string>,
 *     aFases: array<int|string, string>,
 *     aOpcionesCasas: array<int|string, string>,
 *     id_pau: string,
 *     aviso_tipo: string,
 *     objeto: string,
 *     id_fase_ref: string,
 *     dl_propia: bool,
 *     aviso_off: bool,
 *     aviso_on: bool,
 *     aviso_outdate: bool,
 *     id_tipo_activ: string,
 * }
 */
function cambios_usuario_avisos_pref_form_from_payload(array $payload): array
{
    return [
        'aTiposAviso' => notas_desplegable_opciones($payload['aTiposAviso'] ?? []),
        'aObjetos' => notas_desplegable_opciones($payload['aObjetos'] ?? []),
        'aFases' => notas_desplegable_opciones($payload['aFases'] ?? []),
        'aOpcionesCasas' => notas_desplegable_opciones($payload['aOpcionesCasas'] ?? []),
        'id_pau' => tessera_imprimir_string($payload['id_pau'] ?? ''),
        'aviso_tipo' => tessera_imprimir_string($payload['aviso_tipo'] ?? ''),
        'objeto' => tessera_imprimir_string($payload['objeto'] ?? ''),
        'id_fase_ref' => tessera_imprimir_string($payload['id_fase_ref'] ?? ''),
        'dl_propia' => !array_key_exists('dl_propia', $payload) || !empty($payload['dl_propia']),
        'aviso_off' => !empty($payload['aviso_off']),
        'aviso_on' => !array_key_exists('aviso_on', $payload) || !empty($payload['aviso_on']),
        'aviso_outdate' => !empty($payload['aviso_outdate']),
        'id_tipo_activ' => tessera_imprimir_string($payload['id_tipo_activ'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     valor: string,
 *     operador: string,
 *     chk_old: string,
 *     chk_new: string,
 *     aOpcionesCasas: array<int|string, string>,
 * }
 */
function cambios_usuario_avisos_pref_condicion_from_payload(array $payload): array
{
    return [
        'valor' => tessera_imprimir_string($payload['valor'] ?? ''),
        'operador' => tessera_imprimir_string($payload['operador'] ?? ''),
        'chk_old' => tessera_imprimir_string($payload['chk_old'] ?? 'checked'),
        'chk_new' => tessera_imprimir_string($payload['chk_new'] ?? 'checked'),
        'aOpcionesCasas' => notas_desplegable_opciones($payload['aOpcionesCasas'] ?? []),
    ];
}

/**
 * @return list<array<string, mixed>>
 */
function cambios_propiedades_rows(mixed $raw): array
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
 * @param array<string, mixed> $row
 */
function cambios_propiedad_nom_prop(array $row): string
{
    return tessera_imprimir_string($row['nom_prop'] ?? '');
}

/**
 * @param array<string, mixed> $data
 * @return array{a_valores: array<int|string, mixed>, nombre_usuario: string}
 */
function cambios_usuario_form_avisos_from_payload(array $data): array
{
    return [
        'a_valores' => actividades_lista_datos($data['a_valores'] ?? []),
        'nombre_usuario' => tessera_imprimir_string($data['nombre_usuario'] ?? ''),
    ];
}
