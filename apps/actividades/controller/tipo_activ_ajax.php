<?php

use actividades\model\entity\GestorTipoDeActividad;
use actividades\model\entity\TipoDeActividad;
use core\ConfigGlobal;
use web\Lista;
use web\TiposActividades;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'lista':
        $aWhere = ['_ordre' => 'id_tipo_activ'];
        $oGesTiposDeActividades = new GestorTipoDeActividad();
        $cTiposDeActividades = $oGesTiposDeActividades->getTiposDeActividades($aWhere);

        $a_cabeceras = [];
        $a_cabeceras[] = _("id_tipo_activ");
        $a_cabeceras[] = _("tipo actividad");
        $a_cabeceras[] = _("modificar");

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
            $modificar = "<span class=link id=$id_txt_mod onclick=fnjs_modificar('$id_tipo_activ')> $texto_link</span>";

            $a_valores[$i][3] = $modificar;
        }
        $oLista = new Lista();
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->lista();
        break;
    case 'form_nuevo':
        $oHash = new web\Hash();
        $camposForm = 'iactividad_val!iasistentes_val!id_nom_tipo_activ!isfsv_val!nom_tipo_activ!scroll_id';
        $oHash->setcamposForm($camposForm);
        $a_camposHidden = ['que' => ''];
        $oHash->setArrayCamposHidden($a_camposHidden);
        $oHash->setCamposNo('que');

        $oActividadTipo = new actividades\model\ActividadTipo();
        $oActividadTipo->setPerm_jefe(TRUE);
        $oActividadTipo->setEvitarProcesos(TRUE);
        $oActividadTipo->setPara('gestion');

        $txt = "<form id='frm_tipo_activ'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>NUEVO TIPO</h3>';
        $txt .= $oActividadTipo->getHtml(TRUE);
        $txt .= '<br><table>';
        $txt .= _("id") . ": <input type=text size=5 id=id_nom_tipo_activ  name=id_nom_tipo_activ value=\"\">";
        $txt .= _("nombre") . ": <input type=text size=25 id=nom_tipo_activ  name=nom_tipo_activ value=\"\">";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar_nuevo('#frm_tipo_activ');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";
        echo $txt;
        break;

    case 'form_modificar':
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
        $oTiposActividades = new TiposActividades($Qid_tipo_activ);

        $nom_actividad = $oTiposActividades->getSfsvText();
        $nom_actividad .= ' ' . $oTiposActividades->getAsistentesText();
        $nom_actividad .= ' ' . $oTiposActividades->getActividadText();

        $nom_tipo = $oTiposActividades->getNom_tipoText();

        $oHash = new web\Hash();
        $camposForm = 'nom_tipo_activ';
        $oHash->setCamposNo('que');
        $a_camposHidden = array(
            'que' => '',
            'id_tipo_activ' => $Qid_tipo_activ,
        );
        $oHash->setcamposForm($camposForm);
        $oHash->setArraycamposHidden($a_camposHidden);

        $txt = "<form id='frm_tipo_activ'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>' . $nom_actividad . "</h3>";
        $txt .= _("nombre") . ": <input type=text size=25 id=nom_tipo_activ  name=nom_tipo_activ value=\"$nom_tipo\">";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_tipo_activ','update');\" >";
        $txt .= "<input type='button' value='" . _("eliminar") . "' onclick=\"fnjs_guardar('#frm_tipo_activ','eliminar');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";
        echo $txt;
        break;

    case "nuevo":
        $Qsfsv = (string)filter_input(INPUT_POST, 'isfsv_val');
        $Qasistentes = (string)filter_input(INPUT_POST, 'iasistentes_val');
        $Qactividad = (string)filter_input(INPUT_POST, 'iactividad_val');
        $Qid_nom_tipo_activ = (string)filter_input(INPUT_POST, 'id_nom_tipo_activ');
        $Qnom_tipo_activ = (string)filter_input(INPUT_POST, 'nom_tipo_activ');

        $id_tipo_activ = "$Qsfsv$Qasistentes$Qactividad$Qid_nom_tipo_activ";

        // comprobar que tiene 6 digitos
        if (strlen($id_tipo_activ) != 6) {
            echo _("Id incorrecto");
        }
        $oTipoActividad = new TipoDeActividad($id_tipo_activ);
        $oTipoActividad->setNombre($Qnom_tipo_activ);
        if ($oTipoActividad->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
        }
        if (ConfigGlobal::is_app_installed('procesos')) {
            echo _("IMPORTANTE: Debe añadir un proceso para el nuevo tipo de actividad");
        }

        break;
    case "update":
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qnom_tipo_activ = (string)filter_input(INPUT_POST, 'nom_tipo_activ');

        $oTipoActividad = new TipoDeActividad($Qid_tipo_activ);
        $oTipoActividad->DBCarregar();
        $oTipoActividad->setNombre($Qnom_tipo_activ);
        if ($oTipoActividad->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
        }
        break;
    case "eliminar":
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');

        $oTipoActividad = new TipoDeActividad($Qid_tipo_activ);
        if ($oTipoActividad->DBEliminar() === false) {
            echo _("hay un error, no se ha eliminado");
        }
        break;
}
