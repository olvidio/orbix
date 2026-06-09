<?php

/**
 * Helpers compartidos por tessera_imprimir.php y tessera_imprimir_mpdf.php.
 */

/**
 * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string}
 */
function tessera_imprimir_empty_row(): array
{
    return [
        'id_nivel_asig' => 0,
        'id_nivel' => 0,
        'id_asignatura' => 0,
        'nombre_asignatura' => '',
        'acta' => '',
        'fecha_local' => '',
        'nota' => '',
    ];
}

function tessera_imprimir_int(mixed $value, int $default = 0): int
{
    if (is_int($value)) {
        return $value;
    }
    if (is_string($value) && is_numeric($value)) {
        return (int) $value;
    }

    return $default;
}

function tessera_imprimir_string(mixed $value, string $default = ''): string
{
    if (is_string($value)) {
        return $value;
    }
    if (is_int($value) || is_float($value) || is_bool($value)) {
        return (string) $value;
    }

    return $default;
}

/**
 * @return array{id_nivel: int, id_asignatura: int, nombre_asignatura: string}
 */
function tessera_imprimir_asignatura_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return ['id_nivel' => 0, 'id_asignatura' => 0, 'nombre_asignatura' => ''];
    }

    return [
        'id_nivel' => tessera_imprimir_int($raw['id_nivel'] ?? 0),
        'id_asignatura' => tessera_imprimir_int($raw['id_asignatura'] ?? 0),
        'nombre_asignatura' => tessera_imprimir_string($raw['nombre_asignatura'] ?? ''),
    ];
}

/**
 * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string}
 */
function tessera_imprimir_aprobada_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return tessera_imprimir_empty_row();
    }

    return [
        'id_nivel_asig' => tessera_imprimir_int($raw['id_nivel_asig'] ?? 0),
        'id_nivel' => tessera_imprimir_int($raw['id_nivel'] ?? 0),
        'id_asignatura' => tessera_imprimir_int($raw['id_asignatura'] ?? 0),
        'nombre_asignatura' => tessera_imprimir_string($raw['nombre_asignatura'] ?? ''),
        'acta' => tessera_imprimir_string($raw['acta'] ?? ''),
        'fecha_local' => tessera_imprimir_string($raw['fecha_local'] ?? ''),
        'nota' => tessera_imprimir_string($raw['nota'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return list<array{id_nivel: int, id_asignatura: int, nombre_asignatura: string}>
 */
function tessera_imprimir_asignaturas_from_payload(array $payload): array
{
    $raw = $payload['c_asignaturas'] ?? [];
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        $out[] = tessera_imprimir_asignatura_row($item);
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array<int|string, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string}>
 */
function tessera_imprimir_aprobadas_from_payload(array $payload): array
{
    $raw = $payload['a_aprobadas'] ?? [];
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $item) {
        $out[$key] = tessera_imprimir_aprobada_row($item);
    }

    return $out;
}

/**
 * @param array<int|string, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string}> $aAprobadas
 * @param array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string} $rowEmpty
 * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string}
 */
function tessera_imprimir_current_aprobada_row(array $aAprobadas, array $rowEmpty): array
{
    if (key($aAprobadas) === null) {
        return $rowEmpty;
    }
    $rowCurrent = current($aAprobadas);

    return is_array($rowCurrent) ? tessera_imprimir_aprobada_row($rowCurrent) : $rowEmpty;
}

function tessera_imprimir_fecha_local(string $fechaRaw): string
{
    $fecha = explode('-', $fechaRaw);
    $any = substr($fecha[0], 2);
    $fechaok = ($fecha[2] ?? '') . '.' . ($fecha[1] ?? '') . '.' . $any;
    if (($fecha[1] ?? '') === '00') {
        return '';
    }

    return $fechaok;
}
