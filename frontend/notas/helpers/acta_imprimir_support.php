<?php

/**
 * Helpers compartidos por acta_imprimir.php y acta_imprimir_mpdf.php.
 */

require_once __DIR__ . '/tessera_imprimir_support.php';

/**
 * @param array<int|string, mixed> $payload
 * @return array<string, string>
 */
function acta_imprimir_personas_notas_from_payload(array $payload): array
{
    $raw = $payload['aPersonasNotas_list'] ?? [];
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $row) {
        if (!is_array($row)) {
            continue;
        }
        $nom = tessera_imprimir_string($row['nom'] ?? '');
        $nota = tessera_imprimir_string($row['nota'] ?? '');
        $out[$nom] = $nota;
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return list<string>
 */
function acta_imprimir_examinadores_from_payload(array $payload): array
{
    $raw = $payload['examinadores'] ?? [];
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        $out[] = tessera_imprimir_string($item);
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     acta: string,
 *     errores: string,
 *     num_alumnos: int,
 *     lin_tribunal: int,
 *     lin_max_cara_A: int,
 *     alum_cara_A: int,
 *     alum_cara_B: int,
 *     curso: string,
 *     any: string,
 *     nombre_asignatura: string,
 *     libro: string,
 *     pagina: string,
 *     linea: string,
 *     lugar: string,
 *     lugar_fecha: string,
 *     tribunal_html: string,
 *     examinadores: list<string>,
 *     aPersonasNotas: array<string, string>,
 * }
 */
function acta_imprimir_presentacion_from_payload(array $payload): array
{
    return [
        'acta' => tessera_imprimir_string($payload['acta'] ?? ''),
        'errores' => tessera_imprimir_string($payload['errores'] ?? ''),
        'num_alumnos' => tessera_imprimir_int($payload['num_alumnos'] ?? 0),
        'lin_tribunal' => tessera_imprimir_int($payload['lin_tribunal'] ?? 0),
        'lin_max_cara_A' => tessera_imprimir_int($payload['lin_max_cara_A'] ?? 0),
        'alum_cara_A' => tessera_imprimir_int($payload['alum_cara_A'] ?? 0),
        'alum_cara_B' => tessera_imprimir_int($payload['alum_cara_B'] ?? 0),
        'curso' => tessera_imprimir_string($payload['curso'] ?? ''),
        'any' => tessera_imprimir_string($payload['any'] ?? ''),
        'nombre_asignatura' => tessera_imprimir_string($payload['nombre_asignatura'] ?? ''),
        'libro' => tessera_imprimir_string($payload['libro'] ?? ''),
        'pagina' => tessera_imprimir_string($payload['pagina'] ?? ''),
        'linea' => tessera_imprimir_string($payload['linea'] ?? ''),
        'lugar' => tessera_imprimir_string($payload['lugar'] ?? ''),
        'lugar_fecha' => tessera_imprimir_string($payload['lugar_fecha'] ?? ''),
        'tribunal_html' => tessera_imprimir_string($payload['tribunal_html'] ?? ''),
        'examinadores' => acta_imprimir_examinadores_from_payload($payload),
        'aPersonasNotas' => acta_imprimir_personas_notas_from_payload($payload),
    ];
}

function acta_imprimir_acta_from_post(): string
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);

            return urldecode($parts[0]);
        }

        return '';
    }
    $qacta = filter_input(INPUT_POST, 'acta');
    if (is_string($qacta) && $qacta !== '') {
        return urldecode($qacta);
    }

    return '';
}

function acta_imprimir_cara_from_post(): string
{
    $qcara = filter_input(INPUT_POST, 'cara');
    if (is_string($qcara) && $qcara !== '') {
        return $qcara;
    }

    return 'A';
}

function acta_imprimir_acta_from_request(): string
{
    $actaGet = filter_input(INPUT_GET, 'acta');
    if (is_string($actaGet) && $actaGet !== '') {
        return urldecode($actaGet);
    }

    return '';
}
