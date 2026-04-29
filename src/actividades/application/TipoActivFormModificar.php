<?php

namespace src\actividades\application;

use frontend\shared\helpers\TipoActivGestionFormHashCompose;
use src\actividades\domain\entity\TiposActividades;

/**
 * Devuelve el HTML del formulario para modificar/eliminar un tipo de actividad
 * existente. Portado del case `form_modificar` del dispatcher legacy.
 */
class TipoActivFormModificar
{
    public function execute(array $input = []): string
    {
        $Qid_tipo_activ = (int)($input['id_tipo_activ'] ?? 0);
        $oTiposActividades = new TiposActividades($Qid_tipo_activ);

        $nom_actividad = $oTiposActividades->getSfsvText();
        $nom_actividad .= ' ' . $oTiposActividades->getAsistentesText();
        $nom_actividad .= ' ' . $oTiposActividades->getActividadText();

        $nom_tipo = $oTiposActividades->getNom_tipoText();

        $txt = "<form id='frm_tipo_activ'>";
        $txt .= TipoActivGestionFormHashCompose::modificarHiddenHtml($Qid_tipo_activ);
        $txt .= '<h3>' . $nom_actividad . '</h3>';
        $txt .= _("nombre") . ": <input type=text size=25 id=nom_tipo_activ  name=nom_tipo_activ value=\"$nom_tipo\">";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_tipo_activ','update');\" >";
        $txt .= "<input type='button' value='" . _("eliminar") . "' onclick=\"fnjs_guardar('#frm_tipo_activ','eliminar');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";

        return $txt;
    }
}
