<?php

/**
 * Helpers compartidos del módulo frontend/encargossacd.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use frontend\shared\web\Desplegable;

function encargossacd_post_string(string $name, string $default = ''): string
{
    return tessera_imprimir_string(filter_input(INPUT_POST, $name), $default);
}

function encargossacd_post_int(string $name, int $default = 0): int
{
    $raw = filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT);

    return is_int($raw) ? $raw : $default;
}

function encargossacd_desplegable_opcion_sel(int|string $value): string
{
    return tessera_imprimir_string($value);
}

function encargossacd_desplegable_blanco(int|string|bool $value): bool|string
{
    if (is_bool($value)) {
        return $value;
    }
    if (is_int($value)) {
        return $value !== 0 ? '1' : false;
    }

    return tessera_imprimir_string($value);
}

/**
 * @return array<int|string, string>
 */
function encargossacd_desplegable_opciones(mixed $raw): array
{
    return notas_desplegable_opciones($raw);
}

/**
 * @return array<int, string>
 */
function encargossacd_string_list(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_int($key)) {
            $out[$key] = tessera_imprimir_string($value);
        }
    }

    return $out;
}

/**
 * @return list<array<string, mixed>>
 */
function encargossacd_colaboradores(mixed $raw): array
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
function encargossacd_listas_campos_from_payload(array $payload): array
{
    return [
        'cabecera_left' => tessera_imprimir_string($payload['cabecera_left'] ?? ''),
        'cabecera_right' => tessera_imprimir_string($payload['cabecera_right'] ?? ''),
        'cabecera_right_2' => tessera_imprimir_string($payload['cabecera_right_2'] ?? ''),
        'Html' => tessera_imprimir_string($payload['Html'] ?? ''),
    ];
}

/**
 * @return array<string, mixed>
 */
