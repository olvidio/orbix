<?php

use cambios\model\entity\CambioUsuario;
use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use src\usuarios\application\repositories\LocalRepository;
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

$url_lista_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/usuarios/infrastructure/controllers/usuario_preferencias.php'
);

$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

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
$aOpciones = ['exterior' => ucfirst(_("home")),
    'oficina' => ucfirst(_("oficina")),
    //'personal' => ucfirst(_("personal")),
    //'aniversarios' => ucfirst(_("aniversarios")),
];
if (ConfigGlobal::is_app_installed('cambios')) {
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
$LocalRepository = new LocalRepository();
$aOpciones = $LocalRepository->getArrayLocales();
$oDesplLocales = new Desplegable('idioma_nou', $aOpciones, $idioma, true);

// ----------- Zona Horaria -------------------
$opciones = DateTimeZone::listIdentifiers();
$id_zona_sel = array_search($zona_horaria, $opciones, true);
$oDesplZonaGMT = new Desplegable();
$oDesplZonaGMT->setNombre('zona_horaria_nou');
$oDesplZonaGMT->setOpciones($opciones);
$oDesplZonaGMT->setOpcion_sel($id_zona_sel);


$id_usuario = ConfigGlobal::mi_id_usuario();
$url_avisos = Hash::cmdSinParametros(ConfigGlobal::getWeb() . '/frontend/cambios/controller/usuario_form_avisos.php?' . http_build_query(array('quien' => 'usuario', 'id_usuario' => $id_usuario)));
$url_avisos_lista = Hash::cmdSinParametros(ConfigGlobal::getWeb() . '/apps/cambios/controller/avisos_generar.php?' . http_build_query(array('id_usuario' => $id_usuario, 'aviso_tipo' => CambioUsuario::TIPO_LISTA)));
$url_avisos_mails = Hash::cmdSinParametros(ConfigGlobal::getWeb() . '/apps/cambios/controller/avisos_generar.php?' . http_build_query(array('id_usuario' => $id_usuario, 'aviso_tipo' => CambioUsuario::TIPO_MAIL)));
$url_cambio_password = Hash::cmdSinParametros(ConfigGlobal::getWeb() . '/frontend/usuarios/controller/usuario_form_pwd.php');
$url_cambio_mail = Hash::cmdSinParametros(ConfigGlobal::getWeb() . '/frontend/usuarios/controller/usuario_form_mail.php');
$url_2fa_settings = Hash::cmdSinParametros(ConfigGlobal::getWeb() . '/frontend/usuarios/controller/usuario_form_2fa.php');

$oHash = new Hash();
$oHash->setCamposForm('layout!inicio!oficina!estilo_color!tipo_menu!tipo_tabla!ordenApellidos!idioma_nou!zona_horaria_nou');

$a_campos = [
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

$oView = new ViewSrcPhtml('frontend\usuarios\controller');
$oView->renderizar('preferencias.phtml', $a_campos);
