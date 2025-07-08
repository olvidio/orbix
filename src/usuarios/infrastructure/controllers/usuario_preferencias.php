<?php

use core\ConfigGlobal;
use src\menus\application\repositories\GrupMenuRepository;
use src\menus\application\repositories\GrupMenuRoleRepository;
use src\usuarios\application\repositories\PreferenciaRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$preferenciaRepository = new PreferenciaRepository();

$id_usuario = ConfigGlobal::mi_id_usuario();
$id_role = ConfigGlobal::mi_id_role();

// ----------- Layout -------------------
$oPreferencia = $preferenciaRepository->findById($id_usuario, 'layout');
if ($oPreferencia !== null) {
    $layout = $oPreferencia->getPreferencia()->value();
} else {
    $layout = '';
}

// ----------- Página de inicio -------------------
$oPreferencia = $preferenciaRepository->findById($id_usuario, 'inicio');
if ($oPreferencia !== null) {
    $preferencia = $oPreferencia->getPreferencia();
    [$inicio, $oficina] = explode('#', $preferencia);
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
$GrupMenuRoleRepository = new GrupMenuRoleRepository();
$cGMR = $GrupMenuRoleRepository->getGrupMenuRoles(array('id_role' => $id_role));
$GrupMenuRepository = new GrupMenuRepository();
$oficinas_posibles = [];
foreach ($cGMR as $oGMR) {
    $id_grupmenu = $oGMR->getId_grupmenu();
    $oGrupMenu = $GrupMenuRepository->findById($id_grupmenu);
    $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());

    $oficinas_posibles[$id_grupmenu] = $grup_menu;
}


// ----------- Página de estilo -------------------
$oPreferencia = $preferenciaRepository->findById($id_usuario, 'estilo');
if ($oPreferencia !== null) {
    $preferencia = $oPreferencia->getPreferencia();
    [$estilo_color, $tipo_menu] = explode('#', $preferencia);
} else {
    $estilo_color = '';
    $tipo_menu = '';
}

// color
$estilo_azul_selected = ($estilo_color === "azul") ? "selected" : '';
$estilo_naranja_selected = ($estilo_color === "naranja") ? "selected" : '';
$estilo_verde_selected = ($estilo_color === "verde") ? "selected" : '';

// disposición:
$tipo_menu_h = ($tipo_menu === "horizontal") ? "selected" : '';
$tipo_menu_v = ($tipo_menu === "vertical") ? "selected" : '';

// ----------- Tipo de tablas -------------------
$oPreferencia = $preferenciaRepository->findById($id_usuario, 'tabla_presentacion');
if ($oPreferencia !== null) {
    $tipo_tabla = $oPreferencia->getPreferencia();
} else {
    $tipo_tabla = '';
}
$tipo_tabla_s = ($tipo_tabla === "slickgrid") ? "selected" : '';
$tipo_tabla_h = ($tipo_tabla === "html") ? "selected" : '';

// ----------- Orden Apellidos en listas -------------------
$oPreferencia = $preferenciaRepository->findById($id_usuario, 'ordenApellidos');
if ($oPreferencia !== null) {
    $tipo_apellidos = $oPreferencia->getPreferencia()->value();
} else {
    $tipo_apellidos = '';
}
$tipo_apellidos_nom_ap = ($tipo_apellidos === "nom_ap") ? "selected" : '';
$tipo_apellidos_ap_nom = ($tipo_apellidos === "ap_nom") ? "selected" : '';

// ----------- Idioma -------------------
//Tengo la variable $idioma en ConfigGlobal, pero vuelvo a consultarla
$oPreferencia = $preferenciaRepository->findById($id_usuario, 'idioma');
if ($oPreferencia !== null) {
    $preferencia = $oPreferencia->getPreferencia();
    [$idioma] = explode('#', $preferencia);
} else {
    $idioma = '';
}

// ----------- Zona Horaria -------------------
$oPreferencia = $preferenciaRepository->findById($id_usuario, 'zona_horaria');
if ($oPreferencia !== null) {
    $preferencia = $oPreferencia->getPreferencia()->value();
    [$zona_horaria] = explode('#', $preferencia);
} else {
    $zona_horaria = '';
}

$data['layout'] = $layout;
$data['inicio'] = $inicio;
$data['oficina'] = $oficina;
$data['oficinas_posibles'] = $oficinas_posibles;
$data['estilo_azul_selected'] = $estilo_azul_selected;
$data['estilo_naranja_selected'] = $estilo_naranja_selected;
$data['estilo_verde_selected'] = $estilo_verde_selected;
$data['tipo_menu_h'] = $tipo_menu_h;
$data['tipo_menu_v'] = $tipo_menu_v;
$data['tipo_tabla_s'] = $tipo_tabla_s;
$data['tipo_tabla_h'] = $tipo_tabla_h;
$data['tipo_apellidos_ap_nom'] = $tipo_apellidos_ap_nom;
$data['tipo_apellidos_nom_ap'] = $tipo_apellidos_nom_ap;
$data['idioma'] = $idioma;
$data['zona_horaria'] = $zona_horaria;


ContestarJson::enviar($error_txt, $data);
