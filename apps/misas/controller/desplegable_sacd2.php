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
        // iniciales
        $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
    } else {
        $PersonaEx = new PersonaEx($id_nom);
        $sacdEx = $PersonaEx->getNombreApellidos();
        // iniciales
        $nom = mb_substr($PersonaEx->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaEx->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaEx->getApellido2(), 0, 1);
    }
    $iniciales = strtoupper($nom . $ap1 . $ap2);
    return $iniciales;
}

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona_');
$Qid_sacd = (integer)filter_input(INPUT_POST, 'id_sacd_');
//$key_explode = explode($Qkey, '#');
//$key_explode = explode('#',$Qkey);
//$Qid_sacd = $key_explode[0];

$Qseleccion = 2;
//echo 'HOOOLAAA<br>';
//echo 'SACD:'.$Qkey.$Qid_sacd.$key_explode[0].'<br>';
$a_iniciales = [];

$desplegable_sacd='<SELECT ID="id_sacd">';
//$Qid_sacd=-10016194;
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
        $a_iniciales[$id_nom] = $iniciales;
    
        $key = $id_nom . '#' . $iniciales;

        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
    
        $a_sacd[$key] = $sacd ?? '?';
    }
}
/*
if ($Qseleccion & 4) {
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
        $PersonaSacd = new PersonaSacd($id_nom);
        $sacd = $PersonaSacd->getNombreApellidos();
        // iniciales
        $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
        $iniciales = strtoupper($nom . $ap1 . $ap2);
    
        $a_iniciales[$id_nom] = $iniciales;
    
        $key = $id_nom . '#' . $iniciales;

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
        $PersonaSacd = new PersonaSacd($id_nom);
        $sacd = $PersonaSacd->getNombreApellidos();
        // iniciales
        $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
        $iniciales = strtoupper($nom . $ap1 . $ap2);
    
        $a_iniciales[$id_nom] = $iniciales;
    
        $key = $id_nom . '#' . $iniciales;

        $a_sacd[$key] = $sacd ?? '?';
    }
}
*/
$desplegable_sacd.='</SELECT>';

$jsondata['mensaje']='mensaje de desplegable';
$jsondata['desplegable']=$desplegable_sacd;

header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
