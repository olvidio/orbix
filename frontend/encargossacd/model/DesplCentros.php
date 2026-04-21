<?php

namespace frontend\encargossacd\model;

use frontend\shared\PostRequest;
use web\Desplegable;

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
    public static function build(int $filtro_ctr, int $id_ubi = 0, int $id_zona = 0): Desplegable
    {
        /** @var array<string, mixed> $data */
        $data = PostRequest::getDataFromUrl('/src/encargossacd/ctr_get_select_data', [
            'id_ubi' => $id_ubi,
            'filtro_ctr' => $filtro_ctr,
            'id_zona' => $id_zona,
        ]);

        $oDesplCtr = new Desplegable();
        $oDesplCtr->setNombre((string)($data['id'] ?? 'lst_ctrs'));
        $oDesplCtr->setOpciones(is_array($data['opciones'] ?? null) ? $data['opciones'] : []);
        if (!empty($data['blanco'])) {
            $oDesplCtr->setBlanco(true);
        }
        if (array_key_exists('val_blanco', $data)) {
            $oDesplCtr->setValBlanco((string)$data['val_blanco']);
        }
        $oDesplCtr->setOpcion_sel((string)($data['selected'] ?? ''));
        $oDesplCtr->setAction((string)($data['action'] ?? ''));

        return $oDesplCtr;
    }
}
