<?php

use web\DateTimeLocal;
use ubis\model\entity\CasaPeriodo;
use ubis\model\entity\GestorCasaPeriodo;
use web\Hash;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'nuevo':
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        $Qyear = (integer)filter_input(INPUT_POST, 'year');

        //Buscar el último
        $f_next = '';
        $sf_chk = '';
        $sv_chk = '';
        if (!empty($Qid_ubi) && !empty($Qyear)) {
            $oTipoActividad = new web\TiposActividades();
            $inicio = $Qyear . "-1-1";
            $fin = $Qyear . "-12-31";
            $aWhere = [];
            $aOperador = [];
            $aWhere['id_ubi'] = $Qid_ubi;
            $aWhere['f_ini'] = $inicio;
            $aOperador['f_ini'] = '>=';
            $aWhere['f_fin'] = $fin;
            $aOperador['f_fin'] = '<=';
            $aWhere['_ordre'] = 'f_fin DESC';
            $GesCasaPeriodos = new GestorCasaPeriodo();
            $cCasaPeriodos = $GesCasaPeriodos->getCasaPeriodos($aWhere, $aOperador);
            // Cojo el último
            if (!empty($cCasaPeriodos)) {
                $oCasaPeriodo = $cCasaPeriodos[0];
                $oF_fin = $oCasaPeriodo->getF_fin();
                $oF_next = $oF_fin->add(new DateInterval('P1D'));
                $f_next = $oF_next->getFromLocal();
                $sfsv = $oCasaPeriodo->getSfsv();
                if ($sfsv == 1) {
                    $sf_chk = 'selected';
                    $sv_chk = '';
                } else {
                    $sf_chk = '';
                    $sv_chk = 'selected';
                }
            }
        }

        $oHash = new Hash();
        $a_camposHidden = array(
            'que' => 'guardar',
            'id_ubi' => $Qid_ubi,
            'year' => $Qyear,
        );
        $camposForm = 'f_ini!f_fin!sfsv';
        $oHash->setCamposForm($camposForm);
        $oHash->setArraycamposHidden($a_camposHidden);

        $txt = "<form id='frm_periodo'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>' . _("periodo") . '</h3>';
        $txt .= _("de") . "<input type=text size=12 name=f_ini value=\"$f_next\">   " . _("hasta") . " <input type=text size=12 name=f_fin value=\"\">";
        $txt .= _("asignado a") . " <select name=sfsv>";
        $txt .= "<option value=1 $sv_chk>" . _("sv") . "</option>";
        $txt .= "<option value=2 $sf_chk>" . _("sf") . "</option>";
        $txt .= "<option value=3 >" . _("reservado") . "</option></select>";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar(this.form,'guardar');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";
        echo $txt;
        break;
    case 'form_periodo':
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $oCasaPeriodo = new CasaPeriodo(array('id_item' => $Qid_item));
        $f_ini = $oCasaPeriodo->getF_ini()->getFromLocal();
        $f_fin = $oCasaPeriodo->getF_fin()->getFromLocal();
        $sfsv = $oCasaPeriodo->getSfsv();
        if ($sfsv == 1) {
            $sel_sv = "selected";
        } else {
            $sel_sv = "";
        }
        if ($sfsv == 2) {
            $sel_sf = "selected";
        } else {
            $sel_sf = "";
        }
        if ($sfsv == 3) {
            $sel_res = "selected";
        } else {
            $sel_res = "";
        }

        $oHash = new Hash();
        $a_camposHidden = array(
            'que' => 'update',
            'id_item' => $Qid_item,
        );
        $camposForm = 'f_ini!f_fin!sfsv';
        $camposNo = 'que';
        $oHash->setCamposNo($camposNo);
        $oHash->setCamposForm($camposForm);
        $oHash->setArraycamposHidden($a_camposHidden);

        $txt = "<form id='frm_periodo'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>' . _("periodo") . '</h3>';
        $txt .= _("de") . "<input type=text size=12 name=f_ini value=\"$f_ini\">   " . _("hasta") . " <input type=text size=12 name=f_fin value=\"$f_fin\">";
        $txt .= _("asignado a") . " <select name=sfsv><option value=1 $sel_sv>" . _("sv") . "</option>";
        $txt .= "<option value=2 $sel_sf>" . _("sf") . "</option>";
        $txt .= "<option value=3 $sel_res>" . _("reservado") . "</option></select>";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar(this.form,'guardar');\" >";
        $txt .= "<input type='button' value='" . _("eliminar") . "' onclick=\"fnjs_guardar(this.form,'eliminar');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";
        echo $txt;
        break;
    case "get2":
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        $Qyear = (integer)filter_input(INPUT_POST, 'year');
        // permisos:
        if ($_SESSION['oConfig']->is_jefeCalendario()) {
            $permiso = 'modificar';
        } else {
            $permiso = '';
        }
        $permiso = 'modificar';
        // listado de periodos por casa y año
        if (!empty($Qid_ubi) && !empty($Qyear)) {
            $oTipoActividad = new web\TiposActividades();
            $inicio = $Qyear . "-1-1";
            $fin = $Qyear . "-12-31";
            $aWhere = [];
            $aOperador = [];
            $aWhere['id_ubi'] = $Qid_ubi;
            $aWhere['f_ini'] = $inicio;
            $aOperador['f_ini'] = '>=';
            $aWhere['f_fin'] = $fin;
            $aOperador['f_fin'] = '<=';
            $aWhere['_ordre'] = 'f_ini';
            $GesCasaPeriodos = new GestorCasaPeriodo();
            $cCasaPeriodos = $GesCasaPeriodos->getCasaPeriodos($aWhere, $aOperador);
        } else {
            $cCasaPeriodos = [];
        }

        $a_cabeceras = [];
        $a_cabeceras[] = _("desde");
        $a_cabeceras[] = _("hasta");
        $a_cabeceras[] = _("asignado a");

        $i = 0;
        $txt = '';
        $error_txt = '';
        $a_valores = [];
        $cPeriodos = [];
        foreach ($cCasaPeriodos as $oCasaPeriodo) {
            $i++;
            $id_item = $oCasaPeriodo->getId_item();
            $id_ubi = $oCasaPeriodo->getId_ubi();
            $oF_ini = $oCasaPeriodo->getF_ini();
            $oF_fin = $oCasaPeriodo->getF_fin();
            $f_ini = $oF_ini->getFromLocal();
            $f_fin = $oF_fin->getFromLocal();
            $sfsv = $oCasaPeriodo->getSfsv();

            //crear array para comprobación
            $cPeriodos[] = ['inicio' => $oF_ini, 'fin' => $oF_fin, 'desc' => 'periodo cdc'];

            $oTipoActividad->setSfsvId($sfsv);
            $ssfsv = $oTipoActividad->getSfsvText();

            $a_valores[$i][1] = $f_ini;
            $a_valores[$i][2] = $f_fin;
            if ($permiso == 'modificar') {
                $script = "fnjs_modificar($id_item)";
                $a_valores[$i][3] = array('script' => $script, 'valor' => $ssfsv);
            } else {
                $a_valores[$i][3] = $ssfsv;
            }
        }
        $oLista = new Lista();
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->lista();

        $oDate = new DateTimeLocal();
        $error_txt = $oDate->comprobarSolapes($cPeriodos);
        if ($error_txt) {
            echo "<span class='alert'>$error_txt</span>";
        }
        // Per afegir un periode
        if ($permiso == 'modificar') {
            $txt = "<br><input type='button' value='" . _("nuevo") . "' onclick=\"fnjs_modificar();\" >";
            echo $txt;
        }
        break;
    case "get":
        /* sirve para calendario_ubi_resumen.php
         * en el modulo economics...
         */
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        // listado de periodos por casa
        $GesCasaPeriodos = new GestorCasaPeriodo();
        $cCasaPeriodos = $GesCasaPeriodos->getCasaPeriodos(array('id_ubi' => $Qid_ubi, '_ordre' => 'f_ini'));

        $oHash = new Hash();
        $i = 0;
        $txt = '';
        foreach ($cCasaPeriodos as $oCasaPeriodo) {
            $i++;
            $id_item = $oCasaPeriodo->getId_item();
            $id_ubi = $oCasaPeriodo->getId_ubi();
            $f_ini = $oCasaPeriodo->getF_ini()->getFromLocal();
            $f_fin = $oCasaPeriodo->getF_fin()->getFromLocal();
            $sfsv = $oCasaPeriodo->getSfsv();

            if ($sfsv == 1) {
                $sel_sv = "selected";
            } else {
                $sel_sv = "";
            }
            if ($sfsv == 2) {
                $sel_sf = "selected";
            } else {
                $sel_sf = "";
            }
            if ($sfsv == 3) {
                $sel_res = "selected";
            } else {
                $sel_res = "";
            }

            $form = $i . "_form_" . $id_ubi;
            $form_id_ubi = $i . "_form_" . $id_ubi . "_id_ubi";
            $form_que = $i . "_form_" . $id_ubi . "_que";

            $a_camposHidden = array(
                'id_item' => $id_item,
            );
            $camposForm = 'f_ini!f_fin!sfsv';
            $camposNo = 'que!id_ubi';
            $oHash->setCamposNo($camposNo);
            $oHash->setCamposForm($camposForm);
            $oHash->setArraycamposHidden($a_camposHidden);

            $txt .= "<form id=\"$form\"> ";
            $txt .= $oHash->getCamposHtml();
            $txt .= "<input type=hidden id=\"$form_id_ubi\" name=\"id_ubi\" value=\"\" > ";
            $txt .= "<input type=hidden id=\"$form_que\" name=\"que\" value=\"\" > ";
            $txt .= "<input type=hidden name=id_item value=\"$id_item\" > ";
            $txt .= _("de") . "<input type=text size=12 name=f_ini value=\"$f_ini\">   " . _("hasta") . " <input type=text size=12 name=f_fin value=\"$f_fin\">";
            $txt .= _("asignado a") . " <select name=sfsv><option value=1 $sel_sv>" . _("sv") . "</option>";
            $txt .= "<option value=2 $sel_sf>" . _("sf") . "</option>";
            $txt .= "<option value=3 $sel_res>" . _("reservado") . "</option></select>";
            $txt .= "  <span class=link onclick=fnjs_grabar($id_ubi,$i,\'update\')>" . _("grabar") . "</span>";
            $txt .= "  <span class=link onclick=fnjs_grabar($id_ubi,$i,\'borrar\')>" . _("borrar") . "</span></form>";
        }
        echo "{ que: '" . $Qque . "', txt: '$txt' }";
        break;
    case "guardar":
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        $Qf_ini = (string)filter_input(INPUT_POST, 'f_ini');
        $Qf_fin = (string)filter_input(INPUT_POST, 'f_fin');
        $Qsfsv = (integer)filter_input(INPUT_POST, 'sfsv');
        if (!empty($Qid_item)) {
            $oCasaPeriodo = new CasaPeriodo($Qid_item);
            $oCasaPeriodo->DBCarregar(); //perque agafi els valor que ja té.
        } else {
            $oCasaPeriodo = new CasaPeriodo();
        }
        if (!empty($Qid_ubi)) $oCasaPeriodo->setId_ubi($Qid_ubi);
        if (!empty($Qf_ini)) $oCasaPeriodo->setF_ini($Qf_ini);
        if (!empty($Qf_fin)) $oCasaPeriodo->setF_fin($Qf_fin);
        if (!empty($Qsfsv)) $oCasaPeriodo->setSfsv($Qsfsv);
        if ($oCasaPeriodo->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oCasaPeriodo->getErrorTxt();
        }
        break;
    case "eliminar":
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        if (!empty($Qid_item)) {
            $oCasaPeriodo = new CasaPeriodo($Qid_item);
            if ($oCasaPeriodo->DBEliminar() === false) {
                echo _("hay un error, no se ha eliminado");
                echo "\n" . $oCasaPeriodo->getErrorTxt();
            }
        } else {
            $error_txt = _("no sé cuál he de borar");
            echo "{ que: '" . $Qque . "', error: '$error_txt' }";
        }
        break;
}
