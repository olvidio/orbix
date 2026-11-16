<?php

namespace src\procesos\application;

use src\permisos\domain\XPermisos;
use src\permisos\domain\MenuDlPermissionBits;

/**
 * Configuración del selector de tipo de actividad de fases_activ_cambio: con permiso de des, vcsd o calendario,
 * modo jefe y todas las secciones; si no, la sección propia (sv/sf). El widget lo pinta
 * `frontend/procesos/controller/fases_activ_cambio.php` con `ActividadTipo::htmlDesdeConfig()`.
 */
final class FasesActivCambioTipoActividadData
{

    /**
     * @param array<string, mixed> $input
     * @return array{actividad_tipo: array<string, mixed>}
     */
    public function execute(array $input): array
    {
        $extendida = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sactividad2') !== '';

        $permiso_des = false;
        $ssfsv = '';
        $oPerm = $_SESSION['oPerm'] ?? null;
        if ($oPerm instanceof XPermisos
            && ($oPerm->have_perm_oficina(MenuDlPermissionBits::VCSD)
                || $oPerm->have_perm_oficina(MenuDlPermissionBits::DES)
                || $oPerm->have_perm_oficina(MenuDlPermissionBits::CALENDARIO))
        ) {
            $permiso_des = true;
            $ssfsv = '';
        } else {
            $sessionAuth = $_SESSION['session_auth'] ?? null;
            $mi_sfsv = 0;
            if (is_array($sessionAuth) && isset($sessionAuth['sfsv']) && is_numeric($sessionAuth['sfsv'])) {
                $mi_sfsv = (int) $sessionAuth['sfsv'];
            }
            if ($mi_sfsv === 1) {
                $ssfsv = 'sv';
            }
            if ($mi_sfsv === 2) {
                $ssfsv = 'sf';
            }
        }

        return ['actividad_tipo' => [
            'perm_jefe' => $permiso_des,
            'sfsv' => $ssfsv,
            'id_tipo_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_tipo_activ'),
            'asistentes' => \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sasistentes'),
            'actividad' => \src\shared\domain\helpers\FuncTablasSupport::inputString($input, $extendida ? 'sactividad2' : 'sactividad'),
            'para' => 'procesos',
            'que' => 'buscar',
            'extendida' => $extendida,
        ]];
    }
}
