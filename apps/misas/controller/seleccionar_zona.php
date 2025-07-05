<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ViewTwig;
use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use web\Desplegable;
use web\Hash;
use zonassacd\model\entity\GestorZona;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$id_nom_jefe = '';

$UsuarioRepository = new UsuarioRepository();
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_role = $oMiUsuario->getId_role();

$RoleRepository = new RoleRepository();
$aRoles = $RoleRepository->getArrayRoles();

if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {

    if ($_SESSION['oConfig']->is_jefeCalendario()) {
        $id_nom_jefe = '';
    } else {
        $id_nom_jefe = $oMiUsuario->getId_pau();
        if (empty($id_nom_jefe)) {
            exit(_("No tiene permiso para ver esta página"));
        }
    }
}

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas($id_nom_jefe);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_plantilla_zona()');

$a_TiposPlantilla= array('s'=>'semanal', 'd'=>'semanal y domingos', 'm'=>'mensual');
$oDesplTipoPlantilla = new Desplegable();
$oDesplTipoPlantilla->setOpciones($a_TiposPlantilla);
$oDesplTipoPlantilla->setNombre('TipoPlantilla');
$oDesplTipoPlantilla->setAction('fnjs_ver_plantilla_zona()');

$url_ver_plantilla_zona = 'apps/misas/controller/crear_plantilla.php';
//$url_ver_plantilla_zona = 'apps/misas/controller/ver_plantilla_zona.php';
$oHashZona = new Hash();
$oHashZona->setUrl($url_ver_plantilla_zona);
$oHashZona->setCamposForm('id_zona!TipoPlantilla');
$h_zona = $oHashZona->linkSinVal();

$oHashTipoPlantilla = new Hash();
$oHashTipoPlantilla->setUrl($url_ver_plantilla_zona);
$oHashTipoPlantilla->setCamposForm('id_zona!TipoPlantilla');
$h_TipoPlantilla = $oHashTipoPlantilla->linkSinVal();


$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $h_TipoPlantilla,
    'oDesplTipoPlantilla' => $oDesplTipoPlantilla,
    'url_ver_plantilla_zona' => $url_ver_plantilla_zona,
    'h_zona' => $h_zona,
];

$oView = new ViewTwig('misas/controller');
echo $oView->render('seleccionar_zona_tipo.html.twig', $a_campos);
//echo $oView->render('seleccionar_zona.html.twig', $a_campos);