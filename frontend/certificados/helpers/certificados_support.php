<?php

/**
 * Helpers compartidos del módulo frontend/certificados.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use frontend\shared\security\HashFrontSignedLink;
use src\configuracion\domain\value_objects\ConfigSnapshot;

/**
 * @return array<string, mixed>
 */
function certificados_post_data(mixed $data): array
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
function certificados_hash_campos_hidden(mixed $raw): array
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

function certificados_o_config(): ?ConfigSnapshot
{
    $oConfig = $_SESSION['oConfig'] ?? null;

    return $oConfig instanceof ConfigSnapshot ? $oConfig : null;
}

function certificados_id_item_from_sel_post(): int
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);

            return is_numeric($parts[0]) ? (int) $parts[0] : 0;
        }
    }
    $idRaw = filter_input(INPUT_POST, 'id_item', FILTER_VALIDATE_INT);

    return is_int($idRaw) ? $idRaw : 0;
}

function certificados_id_nom_from_sel_post(): int
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);

            return is_numeric($parts[0]) ? (int) $parts[0] : 0;
        }
    }
    $idRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

    return is_int($idRaw) ? $idRaw : 0;
}

/**
 * @return array{path: string, query?: array<string, mixed>}|null
 */
function certificados_link_spec(mixed $raw): ?array
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
 * @return array<string, string>
 */
function certificados_latin_replace_map(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $k => $v) {
        if (is_string($k)) {
            $out[$k] = tessera_imprimir_string($v);
        }
    }

    return $out;
}

function certificados_creditos_float(mixed $value): float
{
    if (is_int($value) || is_float($value)) {
        return (float) $value;
    }
    if (is_string($value) && is_numeric($value)) {
        return (float) $value;
    }

    return 0.0;
}

/**
 * @return array{id_asignatura: int, id_nivel: int, nombre_asignatura: string, creditos: float}
 */
function certificados_asignatura_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return ['id_asignatura' => 0, 'id_nivel' => 0, 'nombre_asignatura' => '', 'creditos' => 0.0];
    }

    return [
        'id_asignatura' => tessera_imprimir_int($raw['id_asignatura'] ?? 0),
        'id_nivel' => tessera_imprimir_int($raw['id_nivel'] ?? 0),
        'nombre_asignatura' => tessera_imprimir_string($raw['nombre_asignatura'] ?? ''),
        'creditos' => certificados_creditos_float($raw['creditos'] ?? 0),
    ];
}

/**
 * @return list<array{id_asignatura: int, id_nivel: int, nombre_asignatura: string, creditos: float}>
 */
function certificados_asignaturas_from_json(mixed $raw): array
{
    if (!is_string($raw) || $raw === '') {
        return [];
    }
    $decoded = json_decode($raw);
    if (!is_array($decoded)) {
        return [];
    }
    $out = [];
    foreach ($decoded as $item) {
        $rowRaw = null;
        if (is_string($item)) {
            $rowRaw = json_decode($item, true);
        } elseif (is_array($item)) {
            $rowRaw = $item;
        } elseif (is_object($item)) {
            $rowRaw = (array) $item;
        }
        $out[] = certificados_asignatura_row($rowRaw);
    }

    return $out;
}

/**
 * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}
 */
function certificados_aprobada_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_nivel_asig' => 0,
            'id_nivel' => 0,
            'id_asignatura' => 0,
            'nombre_asignatura' => '',
            'creditos' => '',
            'nota_txt' => '',
        ];
    }

    return [
        'id_nivel_asig' => tessera_imprimir_int($raw['id_nivel_asig'] ?? 0),
        'id_nivel' => tessera_imprimir_int($raw['id_nivel'] ?? 0),
        'id_asignatura' => tessera_imprimir_int($raw['id_asignatura'] ?? 0),
        'nombre_asignatura' => tessera_imprimir_string($raw['nombre_asignatura'] ?? ''),
        'creditos' => tessera_imprimir_string($raw['creditos'] ?? ''),
        'nota_txt' => tessera_imprimir_string($raw['nota_txt'] ?? ''),
    ];
}

