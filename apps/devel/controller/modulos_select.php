<?php

use devel\model\entity;

/**
 * Esta página muestra una tabla con los modulos de la aplicación
 *
 *
 * @package    orbix
 * @subpackage    devel
 * @author    Daniel Serrabou
 * @since        18/9/18.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$oPosicion->recordar();

$Qmod = '';

$Qid_sel = (string)filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

/*
* Defino un array con los datos actuales, para saber volver 
*/
$aGoBack = array(
    'mod' => $Qmod,
);
$oPosicion->setParametros($aGoBack, 1);


$aWhere = [];
$aOperador = [];
// por defecto no pongo valor, que lo coja de la base de datos. Sólo sirve para los de paso.
$GesModulos = new entity\GestorModulo();
$cModulos = $GesModulos->getModulos($aWhere, $aOperador);

$a_botones[] = array('txt' => _("modificar"),
    'click' => "fnjs_modificar(\"#seleccionados\")");
$a_botones[] = array('txt' => _("eliminar"),
    'click' => "fnjs_eliminar(\"#seleccionados\")");
$a_botones[] = array('txt' => _("crear tablas"),
    'click' => "fnjs_sql(\"#seleccionados\")");

$a_cabeceras = array(ucfirst(_("nombre")),
    ucfirst(_("descripción")),
    _("módulos requeridos"),
    _("aplicaciones requeridas"),
);

$GesModulos = new entity\GestorModulo();
$cMods = $GesModulos->getModulos();
$a_mods_todos = [];
foreach ($cMods as $oMod) {
    $id_mod = $oMod->getId_mod();
    $nom_mod = $oMod->getNom();
    $a_mods_todos[$id_mod] = $nom_mod;
}

$gesApps = new entity\GestorApp();
$cApps = $gesApps->getApps();
$a_apps_todas = [];
foreach ($cApps as $oApp) {
    $id_app = $oApp->getId_app();
    $nom_app = $oApp->getNom();
    $a_apps_todas[$id_app] = $nom_app;
}

$i = 0;
$a_valores = array();
foreach ($cModulos as $oModulo) {
    $i++;
    $id_mod = $oModulo->getId_mod();
    $nom = $oModulo->getNom();
    $descripcion = $oModulo->getDescripcion();
    $mods_req = $oModulo->getMods_req();
    $apps_req = $oModulo->getApps_req();

    $lista_mods = '';
    if (!empty($mods_req)) {
        $a_mods = explode(',', trim($mods_req, '{}'));
        foreach ($a_mods as $mod) {
            if (empty($mod)) {
                continue;
            }
            $lista_mods .= empty($lista_mods) ? '' : ', ';
            $lista_mods .= $a_mods_todos[$mod];
        }
    }

    $lista_apps = '';
    if (!empty($apps_req)) {
        $a_apps = explode(',', trim($apps_req, '{}'));
        foreach ($a_apps as $app) {
            if (empty($app)) {
                continue;
            }
            $lista_apps .= empty($lista_apps) ? '' : ', ';
            $lista_apps .= $a_apps_todas[$app];
        }
    }

    $a_valores[$i]['sel'] = "$id_mod#";
    $a_valores[$i][1] = $nom;
    $a_valores[$i][2] = $descripcion;
    $a_valores[$i][3] = $lista_mods;
    $a_valores[$i][4] = $lista_apps;
}

if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oTabla = new web\Lista();
$oTabla->setId_tabla("modulos_select");
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new web\Hash();
$oHash->setcamposForm('sel!mod');
$oHash->setcamposNo('scroll_id!sel!refresh');

$txt_eliminar = _("¿Está seguro?");

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new core\View('devel/controller');
echo $oView->render('modulos_select.phtml', $a_campos);
