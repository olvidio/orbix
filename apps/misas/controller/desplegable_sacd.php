<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use encargossacd\model\entity\GestorEncargo;
use misas\domain\repositories\EncargoDiaRepository;
use misas\domain\repositories\InicialesSacdRepository;
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
    $InicialesSacdRepository = new InicialesSacdRepository();
    $InicialesSacd = $InicialesSacdRepository->findById($id_nom);
    if ($InicialesSacd === null) {
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
    } else {
        $iniciales = $InicialesSacd->getIniciales();
    }

    return $iniciales;
}
function nombre_sacd($id_nom) {
    if ($id_nom>0) {
        $PersonaSacd = new PersonaSacd($id_nom);
        $nombre_sacd = $PersonaSacd->getNombreApellidos().' ('.iniciales($id_nom).')';
    } else {
        $PersonaEx = new PersonaEx($id_nom);
        $nombre_sacd = $PersonaEx->getNombreApellidos().' ('.iniciales($id_nom).')';
    }
    return $nombre_sacd;
}

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_sacd = (integer)filter_input(INPUT_POST, 'id_sacd');
$Qseleccion = (integer)filter_input(INPUT_POST, 'seleccion');
$Qdia = (string)filter_input(INPUT_POST, 'dia');

//echo $Qid_zona.'#'.$Qid_sacd.'s'.$Qseleccion;

//$Qseleccion = 2;
// $Qid_sacd=100111501;

$desplegable_sacd='<SELECT ID="id_sacd">';
$sacd=nombre_sacd($Qid_sacd);
$iniciales = iniciales($Qid_sacd);

$key = $Qid_sacd . '#' . $iniciales;
//$desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$key.'('.$iniciales.')</OPTION>';
//if ($iniciales==''){
    $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
//} else {
//    $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
//}
    
if ($Qseleccion & 1) {
    $gesZonaSacd = new GestorZonaSacd();
    $a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);
    
    foreach ($a_Id_nom as $id_nom) {
        $libre=true;
        $inicio_dia = $Qdia.' 00:00:00';
        $fin_dia = $Qdia.' 23:59:59';
        $aWhere = [
            'id_nom' => $id_nom,
            'tstart' => "'$inicio_dia', '$fin_dia'",
        ];
        $aOperador = [
            'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = new EncargoDiaRepository();
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);
        foreach ($cEncargosDia as $oEncargoDia) {
            $id_enc = $oEncargoDia->getId_enc();
            //miro si és Missa a primera hora.

            $aWhere = array();
            $aOperador = array();
            $aWhere['id_enc'] = $id_enc;
            
            $GesEncargos = new GestorEncargo();
            $cEncargos = $GesEncargos->getEncargos($aWhere, $aOperador);
            
 //Tiene que haber sólo uno, falta comprobarlo
            foreach ($cEncargos as $oEncargo) {
                $id_enc = $oEncargo->getId_enc();
                $desc_enc = $oEncargo->getDesc_enc();
                $id_tipo_enc = $oEncargo->getId_tipo_enc();

                if (substr($id_tipo_enc,1,1)=='1')
                {
                    $libre=false;
                }
            }
        }
        if ($libre) {
            $sacd=nombre_sacd($id_nom);
            $iniciales = iniciales($id_nom);
        
            $key = $id_nom . '#' . $iniciales;
    
//            $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
            $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
        }
    }
}    
if ($Qseleccion & 2) {
    $gesZonaSacd = new GestorZonaSacd();
    $a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);
    
    foreach ($a_Id_nom as $id_nom) {
        $sacd=nombre_sacd($id_nom);
        $iniciales = iniciales($id_nom);
    
        $key = $id_nom . '#' . $iniciales;

        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
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
        $sacd=nombre_sacd($id_nom);
        $iniciales = iniciales($id_nom);
    
        $key = $id_nom . '#' . $iniciales;
        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
//        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
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
        $sacd=nombre_sacd($id_nom);
        $iniciales = iniciales($id_nom);
    
        $key = $id_nom . '#' . $iniciales;
        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
//        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'('.$iniciales.')</OPTION>';
    }
}

$desplegable_sacd.='</SELECT>';

$jsondata['mensaje']='mensaje de desplegable';
$jsondata['desplegable']=$desplegable_sacd;

header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