/**
 * @return array<int, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}>
 */
function certificados_aprobadas_from_payload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $item) {
        if (is_int($key)) {
            $out[$key] = certificados_aprobada_row($item);
        } elseif (is_numeric($key)) {
            $out[(int) $key] = certificados_aprobada_row($item);
        }
    }

    return $out;
}

/**
 * @param array<int, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}> $aAprobadas
 * @param array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string} $rowEmpty
 * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}
 */
function certificados_current_aprobada_row(array $aAprobadas, array $rowEmpty): array
{
    if ($aAprobadas === []) {
        return $rowEmpty;
    }

    return certificados_aprobada_row(current($aAprobadas));
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     id_nom: int,
 *     nom: string,
 *     certificado: string,
 *     lugar_fecha: string,
 *     vstgr: string,
 *     dir_stgr: string,
 *     replace: array<string, string>,
 *     txt_superavit: string,
 *     curso_filosofia: string,
 *     any_I: string,
 *     ECTS: string,
 *     iudicium: string,
 *     curso_teologia: string,
 *     pie_ects: string,
 *     any_II: string,
 *     any_III: string,
 *     any_IV: string,
 *     titulo_1: string,
 *     titulo_2: string,
 *     titulo_3: string,
 *     infra: string,
 *     sello: string,
 *     fidem: string,
 *     reg_num: string,
 *     cAsignaturas: list<array{id_asignatura: int, id_nivel: int, nombre_asignatura: string, creditos: float}>,
 *     aAprobadas: array<int, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}>,
 * }
 */
