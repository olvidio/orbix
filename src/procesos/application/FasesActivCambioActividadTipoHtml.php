<?php

namespace src\procesos\application;

use frontend\actividades\helpers\ActividadTipo;

/**
 * HTML del selector de tipo de actividad para fases_activ_cambio.
 */
final class FasesActivCambioActividadTipoHtml
{
    public function render(
        bool $permiso_des,
        string $ssfsv,
        bool $extendida,
        string $id_tipo_activ,
        string $sasistentes,
        string $sactividad_no_extendida,
        string $sactividad2_extendida,
    ): string {
        $o = new ActividadTipo();
        $o->setPerm_jefe($permiso_des);
        $o->setSfsv($ssfsv);
        $o->setId_tipo_activ($id_tipo_activ);
        $o->setAsistentes($sasistentes);
        if ($extendida) {
            $o->setActividad2Digitos($sactividad2_extendida);
        } else {
            $o->setActividad($sactividad_no_extendida);
        }
        $o->setPara('procesos');
        $o->setQue('buscar');

        return $o->captureHtml($extendida);
    }
}
