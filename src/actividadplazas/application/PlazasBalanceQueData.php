<?php

namespace src\actividadplazas\application;

use src\actividades\domain\entity\TiposActividades;
use src\shared\config\ConfigGlobal;
use src\ubis\application\services\DelegacionDropdown;

/**
 * Opciones del desplegable de delegaciones + `id_tipo_activ` resuelto para
 * {@see frontend/actividadplazas/controller/plazas_balance_que.php}.
 */
final class PlazasBalanceQueData
{
    /**
     * @param array<string, mixed> $input POST (id_tipo_activ, sasistentes, sactividad, …)
     * @return array{id_tipo_activ: string, delegaciones_opciones: array<string, string>}
     */
    public static function execute(array $input): array
    {
        $idTipo = (string)($input['id_tipo_activ'] ?? '');
        if ($idTipo === '') {
            $ssfsv = '';
            $mi = (int)ConfigGlobal::mi_sfsv();
            if ($mi === 1) {
                $ssfsv = 'sv';
            }
            if ($mi === 2) {
                $ssfsv = 'sf';
            }
            $sa = (string)($input['sasistentes'] ?? '');
            $sact = (string)($input['sactividad'] ?? '');
            $oTipoActiv = new TiposActividades();
            $oTipoActiv->setSfsvText($ssfsv);
            $oTipoActiv->setAsistentesText($sa);
            $oTipoActiv->setActividadText($sact);
            $idTipo = (string)$oTipoActiv->getId_tipo_activ();
        }

        return [
            'id_tipo_activ' => $idTipo,
            'delegaciones_opciones' => DelegacionDropdown::activasOrdenNombre(),
        ];
    }
}
