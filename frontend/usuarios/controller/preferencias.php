<?php

use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$url_backend = '/src/usuarios/usuario_preferencias';
$data = PostRequest::getDataFromUrl($url_backend);

$layout = $data['layout'];
$inicio = $data['inicio'];
$oficina = $data['oficina'];
$oficinas_posibles = $data['oficinas_posibles'];
$estilo_azul_selected = $data['estilo_azul_selected'];
$estilo_naranja_selected = $data['estilo_naranja_selected'];
$estilo_verde_selected = $data['estilo_verde_selected'];
$tipo_menu_h = $data['tipo_menu_h'];
$tipo_menu_v = $data['tipo_menu_v'];
$tipo_tabla_s = $data['tipo_tabla_s'];
$tipo_tabla_h = $data['tipo_tabla_h'];
$tipo_apellidos_ap_nom = $data['tipo_apellidos_ap_nom'];
$tipo_apellidos_nom_ap = $data['tipo_apellidos_nom_ap'];
$idioma = $data['idioma'];
$zona_horaria = empty($data['zona_horaria']) ? 'UTC' : $data['zona_horaria'];


// ----------- Página de inicio -------------------
$cambios_installed = AppInstalled::is('cambios');
$aOpciones = ['exterior' => ucfirst(_("home")),
    'oficina' => ucfirst(_("oficina")),
    //'personal' => ucfirst(_("personal")),
    //'aniversarios' => ucfirst(_("aniversarios")),
];
if ($cambios_installed) {
    $aOpciones['avisos'] = ucfirst(_("avisos cambios actividades"));
}

// ----------- LayOut -------------------
$oDesplLayout = new Desplegable();
$oDesplLayout->setNombre('layout');
$oDesplLayout->setOpciones(['legacy' => "Legacy", 'burger' => "Burger"]);
$oDesplLayout->setOpcion_sel($layout);

// ----------- Inicio -------------------
$oDesplInicio = new Desplegable();
$oDesplInicio->setNombre('inicio');
$oDesplInicio->setOpciones($aOpciones);
$oDesplInicio->setOpcion_sel($inicio);

// ----------- Oficinas -------------------
$oDesplOficinas = new Desplegable();
$oDesplOficinas->setNombre('oficina');
$oDesplOficinas->setOpciones($oficinas_posibles);
$oDesplOficinas->setOpcion_sel($oficina);
$oDesplOficinas->setBlanco(true);

// ----------- Idioma -------------------
/////////// Consulta al backend ///////////////////
$url_backend = '/src/shared/locales_posibles';
$data = PostRequest::getDataFromUrl($url_backend);

$a_locales = $data['a_locales'];
$oDesplLocales = new Desplegable('idioma_nou', $a_locales, $idioma, true);

// ----------- Zona Horaria -------------------
$opciones = DateTimeZone::listIdentifiers();
$id_zona_sel = array_search($zona_horaria, $opciones, true);
$oDesplZonaGMT = new Desplegable();
$oDesplZonaGMT->setNombre('zona_horaria_nou');
$oDesplZonaGMT->setOpciones($opciones);
$oDesplZonaGMT->setOpcion_sel($id_zona_sel);


$id_usuario = (int)($_SESSION['session_auth']['id_usuario'] ?? 0);
$url_avisos = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/cambios/controller/usuario_form_avisos.php?' . http_build_query(array('quien' => 'usuario', 'id_usuario' => $id_usuario)));
$url_avisos_lista = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/cambios/controller/avisos_generar.php?' . http_build_query(array('id_usuario' => $id_usuario, 'aviso_tipo' => 1)));
$url_avisos_mails = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/cambios/controller/avisos_generar.php?' . http_build_query(array('id_usuario' => $id_usuario, 'aviso_tipo' => 2)));
$url_cambio_password = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/usuarios/controller/usuario_form_pwd.php');
$url_cambio_mail = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/usuarios/controller/usuario_form_mail.php');
$url_2fa_settings = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/usuarios/controller/usuario_form_2fa.php');

$oHash = new HashFront();
$oHash->setCamposForm('layout!inicio!oficina!estilo_color!tipo_menu!tipo_tabla!ordenApellidos!idioma_nou!zona_horaria_nou');

$a_campos = [
    'cambios_installed' => $cambios_installed,
    'url_avisos' => $url_avisos,
    'url_avisos_lista' => $url_avisos_lista,
    'url_avisos_mails' => $url_avisos_mails,
    'oHash' => $oHash,
    'oDesplLayout' => $oDesplLayout,
    'oDesplInicio' => $oDesplInicio,
    'oDesplOficinas' => $oDesplOficinas,
    'estilo_azul_selected' => $estilo_azul_selected,
    'estilo_naranja_selected' => $estilo_naranja_selected,
    'estilo_verde_selected' => $estilo_verde_selected,
    'tipo_menu_h' => $tipo_menu_h,
    'tipo_menu_v' => $tipo_menu_v,
    'tipo_tabla_s' => $tipo_tabla_s,
    'tipo_tabla_h' => $tipo_tabla_h,
    'tipo_apellidos_ap_nom' => $tipo_apellidos_ap_nom,
    'tipo_apellidos_nom_ap' => $tipo_apellidos_nom_ap,
    'oDesplLocales' => $oDesplLocales,
    'oDesplZonaGMT' => $oDesplZonaGMT,
    'url_cambio_password' => $url_cambio_password,
    'url_cambio_mail' => $url_cambio_mail,
    'url_2fa_settings' => $url_2fa_settings,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('preferencias.phtml', $a_campos);
