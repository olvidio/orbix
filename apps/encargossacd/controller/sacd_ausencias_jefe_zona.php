<?php

use core\ConfigGlobal;
use core\ViewTwig;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use web\Desplegable;
use web\Hash;

/**
 * Esta página muestra la ficha de las ausencias de un sacd.
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        27/03/07.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
//

//$Qrefresh = (integer)  filter_input(INPUT_POST, 'refresh');
//$oPosicion->recordar($Qrefresh);
$oPosicion->recordar();

$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_role = $oMiUsuario->getId_role();
//echo 'id_role: '.$id_role.'<br>';


$id_usuario = ConfigGlobal::mi_id_usuario();
//echo 'id_usuario: '.$id_usuario.'<br>';
$id_sacd = $oMiUsuario->getId_pauAsString();
//echo 'id_sacd: '.$id_sacd.'<br>';

$RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
$aRoles = $RoleRepository->getArrayRoles();

$ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
$cZonas = $ZonaRepository->getZonas(array('id_nom' => $id_sacd));
//echo 'count zonas: '.count($cZonas).'<br>';
$InicialesSacdRepository = $GLOBALS['container']->get(InicialesSacdRepositoryInterface::class);
$a_sacd = [];
if (is_array($cZonas) && count($cZonas) > 0) {
    $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
    foreach ($cZonas as $oZona) {
        $id_zona = $oZona->getId_zona();
        $cSacds = $ZonaSacdRepository->getSacdsZona($id_zona);
        foreach ($cSacds as $id_nom) {
//            echo $id_nom.'<br>';
            $InicialesSacd = $InicialesSacdRepository->findById($id_nom);
            if ($InicialesSacd === null) {
                $iniciales = '';
                $color = '';
            } else {
                $iniciales = $InicialesSacd->getIniciales();
                $color = $InicialesSacd->getColor();
            }
            $InicialesSacd = new InicialesSacd();
            $sacd=$InicialesSacd->nombre_sacd($id_nom);
            $iniciales=$InicialesSacd->iniciales($id_nom);
            $key = $iniciales . '#' . $id_nom;
            $a_sacd[$key] = $sacd ?? '?';
        }
    }
} else { // No soy jefe de zona
    if (!is_null($id_sacd))
    {
        $InicialesSacd = new InicialesSacd();
//        echo is_null($id_sacd).'='.($id_sacd=='').'=='.$id_sacd.'<br>';
        $sacd=$InicialesSacd->nombre_sacd($id_sacd);
//        echo is_null($id_sacd).'-->'.$sacd.'<br>';
        $iniciales=$InicialesSacd->iniciales($id_sacd);
        $key = $iniciales . '#' . $id_sacd;
        $a_sacd[$key] = $sacd ?? '?';
    }
}

if (($aRoles[$id_role]==='Oficial_dl') || ($_SESSION['oConfig']->is_jefeCalendario()))
{
//    echo 'OFICIAL DL<br>';
    $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
    $aWhere = [];
    $aOperador = [];
    $aWhere['sacd'] = 't';
    $aWhere['situacion'] = 'A';
    $aWhere['id_tabla'] = "'n','a'";
    $aOperador['id_tabla'] = 'IN';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    $cPersonas = $PersonaSacdRepository->getPersonas($aWhere, $aOperador);
    foreach ($cPersonas as $oPersona) {
        $id_nom = $oPersona->getId_nom();
        $InicialesSacd = new InicialesSacd();
        $sacd=$InicialesSacd->nombre_sacd($id_nom);
        $iniciales=$InicialesSacd->iniciales($id_nom);
        $key = $iniciales . '#' . $id_nom;
        $a_sacd[$key] = $sacd ?? '?';
    }
}

ksort($a_sacd);

$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones($a_sacd);
//$oDesplSacd->setBlanco(TRUE);
$oDesplSacd->setBlanco(FALSE);
$oDesplSacd->setAction('fnjs_ver_ficha()');

$url_get = 'apps/encargossacd/controller/sacd_ausencias_get.php';
$oHashGet = new Hash();
$oHashGet->setUrl($url_get);
$oHashGet->setCamposForm('filtro_sacd!id_nom!historial');
$h_get = $oHashGet->linkSinVal();

$url_ajax = 'apps/encargossacd/controller/sacd_ficha_ajax.php';
$oHashFicha = new Hash();
$oHashFicha->setUrl($url_ajax);
$oHashFicha->setCamposForm('que!id_nom');
$h_ficha = $oHashFicha->linkSinVal();

$oHashLst = new Hash();
$oHashLst->setUrl($url_ajax);
$oHashLst->setCamposForm('que!id_nom!filtro_sacd');
$h_lista = $oHashLst->linkSinVal();

$url_horario = 'apps/encargossacd/controller/horario_sacd_ver.php';
$oHashHorario = new Hash();
$oHashHorario->setUrl($url_horario);
$oHashHorario->setCamposForm('filtro_sacd!id_enc!id_nom');
$h_horario = $oHashHorario->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    //'oHash' => $oHash,
    'oDesplSacd' => $oDesplSacd,
    'url_get' => $url_get,
    'h_get' => $h_get,
    'url_ajax' => $url_ajax,
    'h_ficha' => $h_ficha,
    'h_lista' => $h_lista,
    'url_horario' => $url_horario,
    'h_horario' => $h_horario,
];

$oView = new ViewTwig('encargossacd/controller');
$oView->renderizar('sacd_ausencias_jefe_zona.html.twig', $a_campos);
