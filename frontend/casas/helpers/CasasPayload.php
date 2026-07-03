<?php

declare(strict_types=1);

namespace frontend\casas\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use src\configuracion\domain\value_objects\ConfigSnapshot;

final class CasasPayload
{
public static function oConfig(): ?ConfigSnapshot
{
    $oConfig = $_SESSION['oConfig'] ?? null;

    return $oConfig instanceof ConfigSnapshot ? $oConfig : null;
}

public static function miUsuarioCsvIdPau(): string
{
    $sessionAuth = $_SESSION['session_auth'] ?? null;
    $oMiUsuario = is_array($sessionAuth) ? ($sessionAuth['MiUsuario'] ?? null) : null;
    if (!is_object($oMiUsuario) || !method_exists($oMiUsuario, 'getCsv_id_pau')) {
        return '';
    }

    return PayloadCoercion::string($oMiUsuario->getCsv_id_pau());
}

/**
 * @return array<string, mixed>
 */
public static function postData(mixed $data): array
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

public static function periodoYearSel(int|string $year): string
{
    return PayloadCoercion::string($year);
}

public static function desplegableOpcionSel(int|string $value): string
{
    return PayloadCoercion::string($value);
}

/**
 * @param array<string, mixed> $payload
 * @return array{opciones: array<int|string, string>}
 */
public static function calendarioCasasOpciones(array $payload): array
{
    return [
        'opciones' => NotasFormSupport::desplegableOpciones($payload['opciones'] ?? []),
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
public static function calendarioBodyErrorFromPayload(array $payload): array
{
    return [
        'ok' => !empty($payload['ok']),
        'error' => PayloadCoercion::string($payload['error'] ?? ''),
        'any_anterior' => PayloadCoercion::int($payload['any_anterior'] ?? 0),
        'id_ubi' => PayloadCoercion::int($payload['id_ubi'] ?? 0),
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
public static function ingresoFormFromPayload(array $payload): array
{
    return [
        'ok' => !empty($payload['ok']),
        'error' => PayloadCoercion::string($payload['error'] ?? ''),
        'nom_activ' => PayloadCoercion::string($payload['nom_activ'] ?? ''),
        'id_tarifa' => PayloadCoercion::string($payload['id_tarifa'] ?? ''),
        'puede_modificar_tarifa' => !empty($payload['puede_modificar_tarifa']),
        'precio' => PayloadCoercion::string($payload['precio'] ?? ''),
        'ingresos' => PayloadCoercion::string($payload['ingresos'] ?? ''),
        'num_asistentes' => PayloadCoercion::string($payload['num_asistentes'] ?? ''),
        'observ' => PayloadCoercion::string($payload['observ'] ?? ''),
        'letra_tarifa' => PayloadCoercion::string($payload['letra_tarifa'] ?? ''),
        'a_opciones_tarifa' => NotasFormSupport::desplegableOpciones($payload['a_opciones_tarifa'] ?? []),
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
public static function ingresosListaFromPayload(array $payload): array
{
    return [
        'ok' => !empty($payload['ok']),
        'error' => PayloadCoercion::string($payload['error'] ?? ''),
        'nota' => PayloadCoercion::string($payload['nota'] ?? ''),
        'errores' => PayloadCoercion::string($payload['errores'] ?? ''),
        'grupos' => NotasFormSupport::desplegableOpciones($payload['a_grupos'] ?? []),
        'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
        'valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
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
public static function ecGastosFromPayload(array $payload): array
{
    $casasRaw = $payload['casas'] ?? [];

    return [
        'ok' => !empty($payload['ok']),
        'error' => PayloadCoercion::string($payload['error'] ?? ''),
        'casas' => is_array($casasRaw) ? $casasRaw : [],
        'year' => PayloadCoercion::int($payload['year'] ?? 0),
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
public static function actividadesListaFromPayload(array $payload): array
{
    return [
        'grupos' => NotasFormSupport::desplegableOpciones($payload['a_grupos'] ?? []),
        'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
        'valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
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
public static function grupoListaFromPayload(array $payload): array
{
    return [
        'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
        'valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
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
public static function grupoFormFromPayload(array $payload): array
{
    return [
        'es_nuevo' => !array_key_exists('es_nuevo', $payload) || !empty($payload['es_nuevo']),
        'id_item' => PayloadCoercion::string($payload['id_item'] ?? 'nuevo'),
        'id_ubi_padre' => PayloadCoercion::int($payload['id_ubi_padre'] ?? 0),
        'id_ubi_hijo' => PayloadCoercion::int($payload['id_ubi_hijo'] ?? 0),
        'opciones_casas' => NotasFormSupport::desplegableOpciones($payload['opciones_casas'] ?? []),
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
public static function previsionFromPayload(array $payload): array
{
    return [
        'permitido' => !array_key_exists('permitido', $payload) || !empty($payload['permitido']),
        'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
        'valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        'mi_of' => PayloadCoercion::string($payload['mi_of'] ?? ''),
        'inicio_local' => PayloadCoercion::string($payload['inicio_local'] ?? ''),
        'fin_local' => PayloadCoercion::string($payload['fin_local'] ?? ''),
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
public static function resumenListaFromPayload(array $payload): array
{
    return [
        'modo' => PayloadCoercion::string($payload['modo'] ?? 'periodo'),
        'a_resumen' => is_array($payload['a_resumen'] ?? null) ? $payload['a_resumen'] : [],
        'tot' => is_array($payload['tot'] ?? null) ? $payload['tot'] : [],
        'avisos' => is_array($payload['avisos'] ?? null) ? $payload['avisos'] : [],
        'a_anys' => is_array($payload['a_anys'] ?? null) ? $payload['a_anys'] : [],
    ];
}
}
