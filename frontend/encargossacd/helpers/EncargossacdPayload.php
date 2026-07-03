<?php

declare(strict_types=1);

namespace frontend\encargossacd\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;

final class EncargossacdPayload
{
public static function desplegableOpcionSel(int|string $value): string
{
    return PayloadCoercion::string($value);
}

public static function desplegableBlanco(int|string|bool $value): bool|string
{
    if (is_bool($value)) {
        return $value;
    }
    if (is_int($value)) {
        return $value !== 0 ? '1' : false;
    }

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
 * @return array<int, string>
 */
public static function stringList(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_int($key)) {
            $out[$key] = PayloadCoercion::string($value);
        }
    }

    return $out;
}

/**
 * @return list<array<string, mixed>>
 */
public static function colaboradores(mixed $raw): array
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
 * @param array<int|string, mixed> $payload
 * @return array{cabecera_left: string, cabecera_right: string, cabecera_right_2: string, Html: string}
 */
public static function listasCamposFromPayload(array $payload): array
{
    return [
        'cabecera_left' => PayloadCoercion::string($payload['cabecera_left'] ?? ''),
        'cabecera_right' => PayloadCoercion::string($payload['cabecera_right'] ?? ''),
        'cabecera_right_2' => PayloadCoercion::string($payload['cabecera_right_2'] ?? ''),
        'Html' => PayloadCoercion::string($payload['Html'] ?? ''),
    ];
}

/**
 * @return array<string, mixed>
 */
public static function payloadData(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     mod: string,
 *     tipo_centro: string,
 *     num_enc: int,
 *     chk_prelatura: string,
 *     chk_de_paso: string,
 *     chk_sssc: string,
 *     opciones_sacd: array<int|string, string>,
 *     opciones_sacd_sssc: array<int|string, string>|null,
 *     encargos: list<array{
 *         id_enc: int,
 *         mod_horario: int,
 *         sacd_num: int,
 *         cl_checked: string,
 *         observ: string,
 *         desc_enc: string,
 *         dedic_m: array<int, string>,
 *         dedic_t: array<int, string>,
 *         dedic_v: array<int, string>,
 *         dedic_sacd: array<int, string>,
 *         dedic_ctr_m: string,
 *         dedic_ctr_t: string,
 *         dedic_ctr_v: string,
 *         actual_id_sacd_titular: int,
 *         actual_id_sacd_suplente: int,
 *         colaboradores: array<int, array<string, mixed>>,
 *     }>,
 *     perm_des: bool,
 * }
 */
public static function ctrGetFichaFromPayload(array $payload): array
{
    $opcionesSssc = $payload['opciones_sacd_sssc'] ?? null;

    return [
        'mod' => PayloadCoercion::string($payload['mod'] ?? 'nuevo'),
        'tipo_centro' => PayloadCoercion::string($payload['tipo_centro'] ?? ''),
        'num_enc' => PayloadCoercion::int($payload['num_enc'] ?? 0),
        'chk_prelatura' => PayloadCoercion::string($payload['chk_prelatura'] ?? ''),
        'chk_de_paso' => PayloadCoercion::string($payload['chk_de_paso'] ?? ''),
        'chk_sssc' => PayloadCoercion::string($payload['chk_sssc'] ?? ''),
        'opciones_sacd' => self::desplegableOpciones($payload['opciones_sacd'] ?? []),
        'opciones_sacd_sssc' => is_array($opcionesSssc) ? self::desplegableOpciones($opcionesSssc) : null,
        'encargos' => self::encargosFromPayload($payload['encargos'] ?? null),
        'perm_des' => !empty($payload['perm_des']),
    ];
}

/**
 * @return list<array{
 *     id_enc: int,
 *     mod_horario: int,
 *     sacd_num: int,
 *     cl_checked: string,
 *     observ: string,
 *     desc_enc: string,
 *     dedic_m: array<int, string>,
 *     dedic_t: array<int, string>,
 *     dedic_v: array<int, string>,
 *     dedic_sacd: array<int, string>,
 *     dedic_ctr_m: string,
 *     dedic_ctr_t: string,
 *     dedic_ctr_v: string,
 *     actual_id_sacd_titular: int,
 *     actual_id_sacd_suplente: int,
 *     colaboradores: array<int, array<string, mixed>>,
 * }>
 */
