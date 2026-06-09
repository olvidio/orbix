<?php

/**
 * Helpers compartidos del módulo frontend/personas.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';
require_once __DIR__ . '/../../dossiers/controller/lista_dossiers.php';

use src\permisos\domain\XPermisos;

function personas_o_perm(): ?XPermisos
{
    $oPerm = $_SESSION['oPerm'] ?? null;

    return $oPerm instanceof XPermisos ? $oPerm : null;
}

function personas_have_perm_oficina(string $oficina): bool
{
    $oPerm = personas_o_perm();

    return $oPerm !== null && $oPerm->have_perm_oficina($oficina);
}

/**
 * @return array<int|string, mixed>
 */
function personas_post_payload(mixed $data): array
{
    return is_array($data) ? $data : [];
}

function personas_stack_from_post(): ?int
{
    $stack = filter_input(INPUT_POST, 'stack', FILTER_VALIDATE_INT);

    return is_int($stack) ? $stack : null;
}

/**
 * @return array{id_nom: int, id_tabla: string}
 */
function personas_id_from_sel_post(): array
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);

            return [
                'id_nom' => is_numeric($parts[0]) ? (int) $parts[0] : 0,
                'id_tabla' => $parts[1] ?? '',
            ];
        }
    }

    $idNomRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

    return [
        'id_nom' => is_int($idNomRaw) ? $idNomRaw : 0,
        'id_tabla' => tessera_imprimir_string(filter_input(INPUT_POST, 'id_tabla')),
    ];
}

/**
 * @return array{id_pau: int}
 */
function personas_id_pau_from_sel_post(): array
{
    $ids = personas_id_from_sel_post();
    $idPauRaw = filter_input(INPUT_POST, 'id_pau', FILTER_VALIDATE_INT);
    $idPau = is_int($idPauRaw) ? $idPauRaw : 0;

    return [
        'id_pau' => $ids['id_nom'] !== 0 ? $ids['id_nom'] : $idPau,
    ];
}

function personas_session_go_to_set_tabla(string $objPau): void
{
    if (!isset($_SESSION['session_go_to']) || !is_array($_SESSION['session_go_to'])) {
        return;
    }
    if (!isset($_SESSION['session_go_to']['sel']) || !is_array($_SESSION['session_go_to']['sel'])) {
        return;
    }
    $_SESSION['session_go_to']['sel']['tabla'] = $objPau;
}

function personas_posicion_int_param(mixed $value, int $default = 0): int
{
    if (is_int($value)) {
        return $value;
    }
    if (is_string($value) && is_numeric($value)) {
        return (int) $value;
    }

    return $default;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     Qobj_pau: string,
 *     titulo: string,
 *     dl: string,
 *     f_nacimiento: string,
 *     situacion: string,
 *     f_situacion: string,
 *     profesion: string,
 *     stgr: string,
 *     observ: string,
 *     ctr: string,
 *     telfs: string,
 *     mails: string,
 *     aviso: string,
 * }
 */
