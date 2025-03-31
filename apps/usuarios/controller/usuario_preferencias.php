<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use menus\model\entity\GestorGrupMenuRole;
use menus\model\entity\GrupMenu;
use usuarios\model\entity\GestorPreferencia;
use usuarios\model\entity\Preferencia;
use web\ContestarJson;
use web\Desplegable;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$oGesPref = new GestorPreferencia();

$id_usuario = ConfigGlobal::mi_id_usuario();
$id_role = ConfigGlobal::mi_id_role();
// ----------- Página de inicio -------------------
$aPref = $oGesPref->getPreferencias(array('id_usuario' => $id_usuario, 'tipo' => 'inicio'));
if (is_array($aPref) && count($aPref) > 0) {
    $oPreferencia = $aPref[0];
    $preferencia = $oPreferencia->getPreferencia();
    list($inicio, $oficina) = explode('#', $preferencia);
} else {
    $inicio = '';
    $oficina = '';
}

$aOpciones = ['exterior' => ucfirst(_("home")),
    'oficina' => ucfirst(_("oficina")),
    'personal' => ucfirst(_("personal")),
    'aniversarios' => ucfirst(_("aniversarios")),
];
if (ConfigGlobal::is_app_installed('cambios')) {
    $aOpciones['avisos'] = ucfirst(_("avisos cambios actividades"));
}

//oficinas posibles:
$GesGMR = new GestorGrupMenuRole();
$cGMR = $GesGMR->getGrupMenuRoles(array('id_role' => $id_role));
//$mi_oficina_menu=$cGMR[0]->getId_grupmenu();
$oficinas_posibles = '';
foreach ($cGMR as $oGMR) {
    $id_grupmenu = $oGMR->getId_grupmenu();
    $oGrupMenu = new GrupMenu($id_grupmenu);
    $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());

    if ($id_grupmenu == $oficina) {
        $sel = "selected";
    } else {
        $sel = "";
    }
    $oficinas_posibles .= "<option value=$id_grupmenu $sel>$grup_menu</option>";
}


// ----------- Página de estilo -------------------
$aPref = $oGesPref->getPreferencias(array('id_usuario' => $id_usuario, 'tipo' => 'estilo'));
if (is_array($aPref) && count($aPref) > 0) {
    $oPreferencia = $aPref[0];
    $preferencia = $oPreferencia->getPreferencia();
    list($estilo_color, $tipo_menu) = explode('#', $preferencia);
} else {
    $estilo_color = '';
    $tipo_menu = '';
}

// color
$estilo_azul_selected  = ($estilo_color === "azul") ? "selected" : '';
$estilo_naranja_selected  = ($estilo_color === "naranja") ? "selected" : '';
$estilo_verde_selected = ($estilo_color === "verde") ? "selected" : '';

// disposición:
$tipo_menu_h = ($tipo_menu === "horizontal") ? "selected" : '';
$tipo_menu_v = ($tipo_menu === "vertical") ? "selected" : '';

// ----------- Tipo de tablas -------------------
$oPref = new Preferencia(array('id_usuario' => $id_usuario, 'tipo' => 'tabla_presentacion'));
$tipo_tabla = $oPref->getPreferencia();
$tipo_tabla_s = ($tipo_tabla === "slickgrid") ? "selected" : '';
$tipo_tabla_h = ($tipo_tabla === "html") ? "selected" : '';

// ----------- Orden Apellidos en listas -------------------
$oPref = new Preferencia(array('id_usuario' => $id_usuario, 'tipo' => 'ordenApellidos'));
$tipo_apellidos = $oPref->getPreferencia();
$tipo_apellidos_nom_ap = ($tipo_apellidos === "nom_ap") ? "selected" : '';
$tipo_apellidos_ap_nom = ($tipo_apellidos === "ap_nom") ? "selected" : '';

// ----------- Idioma -------------------
//Tengo la variable $idioma en ConfigGlobal, pero vuelvo a consultarla
$aPref = $oGesPref->getPreferencias(array('id_usuario' => $id_usuario, 'tipo' => 'idioma'));
if (is_array($aPref) && count($aPref) > 0) {
    $oPreferencia = $aPref[0];
    $preferencia = $oPreferencia->getPreferencia();
    list($idioma) = explode('#', $preferencia);
} else {
    $idioma = '';
}

// ----------- Zona Horaria -------------------
$aPref = $oGesPref->getPreferencias(array('id_usuario' => $id_usuario, 'tipo' => 'zona_horaria'));
if (is_array($aPref) && count($aPref) > 0) {
    $oPreferencia = $aPref[0];
    $preferencia = $oPreferencia->getPreferencia();
    list($zona_horaria) = explode('#', $preferencia);
} else {
    $zona_horaria = '';
}

$data['inicio'] = $inicio;
$data['oficina'] = $oficina;
$data['oficinas_posibles'] = $oficinas_posibles;
$data['estilo_azul_selected'] = $estilo_azul_selected ;
$data['estilo_naranja_selected'] = $estilo_naranja_selected ;
$data['estilo_verde_selected'] = $estilo_verde_selected ;
$data['tipo_menu_h'] = $tipo_menu_h;
$data['tipo_menu_v'] = $tipo_menu_v;
$data['tipo_tabla_s'] = $tipo_tabla_s;
$data['tipo_tabla_h'] = $tipo_tabla_h;
$data['tipo_apellidos_ap_nom'] = $tipo_apellidos_ap_nom;
$data['tipo_apellidos_nom_ap'] = $tipo_apellidos_nom_ap;
$data['idioma'] = $idioma;
$data['zona_horaria'] = $zona_horaria;


ContestarJson::enviar($error_txt, $data);