public static function encargosFromPayload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $enc) {
        if (!is_array($enc)) {
            continue;
        }
        $out[] = [
            'id_enc' => PayloadCoercion::int($enc['id_enc'] ?? 0),
            'mod_horario' => PayloadCoercion::int($enc['mod_horario'] ?? 0),
            'sacd_num' => PayloadCoercion::int($enc['sacd_num'] ?? 1),
            'cl_checked' => PayloadCoercion::string($enc['cl_checked'] ?? ''),
            'observ' => PayloadCoercion::string($enc['observ'] ?? ''),
            'desc_enc' => PayloadCoercion::string($enc['desc_enc'] ?? ''),
            'dedic_m' => self::stringList($enc['dedic_m'] ?? []),
            'dedic_t' => self::stringList($enc['dedic_t'] ?? []),
            'dedic_v' => self::stringList($enc['dedic_v'] ?? []),
            'dedic_sacd' => self::stringList($enc['dedic_sacd'] ?? []),
            'dedic_ctr_m' => PayloadCoercion::string($enc['dedic_ctr_m'] ?? ''),
            'dedic_ctr_t' => PayloadCoercion::string($enc['dedic_ctr_t'] ?? ''),
            'dedic_ctr_v' => PayloadCoercion::string($enc['dedic_ctr_v'] ?? ''),
            'actual_id_sacd_titular' => PayloadCoercion::int($enc['actual_id_sacd_titular'] ?? 0),
            'actual_id_sacd_suplente' => PayloadCoercion::int($enc['actual_id_sacd_suplente'] ?? 0),
            'colaboradores' => self::colaboradores($enc['colaboradores'] ?? null),
        ];
    }

    return $out;
}

/**
 * @return array{
 *     id_enc: int,
 *     id_item_h: int,
 *     dia_num: string,
 *     dia_ref: string,
 *     mas_menos: string,
 *     dia_inc: string,
 *     h_ini: string,
 *     h_fin: string,
 *     n_sacd: string,
 *     mes: string,
 *     f_ini: string,
 *     f_fin: string,
 *     excep: string,
 *     texto_horario: string,
 * }
 */
public static function horarioRow(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_enc' => 0,
            'id_item_h' => 0,
            'dia_num' => '',
            'dia_ref' => '',
            'mas_menos' => '',
            'dia_inc' => '',
            'h_ini' => '',
            'h_fin' => '',
            'n_sacd' => '',
            'mes' => '',
            'f_ini' => '',
            'f_fin' => '',
            'excep' => '',
            'texto_horario' => '',
        ];
    }

    return [
        'id_enc' => PayloadCoercion::int($raw['id_enc'] ?? 0),
        'id_item_h' => PayloadCoercion::int($raw['id_item_h'] ?? 0),
        'dia_num' => PayloadCoercion::string($raw['dia_num'] ?? ''),
        'dia_ref' => PayloadCoercion::string($raw['dia_ref'] ?? ''),
        'mas_menos' => PayloadCoercion::string($raw['mas_menos'] ?? ''),
        'dia_inc' => PayloadCoercion::string($raw['dia_inc'] ?? ''),
        'h_ini' => PayloadCoercion::string($raw['h_ini'] ?? ''),
        'h_fin' => PayloadCoercion::string($raw['h_fin'] ?? ''),
        'n_sacd' => PayloadCoercion::string($raw['n_sacd'] ?? ''),
        'mes' => PayloadCoercion::string($raw['mes'] ?? ''),
        'f_ini' => PayloadCoercion::string($raw['f_ini'] ?? ''),
        'f_fin' => PayloadCoercion::string($raw['f_fin'] ?? ''),
        'excep' => PayloadCoercion::string($raw['excep'] ?? ''),
        'texto_horario' => PayloadCoercion::string($raw['texto_horario'] ?? ''),
    ];
}

/**
 * @return array{
 *     id_enc: int,
 *     sf_sv: int,
 *     desc_enc: string,
 *     seccion: string,
 *     nombre_ubi: string,
 *     desc_lugar: string,
 *     idioma: string,
 * }
 */
