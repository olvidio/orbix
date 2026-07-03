<?php

declare(strict_types=1);

namespace frontend\cambios\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class CambiosPayload
{
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

    /**
     * @return array<string, mixed>
     */
    public static function hashCamposHidden(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $k => $v) {
            if (is_string($k)) {
                $out[$k] = $v;
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     effective_id_usuario: int,
     *     effective_aviso_tipo: int,
     *     a_valores: array<int|string, mixed>,
     *     aOpcionesUsuarios: array<int|string, string>,
     *     aOpcionesAvisoTipo: array<int|string, string>,
     *     url_eliminar: string,
     *     url_eliminar_fecha: string,
     *     h_eliminar: string,
     *     h_eliminar_fecha: string,
     * }
     */
    public static function avisosGenerarFromPayload(array $payload): array
    {
        return [
            'effective_id_usuario' => PayloadCoercion::int($payload['effective_id_usuario'] ?? 0),
            'effective_aviso_tipo' => PayloadCoercion::int($payload['effective_aviso_tipo'] ?? 0),
            'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
            'aOpcionesUsuarios' => NotasFormSupport::desplegableOpciones($payload['aOpcionesUsuarios'] ?? []),
            'aOpcionesAvisoTipo' => NotasFormSupport::desplegableOpciones($payload['aOpcionesAvisoTipo'] ?? []),
            'url_eliminar' => PayloadCoercion::string($payload['url_eliminar'] ?? ''),
            'url_eliminar_fecha' => PayloadCoercion::string($payload['url_eliminar_fecha'] ?? ''),
            'h_eliminar' => PayloadCoercion::string($payload['h_eliminar'] ?? ''),
            'h_eliminar_fecha' => PayloadCoercion::string($payload['h_eliminar_fecha'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     aTiposAviso: array<int|string, string>,
     *     aObjetos: array<int|string, string>,
     *     aFases: array<int|string, string>,
     *     aOpcionesCasas: array<int|string, string>,
     *     id_pau: string,
     *     aviso_tipo: string,
     *     objeto: string,
     *     id_fase_ref: string,
     *     dl_propia: bool,
     *     aviso_off: bool,
     *     aviso_on: bool,
     *     aviso_outdate: bool,
     *     id_tipo_activ: string,
     * }
     */
    public static function usuarioAvisosPrefFormFromPayload(array $payload): array
    {
        return [
            'aTiposAviso' => NotasFormSupport::desplegableOpciones($payload['aTiposAviso'] ?? []),
            'aObjetos' => NotasFormSupport::desplegableOpciones($payload['aObjetos'] ?? []),
            'aFases' => NotasFormSupport::desplegableOpciones($payload['aFases'] ?? []),
            'aOpcionesCasas' => NotasFormSupport::desplegableOpciones($payload['aOpcionesCasas'] ?? []),
            'id_pau' => PayloadCoercion::string($payload['id_pau'] ?? ''),
            'aviso_tipo' => PayloadCoercion::string($payload['aviso_tipo'] ?? ''),
            'objeto' => PayloadCoercion::string($payload['objeto'] ?? ''),
            'id_fase_ref' => PayloadCoercion::string($payload['id_fase_ref'] ?? ''),
            'dl_propia' => !array_key_exists('dl_propia', $payload) || !empty($payload['dl_propia']),
            'aviso_off' => !empty($payload['aviso_off']),
            'aviso_on' => !array_key_exists('aviso_on', $payload) || !empty($payload['aviso_on']),
            'aviso_outdate' => !empty($payload['aviso_outdate']),
            'id_tipo_activ' => PayloadCoercion::string($payload['id_tipo_activ'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     valor: string,
     *     operador: string,
     *     chk_old: string,
     *     chk_new: string,
     *     aOpcionesCasas: array<int|string, string>,
     * }
     */
    public static function usuarioAvisosPrefCondicionFromPayload(array $payload): array
    {
        return [
            'valor' => PayloadCoercion::string($payload['valor'] ?? ''),
            'operador' => PayloadCoercion::string($payload['operador'] ?? ''),
            'chk_old' => PayloadCoercion::string($payload['chk_old'] ?? 'checked'),
            'chk_new' => PayloadCoercion::string($payload['chk_new'] ?? 'checked'),
            'aOpcionesCasas' => NotasFormSupport::desplegableOpciones($payload['aOpcionesCasas'] ?? []),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function propiedadesRows(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            if (is_array($item)) {
                $out[] = $item;
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function propiedadNomProp(array $row): string
    {
        return PayloadCoercion::string($row['nom_prop'] ?? '');
    }

    /**
     * @param array<string, mixed> $data
     * @return array{a_valores: array<int|string, mixed>, nombre_usuario: string}
     */
    public static function usuarioFormAvisosFromPayload(array $data): array
    {
        return [
            'a_valores' => ActividadesListaSupport::datos($data['a_valores'] ?? []),
            'nombre_usuario' => PayloadCoercion::string($data['nombre_usuario'] ?? ''),
        ];
    }
}
