<?php

namespace src\actividades\application;

use actividades\model\ActividadTipo;
use web\Hash;

/**
 * Devuelve el HTML del formulario para crear un nuevo tipo de actividad.
 * Portado del case `form_nuevo` del dispatcher legacy.
 */
class TipoActivFormNuevo
{
    public function execute(array $input = []): string
    {
        $oHash = new Hash();
        $oHash->setCamposForm('iactividad_val!iasistentes_val!id_nom_tipo_activ!isfsv_val!nom_tipo_activ');

        $oActividadTipo = new ActividadTipo();
        $oActividadTipo->setPerm_jefe(true);
        $oActividadTipo->setEvitarProcesos(true);
        $oActividadTipo->setPara('gestion');

        // ActividadTipo::getHtml() hace echo directamente del twig renderizado,
        // por lo que capturamos la salida con ob_* para devolverla como string.
        ob_start();
        $oActividadTipo->getHtml(true);
        $htmlTipo = (string)ob_get_clean();

        $txt = "<form id='frm_tipo_activ'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>NUEVO TIPO</h3>';
        $txt .= $htmlTipo;
        $txt .= '<br><table>';
        $txt .= _("id") . ": <input type=text size=5 id=id_nom_tipo_activ  name=id_nom_tipo_activ value=\"\">";
        $txt .= _("nombre") . ": <input type=text size=25 id=nom_tipo_activ  name=nom_tipo_activ value=\"\">";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar_nuevo('#frm_tipo_activ');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";

        return $txt;
    }
}
