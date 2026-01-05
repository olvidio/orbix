<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\personas\domain\entity\Persona;
use function core\is_true;

/**
 * Esta página lista los asistentes a una actividad seleccionada
 *
 * Admite dos tipos de lista: una simple
 * y otra con datos útiles al cl de la actividad
 *
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
    $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
    $oActividad = $ActividadAllRepository->findById($id_pau);
    $nom_activ = $oActividad->getNom_activ();
}

$queSel = (string)filter_input(INPUT_POST, 'queSel');
$AsistenteRepository = $GLOBALS['container'] ->get(AsistenteRepositoryInterface::class);

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
            if (empty($inc) || $inc === "?") {
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
$a_valores = [];
$aListaCargos = [];
$msg_err = '';
// primero los cargos
if (ConfigGlobal::is_app_installed('actividadcargos')) {
    $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
    $cCargosEnActividad = $ActividadCargoRepository->getActividadCargos(array('id_activ' => $id_pau));
    $mi_sfsv = ConfigGlobal::mi_sfsv();
    $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
    foreach ($cCargosEnActividad as $oActividadCargo) {
        $c++;
        $num++; // número total de asistentes.
        $id_nom = $oActividadCargo->getId_nom();
        $aListaCargos[] = $id_nom;
        $id_cargo = $oActividadCargo->getId_cargo();
        $oCargo = $CargoRepository->findById($id_cargo);
        $tipo_cargo = $oCargo->getTipoCargoVo()->value();
        $cargo = $oCargo->getCargoVo()->value();
        // para los sacd en sf
        if ($tipo_cargo === 'sacd' && $mi_sfsv == 2) {
            continue;
        }

        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            continue;
        }
        $nom = $oPersona->getPrefApellidosNombre();

        $puede_agd = $oActividadCargo->isPuede_agd();
        $observ_c = $oActividadCargo->getObserv();
        $ctr_dl = $oPersona->getCentro_o_dl();

        is_true($puede_agd) ? $chk_puede_agd = "si" : $chk_puede_agd = "no";

        // ahora miro si también asiste:
        $aWhere = array('id_activ' => $id_pau, 'id_nom' => $id_nom);
        $aOperador = [];
        // me aseguro de que no sea un cargo vacío (sin id_nom)
        if (!empty($id_nom) && $cAsistente = $AsistenteRepository->getAsistentes($aWhere, $aOperador)) {
            if (is_array($cAsistente) && count($cAsistente) > 1) {
                $tabla = '';
                foreach ($cAsistente as $Asistente) {
                    $tabla .= "<li>" . $Asistente->getNomTabla() . "</li>";
                }
                $msg_err .= "ERROR: más de un asistente con el mismo id_nom<br>";
                $msg_err .= "<br>$nom(" . $oPersona->getId_tabla() . ")<br><br>En las tablas:<ul>$tabla</ul>";
                exit ("$msg_err");
            }
            $propio = $cAsistente[0]->isPropio();
            $falta = $cAsistente[0]->isFalta();
            $est_ok = $cAsistente[0]->isEst_ok();
            $observ = $cAsistente[0]->getObserv();

            if (is_true($propio)) {
                $chk_propio = _("sí");
            } else {
                $chk_propio = _("no");
            }
            is_true($falta) ? $chk_falta = _("sí") : $chk_falta = _("no");
            is_true($est_ok) ? $chk_est_ok = _("sí") : $chk_est_ok = _("no");
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
$asistentes = [];
$msg_err = '';
foreach ($AsistenteRepository->getAsistentes(array('id_activ' => $id_pau)) as $oAsistente) {
    $c++;
    $num++;
    $id_nom = $oAsistente->getId_nom();
    // si ya está en la lista voy a por otro asistente
    if (in_array($id_nom, $aListaCargos)) {
        $num--;
        continue;
    }

    $oPersona = Persona::findPersonaEnGlobal($id_nom);
    if ($oPersona === null) {
        $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
        continue;
    }
    $nom = $oPersona->getPrefApellidosNombre();
    $ctr_dl = $oPersona->getCentro_o_dl();

    $propio = $oAsistente->isPropio();
    $falta = $oAsistente->isFalta();
    $est_ok = $oAsistente->isEst_ok();
    $observ = $oAsistente->getObserv();

    if (ConfigGlobal::is_app_installed('actividadplazas')) {
        $plaza = $oAsistente->getPlaza();
        if ($plaza < 4) continue;
    }
    if (is_true($propio)) {
        $chk_propio = _("sí");
    } else {
        $chk_propio = _("no");
    }
    is_true($falta) ? $chk_falta = _("sí") : $chk_falta = _("no");
    is_true($est_ok) ? $chk_est_ok = _("sí") : $chk_est_ok = _("no");

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
if (ConfigGlobal::is_app_installed('actividadcargos')) {
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
$aAsistentes = [];
foreach ($a_valores as $k => $val) {
    $c = $val[1];
    $oPersona = $val[7];
    $a_datos_cl = [];
    if ($queSel === "listcl") {
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

$oView = new ViewPhtml('asistentes\controller');
$oView->renderizar('lista_asistentes.phtml', $a_campos);