function personas_home_from_payload(array $payload, string $defaultObjPau, string $defaultAviso): array
{
    return [
        'Qobj_pau' => tessera_imprimir_string($payload['Qobj_pau'] ?? $defaultObjPau),
        'titulo' => tessera_imprimir_string($payload['titulo'] ?? ''),
        'dl' => tessera_imprimir_string($payload['dl'] ?? ''),
        'f_nacimiento' => tessera_imprimir_string($payload['f_nacimiento'] ?? ''),
        'situacion' => tessera_imprimir_string($payload['situacion'] ?? ''),
        'f_situacion' => tessera_imprimir_string($payload['f_situacion'] ?? ''),
        'profesion' => tessera_imprimir_string($payload['profesion'] ?? ''),
        'stgr' => tessera_imprimir_string($payload['stgr'] ?? ''),
        'observ' => tessera_imprimir_string($payload['observ'] ?? ''),
        'ctr' => tessera_imprimir_string($payload['ctr'] ?? ''),
        'telfs' => tessera_imprimir_string($payload['telfs'] ?? ''),
        'mails' => tessera_imprimir_string($payload['mails'] ?? ''),
        'aviso' => tessera_imprimir_string($payload['aviso'] ?? $defaultAviso),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     id_nom: int,
 *     Qobj_pau: string,
 *     trato: string,
 *     nom: string,
 *     apel_fam: string,
 *     nx1: string,
 *     apellido1: string,
 *     nx2: string,
 *     apellido2: string,
 *     lugar_nacimiento: string,
 *     f_nacimiento: string,
 *     f_situacion: string,
 *     profesion: string,
 *     sacd: string,
 *     eap: string,
 *     inc: string,
 *     f_inc: string,
 *     ce: string,
 *     ce_lugar: string,
 *     ce_ini: string,
 *     ce_fin: string,
 *     observ: string,
 *     titulo: string,
 *     nom_ctr: string,
 *     id_ctr: string,
 *     id_tabla: string,
 *     dl: string,
 *     idioma_preferido: string,
 *     situacion: string,
 *     nivel_stgr: string,
 *     edad: string,
 *     opciones_dl: array<int|string, string>,
 *     opciones_centros: array<int|string, string>,
 *     opciones_situacion: array<int|string, string>,
 *     opciones_lengua: array<int|string, string>,
 *     opciones_stgr: array<int|string, string>,
 *     opciones_inc: array<int|string, string>,
 * }
 */
function personas_editar_form_from_payload(array $payload, int $defaultIdNom, string $defaultObjPau): array
{
    return [
        'id_nom' => tessera_imprimir_int($payload['id_nom'] ?? $defaultIdNom),
        'Qobj_pau' => tessera_imprimir_string($payload['Qobj_pau'] ?? $defaultObjPau),
        'trato' => tessera_imprimir_string($payload['trato'] ?? ''),
        'nom' => tessera_imprimir_string($payload['nom'] ?? ''),
        'apel_fam' => tessera_imprimir_string($payload['apel_fam'] ?? ''),
        'nx1' => tessera_imprimir_string($payload['nx1'] ?? ''),
        'apellido1' => tessera_imprimir_string($payload['apellido1'] ?? ''),
        'nx2' => tessera_imprimir_string($payload['nx2'] ?? ''),
        'apellido2' => tessera_imprimir_string($payload['apellido2'] ?? ''),
        'lugar_nacimiento' => tessera_imprimir_string($payload['lugar_nacimiento'] ?? ''),
        'f_nacimiento' => tessera_imprimir_string($payload['f_nacimiento'] ?? ''),
        'f_situacion' => tessera_imprimir_string($payload['f_situacion'] ?? ''),
        'profesion' => tessera_imprimir_string($payload['profesion'] ?? ''),
        'sacd' => tessera_imprimir_string($payload['sacd'] ?? ''),
        'eap' => tessera_imprimir_string($payload['eap'] ?? ''),
        'inc' => tessera_imprimir_string($payload['inc'] ?? ''),
        'f_inc' => tessera_imprimir_string($payload['f_inc'] ?? ''),
        'ce' => tessera_imprimir_string($payload['ce'] ?? ''),
        'ce_lugar' => tessera_imprimir_string($payload['ce_lugar'] ?? ''),
        'ce_ini' => tessera_imprimir_string($payload['ce_ini'] ?? ''),
        'ce_fin' => tessera_imprimir_string($payload['ce_fin'] ?? ''),
        'observ' => tessera_imprimir_string($payload['observ'] ?? ''),
        'titulo' => tessera_imprimir_string($payload['titulo'] ?? ''),
        'nom_ctr' => tessera_imprimir_string($payload['nom_ctr'] ?? ''),
        'id_ctr' => tessera_imprimir_string($payload['id_ctr'] ?? ''),
        'id_tabla' => tessera_imprimir_string($payload['id_tabla'] ?? ''),
        'dl' => tessera_imprimir_string($payload['dl'] ?? ''),
        'idioma_preferido' => tessera_imprimir_string($payload['idioma_preferido'] ?? ''),
        'situacion' => tessera_imprimir_string($payload['situacion'] ?? ''),
        'nivel_stgr' => tessera_imprimir_string($payload['nivel_stgr'] ?? ''),
        'edad' => tessera_imprimir_string($payload['edad'] ?? ''),
        'opciones_dl' => notas_desplegable_opciones($payload['opciones_dl'] ?? []),
        'opciones_centros' => notas_desplegable_opciones($payload['opciones_centros'] ?? []),
        'opciones_situacion' => notas_desplegable_opciones($payload['opciones_situacion'] ?? []),
        'opciones_lengua' => notas_desplegable_opciones($payload['opciones_lengua'] ?? []),
        'opciones_stgr' => notas_desplegable_opciones($payload['opciones_stgr'] ?? []),
        'opciones_inc' => notas_desplegable_opciones($payload['opciones_inc'] ?? []),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     tabla: string,
 *     obj_pau: string,
 *     id_tabla: string,
 *     permiso: int,
 *     sPrefs: string,
 *     total: int,
 *     aviso: string,
 *     personas: list<array{id_nom: int, id_tabla: string, nom: string, nombre_ubi: string, nivel_stgr: string, situacion: string, f_situacion: string}>,
 * }
 */
function personas_select_tabla_from_payload(array $payload, string $defaultTabla, string $defaultAviso): array
{
    $personasRaw = $payload['personas'] ?? [];
    $personas = [];
    if (is_array($personasRaw)) {
        foreach ($personasRaw as $row) {
            $personas[] = personas_select_fila_row($row);
        }
    }

    return [
        'tabla' => tessera_imprimir_string($payload['tabla'] ?? $defaultTabla),
        'obj_pau' => tessera_imprimir_string($payload['obj_pau'] ?? ''),
        'id_tabla' => tessera_imprimir_string($payload['id_tabla'] ?? ''),
        'permiso' => tessera_imprimir_int($payload['permiso'] ?? 1),
        'sPrefs' => tessera_imprimir_string($payload['sPrefs'] ?? ''),
        'total' => tessera_imprimir_int($payload['total'] ?? 0),
        'aviso' => tessera_imprimir_string($payload['aviso'] ?? $defaultAviso),
        'personas' => $personas,
    ];
}

/**
 * @return array{id_nom: int, id_tabla: string, nom: string, nombre_ubi: string, nivel_stgr: string, situacion: string, f_situacion: string}
 */
function personas_select_fila_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_nom' => 0,
            'id_tabla' => '',
            'nom' => '',
            'nombre_ubi' => '',
            'nivel_stgr' => '',
            'situacion' => '',
            'f_situacion' => '',
        ];
    }

    return [
        'id_nom' => tessera_imprimir_int($raw['id_nom'] ?? 0),
        'id_tabla' => tessera_imprimir_string($raw['id_tabla'] ?? ''),
        'nom' => tessera_imprimir_string($raw['nom'] ?? ''),
        'nombre_ubi' => tessera_imprimir_string($raw['nombre_ubi'] ?? ''),
        'nivel_stgr' => tessera_imprimir_string($raw['nivel_stgr'] ?? ''),
        'situacion' => tessera_imprimir_string($raw['situacion'] ?? ''),
        'f_situacion' => tessera_imprimir_string($raw['f_situacion'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{nom: string, nivel_stgr: string, opciones_nivel_stgr: array<int|string, string>}
 */
function personas_stgr_cambio_from_payload(array $payload): array
{
    return [
        'nom' => tessera_imprimir_string($payload['nom'] ?? ''),
        'nivel_stgr' => tessera_imprimir_string($payload['nivel_stgr'] ?? ''),
        'opciones_nivel_stgr' => notas_desplegable_opciones($payload['opciones_nivel_stgr'] ?? []),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     titulo: string,
 *     id_ctr: int|string,
 *     nombre_ctr: string,
 *     dl: string,
 *     hoy: string,
 *     opciones_centros: array<int|string, string>,
 *     opciones_dl: array<int|string, string>,
 *     opciones_situacion: array<int|string, string>,
 * }
 */
function personas_traslado_form_from_payload(array $payload): array
{
    return [
        'titulo' => tessera_imprimir_string($payload['titulo'] ?? ''),
        'id_ctr' => notas_form_scalar($payload['id_ctr'] ?? ''),
        'nombre_ctr' => tessera_imprimir_string($payload['nombre_ctr'] ?? ''),
        'dl' => tessera_imprimir_string($payload['dl'] ?? ''),
        'hoy' => tessera_imprimir_string($payload['hoy'] ?? ''),
        'opciones_centros' => notas_desplegable_opciones($payload['opciones_centros'] ?? []),
        'opciones_dl' => notas_desplegable_opciones($payload['opciones_dl'] ?? []),
        'opciones_situacion' => notas_desplegable_opciones($payload['opciones_situacion'] ?? []),
    ];
}