function certificados_mpdf_from_payload(array $payload): array
{
    return [
        'id_nom' => tessera_imprimir_int($payload['id_nom'] ?? 0),
        'nom' => tessera_imprimir_string($payload['nom'] ?? ''),
        'certificado' => tessera_imprimir_string($payload['certificado'] ?? ''),
        'lugar_fecha' => tessera_imprimir_string($payload['lugar_fecha'] ?? ''),
        'vstgr' => tessera_imprimir_string($payload['vstgr'] ?? ''),
        'dir_stgr' => tessera_imprimir_string($payload['dir_stgr'] ?? ''),
        'replace' => certificados_latin_replace_map($payload['replace'] ?? []),
        'txt_superavit' => tessera_imprimir_string($payload['txt_superavit'] ?? ''),
        'curso_filosofia' => tessera_imprimir_string($payload['curso_filosofia'] ?? ''),
        'any_I' => tessera_imprimir_string($payload['any_I'] ?? ''),
        'ECTS' => tessera_imprimir_string($payload['ECTS'] ?? ''),
        'iudicium' => tessera_imprimir_string($payload['iudicium'] ?? ''),
        'curso_teologia' => tessera_imprimir_string($payload['curso_teologia'] ?? ''),
        'pie_ects' => tessera_imprimir_string($payload['pie_ects'] ?? ''),
        'any_II' => tessera_imprimir_string($payload['any_II'] ?? ''),
        'any_III' => tessera_imprimir_string($payload['any_III'] ?? ''),
        'any_IV' => tessera_imprimir_string($payload['any_IV'] ?? ''),
        'titulo_1' => tessera_imprimir_string($payload['titulo_1'] ?? ''),
        'titulo_2' => tessera_imprimir_string($payload['titulo_2'] ?? ''),
        'titulo_3' => tessera_imprimir_string($payload['titulo_3'] ?? ''),
        'infra' => tessera_imprimir_string($payload['infra'] ?? ''),
        'sello' => tessera_imprimir_string($payload['sello'] ?? ''),
        'fidem' => tessera_imprimir_string($payload['fidem'] ?? ''),
        'reg_num' => tessera_imprimir_string($payload['reg_num'] ?? ''),
        'cAsignaturas' => certificados_asignaturas_from_json($payload['cAsignaturas'] ?? ''),
        'aAprobadas' => certificados_aprobadas_from_payload($payload['aAprobadas'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     cabeceras: list<array<string, mixed>|string>,
 *     botones: list<array<string, mixed>>,
 *     valores: array<int|string, mixed>,
 * }
 */
function certificados_emitido_lista_tabla_from_payload(array $payload): array
{
    return [
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'botones' => actividades_lista_botones($payload['a_botones'] ?? []),
        'valores' => actividades_lista_datos($payload['a_valores'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     id_nom: int,
 *     nom: string,
 *     idioma: string,
 *     destino: string,
 *     certificado: string,
 *     f_certificado: string,
 *     f_enviado: string,
 *     firmado: mixed,
 *     content: string,
 *     apellidos_nombre: string,
 * }
 */
function certificados_emitido_ver_from_payload(array $payload): array
{
    $contentRaw = $payload['content'] ?? '';
    $content = is_string($contentRaw) ? $contentRaw : '';

    return [
        'id_nom' => tessera_imprimir_int($payload['id_nom'] ?? 0),
        'nom' => tessera_imprimir_string($payload['nom'] ?? ''),
        'idioma' => tessera_imprimir_string($payload['idioma'] ?? ''),
        'destino' => tessera_imprimir_string($payload['destino'] ?? ''),
        'certificado' => tessera_imprimir_string($payload['certificado'] ?? ''),
        'f_certificado' => tessera_imprimir_string($payload['f_certificado'] ?? ''),
        'f_enviado' => tessera_imprimir_string($payload['f_enviado'] ?? ''),
        'firmado' => $payload['firmado'] ?? false,
        'content' => $content,
        'apellidos_nombre' => tessera_imprimir_string($payload['apellidos_nombre'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{aviso: string, nom: string, f_enviado: string}
 */
function certificados_adjuntar_form_from_payload(array $payload): array
{
    return [
        'aviso' => tessera_imprimir_string($payload['aviso'] ?? ''),
        'nom' => tessera_imprimir_string($payload['nom'] ?? ''),
        'f_enviado' => tessera_imprimir_string($payload['f_enviado'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     id_nom: int,
 *     id_item: int,
 *     nom: string,
 *     idioma: string,
 *     destino: string,
 *     certificado: string,
 *     f_certificado: string,
 *     f_recibido: string,
 *     chk_firmado: string,
 *     a_locales: array<int|string, string>,
 * }
 */
function certificados_recibido_form_from_payload(array $payload): array
{
    return [
        'id_nom' => tessera_imprimir_int($payload['id_nom'] ?? 0),
        'id_item' => tessera_imprimir_int($payload['id_item'] ?? 0),
        'nom' => tessera_imprimir_string($payload['nom'] ?? ''),
        'idioma' => tessera_imprimir_string($payload['idioma'] ?? ''),
        'destino' => tessera_imprimir_string($payload['destino'] ?? ''),
        'certificado' => tessera_imprimir_string($payload['certificado'] ?? ''),
        'f_certificado' => tessera_imprimir_string($payload['f_certificado'] ?? ''),
        'f_recibido' => tessera_imprimir_string($payload['f_recibido'] ?? ''),
        'chk_firmado' => tessera_imprimir_string($payload['chk_firmado'] ?? ''),
        'a_locales' => notas_desplegable_opciones($payload['a_locales'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     id_item: int,
 *     id_nom: int,
 *     nom: string,
 *     apellidos_nombre: string,
 * }
 */
function certificados_upload_firmado_from_payload(array $payload): array
{
    return [
        'id_item' => tessera_imprimir_int($payload['id_item'] ?? 0),
        'id_nom' => tessera_imprimir_int($payload['id_nom'] ?? 0),
        'nom' => tessera_imprimir_string($payload['nom'] ?? ''),
        'apellidos_nombre' => tessera_imprimir_string($payload['apellidos_nombre'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     nombreApellidos: string,
 *     lugar_nacimiento: string,
 *     f_nacimiento: string,
 *     nivel_stgr: mixed,
 *     region_latin: string,
 *     vstgr: string,
 *     dir_stgr: string,
 *     lugar_firma: string,
 *     contador: string,
 *     f_certificado: string,
 *     any: string,
 * }
 */
function certificados_imprimir_persona_from_payload(array $payload): array
{
    return [
        'nombreApellidos' => tessera_imprimir_string($payload['nombreApellidos'] ?? ''),
        'lugar_nacimiento' => tessera_imprimir_string($payload['lugar_nacimiento'] ?? ''),
        'f_nacimiento' => tessera_imprimir_string($payload['f_nacimiento'] ?? ''),
        'nivel_stgr' => $payload['nivel_stgr'] ?? null,
        'region_latin' => tessera_imprimir_string($payload['region_latin'] ?? ''),
        'vstgr' => tessera_imprimir_string($payload['vstgr'] ?? ''),
        'dir_stgr' => tessera_imprimir_string($payload['dir_stgr'] ?? ''),
        'lugar_firma' => tessera_imprimir_string($payload['lugar_firma'] ?? ''),
        'contador' => tessera_imprimir_string($payload['contador'] ?? ''),
        'f_certificado' => tessera_imprimir_string($payload['f_certificado'] ?? ''),
        'any' => tessera_imprimir_string($payload['any_2digit'] ?? ''),
    ];
}

function certificados_url_nuevo_from_spec(mixed $spec): string
{
    return HashFrontSignedLink::tryFromSpec($spec);
}

/**
 * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}
 */
function certificados_mpdf_empty_aprobada_row(): array
{
    return certificados_aprobada_row([]);
}

/**
 * @param array{
 *     curso_filosofia: string,
 *     curso_teologia: string,
 *     any_I: string,
 *     any_II: string,
 *     any_III: string,
 *     any_IV: string,
 *     ECTS: string,
 *     iudicium: string,
 *     pie_ects: string,
 * } $labels
 */
function certificados_mpdf_titulo(int $id_asignatura, array $labels): void
{
    switch ($id_asignatura) {
        case 1101:
            ?>
    <tr>
        <td class="space_doble"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="curso"><?= $labels['curso_filosofia'] ?></td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any"><?= $labels['any_I'] ?></td>
        <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
        <td class="cabecera"><?= $labels['iudicium'] ?></td>
    </tr>
            <?php
            break;
        case 1201:
            ?>
    <tr>
        <td class="space_doble"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any"><?= $labels['any_II'] ?></td>
        <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
        <td class="cabecera"><?= $labels['iudicium'] ?></td>
    </tr>
            <?php
            break;
        case 2101:
            ?>
    <tr>
        <td class="space_doble"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="curso"><?= $labels['curso_teologia'] ?></td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any"><?= $labels['any_I'] ?></td>
        <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
        <td class="cabecera"><?= $labels['iudicium'] ?></td>
    </tr>
            <?php
            break;
        case 2201:
            ?>
</table>
<br>
</div>
<div class="ects"><?= $labels['pie_ects'] ?>
</div>
<div class="A4">
    <table>
        <col style="width: 7%">
        <col style="width: 45%">
        <col style="width: 5%">
        <col style="width: 36%">
        <col style="width: 7%">
        <tr>
            <td class="space_doble"></td>
        </tr>
        <tr>
            <td></td>
            <td class="any"><?= $labels['any_II'] ?></td>
            <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
            <td class="cabecera"><?= $labels['iudicium'] ?></td>
        </tr>
            <?php
            break;
        case 2301:
            ?>
            <tr>
                <td class="space_doble"></td>
            </tr>
            <tr>
                <td></td>
                <td class="any"><?= $labels['any_III'] ?></td>
                <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
                <td class="cabecera"><?= $labels['iudicium'] ?></td>
            </tr>
            <?php
            break;
        case 2401:
            ?>
            <tr>
                <td class="space_doble"></td>
            </tr>
            <tr>
                <td></td>
                <td class="any"><?= $labels['any_IV'] ?></td>
                <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
                <td class="cabecera"><?= $labels['iudicium'] ?></td>
            </tr>
            <?php
            break;
    }
}
