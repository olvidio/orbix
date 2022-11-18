<?php

use core\ConfigGlobal;
use encargossacd\model\EncargoFunciones;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoSacd;
use personas\model\entity\PersonaDl;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use web\DateTimeLocal;

/* Listado de ateción sacd. según cr 9/05, Anexo2,9.4 b) 
*
*@package	delegacion
*@subpackage	des
*@author	Dani Serrabou
*@since		11/12/06.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oEncargoFunciones = new EncargoFunciones();

$Qsf = (integer)filter_input(INPUT_POST, 'sf');

$any = $_SESSION['oConfig']->any_final_curs('crt');
$inicurs = core\curso_est("inicio", $any, "crt")->getFromLocal();
$fincurs = core\curso_est("fin", $any, "crt")->getFromLocal();

$cabecera_left = sprintf(_("Curso:  %s - %s"), $inicurs, $fincurs);
$cabecera_right = ConfigGlobal::mi_delef();
$cabecera_right_2 = _("ref. cr 1/14, 10, b)");

// ciudad de la dl
$oEncargoFunciones = new EncargoFunciones();
$poblacion = $oEncargoFunciones->getLugar_dl();
$oDateLocal = new DateTimeLocal();
$hoy_local = $oDateLocal->getFromLocal('.');
$lugar_fecha = "$poblacion, $hoy_local";

$tipos_de_ctr = array('igl', 'cgioc', '^cgi$');

$Html = '';
$txt_tipo_ctr = "";
foreach ($tipos_de_ctr as $tipo_ctr_que) {
    switch ($tipo_ctr_que) {
        case 'igl':
            if (empty($Qsf)) {
                $txt_tipo_ctr = "1. " . _("Iglesias");
            }
            $Html .= "<div><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            break;
        case 'cgioc':
            if (!empty($Qsf)) {
                $txt_tipo_ctr = "1. " . _("oc");
                $Html .= "<div><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            } else {
                $txt_tipo_ctr = "2. " . _("oc");
                $Html .= "<div class=salta_pag ><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            }
            break;
        case '^cgi$':
            if (!empty($Qsf)) {
                $txt_tipo_ctr = "2. " . _("lp");
            } else {
                $txt_tipo_ctr = "3. " . _("lp");
            }
            $Html .= "<div><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            break;
    }
    if (!empty($txt_tipo_ctr))

        $aWhere = array();
    $aOperador = array();
    $aWhere['status'] = 't';
    $aWhere['tipo_ctr'] = "^$tipo_ctr_que";
    $aOperador['tipo_ctr'] = '~';
    $aWhere['_ordre'] = 'nombre_ubi';
    if ($Qsf == 1) {
        $GesCentros = new GestorCentroEllas();
        $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
    } else {
        $GesCentros = new GestorCentroDl();
        $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
    }
    // Bucle por cada centro
    $contador_ctr = 0;
    $actual_orden = '';
    //print_r($cCentros);
    foreach ($cCentros as $oCentro) {
        $contador_ctr++;
        $id_ubi = $oCentro->getId_ubi();
        $nombre_ubi = $oCentro->getNombre_ubi();
        $tipo_ctr = $oCentro->getTipo_ctr();

        $cargos = [];
        /* DESACTIVADO CARGOS
        // busco los cargos del cl.
        $GesCargosCl = new GestorCargoCl();
        $aWhereC = array();
        $aOperadorC = array();
        $aWhereC['id_ubi'] = $id_ubi;
        $aWhereC['f_cese'] = 'null';
        $aOperadorC['f_cese'] = 'IS NULL';
        $aWhereC['_ordre'] = 'orden_cargo';
        $cCargosCl = $GesCargosCl->getCargosCl($aWhereC,$aOperadorC);
        foreach ($cCargosCl as $oCargoCl) {
            $id_nom =  $oCargoCl->getId_nom();
            $orden_cargo =  $oCargoCl->getOrden_cargo();
            $cargo =  $oCargoCl->getCargo();
            $cargos[$id_nom]=$orden_cargo."#".$cargo;
        }
        */
        $GesEncargo = new GestorEncargo();
        $aWhere = array();
        $aOperador = array();
        $aWhere['id_ubi'] = $id_ubi;
        $cEncargos = $GesEncargo->getEncargos($aWhere, $aOperador);
        $sacds = array();
        foreach ($cEncargos as $oEncargo) {
            $id_enc = $oEncargo->getId_enc();
            $sacd_titular = "";
            $sacd_suplente = "";
            $sacds_colaboradores = array();
            $aWhereT = [];
            $aOperadorT = [];
            $aWhereT['id_enc'] = $id_enc;
            $aWhereT['f_fin'] = 'null';
            $aOperadorT['f_fin'] = 'IS NULL';
            $aWhereT['_ordre'] = 'modo';
            $GesEncargoSacd = new GestorEncargoSacd();
            $cEncargosSacd = $GesEncargoSacd->getEncargoSacd($aWhereT, $aOperadorT);
            $s = 0;
            $dedic_horas_ctr = 0;
            foreach ($cEncargosSacd as $oEncargoSacd) {
                $s++;
                $modo = $oEncargoSacd->getModo();
                $id_nom = $oEncargoSacd->getId_nom();
                $oPersona = new PersonaDl($id_nom);
                $nom_ap = $oPersona->getNombreApellidosCrSin();
                // para saber la dedicación
                $dedicacion_txt = $oEncargoFunciones->dedicacion($id_nom, $id_enc);
                $dedic_horas_ctr += $oEncargoFunciones->dedicacion_horas($id_nom, $id_enc);
                // mirara si tiene cargo cl.
                if (!empty($cargos[$id_nom])) {
                    $orden_cargo = strtok($cargos[$id_nom], "#");
                    $cargo = strtok("#");
                    if ($cargo == "sacd") $cargo .= " cl";
                    if (!empty($dedicacion_txt)) {
                        $dedicacion_txt = $cargo . " " . $dedicacion_txt;
                    } else {
                        $dedicacion_txt = $cargo;
                    }
                    $orden_2 = $orden_cargo;
                } else {
                    $orden_2 = 1000 + $s;
                }

                switch ($modo) {
                    case 2:
                    case 3:
                        switch ($tipo_ctr) {
                            case 'igloc':
                                $parentesis = ucfirst(_("rector"));
                                break;
                            case 'igl':
                                $parentesis = ucfirst(_("encargado"));
                                break;
                            /* Ara no (10.11.15)
                        case 'cgioc':
                            $parentesis=ucfirst(_("confesor"));
                            break;
                            */
                            default:
                                $parentesis = _("capellán");
                        }
                        if ($Qsf == 1) {
                            $sacd_titular = $nom_ap;
                        } else {
                            $sacd_titular = sprintf("%s (%s)", $nom_ap, $parentesis);
                        }
                        $dedicacion_titular = $dedicacion_txt;
                        $sacds[$orden_2] = $sacd_titular . "#" . $dedicacion_txt;
                        break;
                    case 4:
                        //$sacd_suplente=$nom_ap;
                        $sacds[$orden_2] = $nom_ap;
                        break;
                    case 5:
                        $sacds[$orden_2] = $nom_ap . "#" . $dedicacion_txt;
                        break;
                }
            }
        }
        $ratio_txt = "";
        /* Desactivado por des 14/10/2008
        // para poner la ratio de alumos por sacd:
        if ($orden==2 || $orden==3) {
            $sql_alum="SELECT num_alum FROM d_cgi_datos WHERE id_ubi=$id_ubi AND curso_ini_any=$any1 ";
            $oDBSt_q_alum=$oDB->query($sql_alum);
            if ($oDBSt_q_alum->rowCount()) {
                $num_alum=$oDBSt_q_alum->fetchColumn();
                $sacd_jor=$dedic_horas_ctr/35; //nº de sacd a jornada completa (33 h).
                $ratio=round($num_alum/$sacd_jor);
                if (strstr($nombre_ubi,"EFA")) {
                    $ratio_txt="  ("._("alumnos").": $num_alum, "._("horas sacd").": $dedic_horas_ctr)";
                } else {
                    $ratio_txt="  ($ratio)";
                }
            } else {
                $ratio_txt="";
            }
        }
        */

        $Html .= "<tr><td class=centro>$nombre_ubi $ratio_txt</td></tr>";
        ksort($sacds);
        foreach ($sacds as $orden_2 => $txt) {
            $sacd = strtok($txt, "#");
            $dedicacion = strtok("#");
            $Html .= "<tr><td>$sacd</td><td>$dedicacion</td></tr>";
        }
    }
    $Html .= "</table></div>";
}


$Html .= "<table><col width=50%>";
$Html .= "<tr><td class=izquierda></td><td class=derecha>$lugar_fecha</td></tr>";
$Html .= "</table>";

$a_campos = ['oPosicion' => $oPosicion,
    'cabecera_left' => $cabecera_left,
    'cabecera_right' => $cabecera_right,
    'cabecera_right_2' => $cabecera_right_2,
    'Html' => $Html,
];

$oView = new core\ViewTwig('encargossacd/controller');
$oView->renderizar('listas.html.twig', $a_campos);
