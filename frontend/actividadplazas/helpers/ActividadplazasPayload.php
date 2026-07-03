<?php

declare(strict_types=1);

namespace frontend\actividadplazas\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\web\Posicion;

final class ActividadplazasPostInput
{
    /**
     * @return array{first: string, second: string}|null
     */
    public static function selHashParts(): ?array
    {
        $aSelRaw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!is_array($aSelRaw) || $aSelRaw === []) {
            return null;
        }
        $sel0 = $aSelRaw[0];
        if (!is_string($sel0) || $sel0 === '') {
            return null;
        }
        $parts = explode('#', $sel0, 2);

        return [
            'first' => $parts[0],
            'second' => $parts[1] ?? '',
        ];
    }

    public static function stackFromPost(): ?int
    {
        $stack = filter_input(INPUT_POST, 'stack', FILTER_VALIDATE_INT);

        return is_int($stack) ? $stack : null;
    }

    /**
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
    public static function gestionPlazasRequestCampos(Posicion $oPosicion, int $stackFromPost): array
    {
        $read = static fn (string $key): string => PayloadCoercion::string(filter_input(INPUT_POST, $key) ?? '');

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
            $oPosicion2 = new Posicion();
            if ($oPosicion2->goStack($stackFromPost)) {
                foreach (array_keys($campos) as $key) {
                    $restored = $oPosicion2->getParametro($key);
                    if (is_scalar($restored) && PayloadCoercion::string($restored) !== '') {
                        $campos[$key] = PayloadCoercion::string($restored);
                    }
                }
                $scrollRestored = $oPosicion2->getParametro('scroll_id');
                if (is_scalar($scrollRestored) && PayloadCoercion::string($scrollRestored) !== '') {
                    $_POST['scroll_id'] = PayloadCoercion::string($scrollRestored);
                }
                $oPosicion2->olvidar($stackFromPost);
            }
        } else {
            foreach (array_keys($campos) as $key) {
                if ($campos[$key] !== '') {
                    continue;
                }
                $restored = $oPosicion->getParametro($key, 0);
                if (is_scalar($restored) && PayloadCoercion::string($restored) !== '') {
                    $campos[$key] = PayloadCoercion::string($restored);
                }
            }
        }

        return $campos;
    }
}

final class ActividadplazasPayload
{
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
    public static function gestionPlazasFromPayload(array $payload): array
    {
        return [
            'ap_nom' => PayloadCoercion::string($payload['ap_nom'] ?? ''),
            'sid_activ' => PayloadCoercion::string($payload['sid_activ'] ?? ''),
            'opciones' => NotasFormSupport::desplegableOpciones($payload['opciones'] ?? []),
            'sactividad' => PayloadCoercion::string($payload['sactividad'] ?? ''),
            'na' => PayloadCoercion::string($payload['na'] ?? ''),
            'dlA' => PayloadCoercion::string($payload['dlA'] ?? ''),
            'dlB' => PayloadCoercion::string($payload['dlB'] ?? ''),
            'concedidasA2B' => PayloadCoercion::int($payload['concedidasA2B'] ?? 0),
            'concedidasB2A' => PayloadCoercion::int($payload['concedidasB2A'] ?? 0),
            'a_cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
            'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
            'id_tipo_activ' => PayloadCoercion::string($payload['id_tipo_activ'] ?? ''),
            'year' => PayloadCoercion::string($payload['year'] ?? ''),
            'periodo' => PayloadCoercion::string($payload['periodo'] ?? ''),
            'empiezamin' => PayloadCoercion::string($payload['empiezamin'] ?? ''),
            'empiezamax' => PayloadCoercion::string($payload['empiezamax'] ?? ''),
            'extendida' => ($payload['extendida'] ?? false) === true,
            'publicado' => ($payload['publicado'] ?? false) === true,
            'otra_dl' => ($payload['otra_dl'] ?? false) === true,
            'a_plazas' => $payload['a_plazas'] ?? [],
            'plazas_totales' => PayloadCoercion::int($payload['plazas_totales'] ?? 0),
            'tot_calendario' => PayloadCoercion::int($payload['tot_calendario'] ?? 0),
            'tot_cedidas' => PayloadCoercion::int($payload['tot_cedidas'] ?? 0),
            'tot_conseguidas' => PayloadCoercion::int($payload['tot_conseguidas'] ?? 0),
            'tot_disponibles' => PayloadCoercion::int($payload['tot_disponibles'] ?? 0),
            'tot_ocupadas' => PayloadCoercion::int($payload['tot_ocupadas'] ?? 0),
            'dl_opciones' => NotasFormSupport::desplegableOpciones($payload['dl_opciones'] ?? []),
        ];
    }
}
