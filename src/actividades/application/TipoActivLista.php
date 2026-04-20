<?php

namespace src\actividades\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use web\Lista;
use web\TiposActividades;

/**
 * Devuelve la tabla HTML con los tipos de actividad existentes. Portado desde
 * el case `lista` del dispatcher legacy frontend/actividades/controller/tipo_activ_ajax.php.
 */
class TipoActivLista
{
    public function execute(array $input = []): string
    {
        $aWhere = ['_ordre' => 'id_tipo_activ'];
        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $cTiposDeActividades = $TipoDeActividadRepository->getTiposDeActividades($aWhere);

        $a_cabeceras = [
            _("id_tipo_activ"),
            _("tipo actividad"),
            _("modificar"),
        ];

        $a_valores = [];
        $i = 0;
        foreach ($cTiposDeActividades as $oTipo) {
            $i++;
            $id_tipo_activ = $oTipo->getId_tipo_activ();
            $oTiposActividades = new TiposActividades($id_tipo_activ);
            $a_valores[$i][1] = $id_tipo_activ;
            $a_valores[$i][2] = $oTiposActividades->getNom();

            $texto_link = _("modificar");
            $id_txt_mod = 'mod_' . $id_tipo_activ;
            $a_valores[$i][3] = "<span class=link id=$id_txt_mod onclick=fnjs_modificar('$id_tipo_activ')> $texto_link</span>";
        }

        $oLista = new Lista();
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        return $oLista->lista();
    }
}