public static function encargoSelectRow(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_enc' => 0,
            'sf_sv' => 0,
            'desc_enc' => '',
            'seccion' => '',
            'nombre_ubi' => '',
            'desc_lugar' => '',
            'idioma' => '',
        ];
    }

    return [
        'id_enc' => PayloadCoercion::int($raw['id_enc'] ?? 0),
        'sf_sv' => PayloadCoercion::int($raw['sf_sv'] ?? 0),
        'desc_enc' => PayloadCoercion::string($raw['desc_enc'] ?? ''),
        'seccion' => PayloadCoercion::string($raw['seccion'] ?? ''),
        'nombre_ubi' => PayloadCoercion::string($raw['nombre_ubi'] ?? ''),
        'desc_lugar' => PayloadCoercion::string($raw['desc_lugar'] ?? ''),
        'idioma' => PayloadCoercion::string($raw['idioma'] ?? ''),
    ];
}

/**
 * @return array{
 *     id_enc: int,
 *     id_tipo_enc: int,
 *     desc_enc: string,
 *     id_item: int,
 *     inicio: string,
 *     fin: string,
 * }
 */
public static function ausenciaRow(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_enc' => 0,
            'id_tipo_enc' => 0,
            'desc_enc' => '',
            'id_item' => 0,
            'inicio' => '',
            'fin' => '',
        ];
    }

    return [
        'id_enc' => PayloadCoercion::int($raw['id_enc'] ?? 0),
        'id_tipo_enc' => PayloadCoercion::int($raw['id_tipo_enc'] ?? 0),
        'desc_enc' => PayloadCoercion::string($raw['desc_enc'] ?? ''),
        'id_item' => PayloadCoercion::int($raw['id_item'] ?? 0),
        'inicio' => PayloadCoercion::string($raw['inicio'] ?? ''),
        'fin' => PayloadCoercion::string($raw['fin'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     f_ini: string,
 *     f_fin: string,
 *     dia_ref: string,
 *     dia_num: string,
 *     mas_menos: string,
 *     dia_inc: string,
 *     h_ini: string,
 *     h_fin: string,
 *     n_sacd: string,
 *     mes: string,
 *     id_item_h: string,
 *     dia: string,
 *     opciones_dia_semana: array<int|string, string>,
 *     opciones_dia_ref: array<int|string, string>,
 *     opciones_ordinales: array<int|string, string>,
 * }
 */
public static function horarioVerFromPayload(array $payload): array
{
    return [
        'f_ini' => PayloadCoercion::string($payload['f_ini'] ?? ''),
        'f_fin' => PayloadCoercion::string($payload['f_fin'] ?? ''),
        'dia_ref' => PayloadCoercion::string($payload['dia_ref'] ?? ''),
        'dia_num' => PayloadCoercion::string($payload['dia_num'] ?? ''),
        'mas_menos' => PayloadCoercion::string($payload['mas_menos'] ?? ''),
        'dia_inc' => PayloadCoercion::string($payload['dia_inc'] ?? ''),
        'h_ini' => PayloadCoercion::string($payload['h_ini'] ?? ''),
        'h_fin' => PayloadCoercion::string($payload['h_fin'] ?? ''),
        'n_sacd' => PayloadCoercion::string($payload['n_sacd'] ?? ''),
        'mes' => PayloadCoercion::string($payload['mes'] ?? ''),
        'id_item_h' => PayloadCoercion::string($payload['id_item_h'] ?? ''),
        'dia' => PayloadCoercion::string($payload['dia'] ?? ''),
        'opciones_dia_semana' => self::desplegableOpciones($payload['opciones_dia_semana'] ?? []),
        'opciones_dia_ref' => self::desplegableOpciones($payload['opciones_dia_ref'] ?? []),
        'opciones_ordinales' => self::desplegableOpciones($payload['opciones_ordinales'] ?? []),
    ];
}

public static function listasComTxtResponse(mixed $data): string
{
    if (is_array($data) && array_key_exists('texto', $data)) {
        return PayloadCoercion::string($data['texto']);
    }

    return is_string($data) ? $data : '';
}

public static function comprobacionesTexto(mixed $data): string
{
    if (is_array($data) && isset($data['texto'])) {
        return PayloadCoercion::string($data['texto']);
    }

    return is_string($data) ? $data : '';
}
public static function sacdFichaEncargoIdUbi(mixed $raw): int
{
    if (!is_array($raw)) {
        return 0;
    }

    return PayloadCoercion::int($raw['id_ubi'] ?? 0);
}

/**
 * @return list<array<string, mixed>>
 */
public static function sacdFichaEncargosFromPayload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $enc) {
        if (!is_array($enc)) {
            continue;
        }
        $enc['id_ubi'] = PayloadCoercion::int($enc['id_ubi'] ?? 0);
        $out[] = $enc;
    }

    return $out;
}
}
