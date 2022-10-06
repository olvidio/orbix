<?php

use actividades\model\entity as actividades;
use actividadcargos\model\entity as actividadcargos;
use asistentes\model\entity as asistentes;
use personas\model\entity as personas;
use usuarios\model\entity as usuarios;
use ubis\model\entity as ubis;
use actividades\model\entity\Actividad;

/**
 * Esta página lista los asistentes a una actividad seleccionada
 *
 * Admite dos tipos de lista: una simple
 * y otra con datos útiles al cl de la actividad
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Josep Companys
 * @since        15/5/02.
 *
 */
/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_pau = (integer)strtok($a_sel[0], "#");
    $nom_activ = strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $id_pau = (integer)filter_input(INPUT_POST, 'id_pau');
    $oActividad = new Actividad($id_pau);
    $nom_activ = $oActividad->getNom_activ();
}

$queSel = (string)filter_input(INPUT_POST, 'queSel');
$gesAsistentes = new asistentes\GestorAsistente();

function datos($oPersona)
{
    $estudios = '';
    $profesion = '';
    $edad = '';
    $inc_f_inc = '';
    $eap = '';
    $observ = '';
    $obj_persona = get_class($oPersona);
    $obj_persona = str_replace("personas\\model\\entity\\", '', $obj_persona);
    switch ($obj_persona) {
        case 'PersonaN':
        case 'PersonaNax':
        case 'PersonaAgd':
        case 'PersonaS':
        case 'PersonaSSSC':
        case 'PersonaDl':
            $profesion = $oPersona->getProfesion();
            $f_nacimiento = $oPersona->getF_nacimiento()->getFromLocal();
            $inc = $oPersona->getInc();
            if (empty($inc) || $inc == "?") {
                $f_inc = "?";
            } else {
                //$get = "getF_$inc()";
                // Ahora sólo está la última
                $oF_inc = $oPersona->getF_inc();
                $f_inc = $oF_inc->getFromLocal();
            }
            if (!empty($inc)) {
                $inc_f_inc = $inc . ' : ' . $f_inc;
            }
            $edad = $oPersona->getEdad();
            $eap = empty($oPersona->getEap()) ? '?' : $oPersona->getEap();
            break;
        case 'PersonaIn':
        case 'PersonaEx':
            $profesion = $oPersona->getProfesion();
            $edad = $oPersona->getEdad();
            $inc = $oPersona->getInc();
            $f_inc = $oPersona->getF_inc()->getFromLocal();
            if (!empty($inc)) {
                $inc_f_inc = $inc . ' : ' . $f_inc;
            }
            $eap = $oPersona->getEap();
            break;
    }

    $a_datos_cl = [
        'estudios' => $estudios,
        'profesion' => $profesion,
        'edad' => $edad,
        'inc_f_inc' => $inc_f_inc,
        'eap' => $eap,
        'observ' => $observ,
    ];
    return $a_datos_cl;
}

// -----------------------------------------------------------

