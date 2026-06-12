<?php

/**
 * Helpers compartidos del módulo frontend/actividadestudios.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\security\HashFrontSignedLink;
use src\configuracion\domain\value_objects\ConfigSnapshot;

function actividadestudios_o_config(): ?ConfigSnapshot
{
    $oConfig = $_SESSION['oConfig'] ?? null;

    return $oConfig instanceof ConfigSnapshot ? $oConfig : null;
}

function actividadestudios_nota_max_default(): int
{
    $oConfig = actividadestudios_o_config();

    return $oConfig !== null ? tessera_imprimir_int($oConfig->getNotaMax()) : 0;
}

function actividadestudios_id_from_sel_post(): int
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

    $idActivRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);
    $idNomRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);
    if (is_int($idActivRaw)) {
        return $idActivRaw;
    }

    return is_int($idNomRaw) ? $idNomRaw : 0;
}

/**
 * @return array{id_activ: int, id_asignatura: int}
 */
function actividadestudios_id_activ_asignatura_from_post(): array
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0);

            return [
                'id_activ' => is_numeric($parts[0] ?? '') ? (int) $parts[0] : 0,
                'id_asignatura' => is_numeric($parts[1] ?? '') ? (int) $parts[1] : 0,
            ];
        }
    }

    $idActivRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);
    $idAsigRaw = filter_input(INPUT_POST, 'id_asignatura', FILTER_VALIDATE_INT);

    return [
        'id_activ' => is_int($idActivRaw) ? $idActivRaw : 0,
        'id_asignatura' => is_int($idAsigRaw) ? $idAsigRaw : 0,
    ];
}

/**
 * @return array{id_nom: int}
 */
function actividadestudios_id_nom_from_sel_post(): array
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);

            return [
                'id_nom' => is_numeric($parts[0]) ? (int) $parts[0] : 0,
            ];
        }
    }

    $idNomRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

    return [
        'id_nom' => is_int($idNomRaw) ? $idNomRaw : 0,
    ];
}

/**
 * @return array{id_activ: int, nom_activ: string}
 */
function actividadestudios_id_activ_nom_from_sel_post(): array
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);

            return [
                'id_activ' => is_numeric($parts[0]) ? (int) $parts[0] : 0,
                'nom_activ' => tessera_imprimir_string($parts[1] ?? ''),
            ];
        }
    }

    $idActivRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);

    return [
        'id_activ' => is_int($idActivRaw) ? $idActivRaw : 0,
        'nom_activ' => tessera_imprimir_string(filter_input(INPUT_POST, 'nom_activ')),
    ];
}

/**
 * @return array<string, mixed>
 */
function actividadestudios_post_data(mixed $data): array
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
function actividadestudios_string_key_row(mixed $raw): array
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
 * @return array<string, mixed>
 */
function actividadestudios_hash_campos_hidden(mixed $raw): array
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
function actividadestudios_link_spec(mixed $spec): ?array
{
    if (!is_array($spec)) {
        return null;
    }
    $path = tessera_imprimir_string($spec['path'] ?? '');
    if ($path === '') {
        return null;
    }
    $parsed = ['path' => $path];
    $query = $spec['query'] ?? null;
    if (is_array($query)) {
        $q = [];
        foreach ($query as $k => $v) {
            if (is_string($k)) {
                $q[$k] = $v;
            }
        }
        if ($q !== []) {
            $parsed['query'] = $q;
        }
    }

    return $parsed;
}

function actividadestudios_dossier_link(mixed $spec): string
{
    $parsed = actividadestudios_link_spec($spec);

    return $parsed !== null ? DossierTipoFormLinkSpecsSigning::fromSpec($parsed) : '';
}

/**
 * @return array<int|string, string>
 */
function actividadestudios_lista_grupos(mixed $raw): array
{
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
 * @return list<string>
 */
function actividadestudios_aviso_lines(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $line) {
        $out[] = tessera_imprimir_string($line);
    }

    return $out;
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     cabeceras: list<array<string, mixed>|string>,
 *     botones: list<array<string, mixed>>,
 *     valores: array<int|string, mixed>,
 * }
 */