function encargossacd_payload_data(mixed $raw): array
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
function encargossacd_ctr_get_ficha_from_payload(array $payload): array
{
    $opcionesSssc = $payload['opciones_sacd_sssc'] ?? null;

    return [
        'mod' => tessera_imprimir_string($payload['mod'] ?? 'nuevo'),
        'tipo_centro' => tessera_imprimir_string($payload['tipo_centro'] ?? ''),
        'num_enc' => tessera_imprimir_int($payload['num_enc'] ?? 0),
        'chk_prelatura' => tessera_imprimir_string($payload['chk_prelatura'] ?? ''),
        'chk_de_paso' => tessera_imprimir_string($payload['chk_de_paso'] ?? ''),
        'chk_sssc' => tessera_imprimir_string($payload['chk_sssc'] ?? ''),
        'opciones_sacd' => encargossacd_desplegable_opciones($payload['opciones_sacd'] ?? []),
        'opciones_sacd_sssc' => is_array($opcionesSssc) ? encargossacd_desplegable_opciones($opcionesSssc) : null,
        'encargos' => encargossacd_encargos_from_payload($payload['encargos'] ?? null),
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
function encargossacd_encargos_from_payload(mixed $raw): array
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
            'id_enc' => tessera_imprimir_int($enc['id_enc'] ?? 0),
            'mod_horario' => tessera_imprimir_int($enc['mod_horario'] ?? 0),
            'sacd_num' => tessera_imprimir_int($enc['sacd_num'] ?? 1),
            'cl_checked' => tessera_imprimir_string($enc['cl_checked'] ?? ''),
            'observ' => tessera_imprimir_string($enc['observ'] ?? ''),
            'desc_enc' => tessera_imprimir_string($enc['desc_enc'] ?? ''),
            'dedic_m' => encargossacd_string_list($enc['dedic_m'] ?? []),
            'dedic_t' => encargossacd_string_list($enc['dedic_t'] ?? []),
            'dedic_v' => encargossacd_string_list($enc['dedic_v'] ?? []),
            'dedic_sacd' => encargossacd_string_list($enc['dedic_sacd'] ?? []),
            'dedic_ctr_m' => tessera_imprimir_string($enc['dedic_ctr_m'] ?? ''),
            'dedic_ctr_t' => tessera_imprimir_string($enc['dedic_ctr_t'] ?? ''),
            'dedic_ctr_v' => tessera_imprimir_string($enc['dedic_ctr_v'] ?? ''),
            'actual_id_sacd_titular' => tessera_imprimir_int($enc['actual_id_sacd_titular'] ?? 0),
            'actual_id_sacd_suplente' => tessera_imprimir_int($enc['actual_id_sacd_suplente'] ?? 0),
            'colaboradores' => encargossacd_colaboradores($enc['colaboradores'] ?? null),
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
function encargossacd_horario_row(mixed $raw): array
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
        'id_enc' => tessera_imprimir_int($raw['id_enc'] ?? 0),
        'id_item_h' => tessera_imprimir_int($raw['id_item_h'] ?? 0),
        'dia_num' => tessera_imprimir_string($raw['dia_num'] ?? ''),
        'dia_ref' => tessera_imprimir_string($raw['dia_ref'] ?? ''),
        'mas_menos' => tessera_imprimir_string($raw['mas_menos'] ?? ''),
        'dia_inc' => tessera_imprimir_string($raw['dia_inc'] ?? ''),
        'h_ini' => tessera_imprimir_string($raw['h_ini'] ?? ''),
        'h_fin' => tessera_imprimir_string($raw['h_fin'] ?? ''),
        'n_sacd' => tessera_imprimir_string($raw['n_sacd'] ?? ''),
        'mes' => tessera_imprimir_string($raw['mes'] ?? ''),
        'f_ini' => tessera_imprimir_string($raw['f_ini'] ?? ''),
        'f_fin' => tessera_imprimir_string($raw['f_fin'] ?? ''),
        'excep' => tessera_imprimir_string($raw['excep'] ?? ''),
        'texto_horario' => tessera_imprimir_string($raw['texto_horario'] ?? ''),
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
function encargossacd_encargo_select_row(mixed $raw): array
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
        'id_enc' => tessera_imprimir_int($raw['id_enc'] ?? 0),
        'sf_sv' => tessera_imprimir_int($raw['sf_sv'] ?? 0),
        'desc_enc' => tessera_imprimir_string($raw['desc_enc'] ?? ''),
        'seccion' => tessera_imprimir_string($raw['seccion'] ?? ''),
        'nombre_ubi' => tessera_imprimir_string($raw['nombre_ubi'] ?? ''),
        'desc_lugar' => tessera_imprimir_string($raw['desc_lugar'] ?? ''),
        'idioma' => tessera_imprimir_string($raw['idioma'] ?? ''),
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
function encargossacd_ausencia_row(mixed $raw): array
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
        'id_enc' => tessera_imprimir_int($raw['id_enc'] ?? 0),
        'id_tipo_enc' => tessera_imprimir_int($raw['id_tipo_enc'] ?? 0),
        'desc_enc' => tessera_imprimir_string($raw['desc_enc'] ?? ''),
        'id_item' => tessera_imprimir_int($raw['id_item'] ?? 0),
        'inicio' => tessera_imprimir_string($raw['inicio'] ?? ''),
        'fin' => tessera_imprimir_string($raw['fin'] ?? ''),
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
function encargossacd_horario_ver_from_payload(array $payload): array
{
    return [
        'f_ini' => tessera_imprimir_string($payload['f_ini'] ?? ''),
        'f_fin' => tessera_imprimir_string($payload['f_fin'] ?? ''),
        'dia_ref' => tessera_imprimir_string($payload['dia_ref'] ?? ''),
        'dia_num' => tessera_imprimir_string($payload['dia_num'] ?? ''),
        'mas_menos' => tessera_imprimir_string($payload['mas_menos'] ?? ''),
        'dia_inc' => tessera_imprimir_string($payload['dia_inc'] ?? ''),
        'h_ini' => tessera_imprimir_string($payload['h_ini'] ?? ''),
        'h_fin' => tessera_imprimir_string($payload['h_fin'] ?? ''),
        'n_sacd' => tessera_imprimir_string($payload['n_sacd'] ?? ''),
        'mes' => tessera_imprimir_string($payload['mes'] ?? ''),
        'id_item_h' => tessera_imprimir_string($payload['id_item_h'] ?? ''),
        'dia' => tessera_imprimir_string($payload['dia'] ?? ''),
        'opciones_dia_semana' => encargossacd_desplegable_opciones($payload['opciones_dia_semana'] ?? []),
        'opciones_dia_ref' => encargossacd_desplegable_opciones($payload['opciones_dia_ref'] ?? []),
        'opciones_ordinales' => encargossacd_desplegable_opciones($payload['opciones_ordinales'] ?? []),
    ];
}

function encargossacd_listas_com_txt_response(mixed $data): string
{
    if (is_array($data) && array_key_exists('texto', $data)) {
        return tessera_imprimir_string($data['texto']);
    }

    return is_string($data) ? $data : '';
}

function encargossacd_comprobaciones_texto(mixed $data): string
{
    if (is_array($data) && isset($data['texto'])) {
        return tessera_imprimir_string($data['texto']);
    }

    return is_string($data) ? $data : '';
}

function encargossacd_post_sel_id_item_h(int $fallback = 0): int
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!is_array($a_sel_raw) || $a_sel_raw === []) {
        return $fallback;
    }
    $sel0 = $a_sel_raw[0];
    if (!is_string($sel0) || $sel0 === '') {
        return $fallback;
    }
    $parts = explode('#', $sel0, 2);

    return is_numeric($parts[0]) ? (int) $parts[0] : $fallback;
}

