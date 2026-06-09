<?php

/**
 * Helpers compartidos del módulo frontend/dbextern.
 */

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';

use frontend\shared\security\HashFrontSignedLink;

function dbextern_signed_link(mixed $spec): string
{
    return HashFrontSignedLink::tryFromSpec($spec);
}

/**
 * @return array<int, array<string, mixed>>
 */
function dbextern_session_db_listas(): array
{
    $raw = $_SESSION['DBListas'] ?? null;
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $row) {
        if (!is_array($row)) {
            continue;
        }
        if (is_int($key)) {
            $out[$key] = $row;
            continue;
        }
        if (is_numeric($key)) {
            $out[(int) $key] = $row;
        }
    }

    return $out;
}

/**
 * @return array<int, array<string, mixed>>
 */
function dbextern_session_db_orbix(): array
{
    $raw = $_SESSION['DBOrbix'] ?? null;
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $row) {
        if (!is_array($row)) {
            continue;
        }
        if (is_int($key)) {
            $out[$key] = $row;
            continue;
        }
        if (is_numeric($key)) {
            $out[(int) $key] = $row;
        }
    }

    return $out;
}

function dbextern_otro_listas(int $id, string $mov, int $max): int
{
    $listas = dbextern_session_db_listas();
    switch ($mov) {
        case '-':
            $id--;
            if ($id < 1) {
                return 1;
            }
            break;
        case '+':
            $id++;
            if ($id > $max) {
                return $max;
            }
            break;
        default:
            $id = 1;
    }
    if (isset($listas[$id])) {
        return $id;
    }

    return dbextern_otro_listas($id, $mov, $max);
}

/**
 * @return int|false
 */
function dbextern_otro_orbix(int $id, string $mov, int $max): int|false
{
    $orbix = dbextern_session_db_orbix();
    if ($max === 0) {
        return false;
    }
    switch ($mov) {
        case '-':
            $id--;
            if ($id < 1) {
                return 1;
            }
            break;
        case '+':
            $id++;
            if ($id > $max) {
                return $max;
            }
            break;
        default:
            $id = 1;
    }
    if (isset($orbix[$id])) {
        return $id;
    }

    return dbextern_otro_orbix($id, $mov, $max);
}

/**
 * @return array{id_nom_listas: string}
 */
function dbextern_persona_listas_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return ['id_nom_listas' => ''];
    }

    return [
        'id_nom_listas' => tessera_imprimir_string($raw['id_nom_listas'] ?? ''),
    ];
}

/**
 * @return array{id_nom_orbix: string}
 */
function dbextern_persona_orbix_row(mixed $raw): array
{
    if (!is_array($raw)) {
        return ['id_nom_orbix' => ''];
    }

    return [
        'id_nom_orbix' => tessera_imprimir_string($raw['id_nom_orbix'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $matches
 * @return array<int|string, mixed>
 */
function dbextern_lista_bdu_from_matches(array $matches): array
{
    return $matches;
}

/**
 * @return array<int, array<string, mixed>>
 */
function dbextern_lista_from_backend(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $row) {
        if (!is_array($row)) {
            continue;
        }
        if (is_int($key)) {
            $out[$key] = dbextern_row_string_keys($row);
            continue;
        }
        if (is_numeric($key)) {
            $out[(int) $key] = dbextern_row_string_keys($row);
        }
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $row
 * @return array<string, mixed>
 */
function dbextern_row_string_keys(array $row): array
{
    $out = [];
    foreach ($row as $key => $value) {
        if (is_int($key)) {
            continue;
        }
        $out[$key] = $value;
    }

    return $out;
}
