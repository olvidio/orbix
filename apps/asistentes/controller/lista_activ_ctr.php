<?php

use actividades\model\entity\Actividad;
use asistentes\model\entity\GestorAsistente;
use core\ConfigGlobal;
use core\ViewPhtml;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\GestorPersonaSSSC;
use ubis\model\entity\GestorCentroDl;
use web\Periodo;

/**
 * Listados de los asistentes a actividades por ctr
 *
 *
 *
 *
 * @package    delegacion
 * @subpackage    personas
 * @author    Josep Companys
 * @since        15/5/02. modif 22/4/03 Dani para el caso de n que hacen el ca con agd.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qssfsv = (string)filter_input(INPUT_POST, 'ssfsv');

if (ConfigGlobal::mi_sfsv() == 1) {
    if ($Qssfsv === 'sf' && (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des')))) {
        $ssfsv = 'sf';
    } else {
        $ssfsv = 'sv';
    }
}
if (ConfigGlobal::mi_sfsv() == 2) {
    $ssfsv = 'sf';
}

$Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
$Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
$Qn_agd = (string)filter_input(INPUT_POST, 'n_agd');
$Qyear = (integer)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

//no me cabe el valor en el menú en sss+ (pasa de 100 caracteres), por tanto se lo damos por programa
if ($Qn_agd === "sss") {
    $Qsasistentes = "sss+";
}

//desarrollamos la condición que filtre el tipo de actividad		
$condta = '';
$oTipoActiv = new web\TiposActividades();
$oTipoActiv->setSfsvText($ssfsv);
$oTipoActiv->setAsistentesText($Qsasistentes);
$oTipoActiv->setActividadText($Qsactividad);
$condta = $oTipoActiv->getId_tipo_activ();

//para el caso especial de n que hacen su ca en cv de agd:
$condta_plus = '';
if ($Qsasistentes === "n" && ($Qsactividad === "ca" || $Qsactividad === "crt")) {
    if ($Qsactividad === "ca") {
        $activ = "cv";
    } else {
        $activ = $Qsactividad;
    }
    $oTipoActiv = new web\TiposActividades();
    $oTipoActiv->setSfsvText($ssfsv);
    $oTipoActiv->setAsistentesText('agd');
    $oTipoActiv->setActividadText($activ);
    $condta_plus = $oTipoActiv->getId_tipo_activ();
}

// para el caso de los ap. que han hecho el crt con sr.
$condta_sr = '';
if ($Qsactividad === "crt") {
    $oTipoActiv = new web\TiposActividades();
    $oTipoActiv->setSfsvText($ssfsv);
    $oTipoActiv->setAsistentesText('sr');
    $oTipoActiv->setActividadText('crt');
    $condta_sr = $oTipoActiv->getId_tipo_activ();
}
$condicion = '';
$condicion .= empty($condta) ? '' : '^' . $condta;
$condicion .= empty($condta_plus) ? '' : '|^' . $condta_plus;
$condicion .= empty($condta_sr) ? '' : '|^' . $condta_sr;

$aWhereAct = [];
$aOperadorAct = [];
$aWhereAct['id_tipo_activ'] = $condicion;
$aOperadorAct['id_tipo_activ'] = "~";

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();

$aWhere = [];
$aOperador = [];
switch ($Qn_agd) {
    case "a":
        $tabla = "p_agregados";
        $aWhere['tipo_ctr'] = '^a';
        $aOperador['tipo_ctr'] = '~';
        break;
    case "n":
        $tabla = "p_numerarios";
        $aWhere['tipo_ctr'] = '^n';
        $aOperador['tipo_ctr'] = '~';
        break;
    case "nm":
        $tabla = "p_n_agd";
        $aWhere['tipo_ctr'] = '^nm';
        $aOperador['tipo_ctr'] = '~';
        break;
    case "nj":
        $tabla = "p_n_agd";
        $aWhere['tipo_ctr'] = '^nj(ce)*';
        $aOperador['tipo_ctr'] = '~';
        break;
    case "sssc":
        $tabla = "p_sssc";
        $aWhere['tipo_ctr'] = '^ss';
        $aOperador['tipo_ctr'] = '~';
        break;
    case "c": //otro
        $tabla = "p_n_agd";
        $aWhere['id_ubi'] = $_POST['id_ubi'];
        $aOperador['tipo_ctr'] = array();
        break;
}
$aWhere['status'] = 't';
$aWhere['_ordre'] = 'nombre_ubi';
// primero selecciono los centros y las personas que dependen de él
$GesCentros = new GestorCentroDl();
$cCentros = $GesCentros->getCentros($aWhere, $aOperador);

// Bucle para poder sacar los centros de la consulta anterior
$ctr = 0;
$aCentros = array();
foreach ($cCentros as $oCentro) {
    $ctr++;
    $id_ubi = $oCentro->getId_ubi();
    $nombre_ubi = $oCentro->getNombre_ubi();
    //consulta para buscar personas de cada ctr
    if ($tabla === "p_sssc") {
        $GesPersonas = new GestorPersonaSSSC();
        $cPersonas = $GesPersonas->getPersonas(array('id_ctr' => $id_ubi, 'situacion' => 'A', '_ordre' => 'apellido1'));
    } else {
        $GesPersonas = new GestorPersonaDl();
        $cPersonas = $GesPersonas->getPersonas(array('id_ctr' => $id_ubi, 'situacion' => 'A', '_ordre' => 'apellido1,apellido2,nom'));
    }

    $aCentros[$id_ubi]['nombre_ubi'] = $nombre_ubi;

    $i = 0;
    $aPersonasCtr = array();
    $aWhereNom = [];
    foreach ($cPersonas as $oPersona) {
        $i++;
        $id_nom = $oPersona->getId_nom();
        $ap_nom = $oPersona->getPrefApellidosNombre();
        $aWhereNom['id_nom'] = $id_nom;
        $aWhereNom['propio'] = 't';
        $aOperadorNom = [];
        $aWhereAct['f_ini'] = "'$inicioIso','$finIso'";
        $aOperadorAct['f_ini'] = "BETWEEN";

        $GesAsistencias = new GestorAsistente();
        $cAsistencias = $GesAsistencias->getActividadesDeAsistente($aWhereNom, $aOperadorNom, $aWhereAct, $aOperadorAct);
        $aActividades = array();
        if (is_array($cAsistencias) && count($cAsistencias) == 0) {
            $nom_activ = _("pendiente de solicitar");
        } else {
            $a = 0;
            foreach ($cAsistencias as $oAsistente) {
                $id_activ = $oAsistente->getId_activ();
                $oActividad = new Actividad($id_activ);
                $nom_activ = $oActividad->getNom_activ();
                $a++;
                $aActividades[] = $nom_activ;
            }
        }
        $aPersonasCtr[$i]['ap_nom'] = $ap_nom;
        $aPersonasCtr[$i]['actividades'] = $aActividades;
    }
    $aCentros[$id_ubi]['personas'] = $aPersonasCtr;
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'aCentros' => $aCentros,
];

$oView = new ViewPhtml('asistentes/controller');
$oView->renderizar('lista_activ_ctr.phtml', $a_campos);
