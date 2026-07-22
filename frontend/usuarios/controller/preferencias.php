<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    ListNavSupport::buildReturnParametrosFromPost(),
);


$prefData = UsuariosPayload::preferenciasFromPayload(
    UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/usuario_preferencias'))
);

$cambios_installed = AppInstalled::is('cambios');
$aOpciones = ['exterior' => ucfirst(_("home")),
    'oficina' => ucfirst(_("oficina")),
];
if ($cambios_installed) {
    $aOpciones['avisos'] = ucfirst(_("avisos cambios actividades"));
}

$oDesplLayout = new Desplegable();
$oDesplLayout->setNombre('layout');
$oDesplLayout->setOpciones([
    'legacy' => 'Legacy',
    'burger' => 'Burger',
    'pills' => 'Pills',
    'pills2' => 'Pills 2 (switcher)',
]);
$oDesplLayout->setOpcion_sel($prefData['layout']);

$oDesplInicio = new Desplegable();
$oDesplInicio->setNombre('inicio');
$oDesplInicio->setOpciones($aOpciones);
$oDesplInicio->setOpcion_sel($prefData['inicio']);

$oDesplOficinas = new Desplegable();
$oDesplOficinas->setNombre('oficina');
$oDesplOficinas->setOpciones($prefData['oficinas_posibles']);
$oDesplOficinas->setOpcion_sel($prefData['oficina']);
$oDesplOficinas->setBlanco(true);

$localesData = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/shared/locales_posibles'));
$a_locales = UsuariosPayload::localesFromPayload($localesData);
$oDesplLocales = new Desplegable('idioma_nou', $a_locales, $prefData['idioma'], true);

$opciones = DateTimeZone::listIdentifiers();
$oDesplZonaGMT = new Desplegable();
$oDesplZonaGMT->setNombre('zona_horaria_nou');
$oDesplZonaGMT->setOpciones($opciones);
$oDesplZonaGMT->setOpcion_sel(UsuariosPayload::zonaHorariaOpcionSel($prefData['zona_horaria']));

$id_usuario = UsuariosPostInput::sessionAuthInt('id_usuario');
$url_avisos = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/cambios/controller/usuario_form_avisos.php?' . http_build_query(array('quien' => 'usuario', 'id_usuario' => $id_usuario)));
$url_avisos_lista = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/cambios/controller/avisos_generar.php?' . http_build_query(array('id_usuario' => $id_usuario, 'aviso_tipo' => 1)));
$url_avisos_mails = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/cambios/controller/avisos_generar.php?' . http_build_query(array('id_usuario' => $id_usuario, 'aviso_tipo' => 2)));
$url_cambio_password = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/usuarios/controller/usuario_form_pwd.php');
$url_cambio_mail = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/usuarios/controller/usuario_form_mail.php');
$url_2fa_settings = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/usuarios/controller/usuario_form_2fa.php');

$oHash = new HashFront();
$oHash->setCamposForm('layout!inicio!oficina!estilo_color!tipo_menu!tipo_tabla!ordenApellidos!idioma_nou!zona_horaria_nou');

$a_campos = [
    'oPosicion' => $oPosicion,
    'cambios_installed' => $cambios_installed,
    'url_avisos' => $url_avisos,
    'url_avisos_lista' => $url_avisos_lista,
    'url_avisos_mails' => $url_avisos_mails,
    'oHash' => $oHash,
    'oDesplLayout' => $oDesplLayout,
    'oDesplInicio' => $oDesplInicio,
    'oDesplOficinas' => $oDesplOficinas,
    'estilo_azul_selected' => $prefData['estilo_azul_selected'],
    'estilo_naranja_selected' => $prefData['estilo_naranja_selected'],
    'estilo_verde_selected' => $prefData['estilo_verde_selected'],
    'tipo_menu_h' => $prefData['tipo_menu_h'],
    'tipo_menu_v' => $prefData['tipo_menu_v'],
    'tipo_tabla_s' => $prefData['tipo_tabla_s'],
    'tipo_tabla_h' => $prefData['tipo_tabla_h'],
    'tipo_apellidos_ap_nom' => $prefData['tipo_apellidos_ap_nom'],
    'tipo_apellidos_nom_ap' => $prefData['tipo_apellidos_nom_ap'],
    'oDesplLocales' => $oDesplLocales,
    'oDesplZonaGMT' => $oDesplZonaGMT,
    'url_cambio_password' => $url_cambio_password,
    'url_cambio_mail' => $url_cambio_mail,
    'url_2fa_settings' => $url_2fa_settings,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('preferencias.phtml', $a_campos);
