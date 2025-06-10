<?php
// Para salir de la sesión.
if (isset($_REQUEST['logout']) && $_REQUEST['logout'] === 'si') {
    session_start();
    // Destruir todas las variables de sesión.
    $_SESSION = [];
    //$GLOBALS = [];
    // Si se desea destruir la sesión completamente, borre también la cookie de sesión.
    // Nota: ¡Esto destruirá la sesión, y no la información de la sesión!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        $arr_cookie_options = array(
            'Expires' => time() - 42000,
            'Path' => $params["path"],
            'Domain' => $params["domain"],
            'Secure' => $params["secure"],
            'HttpOnly' => true,
            'SameSite' => 'None' // None || Lax  || Strict
        );
        setcookie(session_name(), '', $arr_cookie_options);
    }
    // Finalmente, destruir la sesión.
    session_regenerate_id();
    session_destroy();
    header("Location: index.php");
    die();
}

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

use core\ConfigGlobal;
use src\menus\application\repositories\GrupMenuRepository;
use src\menus\application\repositories\GrupMenuRoleRepository;
use src\menus\application\repositories\MenuDbRepository;
use src\menus\application\repositories\MetaMenuRepository;
use src\menus\domain\PermisoMenu;
use src\usuarios\application\repositories\PreferenciaRepository;
use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use web\Hash;
use src\layouts\LayoutFactory;

$PreferenciaRepository = new PreferenciaRepository();
$UsuarioRepository = new UsuarioRepository();
$RoleRepository = new RoleRepository();

$id_usuario = ConfigGlobal::mi_id_usuario();
$oUsuario = $UsuarioRepository->findById($id_usuario);
$id_role = $oUsuario->getId_role();

$oPermisoMenu = new PermisoMenu();

// ----------- Preferencias -------------------
//Busco la página inicial en las preferencias:
// ----------- Página de inicio -------------------
$GrupMenuRoleRepository = new GrupMenuRoleRepository();
$pag_ini = '';
$oPreferencia = $PreferenciaRepository->findById($id_usuario, 'inicio');
if ($oPreferencia !== null) {
    $preferencia = $oPreferencia->getPreferencia();
    [$inicio, $mi_id_grupmenu] = explode('#', $preferencia);
} else {
    $inicio = '';
    $cGMR = $GrupMenuRoleRepository->getGrupMenuRoles(array('id_role' => $id_role));
    if (empty($cGMR)) {
        $oRole = $RoleRepository->findById($id_role);
        $nom_role = $oRole->getRole();
        $msg = sprintf(_("El role: '%s' no tiene ningún grupmenu asignado"), $nom_role);
        die ($msg);
    }
    $mi_id_grupmenu = $cGMR[0]->getId_grupmenu();
}

if (isset($primera)) {
    // Verificar si el usuario tiene 2FA habilitado
    // Si no lo tiene, redirigir a la página de configuración de 2FA
    $oUsuario = $UsuarioRepository->findById($id_usuario);
    $has_2fa = $oUsuario->has2fa();

    //if (!$has_2fa && ServerConf::$dmz) { // la función "Configglobal::is_dmz()" se desactiva para la sf
    if (!$has_2fa) { // la función "Configglobal::is_dmz()" se desactiva para la sf
        // Redirigir a la página de verificación de 2FA para usuarios nuevos
        $url_check_2fa = ConfigGlobal::getWeb() . '/src/usuarios/infrastructure/controllers/check_first_login_2fa.php';
        header("Location: $url_check_2fa");
        exit();
    }

    // Obtener la oficina del menú de la sesión
    $mi_oficina_menu = isset($_SESSION['session_auth']['mi_oficina_menu']) ? $_SESSION['session_auth']['mi_oficina_menu'] : '';

    if ($mi_id_grupmenu === "admin") {
        $mi_id_grupmenu = "sistema";
    }
    switch ($inicio) {
        case "oficina":
            $id_grupmenu = $mi_id_grupmenu;
            break;
        case "personal":
            $id_grupmenu = $mi_id_grupmenu;
            $pag_ini = ConfigGlobal::$directorio . '/inici/personal.php';
            break;
        case "avisos":
            $id_grupmenu = $mi_id_grupmenu;
            $pag_ini = ConfigGlobal::$directorio . '/apps/cambios/controller/avisos_generar.php';
            break;
        case "aniversarios":
            $id_grupmenu = $mi_id_grupmenu;
            //$pag_ini=ConfigGlobal::$directorio."/public/aniversarios.php";
            $pag_ini = '';
            break;
        case "exterior":
            $oficina = $mi_oficina_menu;
            //$pag_ini=ConfigGlobal::$directorio.'/public/exterior_home.php';
            $pag_ini = '';
            break;
        default:
            $id_grupmenu = $mi_id_grupmenu;
            $pag_ini = '';
    }
} elseif (isset($_GET['id_grupmenu']) && $_GET['id_grupmenu'] === "public_home") {
    $pag_ini = ConfigGlobal::$directorio . '/public/public_home.php';
} elseif (isset($_GET['id_grupmenu']) && $_GET['id_grupmenu'] === "armari_doc") {
    $pag_ini = ConfigGlobal::$dir_web . '/oficinas/scdl/File/todos/DOCUMENTS.htm';
}
if (ConfigGlobal::mi_usuario() === 'auxiliar') {
    $pag_ini = '';
}

