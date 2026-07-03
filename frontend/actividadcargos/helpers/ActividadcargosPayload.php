<?php

declare(strict_types=1);

namespace frontend\actividadcargos\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ActividadcargosPayload
{
    /**
     * @param array<int|string, mixed> $raw
     * @return array<string, mixed>
     */
    public static function stringKeyPayload(array $raw): array
    {
        $out = [];
        foreach ($raw as $key => $value) {
            if (is_string($key)) {
                $out[$key] = $value;
            }
        }

        return $out;
    }
}

final class ActividadcargosRenderSupport
{
    /**
     * @return array{campos_form?: string, campos_no: string, campos_hidden?: array<string, mixed>}|null
     */
    public static function hashFormConfig(mixed $raw): ?array
    {
        if (!is_array($raw)) {
            return null;
        }
        $camposNo = $raw['campos_no'] ?? null;
        if (!is_string($camposNo)) {
            return null;
        }
        $cfg = ['campos_no' => $camposNo];
        $cf = $raw['campos_form'] ?? null;
        if (is_string($cf) && $cf !== '') {
            $cfg['campos_form'] = $cf;
        }
        $hidden = $raw['campos_hidden'] ?? null;
        if (is_array($hidden)) {
            $hiddenOut = [];
            foreach ($hidden as $k => $v) {
                if (is_string($k)) {
                    $hiddenOut[$k] = $v;
                }
            }
            if ($hiddenOut !== []) {
                $cfg['campos_hidden'] = $hiddenOut;
            }
        }

        return $cfg;
    }

    /**
     * @return array{opciones: array<int|string, string>, opcion_sel: string}|null
     */
    public static function desplegableSelect(mixed $raw): ?array
    {
        if (!is_array($raw)) {
            return null;
        }
        $opcionesRaw = $raw['opciones'] ?? null;
        if (!is_array($opcionesRaw)) {
            return null;
        }

        return [
            'opciones' => NotasFormSupport::desplegableOpciones($opcionesRaw),
            'opcion_sel' => PayloadCoercion::string($raw['opcion_sel'] ?? ''),
        ];
    }
}
