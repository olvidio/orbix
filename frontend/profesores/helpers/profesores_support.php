<?php

/**
 * Helpers compartidos del módulo frontend/profesores.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use frontend\shared\security\HashFrontSignedLink;
use src\permisos\domain\XPermisos;

function profesores_o_perm(): ?XPermisos
{
    $oPerm = $_SESSION['oPerm'] ?? null;

    return $oPerm instanceof XPermisos ? $oPerm : null;
}

/**
 * @return array{id_nom: int, id_tabla: string}
 */
function profesores_id_from_sel_post(): array
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
    $idPauRaw = filter_input(INPUT_POST, 'id_pau', FILTER_VALIDATE_INT);
    $idNom = is_int($idNomRaw) ? $idNomRaw : 0;
    $idPau = is_int($idPauRaw) ? $idPauRaw : 0;

    return [
        'id_nom' => $idNom !== 0 ? $idNom : $idPau,
        'id_tabla' => tessera_imprimir_string(filter_input(INPUT_POST, 'id_tabla')),
    ];
}

/**
 * @return array{path: string, query?: array<string, mixed>}|null
 */
function profesores_link_spec_from_mixed(mixed $raw): ?array
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
        foreach ($query as $key => $value) {
            $q[(string) $key] = $value;
        }
        $spec['query'] = $q;
    }

    return $spec;
}

/**
 * @return array<int|string, mixed>
 */
function profesores_go_cosas_link_specs(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }

    return $raw;
}

/**
 * @param array<int|string, mixed> $goCosasLinkSpecs
 * @return array<string, string>
 */
function profesores_go_cosas_from_specs(mixed $fichaSelfLinkSpec, array $goCosasLinkSpecs): array
{
    $goTo = HashFrontSignedLink::tryFromSpec($fichaSelfLinkSpec);
    $goCosas = [];
    foreach ($goCosasLinkSpecs as $key => $spec) {
        if (!is_string($key) || !is_array($spec)) {
            continue;
        }
        if ($key === 'print') {
            $goCosas[$key] = HashFrontSignedLink::tryFromSpec($spec);
            continue;
        }
        $parsed = profesores_link_spec_from_mixed($spec);
        if ($parsed === null) {
            continue;
        }
        $query = $parsed['query'] ?? [];
        $query['go_to'] = $goTo;
        $parsed['query'] = $query;
        $goCosas[$key] = HashFrontSignedLink::fromSpec($parsed);
    }

    return $goCosas;
}

/**
 * @param array<int|string, mixed> $data
 * @return array<string, mixed>
 */
function profesores_ficha_view_vars(array $data): array
{
    $out = [];
    foreach ($data as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $data
 * @return array{id_tabla: string, a_cabeceras: list<array<string, mixed>|string>, a_botones: list<array<string, mixed>>, a_valores: array<int|string, mixed>}
 */
function profesores_lista_tabla_from_payload(array $data): array
{
    return [
        'id_tabla' => tessera_imprimir_string($data['id_tabla'] ?? ''),
        'a_cabeceras' => actividades_lista_cabeceras($data['a_cabeceras'] ?? []),
        'a_botones' => actividades_lista_botones($data['a_botones'] ?? []),
        'a_valores' => actividades_lista_datos($data['a_valores'] ?? []),
    ];
}
