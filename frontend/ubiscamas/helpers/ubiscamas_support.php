<?php

/**
 * Helpers compartidos del módulo frontend/ubiscamas.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use frontend\shared\security\HashFrontSignedLink;

/**
 * @return array<string, mixed>
 */
function ubiscamas_post_data(mixed $data): array
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
 * @return array<string, mixed>
 */
function ubiscamas_hash_campos_hidden(mixed $raw): array
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
 * @return array{path: string, query?: array<string, mixed>}|null
 */
function ubiscamas_link_spec(mixed $raw): ?array
{
    if (!is_array($raw)) {
        return null;
    }
    $path = $raw['path'] ?? null;
    if (!is_string($path) || $path === '') {
        return null;
    }
    $spec = ['path' => $path];
    $query = $raw['query'] ?? null;
    if (is_array($query)) {
        $q = [];
        foreach ($query as $k => $v) {
            $q[(string) $k] = $v;
        }
        if ($q !== []) {
            $spec['query'] = $q;
        }
    }

    return $spec;
}

/**
 * @param array<string, mixed> $data
 * @return array{
 *     cabeceras: list<array<string, mixed>|string>,
 *     botones: list<array<string, mixed>>,
 *     valores: array<int|string, mixed>,
 *     id_activ: int|string,
 *     id_ubi: int|string,
 *     habitaciones_con_camas: array<int|string, mixed>,
 *     camas_con_asistentes: array<int|string, mixed>,
 *     asistentes_sin_cama: int|string,
 *     solo_vip: bool|string,
 *     url_update_cama_full: string,
 *     ctx_update_cama: string,
 *     update_solo_vip_full_url: string,
 *     ctx_update_solo_vip: string,
 *     reload_main_url: string,
 *     distribucion_open_url: string,
 *     nombres_open_url: string,
 * }
 */
function ubiscamas_habitaciones_lista_from_payload(array $data): array
{
    $signed = [
        'reload_main_url' => HashFrontSignedLink::tryFromSpec($data['reload_main_link_spec'] ?? null),
        'distribucion_open_url' => HashFrontSignedLink::tryFromSpec($data['distribucion_open_link_spec'] ?? null),
        'nombres_open_url' => HashFrontSignedLink::tryFromSpec($data['nombres_open_link_spec'] ?? null),
    ];

    return [
        'cabeceras' => actividades_lista_cabeceras($data['a_cabeceras'] ?? []),
        'botones' => actividades_lista_botones($data['a_botones'] ?? []),
        'valores' => actividades_lista_datos($data['a_valores'] ?? []),
        'id_activ' => notas_form_scalar($data['id_activ'] ?? 0),
        'id_ubi' => notas_form_scalar($data['id_ubi'] ?? 0),
        'habitaciones_con_camas' => actividades_lista_datos($data['habitaciones_con_camas'] ?? []),
        'camas_con_asistentes' => actividades_lista_datos($data['camas_con_asistentes'] ?? []),
        'asistentes_sin_cama' => notas_form_scalar($data['asistentes_sin_cama'] ?? 0),
        'solo_vip' => notas_form_bool_or_string($data['solo_vip'] ?? ''),
        'url_update_cama_full' => tessera_imprimir_string($data['url_update_cama_full'] ?? ''),
        'ctx_update_cama' => tessera_imprimir_string($data['ctx_update_cama'] ?? ''),
        'update_solo_vip_full_url' => tessera_imprimir_string($data['update_solo_vip_full_url'] ?? ''),
        'ctx_update_solo_vip' => tessera_imprimir_string($data['ctx_update_solo_vip'] ?? ''),
        'reload_main_url' => $signed['reload_main_url'],
        'distribucion_open_url' => $signed['distribucion_open_url'],
        'nombres_open_url' => $signed['nombres_open_url'],
    ];
}

/**
 * @param array<string, mixed> $data
 * @return list<array{nombre: string, planta: string, habitacion: string}>
 */
function ubiscamas_nombres_lista_from_payload(array $data): array
{
    $habitacionesRaw = $data['habitaciones_con_camas'] ?? [];
    $camasAsistentesRaw = $data['camas_con_asistentes'] ?? [];
    if (!is_array($habitacionesRaw)) {
        return [];
    }
    $camasAsistentes = is_array($camasAsistentesRaw) ? $camasAsistentesRaw : [];
    $aLista = [];
    foreach ($habitacionesRaw as $roomData) {
        if (!is_array($roomData)) {
            continue;
        }
        $aHabitacionRaw = $roomData['habitacion'] ?? null;
        if (!is_array($aHabitacionRaw)) {
            continue;
        }
        $camasRaw = $roomData['camas'] ?? null;
        if (!is_array($camasRaw)) {
            continue;
        }
        foreach ($camasRaw as $aCama) {
            if (!is_array($aCama)) {
                continue;
            }
            $idCama = tessera_imprimir_int($aCama['id_cama'] ?? 0);
            $nombrePersona = '';
            $asistenteRaw = $camasAsistentes[$idCama] ?? null;
            if (is_array($asistenteRaw)) {
                $nombrePersona = tessera_imprimir_string($asistenteRaw['apellidos'] ?? '');
            }
            if ($nombrePersona === '') {
                continue;
            }
            $aLista[] = [
                'nombre' => $nombrePersona,
                'planta' => tessera_imprimir_string($aHabitacionRaw['planta'] ?? ''),
                'habitacion' => tessera_imprimir_string($aHabitacionRaw['nombre'] ?? ''),
            ];
        }
    }

    usort(
        $aLista,
        static fn (array $a, array $b): int => strcasecmp($a['nombre'], $b['nombre'])
    );

    return $aLista;
}

