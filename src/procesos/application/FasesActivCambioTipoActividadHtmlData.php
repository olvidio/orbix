<?php

namespace src\procesos\application;

/**
 * Payload para {@see frontend/procesos/controller/fases_activ_cambio.php}:
 * HTML del selector tipo actividad (procesos) sin importar caso de uso en el frontend.
 */
final class FasesActivCambioTipoActividadHtmlData
{
    /**
     * @param array<string, mixed> $input post (campos centro / tipo actividad)
     * @return array{tipo_actividad_html: string}
     */
    public static function execute(array $input): array
    {
        $extendida = !empty((string)($input['sactividad2'] ?? ''));

        $permiso_des = false;
        $ssfsv = '';
        if ($_SESSION['oPerm']->have_perm_oficina('vcsd')
            || $_SESSION['oPerm']->have_perm_oficina('des')
            || $_SESSION['oPerm']->have_perm_oficina('calendario')
        ) {
            $permiso_des = true;
            $ssfsv = '';
        } else {
            $mi_sfsv = (int)($_SESSION['session_auth']['sfsv'] ?? 0);
            if ($mi_sfsv === 1) {
                $ssfsv = 'sv';
            }
            if ($mi_sfsv === 2) {
                $ssfsv = 'sf';
            }
        }

        $html = FasesActivCambioActividadTipoHtml::render(
            $permiso_des,
            $ssfsv,
            $extendida,
            (string)($input['id_tipo_activ'] ?? ''),
            (string)($input['sasistentes'] ?? ''),
            (string)($input['sactividad'] ?? ''),
            (string)($input['sactividad2'] ?? ''),
        );

        return ['tipo_actividad_html' => $html];
    }
}
