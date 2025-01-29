<?php

use cambios\model\entity\CambioUsuario;
use core\ConfigGlobal;
use core\ViewPhtml;
use menus\model\entity\GestorGrupMenuRole;
use menus\model\entity\GrupMenu;
use usuarios\model\entity\GestorLocal;
use usuarios\model\entity\GestorPreferencia;
use usuarios\model\entity\Preferencia;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************
//	require_once ("classes/personas/ext_web_preferencias_gestor.class");

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$oGesPref = new GestorPreferencia();

$id_usuario = ConfigGlobal::mi_id_usuario();
$id_role = ConfigGlobal::mi_id_role();
// ----------- Página de inicio -------------------
$aPref = $oGesPref->getPreferencias(array('id_usuario' => $id_usuario, 'tipo' => 'inicio'));
if (is_array($aPref) && count($aPref) > 0) {
    $oPreferencia = $aPref[0];
    $preferencia = $oPreferencia->getPreferencia();
    list($inicio, $oficina) = preg_split('/#/', $preferencia);
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

$oDesplInicio = new Desplegable();
$oDesplInicio->setNombre('inicio');
$oDesplInicio->setOpciones($aOpciones);
$oDesplInicio->setOpcion_sel($inicio);


//oficinas posibles:
$GesGMR = new GestorGrupMenuRole();
$cGMR = $GesGMR->getGrupMenuRoles(array('id_role' => $id_role));
//$mi_oficina_menu=$cGMR[0]->getId_grupmenu();
$posibles = '';
foreach ($cGMR as $oGMR) {
    $id_grupmenu = $oGMR->getId_grupmenu();
    $oGrupMenu = new GrupMenu($id_grupmenu);
    $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());

    if ($id_grupmenu == $oficina) {
        $sel = "selected";
    } else {
        $sel = "";
    }
    $posibles .= "<option value=$id_grupmenu $sel>$grup_menu</option>";
}


// ----------- Página de estilo -------------------
$aPref = $oGesPref->getPreferencias(array('id_usuario' => $id_usuario, 'tipo' => 'estilo'));
if (is_array($aPref) && count($aPref) > 0) {
    $oPreferencia = $aPref[0];
    $preferencia = $oPreferencia->getPreferencia();
    list($estilo_color, $tipo_menu) = preg_split('/#/', $preferencia);
} else {
    $estilo_color = '';
    $tipo_menu = '';
}

// color
$estil_azul = ($estilo_color === "azul") ? "selected" : '';
$estil_naranja = ($estilo_color === "naranja") ? "selected" : '';
$estil_verde = ($estilo_color === "verde") ? "selected" : '';

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
    list($idioma) = preg_split('/#/', $preferencia);
} else {
    $idioma = '';
}
$oGesLocales = new GestorLocal();
$oDesplLocales = $oGesLocales->getListaLocales();
$oDesplLocales->setNombre('idioma_nou');
$oDesplLocales->setOpcion_sel($idioma);

$aniversarios = Hash::link(ConfigGlobal::getWeb() . '/programas/aniversarios.php');
$avisos = Hash::link(ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_form.php?' . http_build_query(array('quien' => 'usuario', 'id_usuario' => $id_usuario)));
$avisos_lista = Hash::link(ConfigGlobal::getWeb() . '/apps/cambios/controller/avisos_generar.php?' . http_build_query(array('id_usuario' => $id_usuario, 'aviso_tipo' => CambioUsuario::TIPO_LISTA)));
$avisos_mails = Hash::link(ConfigGlobal::getWeb() . '/apps/cambios/controller/avisos_generar.php?' . http_build_query(array('id_usuario' => $id_usuario, 'aviso_tipo' => CambioUsuario::TIPO_MAIL)));
$cambio_password = Hash::link(ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_form_pwd.php');
$cambio_mail = Hash::link(ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_form_mail.php');

$oHash = new Hash();
$oHash->setCamposForm('inicio!oficina!estilo_color!tipo_menu!tipo_tabla!ordenApellidos!idioma_nou');

$a_campos = [
    'aniversarios' => $aniversarios,
    'avisos' => $avisos,
    'avisos_lista' => $avisos_lista,
    'avisos_mails' => $avisos_mails,
    'oHash' => $oHash,
    'oDesplInicio' => $oDesplInicio,
    'posibles' => $posibles,
    'estil_azul' => $estil_azul,
    'estil_naranja' => $estil_naranja,
    'estil_verde' => $estil_verde,
    'tipo_menu_h' => $tipo_menu_h,
    'tipo_menu_v' => $tipo_menu_v,
    'tipo_tabla_s' => $tipo_tabla_s,
    'tipo_tabla_h' => $tipo_tabla_h,
    'tipo_apellidos_ap_nom' => $tipo_apellidos_ap_nom,
    'tipo_apellidos_nom_ap' => $tipo_apellidos_nom_ap,
    'oDesplLocales' => $oDesplLocales,
    'cambio_password' => $cambio_password,
    'cambio_mail' => $cambio_mail,
];

$oView = new ViewPhtml('usuarios/controller');
$oView->renderizar('personal.phtml', $a_campos);