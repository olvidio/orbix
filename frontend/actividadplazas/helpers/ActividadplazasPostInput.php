<?php

declare(strict_types=1);

namespace frontend\actividadplazas\helpers;

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
