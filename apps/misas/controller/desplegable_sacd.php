<?php


use Illuminate\Http\JsonResponse;
use misas\domain\entity\InicialesSacd;
use misas\domain\repositories\EncargoDiaRepositoryInterface;
use personas\legacy\GestorPersonaSacd;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;


// INICIO Cabecera global de URL de controlador *********************************
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

$desplegable_sacd = '<SELECT ID="id_sacd">';
$InicialesSacd = new InicialesSacd();
$sacd = $InicialesSacd->nombre_sacd($Qid_sacd);
$iniciales = $InicialesSacd->iniciales($Qid_sacd);

//$key = $Qid_sacd . '#' . $iniciales;
$key = $iniciales . '#' . $Qid_sacd;
$desplegable_sacd .= '<OPTION VALUE="' . $key . '">' . $sacd . '</OPTION>';
if ($Qid_sacd != 0) {
    $desplegable_sacd .= '<OPTION VALUE=""></OPTION>';
}

$lista_sacd = [];
//libre
if ($Qseleccion & 1) {
    $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
    $a_Id_nom = $ZonaSacdRepository->getIdSacdsDeZona($Qid_zona);

    $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
    foreach ($a_Id_nom as $id_nom) {
        $libre = true;
        $inicio_dia = $Qdia . ' 00:00:00';
        $fin_dia = $Qdia . ' 23:59:59';
        $aWhere = [
            'id_nom' => $id_nom,
            'tstart' => "'$inicio_dia', '$fin_dia'",
        ];
        $aOperador = [
            'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);
        foreach ($cEncargosDia as $oEncargoDia) {
            $id_enc = $oEncargoDia->getId_enc();
            //miro si és Missa a primera hora.

            $aWhere = [];
            $aOperador = [];
            $aWhere['id_enc'] = $id_enc;

            $cEncargos = $EncargoRepository->getEncargos($aWhere, $aOperador);
            foreach ($cEncargos as $oEncargo) {
                $id_enc = $oEncargo->getId_enc();
                $desc_enc = $oEncargo->getDesc_enc();
                $id_tipo_enc = $oEncargo->getId_tipo_enc();

                if (substr($id_tipo_enc, 1, 1) == '1') {
                    $libre = false;
                }
            }
        }
        if ($libre) {
            $aWhere = [];
            $aWhere['id_zona'] = $Qid_zona;
            $aWhere['id_nom'] = $id_nom;
            $cZonaSacd = $ZonaSacdRepository->getZonasSacds($aWhere);
            $dia = strtotime($Qdia);
            $n_dia_semana = date('N', $dia);
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
            $sacd = $InicialesSacd->nombre_sacd($id_nom);
            $iniciales = $InicialesSacd->iniciales($id_nom);

//            $key = $id_nom . '#' . $iniciales;
            $key = $iniciales . '#' . $id_nom;
            $lista_sacd[$key] = $sacd;

//            $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
        }
    }
}
//zona
if ($Qseleccion & 2) {
    $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
    $a_Id_nom = $ZonaSacdRepository->getIdSacdsDeZona($Qid_zona);

    foreach ($a_Id_nom as $id_nom) {
        $InicialesSacd = new InicialesSacd();
        $sacd = $InicialesSacd->nombre_sacd($id_nom);
        $iniciales = $InicialesSacd->iniciales($id_nom);

//        $key = $id_nom . '#' . $iniciales;
        $key = $iniciales . '#' . $id_nom;
        $lista_sacd[$key] = $sacd;

//        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
    }
}

if ($Qseleccion & 4) {
    $aWhere = [];
    $aOperador = [];
    $aWhere['sacd'] = 't';
    $aWhere['situacion'] = 'A';
    $aWhere['id_tabla'] = "'n','a'";
    $aOperador['id_tabla'] = 'IN';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    $GesPersonas = new GestorPersonaSacd();

    $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
    foreach ($cPersonas as $oPersona) {
        $id_nom = $oPersona->getId_nom();
        $InicialesSacd = new InicialesSacd();
        $sacd = $InicialesSacd->nombre_sacd($id_nom);
        $iniciales = $InicialesSacd->iniciales($id_nom);

//        $key = $id_nom . '#' . $iniciales;

        $key = $iniciales . '#' . $id_nom;
        $lista_sacd[$key] = $sacd;
//        $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';

    }
}
if ($Qseleccion & 8) {
    $aWhere = [];
    $aOperador = [];
    $aWhere['sacd'] = 't';
    $aWhere['situacion'] = 'A';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    $GesPersonas = new GestorPersonaSacd();

    $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
    foreach ($cPersonas as $oPersona) {
        $id_nom = $oPersona->getId_nom();
        $InicialesSacd = new InicialesSacd();
        $sacd = $InicialesSacd->nombre_sacd($id_nom);
        $iniciales = $InicialesSacd->iniciales($id_nom);

//        $key = $id_nom . '#' . $iniciales;
        $key = $iniciales . '#' . $id_nom;
        $lista_sacd[$key] = $sacd;
//      $desplegable_sacd.='<OPTION VALUE="'.$key.'">'.$sacd.'</OPTION>';
    }
}

ksort($lista_sacd);
foreach ($lista_sacd as $key => $sacd) {
//        echo $key.'-->'.$sacd.'<br>';
    $desplegable_sacd .= '<OPTION VALUE="' . $key . '">' . $sacd . '</OPTION>';
}

$desplegable_sacd .= '</SELECT>';

//echo $desplegable_sacd;

$jsondata['mensaje'] = 'mensaje de desplegable';
$jsondata['desplegable'] = $desplegable_sacd;

(new JsonResponse($jsondata))->send();
