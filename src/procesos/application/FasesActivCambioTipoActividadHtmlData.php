<?php

namespace src\procesos\application;

use src\permisos\domain\XPermisos;

/**
 * Payload para fases_activ_cambio: HTML del selector tipo actividad.
 */
final class FasesActivCambioTipoActividadHtmlData
{
    public function __construct(
        private readonly FasesActivCambioActividadTipoHtml $actividadTipoHtml,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{tipo_actividad_html: string}
     */
    public function execute(array $input): array
    {
        $extendida = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sactividad2') !== '';

        $permiso_des = false;
        $ssfsv = '';
        $oPerm = $_SESSION['oPerm'] ?? null;
        if ($oPerm instanceof XPermisos
            && ($oPerm->have_perm_oficina('vcsd')
                || $oPerm->have_perm_oficina('des')
                || $oPerm->have_perm_oficina('calendario'))
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

        $html = $this->actividadTipoHtml->render(
            $permiso_des,
            $ssfsv,
            $extendida,
            \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_tipo_activ'),
            \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sasistentes'),
            \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sactividad'),
            \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sactividad2'),
        );

        return ['tipo_actividad_html' => $html];
    }
}
