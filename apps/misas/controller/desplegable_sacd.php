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

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


//$gestorPersonaSacd = new GestorPersonaSacd();

//$Qid_zona = 3; // l'hospitalet (24) Sarrià(3)
$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qseleccion = (string)filter_input(INPUT_POST, 'seleccion');

$a_iniciales = [];

$desplegable_sacd='<SELECT ID="sacd">';


if ($Qseleccion & 2) {
    $gesZonaSacd = new GestorZonaSacd();
    $a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);
    
    foreach ($a_Id_nom as $id_nom) {
        $PersonaSacd = new PersonaSacd($id_nom);
        $sacd = $PersonaSacd->getNombreApellidos();
        // iniciales
        $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
        $iniciales = strtoupper($nom . $ap1 . $ap2);
    
        $a_iniciales[$id_nom] = $iniciales;
    
        $key = $id_nom . '#' . $iniciales;

        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
    
        $a_sacd[$key] = $sacd ?? '?';
    }
}
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

$desplegable_sacd.='</SELECT>';
//$desplegable_sacd='hola';

$jsondata['mensaje']='mensaje de desplegable';
$jsondata['desplegable']=$desplegable_sacd;

header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
