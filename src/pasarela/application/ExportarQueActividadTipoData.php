<?php

namespace src\pasarela\application;

use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;
use src\permisos\domain\MenuDlPermissionBits;

/**
 * Configuración del selector de tipo de actividad de la pantalla «exportar qué» (sección según permisos, modo
 * jefe). El widget lo pinta `frontend/pasarela/controller/exportar_que.php` con `ActividadTipo::htmlDesdeConfig()`.
 */
final class ExportarQueActividadTipoData
{
    /**
     * @param array<string, string> $input
     * @return array{actividad_tipo: array<string, mixed>}
     */
    public function execute(array $input): array
    {
        $Qid_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $Qsasistentes = (string)($input['sasistentes'] ?? '');
        $Qsactividad = (string)($input['sactividad'] ?? '');
        $Qsnom_tipo = (string)($input['snom_tipo'] ?? '');

        $isfsv = ConfigGlobal::mi_sfsv();
        $oPerm = $_SESSION['oPerm'] ?? null;
        if ($oPerm instanceof XPermisos
            && ($oPerm->have_perm_oficina(MenuDlPermissionBits::VCSD)
                || $oPerm->have_perm_oficina(MenuDlPermissionBits::PFFE)
                || $oPerm->have_perm_oficina(MenuDlPermissionBits::CALENDARIO))
        ) {
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

        $oConfig = $_SESSION['oConfig'] ?? null;
        $perm_jefe = false;
        if ((is_object($oConfig) && method_exists($oConfig, 'is_jefeCalendario') && $oConfig->is_jefeCalendario())
            || ($oPerm instanceof XPermisos
                && ($oPerm->have_perm_oficina(MenuDlPermissionBits::PFFE) || $oPerm->have_perm_oficina(MenuDlPermissionBits::VCSD))
                && ConfigGlobal::mi_sfsv() === 1)
            || ($oPerm instanceof XPermisos
                && $oPerm->only_perm(MenuDlPermissionBits::ADMIN_SF)
                && ConfigGlobal::mi_sfsv() === 2)
        ) {
            $perm_jefe = true;
        }

        return [
            'actividad_tipo' => [
                'perm_jefe' => $perm_jefe,
                'id_tipo_activ' => $Qid_tipo_activ,
                'sfsv' => $ssfsv,
                'asistentes' => $Qsasistentes,
                'actividad' => $Qsactividad,
                'nom_tipo' => $Qsnom_tipo,
                'evitar_procesos' => true,
                'sfsv_all' => true,
            ],
        ];
    }
}
