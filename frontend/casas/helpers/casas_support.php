<?php

/**
 * Helpers compartidos del módulo frontend/casas.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use src\configuracion\domain\value_objects\ConfigSnapshot;

function casas_o_config(): ?ConfigSnapshot
{
    $oConfig = $_SESSION['oConfig'] ?? null;

    return $oConfig instanceof ConfigSnapshot ? $oConfig : null;
}

function casas_mi_usuario_csv_id_pau(): string
{
    $sessionAuth = $_SESSION['session_auth'] ?? null;
    $oMiUsuario = is_array($sessionAuth) ? ($sessionAuth['MiUsuario'] ?? null) : null;
    if (!is_object($oMiUsuario) || !method_exists($oMiUsuario, 'getCsv_id_pau')) {
        return '';
    }

    return tessera_imprimir_string($oMiUsuario->getCsv_id_pau());
}

/**
 * @return array<string, mixed>
 */
function casas_post_data(mixed $data): array
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

function casas_periodo_year_sel(int|string $year): string
{
    return tessera_imprimir_string($year);
}

function casas_desplegable_opcion_sel(int|string $value): string
{
    return tessera_imprimir_string($value);
}

/**
 * @param array<string, mixed> $payload
 * @return array{opciones: array<int|string, string>}
 */
function casas_calendario_casas_opciones(array $payload): array
{
    return [
        'opciones' => notas_desplegable_opciones($payload['opciones'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     ok: bool,
 *     error: string,
 *     any_anterior: int,
 *     id_ubi: int,
 * }
 */
function casas_calendario_body_error_from_payload(array $payload): array
{
    return [
        'ok' => !empty($payload['ok']),
        'error' => tessera_imprimir_string($payload['error'] ?? ''),
        'any_anterior' => tessera_imprimir_int($payload['any_anterior'] ?? 0),
        'id_ubi' => tessera_imprimir_int($payload['id_ubi'] ?? 0),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     ok: bool,
 *     error: string,
 *     nom_activ: string,
 *     id_tarifa: string,
 *     puede_modificar_tarifa: bool,
 *     precio: string,
 *     ingresos: string,
 *     num_asistentes: string,
 *     observ: string,
 *     letra_tarifa: string,
 *     a_opciones_tarifa: array<int|string, string>,
 * }
 */
function casas_ingreso_form_from_payload(array $payload): array
{
    return [
        'ok' => !empty($payload['ok']),
        'error' => tessera_imprimir_string($payload['error'] ?? ''),
        'nom_activ' => tessera_imprimir_string($payload['nom_activ'] ?? ''),
        'id_tarifa' => tessera_imprimir_string($payload['id_tarifa'] ?? ''),
        'puede_modificar_tarifa' => !empty($payload['puede_modificar_tarifa']),
        'precio' => tessera_imprimir_string($payload['precio'] ?? ''),
        'ingresos' => tessera_imprimir_string($payload['ingresos'] ?? ''),
        'num_asistentes' => tessera_imprimir_string($payload['num_asistentes'] ?? ''),
        'observ' => tessera_imprimir_string($payload['observ'] ?? ''),
        'letra_tarifa' => tessera_imprimir_string($payload['letra_tarifa'] ?? ''),
        'a_opciones_tarifa' => notas_desplegable_opciones($payload['a_opciones_tarifa'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     ok: bool,
 *     error: string,
 *     nota: string,
 *     errores: string,
 *     grupos: array<int|string, string>,
 *     cabeceras: list<array<string, mixed>|string>,
 *     valores: array<int|string, mixed>,
 * }
 */
function casas_ingresos_lista_from_payload(array $payload): array
{
    return [
        'ok' => !empty($payload['ok']),
        'error' => tessera_imprimir_string($payload['error'] ?? ''),
        'nota' => tessera_imprimir_string($payload['nota'] ?? ''),
        'errores' => tessera_imprimir_string($payload['errores'] ?? ''),
        'grupos' => notas_desplegable_opciones($payload['a_grupos'] ?? []),
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'valores' => actividades_lista_datos($payload['a_valores'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     ok: bool,
 *     error: string,
 *     casas: array<int|string, mixed>,
 *     year: int,
 * }
 */
function casas_ec_gastos_from_payload(array $payload): array
{
    $casasRaw = $payload['casas'] ?? [];

    return [
        'ok' => !empty($payload['ok']),
        'error' => tessera_imprimir_string($payload['error'] ?? ''),
        'casas' => is_array($casasRaw) ? $casasRaw : [],
        'year' => tessera_imprimir_int($payload['year'] ?? 0),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     grupos: array<int|string, string>,
 *     cabeceras: list<array<string, mixed>|string>,
 *     valores: array<int|string, mixed>,
 * }
 */
function casas_actividades_lista_from_payload(array $payload): array
{
    return [
        'grupos' => notas_desplegable_opciones($payload['a_grupos'] ?? []),
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'valores' => actividades_lista_datos($payload['a_valores'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     cabeceras: list<array<string, mixed>|string>,
 *     valores: array<int|string, mixed>,
 *     puede_anadir: bool,
 * }
 */
function casas_grupo_lista_from_payload(array $payload): array
{
    return [
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'puede_anadir' => !empty($payload['puede_anadir']),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     es_nuevo: bool,
 *     id_item: string,
 *     id_ubi_padre: int,
 *     id_ubi_hijo: int,
 *     opciones_casas: array<int|string, string>,
 * }
 */
function casas_grupo_form_from_payload(array $payload): array
{
    return [
        'es_nuevo' => !array_key_exists('es_nuevo', $payload) || !empty($payload['es_nuevo']),
        'id_item' => tessera_imprimir_string($payload['id_item'] ?? 'nuevo'),
        'id_ubi_padre' => tessera_imprimir_int($payload['id_ubi_padre'] ?? 0),
        'id_ubi_hijo' => tessera_imprimir_int($payload['id_ubi_hijo'] ?? 0),
        'opciones_casas' => notas_desplegable_opciones($payload['opciones_casas'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     permitido: bool,
 *     cabeceras: list<array<string, mixed>|string>,
 *     valores: array<int|string, mixed>,
 *     mi_of: string,
 *     inicio_local: string,
 *     fin_local: string,
 * }
 */
function casas_prevision_from_payload(array $payload): array
{
    return [
        'permitido' => !array_key_exists('permitido', $payload) || !empty($payload['permitido']),
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'mi_of' => tessera_imprimir_string($payload['mi_of'] ?? ''),
        'inicio_local' => tessera_imprimir_string($payload['inicio_local'] ?? ''),
        'fin_local' => tessera_imprimir_string($payload['fin_local'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     modo: string,
 *     a_resumen: array<int|string, mixed>,
 *     tot: array<int|string, mixed>,
 *     avisos: array<int|string, mixed>,
 *     a_anys: array<int|string, mixed>,
 * }
 */
function casas_resumen_lista_from_payload(array $payload): array
{
    return [
        'modo' => tessera_imprimir_string($payload['modo'] ?? 'periodo'),
        'a_resumen' => is_array($payload['a_resumen'] ?? null) ? $payload['a_resumen'] : [],
        'tot' => is_array($payload['tot'] ?? null) ? $payload['tot'] : [],
        'avisos' => is_array($payload['avisos'] ?? null) ? $payload['avisos'] : [],
        'a_anys' => is_array($payload['a_anys'] ?? null) ? $payload['a_anys'] : [],
    ];
}
