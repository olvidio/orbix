<?php

declare(strict_types=1);

namespace frontend\ubiscamas\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\security\HashFrontSignedLink;

final class UbiscamasPayload
{
/**
 * @return array<string, mixed>
 */
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

/**
 * @return list<array{id_nom: int, apellidos: string}>
 */
public static function asistentesSinCamaList(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        if (!is_array($item)) {
            continue;
        }
        $idNom = \frontend\shared\helpers\PayloadCoercion::int($item['id_nom'] ?? 0);
        $apellidos = \frontend\shared\helpers\PayloadCoercion::string($item['apellidos'] ?? '');
        if ($idNom <= 0 && $apellidos === '') {
            continue;
        }
        $out[] = [
            'id_nom' => $idNom,
            'apellidos' => $apellidos,
        ];
    }

    return $out;
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
 * @return array{path: string, query?: array<string, mixed>}|null
 */
public static function linkSpec(mixed $raw): ?array
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
 *     asistentes_sin_cama: list<array{id_nom: int, apellidos: string}>,
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
public static function habitacionesListaFromPayload(array $data): array
{
    $signed = [
        'reload_main_url' => HashFrontSignedLink::tryFromSpec($data['reload_main_link_spec'] ?? null),
        'distribucion_open_url' => HashFrontSignedLink::tryFromSpec($data['distribucion_open_link_spec'] ?? null),
        'nombres_open_url' => HashFrontSignedLink::tryFromSpec($data['nombres_open_link_spec'] ?? null),
    ];

    return [
        'cabeceras' => ActividadesListaSupport::cabeceras($data['a_cabeceras'] ?? []),
        'botones' => ActividadesListaSupport::botones($data['a_botones'] ?? []),
        'valores' => ActividadesListaSupport::datos($data['a_valores'] ?? []),
        'id_activ' => NotasFormSupport::formScalar($data['id_activ'] ?? 0),
        'id_ubi' => NotasFormSupport::formScalar($data['id_ubi'] ?? 0),
        'habitaciones_con_camas' => ActividadesListaSupport::datos($data['habitaciones_con_camas'] ?? []),
        'camas_con_asistentes' => ActividadesListaSupport::datos($data['camas_con_asistentes'] ?? []),
        'asistentes_sin_cama' => self::asistentesSinCamaList($data['asistentes_sin_cama'] ?? []),
        'solo_vip' => NotasFormSupport::formBoolOrString($data['solo_vip'] ?? ''),
        'url_update_cama_full' => \frontend\shared\helpers\PayloadCoercion::string($data['url_update_cama_full'] ?? ''),
        'ctx_update_cama' => \frontend\shared\helpers\PayloadCoercion::string($data['ctx_update_cama'] ?? ''),
        'update_solo_vip_full_url' => \frontend\shared\helpers\PayloadCoercion::string($data['update_solo_vip_full_url'] ?? ''),
        'ctx_update_solo_vip' => \frontend\shared\helpers\PayloadCoercion::string($data['ctx_update_solo_vip'] ?? ''),
        'reload_main_url' => $signed['reload_main_url'],
        'distribucion_open_url' => $signed['distribucion_open_url'],
        'nombres_open_url' => $signed['nombres_open_url'],
    ];
}

/**
 * @param array<string, mixed> $data
 * @return list<array{nombre: string, planta: string, habitacion: string}>
 */
public static function nombresListaFromPayload(array $data): array
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
            $idCama = \frontend\shared\helpers\PayloadCoercion::int($aCama['id_cama'] ?? 0);
            $nombrePersona = '';
            $asistenteRaw = $camasAsistentes[$idCama] ?? null;
            if (is_array($asistenteRaw)) {
                $nombrePersona = \frontend\shared\helpers\PayloadCoercion::string($asistenteRaw['apellidos'] ?? '');
            }
            if ($nombrePersona === '') {
                continue;
            }
            $aLista[] = [
                'nombre' => $nombrePersona,
                'planta' => \frontend\shared\helpers\PayloadCoercion::string($aHabitacionRaw['planta'] ?? ''),
                'habitacion' => \frontend\shared\helpers\PayloadCoercion::string($aHabitacionRaw['nombre'] ?? ''),
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
public static function camaFormViewFromPayload(array $payload, array $hashBlock): array
{
    return [
        'hash_form_html' => \frontend\shared\helpers\PayloadCoercion::string($hashBlock['hash_form_html'] ?? ''),
        'cama_update_url' => \frontend\shared\helpers\PayloadCoercion::string($hashBlock['cama_update_url'] ?? ''),
        'id_cama' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_cama'] ?? ''),
        'id_habitacion' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_habitacion'] ?? ''),
        'id_ubi' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_ubi'] ?? 0),
        'descripcion' => \frontend\shared\helpers\PayloadCoercion::string($payload['descripcion'] ?? ''),
        'larga' => !empty($payload['larga']),
        'vip' => !empty($payload['vip']),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @param array<string, mixed> $hashBlock
 * @return array<string, mixed>
 */
public static function habitacionFormViewFromPayload(array $payload, array $hashBlock): array
{
    return [
        'hash_form_html' => \frontend\shared\helpers\PayloadCoercion::string($hashBlock['hash_form_html'] ?? ''),
        'hash_actualizar_html' => \frontend\shared\helpers\PayloadCoercion::string($hashBlock['hash_actualizar_html'] ?? ''),
        'id_habitacion' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_habitacion'] ?? ''),
        'id_ubi' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_ubi'] ?? 0),
        'orden' => NotasFormSupport::formScalar($payload['orden'] ?? ''),
        'nombre' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombre'] ?? ''),
        'numero_camas' => NotasFormSupport::formScalar($payload['numero_camas'] ?? ''),
        'numero_camas_vip' => NotasFormSupport::formScalar($payload['numero_camas_vip'] ?? ''),
        'planta' => \frontend\shared\helpers\PayloadCoercion::string($payload['planta'] ?? ''),
        'sillon' => !empty($payload['sillon']),
        'adaptada' => !empty($payload['adaptada']),
        'observaciones' => \frontend\shared\helpers\PayloadCoercion::string($payload['observaciones'] ?? ''),
        'despacho' => !empty($payload['despacho']),
        'tipoLavabo' => $payload['tipoLavabo'] ?? null,
        'a_tipos_tipoLavabo' => NotasFormSupport::desplegableOpciones($payload['a_tipos_tipoLavabo'] ?? []),
        'a_camas' => is_array($payload['a_camas'] ?? null) ? $payload['a_camas'] : [],
        'url_cama_form' => \frontend\shared\helpers\PayloadCoercion::string($hashBlock['url_cama_form'] ?? ''),
        'h_cama_form_params' => \frontend\shared\helpers\PayloadCoercion::string($hashBlock['h_cama_form_params'] ?? ''),
        'url_cama_delete' => \frontend\shared\helpers\PayloadCoercion::string($hashBlock['url_cama_delete'] ?? ''),
        'h_cama_delete_params' => \frontend\shared\helpers\PayloadCoercion::string($hashBlock['h_cama_delete_params'] ?? ''),
    ];
}

/**
 * @return array{
 *   url_nuevo_spec?: array{path?: string, query?: array<string, mixed>},
 *   a_links_dl_specs?: list<array{label?: string, spec?: array{path?: string, query?: array<string, mixed>}}>
 * }
 */
public static function cdcUrlSigningInput(mixed $urlNuevoSpec, mixed $aLinksDlSpecs): array
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
}
