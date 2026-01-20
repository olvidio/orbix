<?php

use core\ConfigGlobal;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use web\ContestarJson;
use function core\is_true;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qque = (string)filter_input(INPUT_POST, 'que');

$oMiUsuario = ConfigGlobal::MiUsuario();
$miSfsv = ConfigGlobal::mi_sfsv();

switch ($Qobj_pau) {
    case 'PersonaN':
        $repoPersona = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
        break;
    case 'PersonaNax':
        $repoPersona = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class);
        break;
    case 'PersonaAgd':
        $repoPersona = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
        break;
    case 'PersonaS':
        $repoPersona = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
        break;
    case 'PersonaSSSC':
        $repoPersona = $GLOBALS['container']->get(PersonaSSSCRepositoryInterface::class);
        break;
    case 'PersonaEx':
        $repoPersona = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
        break;
    default:
        echo "No existe la clase de la persona";
        die();
}


$oPersona = $repoPersona->findById($Qid_nom);
if ($Qque === 'eliminar') {
    $error_txt = '';
    $dl = $oPersona->getDl();
    // solo lo dejo borrar si es de mi dl.
    if (ConfigGlobal::mi_delef() === $dl) {
        if ($repoPersona->Eliminar($oPersona) === false) {
            $error_txt .= _("hay un error, no se ha eliminado");
            $error_txt .= "\n" . $repoPersona->getErrorTxt();
        }
    } else {
        $error_txt .= _("No se ha eliminado, porque no es de mi dl");
    }

    ContestarJson::enviar($error_txt, 'ok');
    die();
}


$dl = (string)filter_input(INPUT_POST, 'dl');
$id_ctr = (int)filter_input(INPUT_POST, 'id_ctr');
$situacion = (string)filter_input(INPUT_POST, 'situacion');
$idioma_preferido = (string)filter_input(INPUT_POST, 'idioma_preferido');
$nivel_stgr = (int)filter_input(INPUT_POST, 'nivel_stgr');

$trato = (string)filter_input(INPUT_POST, 'trato');
$nom = (string)filter_input(INPUT_POST, 'nom');
$apel_fam = (string)filter_input(INPUT_POST, 'apel_fam');
$nx1 = (string)filter_input(INPUT_POST, 'nx1');
$apellido1 = (string)filter_input(INPUT_POST, 'apellido1');
$nx2 = (string)filter_input(INPUT_POST, 'nx2');
$apellido2 = (string)filter_input(INPUT_POST, 'apellido2');
$lugar_nacimiento = (string)filter_input(INPUT_POST, 'lugar_nacimiento');
$f_nacimiento = (string)filter_input(INPUT_POST, 'f_nacimiento');
$f_situacion = (string)filter_input(INPUT_POST, 'f_situacion');
$profesion = (string)filter_input(INPUT_POST, 'profesion');
$sacd = (string)filter_input(INPUT_POST, 'sacd');
$eap = (string)filter_input(INPUT_POST, 'eap');
$inc = (string)filter_input(INPUT_POST, 'inc');
$f_inc = (string)filter_input(INPUT_POST, 'f_inc');
$ce = (int)filter_input(INPUT_POST, 'ce');
$ce_lugar = (string)filter_input(INPUT_POST, 'ce_lugar');
$ce_ini = (int)filter_input(INPUT_POST, 'ce_ini');
$ce_fin = (int)filter_input(INPUT_POST, 'ce_fin');
$observ = (string)filter_input(INPUT_POST, 'observ');

$oPersona->setDl($dl);
$oPersona->setId_ctr($id_ctr);
$oPersona->setSituacion($situacion);
$oPersona->setIdioma_preferido($idioma_preferido);
$oPersona->setNivel_stgr($nivel_stgr);
$oPersona->setTrato($trato);
$oPersona->setNom($nom);
$oPersona->setApel_fam($apel_fam);
$oPersona->setNx1($nx1);
$oPersona->setApellido1($apellido1);
$oPersona->setNx2($nx2);
$oPersona->setApellido2($apellido2);
$oPersona->setLugar_nacimiento($lugar_nacimiento);
// asegurar tipo correcto para f_nacimiento
$oF_nacimiento = empty($f_nacimiento) ? null : DateTimeLocal::createFromLocal($f_nacimiento);
$oPersona->setF_nacimiento($oF_nacimiento);
// asegurar tipo correcto para f_situacion
$oF_situacion = empty($f_situacion) ? null : DateTimeLocal::createFromLocal($f_situacion);
$oPersona->setF_situacion($oF_situacion);
$oPersona->setProfesion($profesion);
$oPersona->setSacd(is_true($sacd));
$oPersona->setEap($eap);
$oPersona->setInc($inc);
// asegurar tipo correcto para f_inc
$oF_inc = empty($f_inc) ? null : DateTimeLocal::createFromLocal($f_inc);
$oPersona->setF_inc($oF_inc);
$oPersona->setCe($ce);
$oPersona->setCe_lugar($ce_lugar);
$oPersona->setCe_ini($ce_ini);
$oPersona->setCe_fin($ce_fin);
$oPersona->setObserv($observ);

$error_txt = '';
if ($repoPersona->Guardar($oPersona) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $repoPersona->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');