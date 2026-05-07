<?php

namespace src\pasarela\application;

use frontend\actividades\helpers\ActividadTipo;
use src\shared\config\ConfigGlobal;

/**
 * HTML del selector de tipo de actividad para la pantalla «exportar qué».
 * Replica la configuración que antes hacía {@see frontend\pasarela\controller\exportar_que}
 * sobre {@see ActividadTipo}.
 */
final class ExportarQueActividadTipoHtml
{
    public static function execute(array $input): array
    {
        $Qid_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $Qsasistentes = (string)($input['sasistentes'] ?? '');
        $Qsactividad = (string)($input['sactividad'] ?? '');
        $Qsnom_tipo = (string)($input['snom_tipo'] ?? '');

        $isfsv = ConfigGlobal::mi_sfsv();
        $permiso_des = false;
        if ($_SESSION['oPerm']->have_perm_oficina('vcsd')
            || $_SESSION['oPerm']->have_perm_oficina('des')
            || $_SESSION['oPerm']->have_perm_oficina('calendario')
        ) {
            $permiso_des = true;
            $ssfsv = '';
        } else {
            $ssfsv = '';
            if ($isfsv === 1) {
                $ssfsv = 'sv';
            }
            if ($isfsv === 2) {
                $ssfsv = 'sf';
            }
        }

        $oActividadTipo = new ActividadTipo();
        $oActividadTipo->setPerm_jefe($permiso_des);
        $oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
        $oActividadTipo->setSfsv($ssfsv);
        $oActividadTipo->setAsistentes($Qsasistentes);
        $oActividadTipo->setActividad($Qsactividad);
        $oActividadTipo->setNom_tipo($Qsnom_tipo);
        $oActividadTipo->setEvitarProcesos(true);

        $perm_jefe = false;
        if ($_SESSION['oConfig']->is_jefeCalendario()
            || (($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) && ConfigGlobal::mi_sfsv() === 1)
            || ($_SESSION['oPerm']->have_perm_oficina('admin_sf') && ConfigGlobal::mi_sfsv() === 2)
        ) {
            $perm_jefe = true;
        }
        $oActividadTipo->setPerm_jefe($perm_jefe);
        $oActividadTipo->setSfsvAll(true);

        return [
            'html' => $oActividadTipo->captureHtml(),
        ];
    }
}