function actividadestudios_lista_from_payload(array $payload): array
{
    return [
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'botones' => actividades_lista_botones($payload['a_botones'] ?? []),
        'valores' => actividades_lista_datos($payload['a_valores'] ?? []),
    ];
}

/**
 * @return array<int|string, mixed>
 */
function actividadestudios_lista_valores(mixed $raw, mixed $select = null, mixed $scrollId = null): array
{
    $valores = actividades_lista_datos($raw);
    $selectStr = tessera_imprimir_string($select);
    if ($selectStr !== '') {
        $valores['select'] = $selectStr;
    }
    $scrollStr = tessera_imprimir_string($scrollId);
    if ($scrollStr !== '') {
        $valores['scroll_id'] = $scrollStr;
    }

    return $valores;
}

function actividadestudios_desplegable_blanco(mixed $value): bool|string
{
    if (is_bool($value)) {
        return $value;
    }
    if (is_int($value)) {
        return $value === 1 ? '1' : '';
    }

    return tessera_imprimir_string($value);
}

function actividadestudios_desplegable_opcion_sel(mixed $value): string
{
    if (is_int($value) || is_float($value)) {
        return (string) $value;
    }

    return tessera_imprimir_string($value);
}

function actividadestudios_signed_link(mixed $spec): string
{
    $parsed = actividadestudios_link_spec($spec);
    if ($parsed === null) {
        return '';
    }

    return HashFrontSignedLink::fromSpec($parsed);
}

/**
 * @return list<array{nom_activ: string, creditos: string, aLista: array<mixed>}>
 */
function actividadestudios_ca_posibles_actividades(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        if (!is_array($item)) {
            continue;
        }
        $out[] = [
            'nom_activ' => tessera_imprimir_string($item['nom_activ'] ?? ''),
            'creditos' => tessera_imprimir_string($item['creditos'] ?? ''),
            'aLista' => is_array($item['aLista'] ?? null) ? $item['aLista'] : [],
        ];
    }

    return $out;
}

/**
 * @return array<string, array{stgr: string, aActividades: list<array{nom_activ: string, creditos: string, aLista: array<mixed>}>}>
 */
function actividadestudios_ca_posibles_c_personas(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $nomPersona => $datos) {
        if (!is_array($datos)) {
            continue;
        }
        $nom = tessera_imprimir_string($nomPersona);
        if ($nom === '') {
            continue;
        }
        $out[$nom] = [
            'stgr' => tessera_imprimir_string($datos['stgr'] ?? ''),
            'aActividades' => actividadestudios_ca_posibles_actividades($datos['aActividades'] ?? null),
        ];
    }

    return $out;
}

/**
 * @param array<string, mixed> $row
 * @return array<string, mixed>
 */
function actividadestudios_ca_posible_row(array $row): array
{
    return [
        'msg_txt' => tessera_imprimir_string($row['msg_txt'] ?? ''),
        'texto' => tessera_imprimir_string($row['texto'] ?? ''),
        'nc_bienio' => tessera_imprimir_string($row['nc_bienio'] ?? ''),
        'nc_cuadrienio1' => tessera_imprimir_string($row['nc_cuadrienio1'] ?? ''),
        'nc_cuadrienio2' => tessera_imprimir_string($row['nc_cuadrienio2'] ?? ''),
        'nc_cuadrienio' => tessera_imprimir_string($row['nc_cuadrienio'] ?? ''),
        'nc_repaso' => tessera_imprimir_string($row['nc_repaso'] ?? ''),
        'nc_ce' => tessera_imprimir_string($row['nc_ce'] ?? ''),
        'nc_otros' => tessera_imprimir_string($row['nc_otros'] ?? ''),
        'stgr' => tessera_imprimir_string($row['stgr'] ?? ''),
        'ctr' => tessera_imprimir_string($row['ctr'] ?? ''),
        'ref' => tessera_imprimir_string($row['ref'] ?? ''),
        'height' => tessera_imprimir_string($row['height'] ?? ''),
        'cPersonas' => actividadestudios_ca_posibles_c_personas($row['cPersonas'] ?? null),
        'aActividades' => actividadestudios_ca_posibles_actividades($row['aActividades'] ?? null),
    ];
}

