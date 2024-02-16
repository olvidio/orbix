<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use misas\domain\repositories\EncargoDiaRepository;
use misas\model\EncargosZona;
use personas\model\entity\PersonaSacd;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use zonassacd\model\entity\GestorZonaSacd;
use personas\model\entity\GestorPersona;
use personas\model\entity\PersonaEx;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

function iniciales($id_nom) {
    if ($id_nom>0) {
        $PersonaSacd = new PersonaSacd($id_nom);
        $nom = $PersonaSacd->getNom();
        $ap1 = $PersonaSacd->getApellido1();
        $ap2 = $PersonaSacd->getApellido2();
    } else {
        $PersonaEx = new PersonaEx($id_nom);
        $sacdEx = $PersonaEx->getNombreApellidos();
        $nom = $PersonaEx->getNom();
        $ap1 = $PersonaEx->getApellido1();
        $ap2 = $PersonaEx->getApellido2();
    }

    // iniciales
    $inom='';
    if (!is_null($nom))
        $inom = mb_substr($nom, 0, 1);
    $iap1='';
    if (!is_null($ap1))
        $iap1 = mb_substr($ap1, 0, 1);
    $iap2='';
    if (!is_null($ap2))
        $iap2 = mb_substr($ap2, 0, 1);

    $iniciales = strtoupper($inom . $iap1 . $iap2);
    return $iniciales;
}

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_sacd = (integer)filter_input(INPUT_POST, 'id_sacd');
$Qseleccion = (integer)filter_input(INPUT_POST, 'seleccion');

//echo $Qid_zona.'#'.$Qid_sacd.'s'.$Qseleccion;

//$Qseleccion = 2;
// $Qid_sacd=100111501;

$desplegable_sacd='<SELECT ID="id_sacd">';
if ($Qid_sacd>0) {
    $PersonaSacd = new PersonaSacd($Qid_sacd);
    $sacd = $PersonaSacd->getNombreApellidos();
} else {
    $PersonaEx = new PersonaEx($Qid_sacd);
    $sacd = $PersonaEx->getNombreApellidos();
}
$iniciales = iniciales($Qid_sacd);
$a_iniciales[$Qid_sacd] = $iniciales;
    
$key = $Qid_sacd . '#' . $iniciales;
//$desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$key.'('.$iniciales.')</OPTION>';
$desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
    
$a_sacd[$key] = $sacd ?? '?';

if ($Qseleccion & 2) {
    $gesZonaSacd = new GestorZonaSacd();
    $a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);
    
    foreach ($a_Id_nom as $id_nom) {
        if ($id_nom>0) {
            $PersonaSacd = new PersonaSacd($id_nom);
            $sacd = $PersonaSacd->getNombreApellidos();
        } else {
            $PersonaEx = new PersonaEx($id_nom);
            $sacd = $PersonaEx->getNombreApellidos();
        }
        $iniciales = iniciales($id_nom);
    
        $key = $id_nom . '#' . $iniciales;

        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
    
        $a_sacd[$key] = $sacd ?? '?';
    }
}

if ($Qseleccion & 4) {
//    echo 'seleccion 4';
    $a_Clases = [];
    $a_Clases[] = array('clase' => 'PersonaN', 'get' => 'getPersonas');
    $a_Clases[] = array('clase' => 'PersonaAgd', 'get' => 'getPersonas');
    $aWhere = [];
    $aOperador = [];
    $aWhere['sacd'] = 't';
    $aWhere['situacion'] = 'A';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    $GesPersonas = new GestorPersona();
    $GesPersonas->setClases($a_Clases);
    $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
    foreach ($cPersonas as $oPersona) {
        $id_nom = $oPersona->getId_nom();
        if ($id_nom>0) {
            $PersonaSacd = new PersonaSacd($id_nom);
            $sacd = $PersonaSacd->getNombreApellidos();
/*        } else {
            $PersonaEx = new PersonaEx($id_nom);
            $sacd = $PersonaEx->getNombreApellidos();
*/        }
        $iniciales = iniciales($id_nom);
    
        $key = $id_nom . '#' . $iniciales;

        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
    
        $a_sacd[$key] = $sacd ?? '?';
    }
}
if ($Qseleccion & 8) { 
    $a_Clases = [];
    $a_Clases[] = array('clase' => 'PersonaEx', 'get' => 'getPersonasEx');
    $aWhere = [];
    $aOperador = [];
    $aWhere['sacd'] = 't';
    $aWhere['situacion'] = 'A';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    $GesPersonas = new GestorPersona();
    $GesPersonas->setClases($a_Clases);
    $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
    foreach ($cPersonas as $oPersona) {
        $id_nom = $oPersona->getId_nom();
        if ($id_nom>0) {
            $PersonaSacd = new PersonaSacd($id_nom);
            $sacd = $PersonaSacd->getNombreApellidos();
        } else {
            $PersonaEx = new PersonaEx($id_nom);
            $sacd = $PersonaEx->getNombreApellidos();
        }
        $iniciales = iniciales($id_nom);
    
        $key = $id_nom . '#' . $iniciales;

        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
    
        $a_sacd[$key] = $sacd ?? '?';
    }
}

$desplegable_sacd.='</SELECT>';

$jsondata['mensaje']='mensaje de desplegable';
$jsondata['desplegable']=$desplegable_sacd;

header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
