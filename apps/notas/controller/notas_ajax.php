<?php

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use asignaturas\model\entity\Asignatura;
use asignaturas\model\entity\GestorAsignatura;
use core\ConfigGlobal;
use notas\model\entity\GestorActa;
use notas\model\entity\GestorNota;
use notas\model\entity\GestorPersonaNotaDB;
use notas\model\entity\PersonaNotaDB;
use profesores\model\entity\GestorProfesor;
use src\ubis\application\services\DelegacionDropdown;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');

switch ($Qque) {
    case 'buscar_acta':
        $Qacta = (string)filter_input(INPUT_POST, 'acta');
        // si es número busca en la dl.
        $matches = [];
        preg_match("/^(\d*)(\/)?(\d*)/", $Qacta, $matches);
        if (!empty($matches[1])) {
            $mi_dele = ConfigGlobal::mi_delef();
            $Qacta = empty($matches[3]) ? "$mi_dele " . $matches[1] . '/' . date("y") : "$mi_dele $Qacta";
        }
        $GesActas = new GestorActa();
        $cActas = $GesActas->getActas(['acta' => $Qacta]);
        $json = "{\"id_asignatura\":\"no\"}";
        if (count($cActas) === 1) {
            $oActa = $cActas[0];
            $f_acta = $oActa->getF_acta()->getFromLocal();
            $id_asignatura = $oActa->getId_asignatura();
            $id_activ = $oActa->getId_activ();
            if (!empty($id_activ)) {
                $oActividad = new ActividadAll($id_activ);
                $nom_activ = $oActividad->getNom_activ();
                $id_tipo_actividad = $oActividad->getId_tipo_activ();
                $epoca = PersonaNotaDB::EPOCA_CA;
                if ($id_tipo_actividad === 132500) { //sem invierno
                    $epoca = PersonaNotaDB::EPOCA_INVIERNO;
                }
            } else {
                $nom_activ = '';
                $epoca = PersonaNotaDB::EPOCA_OTRO;
            }
            // hace falta el id_nivel (para las no opcionales):
            $oAsignatura = new Asignatura($id_asignatura);
            $id_nivel = $oAsignatura->getId_nivel();
            $json = "{\"id_asignatura\":\"$id_asignatura\",
                        \"id_nivel\":\"$id_nivel\",
                        \"id_activ\":\"$id_activ\",
                          \"f_acta\":\"$f_acta\",
                       \"nom_activ\":\"$nom_activ\",
                       \"epoca\":\"$epoca\",
                       \"acta\":\"$Qacta\"
                    }";

        }
        echo $json;
        break;
    case 'frm_buscar':
        $Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
        $Qf_acta_iso = (string)filter_input(INPUT_POST, 'f_acta_string');
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');

        if (empty($Qdl_org)) {
            $Qdl_org = ConfigGlobal::mi_delef();
        }

        $oDesplDelegacionesOrg = DelegacionDropdown::delegacionesURegiones();
        $oDesplDelegacionesOrg->setNombre('dl_org');
        $oDesplDelegacionesOrg->setOpcion_sel($Qdl_org);
        $oDesplDelegacionesOrg->setAction('fnjs_buscar_ca()');


        if (!empty($Qf_acta_iso)) { // 3 meses cerca de la fecha del acta.
            $oF_acta = new DateTime($Qf_acta_iso);
            $oData2 = new DateTime($Qf_acta_iso);
            $oF_acta->add(new DateInterval('P3M'));
            $f_fin_iso = $oF_acta->format('Y-m-d');
            $oData2->sub(new DateInterval('P3M'));
            $f_ini_iso = $oData2->format('Y-m-d');
        } else { // desde hoy, 10 meses antes.
            $oData = new web\DateTimeLocal();
            $oData2 = clone $oData;
            $oData->add(new DateInterval('P1M'));
            $f_fin_iso = $oData->format('Y-m-d');
            $oData2->sub(new DateInterval('P10M'));
            $f_ini_iso = $oData2->format('Y-m-d');
        }
        $aWhere = [];
        $aOperador = [];
        $aWhere['f_ini'] = "'$f_ini_iso','$f_fin_iso'";
        $aOperador['f_ini'] = 'BETWEEN';
        $aWhere['id_tipo_activ'] = '^1(12|33)';
        $aOperador['id_tipo_activ'] = '~';
        $aWhere['_ordre'] = 'f_ini';
        $aWhere['dl_org'] = $Qdl_org;
        $GesActividades = new GestorActividad();
        $cActividades = $GesActividades->getActividades($aWhere, $aOperador);
        $aActividades = [];
        foreach ($cActividades as $oActividad) {
            $id_actividad = $oActividad->getId_activ();
            $nom_activ = $oActividad->getNom_activ();
            $aActividades[$id_actividad] = $nom_activ;
        }
        $oDesplActividades = new Desplegable();
        $oDesplActividades->setOpciones($aActividades);
        $oDesplActividades->setBlanco(1);
        $oDesplActividades->setNombre('id_activ_sel');
        $oDesplActividades->setOpcion_sel($Qid_activ);

        $oHash = new Hash();
        //$oHash->setUrl($url_ajax);
        //$oHash->setArrayCamposHidden(['que' => 'update', 'id_ubi' => $Qid_ubi]);

        $oHash->setCamposForm('pres_nom!pres_telf!pres_mail!zona!observ');
        $oHash->setCamposNo('scroll_id!sel');

        $txt = "<form id='frm_buscar'>";
        $txt .= '<h3>' . _("seleccionar la actividad") . '</h3>';
        $txt .= $oHash->getCamposHtml();
        $txt .= '<br>';
        $txt .= _("organniza");
        $txt .= '<br>';
        $txt .= $oDesplDelegacionesOrg->desplegable();
        $txt .= '<br>';
        $txt .= _("actividad");
        $txt .= '<br>';
        $txt .= $oDesplActividades->desplegable();
        $txt .= '<br>';
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_update_activ('#frm_buscar');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";

        echo $txt;
        break;
    case 'posibles_opcionales':
        // todas las opcionales
        $aWhere = [];
        $aOperador = [];
        $aWhere['status'] = 't';
        $aWhere['id_nivel'] = '3000,5000';
        $aOperador['id_nivel'] = 'BETWEEN';
        $aWhere['_ordre'] = 'nombre_corto';
        $GesAsignaturas = new GestorAsignatura();
        $cOpcionales = $GesAsignaturas->getAsignaturas($aWhere, $aOperador);
        // Asignaturas opcionales superadas
        $GesNotas = new GestorNota();
        $cSuperadas = $GesNotas->getNotas(array('superada' => 't'));
        $cond = '';
        $c = 0;
        foreach ($cSuperadas as $Nota) {
            if ($c > 0) $cond .= '|';
            $c++;
            $cond .= $Nota->getId_situacion();
        }
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_situacion'] = $cond;
        $aOperador['id_situacion'] = '~';
        $aWhere['id_nom'] = $Qid_nom;
        $aWhere['id_asignatura'] = 3000;
        $aOperador['id_asignatura'] = '>';
        $GesPersonaNotas = new GestorPersonaNotaDB();
        $cAsignaturasOpSuperadas = $GesPersonaNotas->getPersonaNotas($aWhere, $aOperador);
        $aOpSuperadas = [];
        foreach ($cAsignaturasOpSuperadas as $oAsignatura) {
            $id_asignatura = $oAsignatura->getId_asignatura();
            $aOpSuperadas[$id_asignatura] = $id_asignatura;
        }
        // asignaturas opcionales posibles
        $aFaltan = [];
        foreach ($cOpcionales as $oAsignatura) {
            $id_asignatura = $oAsignatura->getId_asignatura();
            $nombre_corto = $oAsignatura->getNombre_corto();
            if (array_key_exists($id_asignatura, $aOpSuperadas)) continue;
            $aFaltan[$id_asignatura] = $nombre_corto;
        }

        $oDesplPosiblesOpcionales = new Desplegable();
        $oDesplPosiblesOpcionales->setNombre('id_asignatura');
        $oDesplPosiblesOpcionales->setOpciones($aFaltan);