/**
 * @param array<string, mixed> $payload
 * @param array<string, mixed> $hashBlock
 * @return array{
 *     hash_form_html: string,
 *     cama_update_url: string,
 *     id_cama: string,
 *     id_habitacion: string,
 *     id_ubi: int,
 *     descripcion: string,
 *     larga: bool,
 *     vip: bool,
 * }
 */
function ubiscamas_cama_form_view_from_payload(array $payload, array $hashBlock): array
{
    return [
        'hash_form_html' => tessera_imprimir_string($hashBlock['hash_form_html'] ?? ''),
        'cama_update_url' => tessera_imprimir_string($hashBlock['cama_update_url'] ?? ''),
        'id_cama' => tessera_imprimir_string($payload['id_cama'] ?? ''),
        'id_habitacion' => tessera_imprimir_string($payload['id_habitacion'] ?? ''),
        'id_ubi' => tessera_imprimir_int($payload['id_ubi'] ?? 0),
        'descripcion' => tessera_imprimir_string($payload['descripcion'] ?? ''),
        'larga' => !empty($payload['larga']),
        'vip' => !empty($payload['vip']),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @param array<string, mixed> $hashBlock
 * @return array<string, mixed>
 */
function ubiscamas_habitacion_form_view_from_payload(array $payload, array $hashBlock): array
{
    return [
        'hash_form_html' => tessera_imprimir_string($hashBlock['hash_form_html'] ?? ''),
        'hash_actualizar_html' => tessera_imprimir_string($hashBlock['hash_actualizar_html'] ?? ''),
        'id_habitacion' => tessera_imprimir_string($payload['id_habitacion'] ?? ''),
        'id_ubi' => tessera_imprimir_int($payload['id_ubi'] ?? 0),
        'orden' => notas_form_scalar($payload['orden'] ?? ''),
        'nombre' => tessera_imprimir_string($payload['nombre'] ?? ''),
        'numero_camas' => notas_form_scalar($payload['numero_camas'] ?? ''),
        'numero_camas_vip' => notas_form_scalar($payload['numero_camas_vip'] ?? ''),
        'planta' => tessera_imprimir_string($payload['planta'] ?? ''),
        'sillon' => !empty($payload['sillon']),
        'adaptada' => !empty($payload['adaptada']),
        'observaciones' => tessera_imprimir_string($payload['observaciones'] ?? ''),
        'despacho' => !empty($payload['despacho']),
        'tipoLavabo' => $payload['tipoLavabo'] ?? null,
        'a_tipos_tipoLavabo' => notas_desplegable_opciones($payload['a_tipos_tipoLavabo'] ?? []),
        'a_camas' => is_array($payload['a_camas'] ?? null) ? $payload['a_camas'] : [],
        'url_cama_form' => tessera_imprimir_string($hashBlock['url_cama_form'] ?? ''),
        'h_cama_form_params' => tessera_imprimir_string($hashBlock['h_cama_form_params'] ?? ''),
        'url_cama_delete' => tessera_imprimir_string($hashBlock['url_cama_delete'] ?? ''),
        'h_cama_delete_params' => tessera_imprimir_string($hashBlock['h_cama_delete_params'] ?? ''),
    ];
}

/**
 * @return array{
 *   url_nuevo_spec?: array{path?: string, query?: array<string, mixed>},
 *   a_links_dl_specs?: list<array{label?: string, spec?: array{path?: string, query?: array<string, mixed>}}>
 * }
 */
function ubiscamas_cdc_url_signing_input(mixed $urlNuevoSpec, mixed $aLinksDlSpecs): array
{
    $out = [];
    if (is_array($urlNuevoSpec)) {
        $spec = [];
        if (isset($urlNuevoSpec['path']) && is_string($urlNuevoSpec['path'])) {
            $spec['path'] = $urlNuevoSpec['path'];
        }
        $query = $urlNuevoSpec['query'] ?? null;
        if (is_array($query)) {
            $q = [];
            foreach ($query as $k => $v) {
                $q[(string) $k] = $v;
            }
            if ($q !== []) {
                $spec['query'] = $q;
            }
        }
        if ($spec !== []) {
            $out['url_nuevo_spec'] = $spec;
        }
    }
    $links = [];
    if (is_array($aLinksDlSpecs)) {
        foreach ($aLinksDlSpecs as $item) {
            if (!is_array($item)) {
                continue;
            }
            $entry = [];
            if (isset($item['label']) && is_string($item['label'])) {
                $entry['label'] = $item['label'];
            }
            $itemSpec = $item['spec'] ?? null;
            if (is_array($itemSpec)) {
                $s = [];
                if (isset($itemSpec['path']) && is_string($itemSpec['path'])) {
                    $s['path'] = $itemSpec['path'];
                }
                $sq = $itemSpec['query'] ?? null;
                if (is_array($sq)) {
                    $q = [];
                    foreach ($sq as $k => $v) {
                        $q[(string) $k] = $v;
                    }
                    if ($q !== []) {
                        $s['query'] = $q;
                    }
                }
                if ($s !== []) {
                    $entry['spec'] = $s;
                }
            }
            if ($entry !== []) {
                $links[] = $entry;
            }
        }
    }
    if ($links !== []) {
        $out['a_links_dl_specs'] = $links;
    }

    return $out;
}
