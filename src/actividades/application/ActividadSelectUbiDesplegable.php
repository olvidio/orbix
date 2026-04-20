<?php

namespace src\actividades\application;

use web\Desplegable;

/**
 * Genera el HTML de los desplegables usados en la pantalla
 * "seleccionar lugar para una actividad". Agrupa la logica de datos
 * (ActividadSelectUbiData) con la construccion del widget Desplegable
 * para que el frontend solo tenga que inyectar el HTML resultante.
 */
final class ActividadSelectUbiDesplegable
{
    /**
     * @param string $tipo 'freq' o 'region'
     * @param string $dl_org Delegacion organizadora (para 'freq').
     * @param int $isfsv Filtro sfsv.
     * @return string HTML del desplegable (o vacio / mensaje si no procede).
     */
    public function ejecutar(string $tipo, string $dl_org, int $isfsv): string
    {
        $service = new ActividadSelectUbiData();
        $data = $service->execute([
            'dl_org' => $dl_org,
            'isfsv' => $isfsv,
        ]);

        switch ($tipo) {
            case 'freq':
                if ($dl_org === '') {
                    return _("falta saber quien organiza");
                }
                $oDespl = Desplegable::desdeOpciones($data['opcionesFreq'], 'id_ubi_1');
                return $oDespl->desplegable();

            case 'region':
                $oDespl = Desplegable::desdeOpciones($data['opcionesRegion'], 'filtro_lugar');
                $oDespl->setAction('fnjs_lugar()');
                if ($dl_org !== '') {
                    $oDespl->setOpcion_sel('dl|' . $dl_org);
                }
                return $oDespl->desplegable();

            default:
                return '';
        }
    }
}
