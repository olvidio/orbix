<?php

namespace src\pasarela\application;

use frontend\actividades\helpers\ActividadTipo;
use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;

/**
 * HTML del selector de tipo de actividad para la pantalla «exportar qué».
 */
final class ExportarQueActividadTipoHtml
{
    /**
     * @param array<string, string> $input
     * @return array{html: string}
     */
    public function execute(array $input): array
    {
        $Qid_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $Qsasistentes = (string)($input['sasistentes'] ?? '');
        $Qsactividad = (string)($input['sactividad'] ?? '');
        $Qsnom_tipo = (string)($input['snom_tipo'] ?? '');

        $isfsv = ConfigGlobal::mi_sfsv();
        $oPerm = $_SESSION['oPerm'] ?? null;
        $permiso_des = false;
        if ($oPerm instanceof XPermisos
            && ($oPerm->have_perm_oficina('vcsd')
                || $oPerm->have_perm_oficina('des')
                || $oPerm->have_perm_oficina('calendario'))
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

        $oConfig = $_SESSION['oConfig'] ?? null;
        $perm_jefe = false;
        if ((is_object($oConfig) && method_exists($oConfig, 'is_jefeCalendario') && $oConfig->is_jefeCalendario())
            || ($oPerm instanceof XPermisos
                && ($oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('vcsd'))
                && ConfigGlobal::mi_sfsv() === 1)
            || ($oPerm instanceof XPermisos
                && $oPerm->have_perm_oficina('admin_sf')
                && ConfigGlobal::mi_sfsv() === 2)
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