/**
 * @return list<array<string, mixed>>
 */
function actividadestudios_ca_posibles_rows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $parsed = actividadestudios_string_key_row($row);
        if ($parsed !== []) {
            $out[] = actividadestudios_ca_posible_row($parsed);
        }
    }

    return $out;
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     modo: string,
 *     msg_txt: string,
 *     titulo: string,
 *     stgr: string,
 *     aActividades: mixed,
 *     pagina: string,
 *     filas: list<array<string, mixed>>,
 * }
 */
function actividadestudios_ca_posibles_from_payload(array $payload): array
{
    return [
        'modo' => tessera_imprimir_string($payload['modo'] ?? ''),
        'msg_txt' => tessera_imprimir_string($payload['msg_txt'] ?? ''),
        'titulo' => tessera_imprimir_string($payload['titulo'] ?? ''),
        'stgr' => tessera_imprimir_string($payload['stgr'] ?? ''),
        'aActividades' => actividadestudios_ca_posibles_actividades($payload['aActividades'] ?? null),
        'pagina' => actividadestudios_signed_link($payload['pagina_link_spec'] ?? null),
        'filas' => actividadestudios_ca_posibles_rows($payload['tabla_filas'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     grupo_estudios: string,
 *     mi_grupo: mixed,
 *     aCentrosNExt: array<int|string, string>,
 *     aCentrosAgdExt: array<int|string, string>,
 * }
 */
function actividadestudios_ca_posibles_que_from_payload(array $payload): array
{
    return [
        'grupo_estudios' => tessera_imprimir_string($payload['grupo_estudios'] ?? ''),
        'mi_grupo' => $payload['mi_grupo'] ?? '',
        'aCentrosNExt' => notas_desplegable_opciones($payload['aCentrosNExt'] ?? []),
        'aCentrosAgdExt' => notas_desplegable_opciones($payload['aCentrosAgdExt'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $row
 * @return array{nom_asignatura: string, nota: string, f_acta: string, acta: string}
 */
function actividadestudios_e43_nota_row(array $row): array
{
    return [
        'nom_asignatura' => tessera_imprimir_string($row['nom_asignatura'] ?? ''),
        'nota' => tessera_imprimir_string($row['nota'] ?? ''),
        'f_acta' => tessera_imprimir_string($row['f_acta'] ?? ''),
        'acta' => tessera_imprimir_string($row['acta'] ?? ''),
    ];
}

/**
 * @return list<array{nom_asignatura: string, nota: string, f_acta: string, acta: string}>
 */
function actividadestudios_e43_notas_rows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        $parsed = actividadestudios_string_key_row($row);
        if ($parsed !== []) {
            $out[] = actividadestudios_e43_nota_row($parsed);
        }
    }

    return $out;
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     msg_err: string,
 *     nom: string,
 *     txt_nacimiento: string,
 *     dl_origen: string,
 *     dl_destino: string,
 *     txt_actividad: string,
 *     matriculas: int,
 *     aAsignaturasMatriculadas: list<array{nom_asignatura: string, nota: string, f_acta: string, acta: string}>,
 * }
 */
function actividadestudios_e43_from_payload(array $payload): array
{
    return [
        'msg_err' => tessera_imprimir_string($payload['msg_err'] ?? ''),
        'nom' => tessera_imprimir_string($payload['nom'] ?? ''),
        'txt_nacimiento' => tessera_imprimir_string($payload['txt_nacimiento'] ?? ''),
        'dl_origen' => tessera_imprimir_string($payload['dl_origen'] ?? ''),
        'dl_destino' => tessera_imprimir_string($payload['dl_destino'] ?? ''),
        'txt_actividad' => tessera_imprimir_string($payload['txt_actividad'] ?? ''),
        'matriculas' => tessera_imprimir_int($payload['matriculas'] ?? 0),
        'aAsignaturasMatriculadas' => actividadestudios_e43_notas_rows($payload['aAsignaturasMatriculadas'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     permiso: int,
 *     nom_activ: string,
 *     matriculados: int,
 *     matriculas_rows: array<int|string, mixed>,
 *     notas: string,
 *     acta_principal: string,
 *     acta_notas_a_actas: array<int|string, mixed>,
 *     acta_txt_cursada: string,
 *     despl_actas_opciones: array<int|string, string>,
 *     msg_err: string,
 * }
 */
function actividadestudios_acta_notas_from_payload(array $payload): array
{
    return [
        'permiso' => tessera_imprimir_int($payload['permiso'] ?? 1),
        'nom_activ' => tessera_imprimir_string($payload['nom_activ'] ?? ''),
        'matriculados' => tessera_imprimir_int($payload['matriculados'] ?? 0),
        'matriculas_rows' => actividades_lista_datos($payload['matriculas_rows'] ?? []),
        'notas' => tessera_imprimir_string($payload['notas'] ?? 'nuevo'),
        'acta_principal' => tessera_imprimir_string($payload['acta_principal'] ?? ''),
        'acta_notas_a_actas' => actividades_lista_datos($payload['acta_notas_a_actas'] ?? []),
        'acta_txt_cursada' => tessera_imprimir_string($payload['acta_txt_cursada'] ?? ''),
        'despl_actas_opciones' => notas_desplegable_opciones($payload['despl_actas_opciones'] ?? []),
        'msg_err' => tessera_imprimir_string($payload['msg_err'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     mod: string,
 *     id_activ: int,
 *     id_asignatura: int,
 *     nombre_corto: string,
 *     chk_avisado: string,
 *     chk_confirmado: string,
 *     chk_preceptor: string,
 *     f_ini: string,
 *     f_fin: string,
 *     oDesplProfesores_opciones: array<int|string, string>,
 *     oDesplAsignaturas_opciones: array<int|string, string>,
 *     id_profesor_sel: int|string,
 *     camposForm: string,
 *     a_camposHidden: array<string, mixed>,
 * }
 */
function actividadestudios_form_asignaturas_from_payload(array $payload): array
{
    return [
        'mod' => tessera_imprimir_string($payload['mod'] ?? 'nuevo'),
        'id_activ' => tessera_imprimir_int($payload['id_activ'] ?? 0),
        'id_asignatura' => tessera_imprimir_int($payload['id_asignatura'] ?? 0),
        'nombre_corto' => tessera_imprimir_string($payload['nombre_corto'] ?? ''),
        'chk_avisado' => tessera_imprimir_string($payload['chk_avisado'] ?? ''),
        'chk_confirmado' => tessera_imprimir_string($payload['chk_confirmado'] ?? ''),
        'chk_preceptor' => tessera_imprimir_string($payload['chk_preceptor'] ?? ''),
        'f_ini' => tessera_imprimir_string($payload['f_ini'] ?? ''),
        'f_fin' => tessera_imprimir_string($payload['f_fin'] ?? ''),
        'oDesplProfesores_opciones' => notas_desplegable_opciones($payload['oDesplProfesores_opciones'] ?? []),
        'oDesplAsignaturas_opciones' => notas_desplegable_opciones($payload['oDesplAsignaturas_opciones'] ?? []),
        'id_profesor_sel' => notas_form_scalar($payload['id_profesor_sel'] ?? -1),
        'camposForm' => tessera_imprimir_string($payload['camposForm'] ?? ''),
        'a_camposHidden' => actividadestudios_hash_campos_hidden($payload['a_camposHidden'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     nom_activ: string,
 *     mod: string,
 *     id_asignatura_real: int|string,
 *     nombre_corto: string,
 *     chk_preceptor: string,
 *     id_preceptor: int|string,
 *     condicion_js: string,
 *     oDesplNiveles_opciones: array<int|string, string>,
 *     oDesplProfesores_opciones: array<int|string, string>,
 *     camposForm: string,
 *     a_camposHidden: array<string, mixed>,
 * }
 */
function actividadestudios_form_matriculas_from_payload(array $payload): array
{
    return [
        'nom_activ' => tessera_imprimir_string($payload['nom_activ'] ?? ''),
        'mod' => tessera_imprimir_string($payload['mod'] ?? 'nuevo'),
        'id_asignatura_real' => notas_form_scalar($payload['id_asignatura_real'] ?? 0),
        'nombre_corto' => tessera_imprimir_string($payload['nombre_corto'] ?? ''),
        'chk_preceptor' => tessera_imprimir_string($payload['chk_preceptor'] ?? ''),
        'id_preceptor' => notas_form_scalar($payload['id_preceptor'] ?? ''),
        'condicion_js' => tessera_imprimir_string($payload['condicion_js'] ?? ''),
        'oDesplNiveles_opciones' => notas_desplegable_opciones($payload['oDesplNiveles_opciones'] ?? []),
        'oDesplProfesores_opciones' => notas_desplegable_opciones($payload['oDesplProfesores_opciones'] ?? []),
        'camposForm' => tessera_imprimir_string($payload['camposForm'] ?? ''),
        'a_camposHidden' => actividadestudios_hash_campos_hidden($payload['a_camposHidden'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{titulo: string, msg_err: string, a_valores: array<int|string, mixed>}
 */
function actividadestudios_matriculas_lista_from_payload(array $payload): array
{
    return [
        'titulo' => tessera_imprimir_string($payload['titulo'] ?? ''),
        'msg_err' => tessera_imprimir_string($payload['msg_err'] ?? ''),
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{titulo: string, msg_err: string, aviso: string, a_valores: array<int|string, mixed>}
 */
function actividadestudios_matriculas_lista_otras_r_from_payload(array $payload): array
{
    return [
        'titulo' => tessera_imprimir_string($payload['titulo'] ?? ''),
        'msg_err' => tessera_imprimir_string($payload['msg_err'] ?? ''),
        'aviso' => tessera_imprimir_string($payload['aviso'] ?? ''),
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{msg_err: string, aviso: string, a_valores: array<int|string, mixed>}
 */
function actividadestudios_matriculas_pendientes_from_payload(array $payload): array
{
    return [
        'msg_err' => tessera_imprimir_string($payload['msg_err'] ?? ''),
        'aviso' => tessera_imprimir_string($payload['aviso'] ?? ''),
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{msg_err: string, nom_activ: string, nom_director_est: string, datos_asignatura: array<int|string, mixed>}
 */
function actividadestudios_lista_clases_ca_from_payload(array $payload): array
{
    return [
        'msg_err' => tessera_imprimir_string($payload['msg_err'] ?? ''),
        'nom_activ' => tessera_imprimir_string($payload['nom_activ'] ?? ''),
        'nom_director_est' => tessera_imprimir_string($payload['nom_director_est'] ?? ''),
        'datos_asignatura' => actividades_lista_datos($payload['datos_asignatura'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     msg_err: string,
 *     nom_activ: string,
 *     nom_director_est: string,
 *     aPreceptores: array<int|string, mixed>,
 *     aProfesores: array<int|string, mixed>,
 *     aAlumnos: array<int|string, mixed>,
 * }
 */
function actividadestudios_plan_estudios_ca_from_payload(array $payload): array
{
    return [
        'msg_err' => tessera_imprimir_string($payload['msg_err'] ?? ''),
        'nom_activ' => tessera_imprimir_string($payload['nom_activ'] ?? ''),
        'nom_director_est' => tessera_imprimir_string($payload['nom_director_est'] ?? ''),
        'aPreceptores' => actividades_lista_datos($payload['aPreceptores'] ?? []),
        'aProfesores' => actividades_lista_datos($payload['aProfesores'] ?? []),
        'aAlumnos' => actividades_lista_datos($payload['aAlumnos'] ?? []),
    ];
}

function actividadestudios_echo_string(mixed $value): void
{
    echo tessera_imprimir_string($value);
}

function actividadestudios_file_contents_string(string $path): string
{
    $content = file_get_contents($path);

    return is_string($content) ? $content : '';
}