// primero el cl:
$c = 0;
$num = 0;
$a_valores = array();
$aListaCargos = array();
$msg_err = '';
// primero los cargos
if (core\configGlobal::is_app_installed('actividadcargos')) {
    $GesCargosEnActividad = new actividadcargos\GestorActividadCargo();
    $cCargosEnActividad = $GesCargosEnActividad->getActividadCargos(array('id_activ' => $id_pau));
    $mi_sfsv = core\ConfigGlobal::mi_sfsv();
    foreach ($cCargosEnActividad as $oActividadCargo) {
        $c++;
        $num++; // número total de asistentes.
        $id_nom = $oActividadCargo->getId_nom();
        $aListaCargos[] = $id_nom;
        $id_cargo = $oActividadCargo->getId_cargo();
        $oCargo = new actividadcargos\Cargo(array('id_cargo' => $id_cargo));
        $tipo_cargo = $oCargo->getTipo_cargo();
        // para los sacd en sf
        if ($tipo_cargo == 'sacd' && $mi_sfsv == 2) {
            continue;
        }

        $oPersona = personas\Persona::NewPersona($id_nom);
        if (!is_object($oPersona)) {
            $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            continue;
        }
        $oCargo = new actividadcargos\Cargo($id_cargo);
        $nom = $oPersona->getPrefApellidosNombre();

        $cargo = $oCargo->getCargo();
        $puede_agd = $oActividadCargo->getPuede_agd();
        $observ_c = $oActividadCargo->getObserv();
        $ctr_dl = $oPersona->getCentro_o_dl();

        $puede_agd == 't' ? $chk_puede_agd = "si" : $chk_puede_agd = "no";

        // ahora miro si también asiste:
        $aWhere = array('id_activ' => $id_pau, 'id_nom' => $id_nom);
        $aOperador = [];
        // me aseguro de que no sea un cargo vacio (sin id_nom)
        if (!empty($id_nom) && $cAsistente = $gesAsistentes->getAsistentes($aWhere, $aOperador)) {
            if (is_array($cAsistente) && count($cAsistente) > 1) {
                $tabla = '';
                foreach ($cAsistente as $Asistente) {
                    $tabla .= "<li>" . $Asistente->getNomTabla() . "</li>";
                }
                $msg_err .= "ERROR: más de un asistente con el mismo id_nom<br>";
                $msg_err .= "<br>$nom(" . $oPersona->getId_tabla() . ")<br><br>En las tablas:<ul>$tabla</ul>";
                exit ("$msg_err");
            }
            $propio = $cAsistente[0]->getPropio();
            $falta = $cAsistente[0]->getFalta();
            $est_ok = $cAsistente[0]->getEst_ok();
            $observ = $cAsistente[0]->getObserv();

            if ($propio == 't') {
                $chk_propio = _("sí");
            } else {
                $chk_propio = _("no");
            }
            $falta == 't' ? $chk_falta = _("sí") : $chk_falta = _("no");
            $est_ok == 't' ? $chk_est_ok = _("sí") : $chk_est_ok = _("no");
            $asis = "t";
            $a_valores[$c][3] = $chk_propio;
            $a_valores[$c][4] = $chk_est_ok;
            $a_valores[$c][5] = $chk_falta;
        } else {
            $a_valores[$c][3] = array('span' => 3, 'valor' => _("no asiste"));
            $observ = '';
            $num--;
            $asis = "f";
        }

        $a_valores[$c][1] = $cargo;
        $a_valores[$c][2] = "$nom  ($ctr_dl)";
        $a_valores[$c][6] = "$observ_c $observ";
        $a_valores[$c][7] = $oPersona;
    }
}
// ahora los asistentes sin los cargos
$asistentes = array();
$msg_err = '';
foreach ($gesAsistentes->getAsistentes(array('id_activ' => $id_pau)) as $oAsistente) {
    $c++;
    $num++;
    $id_nom = $oAsistente->getId_nom();
    // si ya está en la lista voy a por otro asistente
    if (in_array($id_nom, $aListaCargos)) {
        $num--;
        continue;
    }

    $oPersona = personas\Persona::NewPersona($id_nom);
    if (!is_object($oPersona)) {
        $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
        continue;
    }
    $nom = $oPersona->getPrefApellidosNombre();
    $ctr_dl = $oPersona->getCentro_o_dl();

    $propio = $oAsistente->getPropio();
    $falta = $oAsistente->getFalta();
    $est_ok = $oAsistente->getEst_ok();
    $observ = $oAsistente->getObserv();

    if (core\configGlobal::is_app_installed('actividadplazas')) {
        $plaza = $oAsistente->getPlaza();
        if ($plaza < 4) continue;
    }
    if ($propio == 't') {
        $chk_propio = _("sí");
    } else {
        $chk_propio = _("no");
    }
    $falta == 't' ? $chk_falta = _("sí") : $chk_falta = _("no");
    $est_ok == 't' ? $chk_est_ok = _("sí") : $chk_est_ok = _("no");

    $a_val[2] = "$nom  ($ctr_dl)";
    $a_val[3] = $chk_propio;
    $a_val[4] = $chk_est_ok;
    $a_val[5] = $chk_falta;
    $a_val[6] = $observ;
    $a_val[7] = $oPersona;
    $asistentes[$nom] = $a_val;
}
uksort($asistentes, "core\strsinacentocmp");

$c = 0;
if (core\configGlobal::is_app_installed('actividadcargos')) {
    $c = count($a_valores);
}

//cargos y a continuación añado asistentes 
foreach ($asistentes as $nom => $val) {
    $c++;
    $val[1] = "$c.-";
    $a_valores[$c] = $val;
}

// nuevo array parra pasar a la vista
$txt_cl = '';
$aAsistentes = array();
foreach ($a_valores as $k => $val) {
    $c = $val[1];
    $oPersona = $val[7];
    $a_datos_cl = array();
    if ($queSel == "listcl") {
        $a_datos_cl = datos($oPersona);
        // las observ no son las personales, sino de la asistencia:
        $a_datos_cl['observ'] = $a_valores[$k][6];
    }

    $aAsistentes[$c] = array('nombre' => $val[2],
        'a_datos_cl' => $a_datos_cl
    );
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'nom_activ' => $nom_activ,
    'queSel' => $queSel,
    'aAsistentes' => $aAsistentes,
];

$oView = new core\View('asistentes/controller');
echo $oView->render('lista_asistentes.phtml', $a_campos);