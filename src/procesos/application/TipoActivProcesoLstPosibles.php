<?php

namespace src\procesos\application;

use core\ConfigGlobal;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;

/**
 * Caso de uso: devuelve la mini-tabla HTML con los procesos posibles
 * que el usuario puede asignar a un id_tipo_activ concreto.
 */
class TipoActivProcesoLstPosibles
{
    public function execute(array $input): string
    {
        $Qid_tipo_activ = (int)($input['id_tipo_activ'] ?? 0);
        $Qpropio = (string)($input['propio'] ?? '');
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $aWhere = ['sfsv' => $mi_sfsv, '_ordre' => 'nom_proceso'];
        $ProcesoTipoRepository = $GLOBALS['container']->get(ProcesoTipoRepositoryInterface::class);
        $cProcesosTipo = $ProcesoTipoRepository->getProcesoTipos($aWhere);
        $txt_lista = '';
        foreach ($cProcesosTipo as $oProcesoTipo) {
            $id_tipo_proceso = $oProcesoTipo->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            $nom_proceso = $oProcesoTipo->getNom_proceso();
            $txt_lista .= "<tr><td class=link id=$id_tipo_proceso onclick=fnjs_asignar_proceso(event,'$Qid_tipo_activ','$Qpropio','$id_tipo_proceso')> $nom_proceso</td></tr>";
        }

        return "<table><tr><td class=cabecera>" . _("procesos") . "</td></tr>$txt_lista</table>";
    }
}
