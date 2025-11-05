<?php

use web\Hash;
use web\Lista;
use ubis\model\CuadrosLabor;
use ubis\model\entity\CentroDl;
use ubis\model\entity\GestorCentroDl;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

// Esta misma página:
$url_ajax = 'apps/ubis/controller/centros_ajax.php';

switch ($Qque) {
    case 'nuevo':
        $txt = "<form id='frm_periodo'>";
        $txt .= '<h3>' . _("Periodo") . '</h3>';
        $txt .= "<input type=hidden name=que value=\"update\" > ";
        $txt .= "<input type=hidden name=id_ubi value=\"$Qid_ubi\" > ";
        $txt .= _("de") . "<input type=text size=12 name=f_ini value=\"\">   " . _("hasta") . " <input type=text size=12 name=f_fin value=\"\">";
        $txt .= _("asignado a") . " <select name=sfsv_num><option value=1 >" . _("sv") . "</option>";
        $txt .= "<option value=2 >" . _("sf") . "</option>";
        $txt .= "<option value=3 >" . _("reservado") . "</option></select>";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_periodo');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";
        echo $txt;
        break;
    case 'form_labor':
        $oPermActiv = new CuadrosLabor;
        $oCentro = new CentroDl($Qid_ubi);
        $nombre_ubi = $oCentro->getNombre_ubi();
        $tipo_ctr = $oCentro->getTipo_ctr();
        $tipo_labor = $oCentro->getTipo_labor();

        $oHash = new Hash();
        $oHash->setUrl($url_ajax);
        $aCamposHidden = [
            'que' => 'update',
            'labor' => 'si', // para saber que si el array tipo_labor está en blanco hay que borrar.
            'id_ubi' => $Qid_ubi,
        ];
        $oHash->setArrayCamposHidden($aCamposHidden);
        $oHash->setCamposForm('tipo_ctr!tipo_labor');


        $txt = "<form id='frm_labor' action='$url_ajax'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>' . _("centro") . '  ' . $nombre_ubi . '</h3>';
        $txt .= _("tipo de centro") . "   <input type=text size=12 name=tipo_ctr value=\"$tipo_ctr\">";
        $txt .= '<br>';
        $txt .= _("tipo de labor");
        $txt .= '   ';
        $txt .= $oPermActiv->cuadros_check('tipo_labor', $tipo_labor);
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_labor');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";
        echo $txt;
        break;
    case 'form_num':
        $oCentro = new CentroDl($Qid_ubi);
        $nombre_ubi = $oCentro->getNombre_ubi();
        $n_buzon = $oCentro->getN_buzon();
        $num_pi = $oCentro->getNum_pi();
        $num_cartas = $oCentro->getNum_cartas();

        $oHash = new Hash();
        $oHash->setUrl($url_ajax);
        $aCamposHidden = [
            'que' => 'update',
            'id_ubi' => $Qid_ubi,
        ];
        $oHash->setArrayCamposHidden($aCamposHidden);
        $oHash->setCamposForm('n_buzon!num_pi!num_cartas');

        $txt = "<form id='frm_num'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>' . _("centro") . '  ' . $nombre_ubi . '</h3>';
        $txt .= _("número de buzón") . "   <input type=text size=12 name=n_buzon value=\"$n_buzon\">";
        $txt .= '<br>';
        $txt .= _("número de pi") . "   <input type=text size=12 name=num_pi value=\"$num_pi\">";
        $txt .= '<br>';
        $txt .= _("número de cartas") . "   <input type=text size=12 name=num_cartas value=\"$num_cartas\">";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_num');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";
        echo $txt;
        break;
    case 'form_plazas':
        $oCentro = new CentroDl($Qid_ubi);
        $nombre_ubi = $oCentro->getNombre_ubi();
        $num_habit_indiv = $oCentro->getNum_habit_indiv();
        $plazas = $oCentro->getPlazas();
        $sede = $oCentro->getSede();

        $chk_sede = is_true($sede) ? 'checked' : '';

        $oHash = new Hash();
        $oHash->setUrl($url_ajax);
        $aCamposHidden = [
            'que' => 'update',
            'id_ubi' => $Qid_ubi,
        ];
        $oHash->setArrayCamposHidden($aCamposHidden);
        $oHash->setCamposForm('num_habit_indiv!plazas');
        $oHash->setCamposChk('sede');

        $txt = "<form id='frm_plazas'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>' . _("centro") . '  ' . $nombre_ubi . '</h3>';
        $txt .= _("número de habitaciones individuales") . "   <input type=text size=12 name=num_habit_indiv value=\"$num_habit_indiv\">";
        $txt .= '<br>';
        $txt .= _("plazas") . "   <input type=text size=12 name=plazas value=\"$plazas\">";
        $txt .= '<br>';
        $txt .= "<input type=hidden name=sede value=\"false\">"; // para evitar valor null.
        $txt .= _("sede") . "   <input type=checkbox size=12 name=sede $chk_sede value=\"true\">";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_plazas','guardar');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";
        echo $txt;
        break;
    case "update":
        // también los datos en la actividad.
        if (!empty($Qid_ubi)) {
            $Qtipo_ctr = (string)filter_input(INPUT_POST, 'tipo_ctr');

            $oCentro = new CentroDl($Qid_ubi);
            $oCentro->DBCarregar();
            $oCentro->setTipo_ctr($Qtipo_ctr);
            //cuando el campo es tipo_labor, se pasa un array que hay que convertirlo en número.
            if (isset($_POST['tipo_labor'])) {
                $byte = 0;
                foreach ($_POST['tipo_labor'] as $bit) {
                    $byte = $byte + $bit;
                }
                $oCentro->setTipo_labor($byte);
            } else {
                if (!empty($_POST['labor']) && $_POST['labor'] == 'si') {
                    $oCentro->setTipo_labor(0);
                }
            }

            isset($_POST['n_buzon']) ? $oCentro->setN_buzon($_POST['n_buzon']) : '';
            isset($_POST['num_pi']) ? $oCentro->setNum_pi($_POST['num_pi']) : '';
            isset($_POST['num_cartas']) ? $oCentro->setNum_cartas($_POST['num_cartas']) : '';
            isset($_POST['num_habit_indiv']) ? $oCentro->setNum_habit_indiv($_POST['num_habit_indiv']) : '';
            isset($_POST['plazas']) ? $oCentro->setPlazas($_POST['plazas']) : '';
            if (isset($_POST['sede'])) {
                is_true($_POST['sede'])? $oCentro->setSede('t') : $oCentro->setSede('f');
            }

            if ($oCentro->DBGuardar() === false) {
                echo _("Hay un error, no se ha guardado.");
            }
        }
        break;
    case "get_labor":
        // listado de tipo centro y tipo labor.
        $permiso = 'modificar';
        $oPermActiv = new CuadrosLabor;
        $oGesCentrosDl = new GestorCentroDl();
        $aWhere = array('status' => 't', '_ordre' => 'nombre_ubi');
        $cCentrosDl = $oGesCentrosDl->getCentros($aWhere);
        $c = 0;
        $a_valores = [];
        foreach ($cCentrosDl as $oCentro) {
            $c++;
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $tipo_ctr = $oCentro->getTipo_ctr();
            $tipo_labor = $oCentro->getTipo_labor();

            if ($permiso == 'modificar') {
                $script = "fnjs_modificar($id_ubi,\"labor\")";
                $a_valores[$c][1] = array('script' => $script, 'valor' => $nombre_ubi);
            } else {
                $a_valores[$c][1] = $nombre_ubi;
            }
            $a_valores[$c][2] = $tipo_ctr;
            $a_valores[$c][3] = $oPermActiv->cuadros_check_read($tipo_labor);
        }
        $a_cabeceras = [
            ['name' => ucfirst(_("centro")), 'width' => 100, 'formatter' => 'clickFormatter'],
            ucfirst(_("tipo de centro")),
            ['name' => ucfirst(_("tipo de labor")), 'width' => 200, 'formatter' => 'clickFormatter2'],
        ];

        $oLista = new Lista();
        $oLista->setId_tabla('centros_ajax_labor');
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->mostrar_tabla();
        break;
    case "get_num":
        // listado de numeros de buzón, cartas i pi
        $permiso = 'modificar';
        $oPermActiv = new CuadrosLabor;
        $oGesCentrosDl = new GestorCentroDl();
        $aWhere = array('status' => 't', '_ordre' => 'nombre_ubi');
        $cCentrosDl = $oGesCentrosDl->getCentros($aWhere);
        $c = 0;
        foreach ($cCentrosDl as $oCentro) {
            $c++;
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $n_buzon = $oCentro->getN_buzon();
            $num_pi = $oCentro->getNum_pi();
            $num_cartas = $oCentro->getNum_cartas();

            $num_pi = empty($num_pi) ? '0' : $num_pi;
            $num_cartas = empty($num_cartas) ? '0' : $num_cartas;

            if ($permiso == 'modificar') {
                $script = "fnjs_modificar($id_ubi,\"num\")";
                $a_valores[$c][1] = array('script' => $script, 'valor' => $nombre_ubi);
            } else {
                $a_valores[$c][1] = $nombre_ubi;
            }
            $a_valores[$c][2] = $n_buzon;
            $a_valores[$c][3] = $num_pi;
            $a_valores[$c][4] = $num_cartas;
        }
        $a_cabeceras[] = array('name' => ucfirst(_("centro")), 'width' => 100, 'formatter' => 'clickFormatter');

        $a_cabeceras[] = ucfirst(_("número de buzón"));
        $a_cabeceras[] = ucfirst(_("número de pi"));
        $a_cabeceras[] = ucfirst(_("número de cartas"));

        $oLista = new Lista();
        $oLista->setId_tabla('centros_ajax_num');
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->mostrar_tabla();
        break;
    case "get_plazas":
        // listado de numeros de buzón, cartas i pi
        $permiso = 'modificar';
        $oPermActiv = new CuadrosLabor;
        $oGesCentrosDl = new GestorCentroDl();
        $aWhere = array('status' => 't', '_ordre' => 'nombre_ubi');
        $cCentrosDl = $oGesCentrosDl->getCentros($aWhere);
        $c = 0;
        foreach ($cCentrosDl as $oCentro) {
            $c++;
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $num_habit_indiv = $oCentro->getNum_habit_indiv();
            $plazas = $oCentro->getPlazas();
            $sede = ($oCentro->getSede()) ? _("si") : _("no");

            if ($permiso == 'modificar') {
                $script = "fnjs_modificar($id_ubi,\"plazas\")";
                $a_valores[$c][1] = array('script' => $script, 'valor' => $nombre_ubi);
            } else {
                $a_valores[$c][1] = $nombre_ubi;
            }
            $a_valores[$c][2] = $num_habit_indiv;
            $a_valores[$c][3] = $plazas;
            $a_valores[$c][4] = $sede;
        }
        $a_cabeceras[] = array('name' => ucfirst(_("centro")), 'width' => 100, 'formatter' => 'clickFormatter');

        $a_cabeceras[] = ucfirst(_("número de habitaciones individuales"));
        $a_cabeceras[] = ucfirst(_("plazas"));
        $a_cabeceras[] = ucfirst(_("sede"));


        $oLista = new Lista();
        $oLista->setId_tabla('centros_ajax_plazas');
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->mostrar_tabla();
        break;
}
