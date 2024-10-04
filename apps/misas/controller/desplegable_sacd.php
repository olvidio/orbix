<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use encargossacd\model\entity\GestorEncargo;
use misas\domain\entity\InicialesSacd;
use misas\domain\repositories\EncargoDiaRepository;
use misas\domain\repositories\InicialesSacdRepository;
use misas\model\EncargosZona;
use personas\model\entity\PersonaSacd;
use personas\model\entity\GestorPersona;
use personas\model\entity\PersonaEx;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use zonassacd\model\entity\GestorZonaSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_sacd = (integer)filter_input(INPUT_POST, 'id_sacd');
$Qseleccion = (integer)filter_input(INPUT_POST, 'seleccion');
$Qdia = (string)filter_input(INPUT_POST, 'dia');

//echo $Qid_zona.'#'.$Qid_sacd.'s'.$Qseleccion;

//$Qseleccion = 2;
// $Qid_sacd=100111501;

$desplegable_sacd='<SELECT ID="id_sacd">';
$InicialesSacd = new InicialesSacd();
$sacd=$InicialesSacd->nombre_sacd($Qid_sacd);
$iniciales=$InicialesSacd->iniciales($Qid_sacd);

$key = $Qid_sacd . '#' . $iniciales;
$desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
if ($Qid_sacd!=0)
{
    $desplegable_sacd.='<OPTION VALUE=""></OPTION>';
}

//libre
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
            $aWhere = [];
            $aWhere['id_zona'] = $Qid_zona;
            $aWhere['id_nom'] = $id_nom;
            $GesZonasSacd = new GestorZonaSacd();
            $cZonaSacd = $GesZonasSacd->getZonasSacds($aWhere);
            $dia = strtotime($Qdia);
            $n_dia_semana=date('N', $dia);
            $oZonaSacd = $cZonaSacd[0];
            switch ($n_dia_semana) {
                case 1:
                    $libre = $oZonaSacd->getDw1();
                break;
                case 2:
                    $libre = $oZonaSacd->getDw2();
                break;
                case 3:
                    $libre = $oZonaSacd->getDw3();
                break;
                case 4:
                    $libre = $oZonaSacd->getDw4();
                break;
                case 5:
                    $libre = $oZonaSacd->getDw5();
                break;
                case 6:
                    $libre = $oZonaSacd->getDw6();
                break;
                case 7:
                    $libre = $oZonaSacd->getDw7();
                break;
            }
        }
        if ($libre) {
            $InicialesSacd = new InicialesSacd();
            $sacd=$InicialesSacd->nombre_sacd($id_nom);
            $iniciales=$InicialesSacd->iniciales($id_nom);
        
            $key = $id_nom . '#' . $iniciales;
    
            $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
        }
    }
}
//zona
if ($Qseleccion & 2) {
    $gesZonaSacd = new GestorZonaSacd();
    $a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);
    
    foreach ($a_Id_nom as $id_nom) {
        $InicialesSacd = new InicialesSacd();
        $sacd=$InicialesSacd->nombre_sacd($id_nom);
        $iniciales=$InicialesSacd->iniciales($id_nom);

        $key = $id_nom . '#' . $iniciales;

        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
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
        $InicialesSacd = new InicialesSacd();
        $sacd=$InicialesSacd->nombre_sacd($id_nom);
        $iniciales=$InicialesSacd->iniciales($id_nom);

        $key = $id_nom . '#' . $iniciales;
        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
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
        $InicialesSacd = new InicialesSacd();
        $sacd=$InicialesSacd->nombre_sacd($id_nom);
        $iniciales=$InicialesSacd->iniciales($id_nom);

        $key = $id_nom . '#' . $iniciales;
        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
    }
}

$desplegable_sacd.='</SELECT>';

$jsondata['mensaje']='mensaje de desplegable';
$jsondata['desplegable']=$desplegable_sacd;

header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
