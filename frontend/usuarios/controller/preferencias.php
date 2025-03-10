<?php

use cambios\model\entity\CambioUsuario;
use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\PostRequest;
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

$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/usuarios/controller/usuario_preferencias.php'
);

$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

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


// ----------- Página de inicio -------------------
$aOpciones = ['exterior' => ucfirst(_("home")),
    'oficina' => ucfirst(_("oficina")),
    'personal' => ucfirst(_("personal")),
    'aniversarios' => ucfirst(_("aniversarios")),
];
if (ConfigGlobal::is_app_installed('cambios')) {
    $aOpciones['avisos'] = ucfirst(_("avisos cambios actividades"));
}

// ----------- Inicio -------------------
$oDesplInicio = new Desplegable();
$oDesplInicio->setNombre('inicio');
$oDesplInicio->setOpciones($aOpciones);
$oDesplInicio->setOpcion_sel($inicio);

// ----------- Idioma -------------------
$oGesLocales = new GestorLocal();
$oDesplLocales = $oGesLocales->getListaLocales();
$oDesplLocales->setNombre('idioma_nou');
$oDesplLocales->setOpcion_sel($idioma);


$id_usuario = ConfigGlobal::mi_id_usuario();
$url_avisos = Hash::link(ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_form.php?' . http_build_query(array('quien' => 'usuario', 'id_usuario' => $id_usuario)));
$url_avisos_lista = Hash::link(ConfigGlobal::getWeb() . '/apps/cambios/controller/avisos_generar.php?' . http_build_query(array('id_usuario' => $id_usuario, 'aviso_tipo' => CambioUsuario::TIPO_LISTA)));
$url_avisos_mails = Hash::link(ConfigGlobal::getWeb() . '/apps/cambios/controller/avisos_generar.php?' . http_build_query(array('id_usuario' => $id_usuario, 'aviso_tipo' => CambioUsuario::TIPO_MAIL)));
$url_cambio_password = Hash::link(ConfigGlobal::getWeb() . '/frontend/usuarios/controller/usuario_form_pwd.php');
$url_cambio_mail = Hash::link(ConfigGlobal::getWeb() . '/frontend/usuarios/controller/usuario_form_mail.php');

$oHash = new Hash();
$oHash->setCamposForm('inicio!oficina!estilo_color!tipo_menu!tipo_tabla!ordenApellidos!idioma_nou');

$a_campos = [
    'url_avisos' => $url_avisos,
    'url_avisos_lista' => $url_avisos_lista,
    'url_avisos_mails' => $url_avisos_mails,
    'oHash' => $oHash,
    'oDesplInicio' => $oDesplInicio,
    'oficinas_posibles' => $oficinas_posibles,
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
    'url_cambio_password' => $url_cambio_password,
    'url_cambio_mail' => $url_cambio_mail,
];

$oView = new ViewPhtml('../frontend/usuarios/controller');
$oView->renderizar('preferencias.phtml', $a_campos);