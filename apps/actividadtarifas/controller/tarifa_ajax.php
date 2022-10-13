<?php

use actividadtarifas\model\entity\GestorTipoActivTarifa;
use actividadtarifas\model\entity\GestorTipoTarifa;
use actividadtarifas\model\entity\TipoTarifa;
use core\ConfigGlobal;
use ubis\model\entity\GestorTarifaUbi;
use ubis\model\entity\TarifaUbi;
use web\Desplegable;
use web\Lista;
use web\TiposActividades;
use actividadtarifas\model\entity\TipoActivTarifa;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_activ = (string)filter_input(INPUT_POST, 'id_activ');
$Qid_tarifa = (string)filter_input(INPUT_POST, 'id_tarifa');

switch ($Qque) {
    case 'form_tarifa_ubi':
        $Qid_item = (string)filter_input(INPUT_POST, 'id_item');
        $Qid_ubi = (string)filter_input(INPUT_POST, 'id_ubi');
        $Qyear = (string)filter_input(INPUT_POST, 'year');
        $Qletra = (string)filter_input(INPUT_POST, 'letra');
        $letra = empty($Qletra) ? _("nueva") : $Qletra;

        $oTipoActivTarifa = new TipoActivTarifa();
        $aTipoSerie = $oTipoActivTarifa->getArraySerie();

        $oDesplPosiblesSeries = new Desplegable();
        $oDesplPosiblesSeries->setNombre('id_serie');
        $oDesplPosiblesSeries->setOpciones($aTipoSerie);
        $oDesplPosiblesSeries->setOpcion_sel(1);

        $oHash = new web\Hash();
        $a_camposHidden = array(
            'que' => 'update',
            'id_ubi' => $Qid_ubi,
            'year' => $Qyear,
        );
        if (!empty($Qid_item)) {
            $a_camposHidden['id_item'] = $Qid_item;
            $camposForm = 'cantidad';
            $oTarifaUbi = new TarifaUbi();
            $oTarifaUbi->setId_item($Qid_item);
            $cantidad = $oTarifaUbi->getCantidad();
        } else {
            $camposForm = 'cantidad!id_tarifa!id_serie';
            $cantidad = '';
        }
        $oHash->setCamposNo('que');
        $oHash->setCamposForm($camposForm);
        $oHash->setArraycamposHidden($a_camposHidden);


        $oGesTarifa = new GestorTarifaUbi();
        $txt = "<form id='frm_tarifa_ubi'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>' . sprintf(_("tarifa %s"), $letra) . '</h3>';
        if (empty($Qid_item)) {
            $miSfsv = ConfigGlobal::mi_sfsv();
            $oGesTipoTarifa = new GestorTipoTarifa();
            $oTipoTarifas = $oGesTipoTarifa->getListaTipoTarifas($miSfsv);
            $oTipoTarifas->setNombre('id_tarifa');
            $txt .= _("tarifa").": ";
            $txt .= $oTipoTarifas->desplegable();
            $txt .= '<br><br>';
            $txt .= _("serie").": ";
            $txt .= $oDesplPosiblesSeries->desplegable();
            $txt .= '<br><br>';
        }
        $txt .= _("nuevo importe") . ": <input type=text size=6 id='cantidad' name='cantidad' value=\"$cantidad\" onblur=\"fnjs_comprobar_dinero('#cantidad');\"> " . _("€");
        $txt .= "<br><br>";
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_tarifa_ubi','update');\" >";
        $txt .= "<input type='button' value='" . _("eliminar") . "' onclick=\"fnjs_guardar('#frm_tarifa_ubi','tar_ubi_eliminar');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick='fnjs_cerrar();' >";
        $txt .= "</form> ";
        echo $txt;
        break;
    case "get":
        $Qid_ubi = (string)filter_input(INPUT_POST, 'id_ubi');
        $Qyear = (string)filter_input(INPUT_POST, 'year');

        $miSfsv = ConfigGlobal::mi_sfsv();
        $a_seccion = array(1 => _("sv"), 2 => _("sf"));
        // listado de tarifas por casa y año
        if (!empty($Qid_ubi) && !empty($Qyear)) {
            $oGesTarifa = new GestorTarifaUbi();
            $cTarifas = $oGesTarifa->getTarifas(array('id_ubi' => $Qid_ubi, 'year' => $Qyear, '_ordre' => 'year,id_tarifa'));
        } else {
            $cTarifas = [];
        }

        $a_cabeceras = [];
        $a_cabeceras[] = _("sección");
        $a_cabeceras[] = _("id_tarifa");
        $a_cabeceras[] = _("se aplica a");
        $a_cabeceras[] = _("minimo");
        $a_cabeceras[] = _("precio");
        $a_cabeceras[] = _("método");

        $i = 0;
        $txt = '';
        $error_txt = '';
        $a_valores = array();
        foreach ($cTarifas as $oTarifaUbi) {
            $i++;
            $id_item = $oTarifaUbi->getId_item();
            $id_tarifa = $oTarifaUbi->getId_tarifa();
            $cantidad = $oTarifaUbi->getCantidad();

            $cantidad = "$cantidad " . _("€");

            $oGesTipoActivTarifas = new GestorTipoActivTarifa();
            $cTipoActivTarifas = $oGesTipoActivTarifas->getTipoActivTarifas(array('id_tarifa' => $id_tarifa));
            $txt = '';
            $t = 0;
            foreach ($cTipoActivTarifas as $oTipoActivTarifa) {
                $t++;
                $id_tipo_activ = $oTipoActivTarifa->getId_tipo_activ();
                $id_serie = $oTipoActivTarifa->getId_serie();
                $oTipoActividad = new TiposActividades($id_tipo_activ);
                if ($t > 1) {
                    $txt .= ', ';
                }
                $txt .= $oTipoActividad->getNomGral();
                if ($id_serie !== TipoActivTarifa::S_GENERAL) {
                    $aTipoSerie = $oTipoActivTarifa->getArraySerie();
                    $txt .= " (" . $aTipoSerie[$id_serie] . ")";
                }
            }

            $oTipoTarifa = new TipoTarifa($id_tarifa);
            $seccion = $oTipoTarifa->getSfsv();
            $letra = $oTipoTarifa->getLetra();
            $script = "fnjs_modificar($id_item,'$letra')";

            $a_valores[$i][1] = $a_seccion[$seccion];
            // permiso
            if ($miSfsv == $seccion && $_SESSION['oPerm']->have_perm_oficina('adl')) {
                $a_valores[$i][2] = array('script' => $script, 'valor' => $letra);
            } else {
                $a_valores[$i][2] = $letra;
            }
            $a_valores[$i][3] = $txt;
            $a_valores[$i][4] = '0';
            $a_valores[$i][5] = array('clase' => 'derecha', 'valor' => $cantidad);
            $a_valores[$i][6] = $oTipoTarifa->getModoTxt();
        }
        if (!empty($a_valores)) {
            // Obtain a list of columns
            $secc = [];
            $letr = [];
            foreach ($a_valores as $key => $row) {
                $secc[$key] = $row[1];
                if (is_array($row[2])) {
                    $letra = $row[2]['valor'];
                } else {
                    $letra = $row[2];
                }
                $letr[$key] = $letra;
            }

            // Sort the data with volume descending, edition ascending
            // Add $data as the last parameter, to sort by the common key
            array_multisort($secc, SORT_DESC, $letr, SORT_ASC, SORT_STRING, $a_valores);
        }
        $oLista = new Lista();
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->lista();
        // sólo pueden añadir: adl, pr i actividades
        if (($_SESSION['oPerm']->have_perm_oficina('adl')) || ($_SESSION['oPerm']->have_perm_oficina('pr')) || ($_SESSION['oPerm']->have_perm_oficina('calendario'))) {
            echo '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("añadir tarifa") . '</span>';
        }
        break;
    case "update":
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        $Qyear = (integer)filter_input(INPUT_POST, 'year');
        $Qid_serie = (integer)filter_input(INPUT_POST, 'id_serie');
        $Qcantidad = (string)filter_input(INPUT_POST, 'cantidad');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');

        if (!empty($Qid_item)) {
            $oTarifaUbi = new TarifaUbi();
            $oTarifaUbi->setId_item($Qid_item);
            $oTarifaUbi->DBCarregar(); // para que coja los valores que ya tiene
        } else {
            $oTarifaUbi = new TarifaUbi();
        }
        if (!empty($Qid_ubi)) $oTarifaUbi->setId_ubi($Qid_ubi);
        if (!empty($Qyear)) $oTarifaUbi->setYear($Qyear);
        if (!empty($Qid_tarifa)) $oTarifaUbi->setId_tarifa($Qid_tarifa);
        if (!empty($Qid_serie)) $oTarifaUbi->setId_serie($Qid_serie);
        if (!empty($Qcantidad)) $oTarifaUbi->setCantidad($Qcantidad);
        if (!empty($Qobserv)) $oTarifaUbi->setObserv($Qobserv);
        if ($oTarifaUbi->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oTarifaUbi->getErrorTxt();
        }
        break;
    case "borrar":
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        if (!empty($Qid_item)) {
            $oTarifaUbi = new TarifaUbi();
            $oTarifaUbi->setId_item($Qid_item);
            if ($oTarifaUbi->DBEliminar() === false) {
                echo _("hay un error, no se ha eliminado");
                echo "\n" . $oTarifaUbi->getErrorTxt();
            }
        } else {
            $Qque = (string)filter_input(INPUT_POST, 'que');
            $error_txt = _("no sé cuál he de borar");
            echo "{ que: '" . $Qque . "', error: '$error_txt' }";
        }
        break;
    case "update_inc":
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        $Qyear = (integer)filter_input(INPUT_POST, 'year');
        foreach ($_POST['inc_cantidad'] as $key => $cantidad) {
            $tarifa = strtok($key, '#');
            $id_item = (integer)strtok('#');
            $cantidad = round($cantidad);
            if (empty($id_item) && empty($cantidad)) continue; // no hay ni habia nada.
            $oTarifaUbi = new TarifaUbi(array('id_tarifa' => $id_tarifa, 'id_ubi' => $Qid_ubi, 'year' => $Qyear));
            $oTarifaUbi->DBCarregar();
            if (isset($cantidad)) $oTarifaUbi->setCantidad($cantidad);
            if ($oTarifaUbi->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oTarifaUbi->getErrorTxt();
            }
        }
        break;
    case "tarifas":
        //$oMiUsuario = new Usuario(\core\ConfigGlobal::mi_id_usuario());
        $a_seccion = array(1 => _("sv"), 2 => _("sf"));
        $a_opciones = array(0 => _("por dia"), 1 => _("total"));
        $oGesTipoTarifa = new GestorTipoTarifa();
        $oTipoTarifas = $oGesTipoTarifa->getTipoTarifas(array('_ordre' => 'sfsv,letra'));
        $t = 0;
        $txt = '';
        $error_txt = '';
        $a_cabeceras[] = _("id_tarifa");
        $a_cabeceras[] = _("sección");
        $a_cabeceras[] = _("letra");
        $a_cabeceras[] = _("modo");
        $a_cabeceras[] = _("observ");
        foreach ($oTipoTarifas as $oTipoTarifa) {
            $t++;
            $id_tarifa = $oTipoTarifa->getId_tarifa();
            $modo = $oTipoTarifa->getModo();
            $letra = $oTipoTarifa->getLetra();
            $sfsv = $oTipoTarifa->getSfsv();
            $observ = $oTipoTarifa->getObserv();

            if ($modo == 1) {
                $modo_1 = "selected";
                $modo_0 = "";
            } else {
                $modo_0 = "selected";
                $modo_1 = "";
            }

            $a_valores[$t][1] = $id_tarifa;
            $a_valores[$t][2] = $a_seccion[$sfsv];
            $a_valores[$t][3] = $letra;
            $a_valores[$t][4] = $a_opciones[$modo];
            $a_valores[$t][5] = $observ;
            // permiso
            if (ConfigGlobal::mi_sfsv() == $sfsv && $_SESSION['oPerm']->have_perm_oficina('adl')) {
                $script = "fnjs_modificar($id_tarifa)";
                $a_valores[$t][6] = array('script' => $script, 'valor' => _("modificar"));
            }
        }
        $oLista = new Lista();
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->lista();
        // sólo pueden añadir: adl, pr i actividades
        if (($_SESSION['oPerm']->have_perm_oficina('adl')) || ($_SESSION['oPerm']->have_perm_oficina('pr')) || ($_SESSION['oPerm']->have_perm_oficina('calendario'))) {
            echo '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("nueva tarifa") . '</span>';
        }
        break;
    case 'tar_form':
        if ($Qid_tarifa === 'nuevo') {
            $letra = '';
            $modo = 0;
            $observ = '';
        } else {
            $oTipoTarifa = new TipoTarifa($Qid_tarifa);
            $oTipoTarifa->DBCarregar();
            $letra = $oTipoTarifa->getLetra();
            $modo = $oTipoTarifa->getModo();
            $observ = $oTipoTarifa->getObserv();
        }
        $a_opciones = array(0 => _("por dia"), 1 => _("total"));
        $oDespl = new Desplegable('modo', $a_opciones, $modo, 0);

        $oHash = new web\Hash();
        $camposForm = 'letra!modo!observ';
        $oHash->setCamposNo('que');
        $a_camposHidden = array(
            'que' => 'tar_update',
            'id_tarifa' => $Qid_tarifa,
        );
        $oHash->setCamposForm($camposForm);
        $oHash->setArraycamposHidden($a_camposHidden);

        $txt = "<form id='frm_tarifa'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>' . _("tarifa") . '</h3>';
        $txt .= _("letra") . " <input type=text size=3 name=letra value=\"$letra\">";
        $txt .= '&nbsp;&nbsp;';
        $txt .= _("modo") . $oDespl->desplegable();
        $txt .= '<br><br>';
        $txt .= _("observaciones") . " <input type=text size=25 name=observ value=\"$observ\">";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_tarifa','tar_update');\" >";
        $txt .= "<input type='button' value='" . _("eliminar") . "' onclick=\"fnjs_guardar('#frm_tarifa','tar_eliminar');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";
        echo $txt;
        break;
    case "tar_update":
        $Qletra = (string)filter_input(INPUT_POST, 'letra');
        $Qmodo = (string)filter_input(INPUT_POST, 'modo');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');
        if ($Qid_tarifa === 'nuevo') {
            $oTipoTarifa = new TipoTarifa();
            // miro si soy sf/sv.
            $oTipoTarifa->setSfsv(ConfigGlobal::mi_sfsv());
        } else {
            $oTipoTarifa = new TipoTarifa($Qid_tarifa);
            $oTipoTarifa->DBCarregar();
        }
        if (isset($Qletra)) $oTipoTarifa->setLetra($Qletra);
        if (isset($Qmodo)) $oTipoTarifa->setModo($Qmodo);
        if (isset($Qobserv)) $oTipoTarifa->setObserv($Qobserv);
        if ($oTipoTarifa->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oTipoTarifa->getErrorTxt();
        }
        break;
    case "tar_eliminar":
        $oTipoTarifa = new TipoTarifa($_POST['id_tarifa']);
        $oTipoTarifa->DBCarregar();
        if ($oTipoTarifa->DBEliminar() === false) {
            echo _("hay un error, no se ha borrado");
        }
        break;
    case "tar_ubi_eliminar":
        $Qid_item = (string)filter_input(INPUT_POST, 'id_item');
        $oTarifaUbi = new TarifaUbi($Qid_item);
        $oTarifaUbi->DBCarregar();
        if ($oTarifaUbi->DBEliminar() === false) {
            echo _("hay un error, no se ha borrado");
        }
        break;
}