$Qid_grupmenu = (integer)filter_input(INPUT_GET, 'id_grupmenu');
$id_grupmenu = (integer)(empty($Qid_grupmenu) ? $mi_id_grupmenu : $Qid_grupmenu);

$oPreferencia = $PreferenciaRepository->findById($id_usuario, 'estilo');
if ($oPreferencia !== null) {
    $preferencia = $oPreferencia->getPreferencia();
    [$estilo_color, $tipo_menu] = explode('#', $preferencia);
} else {
    // valores por defecto
    $estilo_color = 'azul';
    $tipo_menu = 'horizontal';
}

// Layout variable to control menu display
$oPreferencia = $PreferenciaRepository->findById($id_usuario, 'layout');
if ($oPreferencia !== null) {
    $layout = $oPreferencia->getPreferencia();
} else {
    // valores por defecto
    $layout = 'legacy';
}

// Create the layout instance
$oLayout = LayoutFactory::create($layout);

$aWhere = array('id_role' => $oUsuario->getId_role());
$cGrupMenuRoles = $GrupMenuRoleRepository->getGrupMenuRoles($aWhere);

$gm = 0;
$grupMenuData = [];
$listaGrupMenu = [];
$GrupMenusRepository = new GrupMenuRepository();
$MenusDbRepository = new MenuDbRepository();
foreach ($cGrupMenuRoles as $oGrupMenuRole) {
    $gm++;
    $id_gm = $oGrupMenuRole->getId_grupmenu();
    // comprobar que tiene algún submenú.
    $cMenuDbs = $MenusDbRepository->getMenuDbs(array('id_grupmenu' => $id_gm));
    if (is_array($cMenuDbs) && count($cMenuDbs) < 1) {
        continue;
    }
    $oGrupMenu = $GrupMenusRepository->findById($id_gm);
    $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());
    $iorden = $oGrupMenu->getOrden();
    if ($iorden < 1) continue;
    $clase = ($id_gm === $id_grupmenu) ? "class='selec'" : '';

    // Add group menu item to the data array
    $grupMenuData[$iorden] = [
        'id_gm' => $id_gm,
        'grup_menu' => $grup_menu,
        'clase' => $clase
    ];
    $listaGrupMenu[$id_gm] = $grup_menu;
}
/*
// Añadir el grupMenu de Utilidades:
$grup_menu = _("Utilidades");
$grupMenuData[1] = [
    'id_gm' => 1,
    'grup_menu' => $grup_menu,
    'clase' => ''
];
$listaGrupMenu[1] = $grup_menu;
*/

if ($gm === 1) {
    //asegurarme que el id_grupmenu seleccionado (pref) es el que se ve.
    $id_grupmenu = $id_gm;
}

// Prepare parameters for the layout
$layoutParams = [
    'id_grupmenu' => $id_grupmenu,
    'oPermisoMenu' => $oPermisoMenu,
    'oUsuario' => $oUsuario,
    'gm' => $gm,
    'grupMenuData' => $grupMenuData,
    'listaGrupMenu' => $listaGrupMenu,
    'tipo_menu' => $tipo_menu
];

// Generate HTML components using the layout
$htmlComponents = $oLayout->generateMenuHtml($layoutParams);

$oHash = new Hash();
$oHash->setUrl(ConfigGlobal::getWeb() . '/src/usuarios/infrastructure/controllers/preferencias_guardar.php');
$oHash->setCamposForm('que!tabla!sPrefs');
$h = $oHash->linkSinVal();

////////////// antes de enviar headers
ob_start();
if ($_SESSION['session_auth']['expire'] == 1) {
    include("frontend/usuarios/controller/usuario_form_pwd.php");
} else if (!empty($pag_ini)) {
    include($pag_ini);
} else {
    include("public/portada.php");
}
$portada_html = ob_get_clean();

