<?php

/**
 * Helpers compartidos del módulo frontend/actividadplazas.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

/**
 * @return array{first: string, second: string}|null
 */
function actividadplazas_sel_hash_parts(): ?array
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!is_array($a_sel_raw) || $a_sel_raw === []) {
        return null;
    }
    $sel0 = $a_sel_raw[0];
    if (!is_string($sel0) || $sel0 === '') {
        return null;
    }
    $parts = explode('#', $sel0, 2);

    return [
        'first' => $parts[0],
        'second' => $parts[1] ?? '',
    ];
}

function actividadplazas_stack_from_post(): ?int
{
    $stack = filter_input(INPUT_POST, 'stack', FILTER_VALIDATE_INT);

    return is_int($stack) ? $stack : null;
}

/**
 * Parámetros de filtro de gestion_plazas (POST + restauración desde Posicion).
 *
 * @return array{
 *     id_tipo_activ: string,
 *     year: string,
 *     periodo: string,
 *     empiezamin: string,
 *     empiezamax: string,
 *     sasistentes: string,
 *     sactividad: string,
 *     sactividad2: string,
 *     extendida: string,
 * }
 */
function actividadplazas_gestion_plazas_request_campos(
    \frontend\shared\web\Posicion $oPosicion,
    int $stackFromPost,
): array {
    $read = static fn (string $key): string => tessera_imprimir_string(filter_input(INPUT_POST, $key) ?? '');

    $campos = [
        'id_tipo_activ' => $read('id_tipo_activ'),
        'year' => $read('year'),
        'periodo' => $read('periodo'),
        'empiezamin' => $read('empiezamin'),
        'empiezamax' => $read('empiezamax'),
        'sasistentes' => $read('sasistentes'),
        'sactividad' => $read('sactividad'),
        'sactividad2' => $read('sactividad2'),
        'extendida' => $read('extendida'),
    ];

    if ($stackFromPost !== 0) {
        $oPosicion2 = new \frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stackFromPost)) {
            foreach (array_keys($campos) as $key) {
                $restored = $oPosicion2->getParametro($key);
                if (is_scalar($restored) && tessera_imprimir_string($restored) !== '') {
                    $campos[$key] = tessera_imprimir_string($restored);
                }
            }
            $scrollRestored = $oPosicion2->getParametro('scroll_id');
            if (is_scalar($scrollRestored) && tessera_imprimir_string($scrollRestored) !== '') {
                $_POST['scroll_id'] = tessera_imprimir_string($scrollRestored);
            }
            $oPosicion2->olvidar($stackFromPost);
        }
    } else {
        foreach (array_keys($campos) as $key) {
            if ($campos[$key] !== '') {
                continue;
            }
            $restored = $oPosicion->getParametro($key, 0);
            if (is_scalar($restored) && tessera_imprimir_string($restored) !== '') {
                $campos[$key] = tessera_imprimir_string($restored);
            }
        }
    }

    return $campos;
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     ap_nom: string,
 *     sid_activ: string,
 *     opciones: array<int|string, string>,
 *     sactividad: string,
 *     na: string,
 *     dlA: string,
 *     dlB: string,
 *     concedidasA2B: int,
 *     concedidasB2A: int,
 *     a_cabeceras: list<array<string, mixed>|string>,
 *     a_valores: array<int|string, mixed>,
 *     id_tipo_activ: string,
 *     year: string,
 *     periodo: string,
 *     empiezamin: string,
 *     empiezamax: string,
 *     extendida: bool,
 *     publicado: bool,
 *     otra_dl: bool,
 *     a_plazas: mixed,
 *     plazas_totales: int,
 *     tot_calendario: int,
 *     tot_cedidas: int,
 *     tot_conseguidas: int,
 *     tot_disponibles: int,
 *     tot_ocupadas: int,
 *     dl_opciones: array<int|string, string>,
 * }
 */
function actividadplazas_gestion_plazas_from_payload(array $payload): array
{
    return [
        'ap_nom' => tessera_imprimir_string($payload['ap_nom'] ?? ''),
        'sid_activ' => tessera_imprimir_string($payload['sid_activ'] ?? ''),
        'opciones' => notas_desplegable_opciones($payload['opciones'] ?? []),
        'sactividad' => tessera_imprimir_string($payload['sactividad'] ?? ''),
        'na' => tessera_imprimir_string($payload['na'] ?? ''),
        'dlA' => tessera_imprimir_string($payload['dlA'] ?? ''),
        'dlB' => tessera_imprimir_string($payload['dlB'] ?? ''),
        'concedidasA2B' => tessera_imprimir_int($payload['concedidasA2B'] ?? 0),
        'concedidasB2A' => tessera_imprimir_int($payload['concedidasB2A'] ?? 0),
        'a_cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'id_tipo_activ' => tessera_imprimir_string($payload['id_tipo_activ'] ?? ''),
        'year' => tessera_imprimir_string($payload['year'] ?? ''),
        'periodo' => tessera_imprimir_string($payload['periodo'] ?? ''),
        'empiezamin' => tessera_imprimir_string($payload['empiezamin'] ?? ''),
        'empiezamax' => tessera_imprimir_string($payload['empiezamax'] ?? ''),
        'extendida' => ($payload['extendida'] ?? false) === true,
        'publicado' => ($payload['publicado'] ?? false) === true,
        'otra_dl' => ($payload['otra_dl'] ?? false) === true,
        'a_plazas' => $payload['a_plazas'] ?? [],
        'plazas_totales' => tessera_imprimir_int($payload['plazas_totales'] ?? 0),
        'tot_calendario' => tessera_imprimir_int($payload['tot_calendario'] ?? 0),
        'tot_cedidas' => tessera_imprimir_int($payload['tot_cedidas'] ?? 0),
        'tot_conseguidas' => tessera_imprimir_int($payload['tot_conseguidas'] ?? 0),
        'tot_disponibles' => tessera_imprimir_int($payload['tot_disponibles'] ?? 0),
        'tot_ocupadas' => tessera_imprimir_int($payload['tot_ocupadas'] ?? 0),
        'dl_opciones' => notas_desplegable_opciones($payload['dl_opciones'] ?? []),
    ];
}
