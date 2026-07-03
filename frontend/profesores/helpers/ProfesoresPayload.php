<?php

declare(strict_types=1);

namespace frontend\profesores\helpers;

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ProfesoresPayload
{
    /**
     * @param array<int|string, mixed> $data
     * @return array<string, mixed>
     */
    public static function fichaViewVars(array $data): array
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
    public static function listaTablaFromPayload(array $data): array
    {
        return [
            'id_tabla' => PayloadCoercion::string($data['id_tabla'] ?? ''),
            'a_cabeceras' => ActividadesListaSupport::cabeceras($data['a_cabeceras'] ?? []),
            'a_botones' => ActividadesListaSupport::botones($data['a_botones'] ?? []),
            'a_valores' => ActividadesListaSupport::datos($data['a_valores'] ?? []),
        ];
    }
}