function encargossacd_sel_id_from_post(string $fallbackField = 'id_enc'): int
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);

            return is_numeric($parts[0]) ? (int) $parts[0] : 0;
        }
    }

    return encargossacd_post_int($fallbackField);
}

/**
 * Construye el bloque HTML de colaboradores (sacd adicionales) de un encargo.
 *
 * @param array<int, array<string, mixed>> $colaboradores
 * @param array<int, string>               $dedicM
 * @param array<int, string>               $dedicT
 * @param array<int, string>               $dedicV
 * @param array<int, string>               $dedicSacd
 * @param array<int|string, string>        $opcionesBase
 * @param array<int|string, string>|null   $opcionesConSssc
 */
function encargossacd_construir_otros_sacd(
    int $e,
    int $mod_horario_e,
    array $colaboradores,
    array $dedicM,
    array $dedicT,
    array $dedicV,
    array $dedicSacd,
    array $opcionesBase,
    ?array $opcionesConSssc,
): string {
    if ($colaboradores === []) {
        return '';
    }

    $html = '';
    foreach ($colaboradores as $colab) {
        $s = tessera_imprimir_int($colab['s'] ?? 0);
        $id_nom = tessera_imprimir_int($colab['id_nom'] ?? 0);
        $necesitaSssc = !empty($colab['necesita_sssc']);

        $opciones = $necesitaSssc && $opcionesConSssc !== null ? $opcionesConSssc : $opcionesBase;
        $oDespl = new Desplegable();
        $oDespl->setBlanco(true);
        $oDespl->setOpciones($opciones);
        $oDespl->setOpcion_sel(encargossacd_desplegable_opcion_sel($id_nom));

        $html .= "<tr><td>sacd $s:</td><td colspan=3 class=contenido><select name=id_sacd[$s]>";
        $html .= $oDespl->options();
        $html .= '</td></tr><tr><td class=etiqueta >' . ucfirst(_('dedicación')) . '</td>';

        if ($mod_horario_e === 3) {
            $txtHorario = tessera_imprimir_string($dedicSacd[$s] ?? '');
            $html .= '<td>' . $txtHorario . '</td></tr><tr>';
        } else {
            $m = tessera_imprimir_string($dedicM[$s] ?? '');
            $t = tessera_imprimir_string($dedicT[$s] ?? '');
            $v = tessera_imprimir_string($dedicV[$s] ?? '');
            $html .= "<td><input type=text size=1 name=dedic_m[$s] value=$m>" . _('mañanas');
            $html .= "</td><td><input type=text size=1 name=dedic_t[$s] value=$t>" . _('tarde 1ª hora');
            $html .= "</td><td><input type=text size=1 name=dedic_v[$s] value=$v>" . _('tarde 2ª hora');
            $html .= '</td></tr><tr>';
        }
    }

    return $html;
}

function encargossacd_sacd_ficha_encargo_id_ubi(mixed $raw): int
{
    if (!is_array($raw)) {
        return 0;
    }

    return tessera_imprimir_int($raw['id_ubi'] ?? 0);
}

/**
 * @return list<array<string, mixed>>
 */
function encargossacd_sacd_ficha_encargos_from_payload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $enc) {
        if (!is_array($enc)) {
            continue;
        }
        $enc['id_ubi'] = tessera_imprimir_int($enc['id_ubi'] ?? 0);
        $out[] = $enc;
    }

    return $out;
}
