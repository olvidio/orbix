<?php

use core\ConfigGlobal;
use core\ViewTwig;
use src\encargossacd\application\traits\EncargoFunciones;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

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
$Qctr_igl = (string)filter_input(INPUT_POST, 'ctr_igl');

$any = $_SESSION['oConfig']->any_final_curs('crt');
$inicurs = core\curso_est("inicio", $any, "crt")->getFromLocal();
$fincurs = core\curso_est("fin", $any, "crt")->getFromLocal();

$cabecera_left = sprintf(_("Curso:  %s - %s"), $inicurs, $fincurs);
$cabecera_right = ConfigGlobal::mi_delef();
$cabecera_right_2 = _("ref. cr 1/14, 10, d)");

// ciudad de la dl
$oEncargoFunciones = new EncargoFunciones();
$poblacion = $oEncargoFunciones->getLugar_dl();
$oDateLocal = new DateTimeLocal();
$hoy_local = $oDateLocal->getFromLocal('.');
$lugar_fecha = "$poblacion, $hoy_local";

// primero selecciono los centros por tipos de ctr
if ($Qctr_igl === 'ctr') {
    if ($Qsf === 1) {
        $tipos_de_ctr = array('n', 'a[jm$]', 's[jm]');
    } else {
        $tipos_de_ctr = array('n', 'a[jm$]', 's[jm]', 'ss');
    }
}

if ($Qctr_igl === 'igl') {
    $tipos_de_ctr = array('ctr', 'igl', 'cgioc', '^cgi$');
}

$Html = '';
$txt_tipo_ctr = "";
foreach ($tipos_de_ctr as $tipo_ctr_que) {
    switch ($tipo_ctr_que) {
        case 'n':
            $txt_tipo_ctr = _("1. ctr de n");
            $Html .= "<div class=salta_pag><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            break;
        case 'a[jm$]':
            $txt_tipo_ctr = _("2. ctr de agd");
            $Html .= "<div class=salta_pag><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            break;
        case 's[jm]':
            $txt_tipo_ctr = _("3. ctr de sg");
            $Html .= "<div class=salta_pag><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            break;
        case 'ss':
            $txt_tipo_ctr = _("4. ctr de sss+");
            $Html .= "<div class=salta_pag><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            break;
        case 'igl':
            $txt_tipo_ctr = "1. " . _("Iglesias");
            $Html .= "<div><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            break;
        case 'cgioc':
            $txt_tipo_ctr = "2. " . _("oc");
            $Html .= "<div class=salta_pag ><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            break;
        case '^cgi$':
            $txt_tipo_ctr = "3. " . _("lp");
            $Html .= "<div><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            break;
    }
    if (!empty($txt_tipo_ctr))

        $aWhere = [];
    $aOperador = [];
    $aWhere['active'] = 't';
    $aWhere['tipo_ctr'] = "^$tipo_ctr_que";
    $aOperador['tipo_ctr'] = '~';
    $aWhere['_ordre'] = 'nombre_ubi';
    if ($Qctr_igl === 'ctr') {
        if ($Qsf == 1) {
            $GesCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
            $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
        } else {
            $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
        }
    }
    if ($Qctr_igl === 'igl') {
        $GesCentrosF = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $cCentrosF = $GesCentrosF->getCentros($aWhere, $aOperador);
        $GesCentrosV = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentrosV = $GesCentrosV->getCentros($aWhere, $aOperador);
        $cCentros = array_merge($cCentrosV, $cCentrosF);
    }
    // Bucle por cada centro
    $contador_ctr = 0;
    $actual_orden = '';
    //print_r($cCentros);
    $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
    $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
    $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
    foreach ($cCentros as $oCentro) {
        $contador_ctr++;
        $id_ubi = $oCentro->getId_ubi();
        $nombre_ubi = $oCentro->getNombre_ubi();
        $tipo_ctr = $oCentro->getTipo_ctr();
        $cargos = [];
        /* DESACTIVADO CARGOS
        // busco los cargos del cl.
        $GesCargosCl = new GestorCargoCl();
        $aWhereC = [];
        $aOperadorC = [];
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
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_ubi'] = $id_ubi;
        $cEncargos = $EncargoRepository->getEncargos($aWhere, $aOperador);
        $sacds = [];
        $dedicacion_ctr = '';
        foreach ($cEncargos as $oEncargo) {
            $id_enc = $oEncargo->getId_enc();
            //exigèncias ctr
            $dedicacion_ctr = $oEncargoFunciones->dedicacion_ctr($id_ubi, $id_enc);

            $sacd_titular = "";
            $sacd_suplente = "";
            $sacds_colaboradores = [];
            $aWhereT['id_enc'] = $id_enc;
            $aWhereT['f_fin'] = 'null';
            $aOperadorT['f_fin'] = 'IS NULL';
            $aWhereT['_ordre'] = 'modo';
            $cTareasSacd = $EncargoSacdRepository->getEncargosSacd($aWhereT, $aOperadorT);
            $s = 0;
            $dedic_horas_ctr = 0;
            foreach ($cTareasSacd as $oTareaSacd) {
                $s++;
                $modo = $oTareaSacd->getModo();
                $id_nom = $oTareaSacd->getId_nom();
                $oPersona = $PersonaDlRepository->findById($id_nom);
                $nom_ap = $oPersona->getNombreApellidosCrSin();
                // para saber la dedicación
                $dedicacion_txt = $oEncargoFunciones->dedicacion($id_nom, $id_enc);
                $dedic_horas_ctr += $oEncargoFunciones->dedicacion_horas($id_nom, $id_enc);
                // mirara si tiene cargo cl.
                if (!empty($cargos[$id_nom])) {
                    $orden_cargo = strtok($cargos[$id_nom], "#");
                    $cargo = strtok("#");
                    if ($cargo === "sacd") $cargo .= " cl";
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
                        if ($Qsf === 1) {
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

        $Html .= "<tr><td class=titulo>$nombre_ubi</td><td>$dedicacion_ctr</td></tr>";
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

$oView = new ViewTwig('encargossacd/controller');
$oView->renderizar('listas.html.twig', $a_campos);