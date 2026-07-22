<?php

namespace frontend\encargossacd\model;

use frontend\encargossacd\helpers\EncargossacdPayload;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Helper que monta un {@see Desplegable} de centros consumiendo el endpoint
 * `/src/encargossacd/ctr_get_select_data`
 * ({@see \src\encargossacd\application\EncargoCtrSelectData}).
 *
 * Antes tenia estado mutable (`setIdZona`) aunque solo se usaba en uno de
 * los dos callers; ahora expone una unica entrada estatica `build()` con
 * argumentos explicitos, mas alineada con el resto de helpers frontend
 * (`PeriodoTdHelper`, `CuadriculaZonaRenderer`, `SacdFichaAjaxHashes`, ...).
 */
final class DesplCentros
{
    public static function build(int $filtro_ctr, int $id_ubi = 0, int $id_zona = 0, ?string $action = null): Desplegable
    {
        $campos = [
            'id_ubi' => $id_ubi,
            'filtro_ctr' => $filtro_ctr,
            'id_zona' => $id_zona,
        ];
        // null => el endpoint aplica el default (fnjs_ver_ficha(), para ctr_ficha).
        // '' => el select queda sin onchange (p.ej. encargo_ver). Solo se envía
        // cuando se quiere fijar explícitamente, para no arrastrar un hidden
        // `action=` que silencie el default en las vistas que no lo pasan.
        if ($action !== null) {
            $campos['action'] = $action;
        }

        /** @var array<string, mixed> $data */
        $data = PostRequest::getDataFromUrl('/src/encargossacd/ctr_get_select_data', $campos);

        $oDesplCtr = new Desplegable();
        $oDesplCtr->setNombre(\frontend\shared\helpers\PayloadCoercion::string($data['id'] ?? 'lst_ctrs'));
        $oDesplCtr->setOpciones(EncargossacdPayload::desplegableOpciones($data['opciones'] ?? []));
        if (!empty($data['blanco'])) {
            $oDesplCtr->setBlanco(true);
        }
        if (array_key_exists('val_blanco', $data)) {
            $oDesplCtr->setValBlanco(\frontend\shared\helpers\PayloadCoercion::string($data['val_blanco']));
        }
        $oDesplCtr->setOpcion_sel(\frontend\shared\helpers\PayloadCoercion::string($data['selected'] ?? ''));
        $oDesplCtr->setAction(\frontend\shared\helpers\PayloadCoercion::string($data['action'] ?? ''));

        return $oDesplCtr;
    }
}