// ------------- Html -------------------
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Orbix</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico"/>
    <?php
    include_once(ConfigGlobal::$dir_estilos . '/colores.php');
    include_once(ConfigGlobal::$dir_estilos . '/todo_en_uno.css.php');
    include_once(ConfigGlobal::$dir_estilos . '/slickgrid_orbix.css.php');

    // Include CSS using the layout
    $layoutParams = [
        'tipo_menu' => $tipo_menu
    ];
    echo $oLayout->includeCss($layoutParams);
    ?>
    <style>
        img.calendar:hover {
            cursor: pointer;
        }
    </style>

    <!-- jQuery CSS -->
    <link type="text/css" rel='stylesheet'
          href='<?= ConfigGlobal::getWeb_NodeScripts() . '/jquery-ui/themes/base/all.css' ?>'/>
    <!-- ClockPicker Stylesheet -->
    <link rel="stylesheet" type="text/css"
          href='<?= ConfigGlobal::getWeb_NodeScripts() . '/clockpicker/dist/jquery-clockpicker.css' ?>'/>
    <!-- SlickGrid Stylesheet CSS -->
    <!-- OJO: IMPORTA el orden. Pueden salir avisos (en consola del navegador) tipo: Grid.xx not defined  -->
    <link type='text/css' rel='stylesheet'
          href='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/styles/css/slick.grid.css' ?>'/>
    <link type='text/css' rel='stylesheet'
          href='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/styles/css/slick.columnpicker.css' ?>'/>
    <link type='text/css' rel='stylesheet'
          href='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/styles/css/slick.gridmenu.css' ?>'/>
    <link type='text/css' rel='stylesheet'
          href='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/styles/css/slick.pager.css' ?>'/>

    <!-- jQuery -->
    <script type="text/javascript"
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/jquery/dist/jquery.min.js' ?>'></script>
    <script type="text/javascript"
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/jquery-ui/dist/jquery-ui.min.js' ?>'></script>
    <script type="text/javascript"
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/jquery-ui/ui/widgets/datepicker.js' ?>'></script>
    <script type="text/javascript"
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/jquery-ui/ui/i18n/datepicker-es.js' ?>'></script>
    <script type="text/javascript"
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/jquery-ui/ui/i18n/datepicker-ca.js' ?>'></script>

    <!-- clockPiker -->
    <script type="text/javascript"
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/clockpicker/dist/jquery-clockpicker.js' ?>'></script>

    <!-- Slick -->
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/sortablejs/Sortable.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/slick.core.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/slick.interactions.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/slick.grid.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/slick.dataview.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/slick.editors.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/plugins/slick.autotooltips.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/plugins/slick.resizer.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/plugins/slick.rowselectionmodel.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/plugins/slick.cellselectionmodel.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/controls/slick.gridmenu.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/controls/slick.pager.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/controls/slick.columnpicker.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/controls/slick.columnmenu.js' ?>'></script>
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/slickgrid/dist/browser/slick.groupitemmetadataprovider.js' ?>'></script>

    <!--  para procesos -->
    <script type='text/javascript'
            src='<?= ConfigGlobal::getWeb_NodeScripts() . '/svg.js/dist/svg.min.js' ?>'></script>

    <script type="text/javascript" src="<?= ConfigGlobal::getWeb_scripts() . '/formatos.js.php?' . rand() ?>"></script>
    <script type="text/javascript" src="<?= ConfigGlobal::getWeb_scripts() . '/selects.js.php?' . rand() ?>"></script>

    <?php
    include_once(ConfigGlobal::$dir_scripts . '/exportar.js.php');
    // Include the JavaScript functions (antes en este fichero)
    include_once(ConfigGlobal::$dir_scripts . '/index.js.php');

    // Include JavaScript using the layout
    $jsParams = ['id_grupmenu' => $id_grupmenu];
    echo $oLayout->includeJs($jsParams);
    ?>
</head>

<body class="otro">
<?php
// Render the final HTML structure
$renderParams = [
    'gm' => $gm
];
echo $oLayout->renderHtml($htmlComponents, $renderParams);
    ?>
    <div id="contenido_sin_menus">
        <div id="cargando">
            <img class="mb-4" src="<?= ConfigGlobal::getWeb_icons() ?>/loading.gif" alt="cargando" width="32" height="32">
            <?= _("Cargando...") ?>
        </div>
        <div id="iframe_export" style="display: none;">
            <form id="frm_export" method="POST" action="libs/export/export.php">
                <input type="hidden" id="frm_export_orientation" name="frm_export_orientation"/>
                <input type="hidden" id="frm_export_ref" name="frm_export_ref"/>
                <input type="hidden" id="frm_export_titulo" name="frm_export_titulo"/>
                <input type="hidden" id="frm_export_modo" name="frm_export_modo"/>
                <input type="hidden" id="frm_export_tipo" name="frm_export_tipo"/>
                <input type="hidden" id="frm_export_ex" name="frm_export_ex"/>
            </form>
        </div>
        <div id="left_slide" class="left-slide">
            <span class=handle onClick="fnjs_ir_a('#ir_atras');"></span>
        </div>
        <div id="main" refe="<?= $pag_ini ?>" >
            <?php echo $portada_html ?>
            <script>
                $(function () {
                    fnjs_cambiar_base_link();
                    fnjs_left_side_hide(); // hide it initially
                });
                /* Hay que ponerlo aquí, para asegurar que haya terminado de cargar todos los scripts. */
                $(document).ready(function () {
                    $.datepicker.setDefaults($.datepicker.regional["es"]); // Para que quede por defecto.
                    $.datepicker.setDefaults($.datepicker.regional["<?= ConfigGlobal::mi_Idioma_short() ?>"]);
                });
            </script>
        </div>
    </div>
</body>
</html>