//		$oDesplPosiblesOpcionales->setOpcion_sel($Qid_asignatura);
        $oDesplPosiblesOpcionales->setBlanco(1);
        echo $oDesplPosiblesOpcionales->desplegable();
        break;

    case 'posibles_preceptores':
        $GesProfes = new GestorProfesor();
        $oDesplProfesores = $GesProfes->getListaProfesores();
        $oDesplProfesores->setBlanco(1);
        $oDesplProfesores->setNombre('id_preceptor');
        echo $oDesplProfesores->desplegable();
        /*
        $cProfesores= $GesProfes->getProfesores();
        $aProfesores=[];
        $msg_err = '';
        foreach ($cProfesores as $oProfesor) {
            $id_nom=$oProfesor->getId_nom();
            $oPersona = personas\Persona::NewPersona($id_nom);
            if (!is_object($oPersona)) {
                $msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
                continue;
            }
            $ap_nom=$oPersona->getPrefApellidosNombre();
            $aProfesores[$id_nom]=$ap_nom;
        }
        uasort($aProfesores,'core\strsinacentocmp');

        $oDesplProfesores = new Desplegable();
        $oDesplProfesores->setOpciones($aProfesores);
        $oDesplProfesores->setBlanco(1);
        $oDesplProfesores->setNombre('id_preceptor');
        echo $oDesplProfesores->desplegable();
         *
         */
        break;

}