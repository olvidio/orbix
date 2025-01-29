<?php
// Para salir de la sesión.
if (isset($_REQUEST['logout']) && $_REQUEST['logout'] === 'si') {
    session_start();
    // Destruir todas las variables de sesión.
    $_SESSION = array();
    //$GLOBALS = array();
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

//$oUsuario = new Usuario(array('id_usuario'=>113));

use core\ConfigGlobal;
use menus\model\entity\GestorGrupMenuRole;
use menus\model\entity\GestorMenuDb;
use menus\model\entity\GrupMenu;
use menus\model\entity\MetaMenu;
use menus\model\PermisoMenu;
use web\Hash;
use usuarios\model\entity\GestorPreferencia;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;

$oGesPref = new GestorPreferencia();

$id_usuario = ConfigGlobal::mi_id_usuario();
$oUsuario = new Usuario(array('id_usuario' => $id_usuario));
$id_role = $oUsuario->getId_role();

$oPermisoMenu = new PermisoMenu();

// ----------- Preferencias -------------------
//Busco la página inicial en las preferencias:
// ----------- Página de inicio -------------------
$pag_ini = '';
$aPref = $oGesPref->getPreferencias(array('id_usuario' => $id_usuario, 'tipo' => 'inicio'));
if (is_array($aPref) && count($aPref) > 0) {
    $oPreferencia = $aPref[0];
    $preferencia = $oPreferencia->getPreferencia();
    [$inicio, $mi_id_grupmenu] = explode('#', $preferencia);
} else {
    $inicio = '';
    $GesGMR = new GestorGrupMenuRole();
    $cGMR = $GesGMR->getGrupMenuRoles(array('id_role' => $id_role));
    if (empty($cGMR)) {
        $oRole = new Role($id_role);
        $nom_role = $oRole->getRole();
        $msg = sprintf(_("El role: '%s' no tiene ningún grupmenu asignado"), $nom_role);
        die ($msg);
    }
    $mi_id_grupmenu = $cGMR[0]->getId_grupmenu();
}

if (isset($primera)) {
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

$aPref = $oGesPref->getPreferencias(array('id_usuario' => $id_usuario, 'tipo' => 'estilo'));
if (is_array(($aPref)) && count($aPref) > 0) {
    $oPreferencia = $aPref[0];
    $preferencia = $oPreferencia->getPreferencia();
    [$estilo_color, $tipo_menu] = explode('#', $preferencia);
} else {
    // valores por defecto
    $estilo_color = 'azul';
    $tipo_menu = 'horizontal';
}

$aWhere = array('id_role' => $oUsuario->getId_role());
$gesGMR = new GestorGrupMenuRole();
$cGrupMenuRoles = $gesGMR->getGrupMenuRoles($aWhere);
$html_barra = "<ul id=\"menu\" class=\"menu\">";
$gm = 0;
$html_gm = array();
foreach ($cGrupMenuRoles as $oGrupMenuRole) {
    $gm++;
    $id_gm = $oGrupMenuRole->getId_grupmenu();
    // comprobar que tiene algún submenú.
    $gesMenuDb = new GestorMenuDb();
    $cMenuDbs = $gesMenuDb->getMenuDbs(array('id_grupmenu' => $id_gm));
    if (is_array($cMenuDbs) && count($cMenuDbs) < 1) {
        continue;
    }
    $oGrupMenu = new GrupMenu($id_gm);
    $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());
    $iorden = $oGrupMenu->getOrden();
    if ($iorden < 1) continue;
    $clase = ($id_gm === $id_grupmenu) ? "class='selec'" : '';
    $html_gm[$iorden] = "<li onclick=\"fnjs_link_menu('$id_gm');\" $clase >$grup_menu</li>";
}
// ordenar la barra de grupmenus
ksort($html_gm);
$html_barra .= implode($html_gm);
$html_exit = "<li onclick=\"fnjs_logout();\" >| " . ucfirst(_("salir")) . "</li>";
$html_exit .= "<li> (login as: " . $oUsuario->getUsuario() . '[' . ConfigGlobal::mi_region_dl() . "])</li>";

$html_barra .= $html_exit;
$html_barra .= "</ul>";
if ($gm === 1) {
    //asegurarme que el id_grupmenu seleccionado (pref) es el que se ve.
    $id_grupmenu = $id_gm;
}

// El grupmenu 'Utilidades' es el 1, lo pongo siempre.
$aWhere = array();
$aOperador = array();
$aWhere['id_grupmenu'] = "^1$|^$id_grupmenu$";
$aOperador['id_grupmenu'] = "~";
$aWhere['_ordre'] = 'orden';
$oLista = new GestorMenuDb();
$oMenuDbs = $oLista->getMenuDbs($aWhere, $aOperador);
$li_submenus = "";
$indice = 1;
$indice_old = 1;
$num_menu_1 = "";
$m = 0;
$raiz_pral = '';
foreach ($oMenuDbs as $oMenuDb) {
    $m++;
    $orden = $oMenuDb->getOrden();
    $menu = $oMenuDb->getMenu();
    $parametros = $oMenuDb->getParametros();
    $id_metamenu = $oMenuDb->getId_metamenu();
    $menu_perm = $oMenuDb->getMenu_perm();
    $id_grupmenu = $oMenuDb->getId_grupmenu();
    //$ok = $oMenuDb->getOk ();

    $oMetamenu = new MetaMenu($id_metamenu);
    $url = $oMetamenu->getUrl();
    //echo "m: $perm_menu,l: $perm_login, ".visible($perm_menu,$perm_login) ;
    // primero si el módulo està instalado:
    $id_mod = $oMetamenu->getId_Mod();
    if (!empty($id_mod) && !ConfigGlobal::is_mod_installed($id_mod)) {
        continue;
    }
    // primero si la app de la ruta está instalada:
    if (!empty($url)) {
        $matches = [];
        $rta = preg_match('@apps/(.+?)/@', $url, $matches);
        if ($rta === false) {
            echo _("error no hay menu");
        } else {
            if ($rta == 1) {
                $url_app = $matches[1];
                if (!ConfigGlobal::is_app_installed($url_app)) continue;
            } else {
                //echo " | ". _("url invàlida en $menu");
            }
        }
    }
    // compruebo que el menu raíz exista:
    if (!empty($orden)) {
        $a_matches = [];
        $rta2 = preg_match('/\{(\d+).*\}/', $orden, $a_matches);
        if ($rta2 === FALSE) {
            echo _("error en orden menus");
        } else {
            $raiz = '{' . $a_matches[1] . '}';
            if ($a_matches[0] == $raiz) {
                $raiz_pral = $raiz;
            }
        }
        if ($raiz != $raiz_pral) {
            continue;
        }
    }

    // hago las rutas absolutas, en vez de relativas:
    $full_url = '';
    if (!empty($url)) $full_url = ConfigGlobal::getWeb() . '/' . $url;
    //$parametros = Hash::param($full_url,$parametros);
    $parametros = Hash::add_hash($parametros, $full_url);
    // quito las llaves "{}"
    $orden = substr($orden, 1, -1);
    $array_orden = preg_split('/,/', $orden);
    $indice = count($array_orden);
    if ($array_orden[0] == $num_menu_1) {
        continue;
    }
    if ($indice == 1 && !$oPermisoMenu->visible($menu_perm)) {
        $num_menu_1 = $array_orden[0];
        continue;
    } else {
        $num_menu_1 = "";
        if (!$oPermisoMenu->visible($menu_perm)) {
            continue;
        }
    }
    if ($indice == $indice_old) {
        if (!empty($full_url)) {
            if (!is_null($url) && strstr($url, 'fnjs') !== false) {
                $li_submenus .= "<li><a class=\"nohref\" onclick=\"$url;\"  >" . _($menu) . "</a>";
            } else {
                $li_submenus .= "<li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . "</a>";
            }
        } else {
            $li_submenus .= "<li><a class=\"nohref\" >" . _($menu) . "</a>";
        }
    } elseif ($indice > $indice_old) {
        if (!is_null($url) && strstr($url, 'fnjs') !== false) {
            $li_submenus .= "<ul><li><a class=\"nohref\" onclick=\"$url;\"  >" . _($menu) . "</a>";
        } else {
            $li_submenus .= "<ul><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . "</a>";
        }
    } else {
        for ($n = $indice; $n < $indice_old; $n++) {
            $li_submenus .= "</li></ul>";
        }
        if (!is_null($url) && strstr($url, 'fnjs') !== false) {
            $li_submenus .= "</li><li><a class=\"nohref\" onclick=\"$url;\"  >" . _($menu) . "</a>";
        } else {
            $li_submenus .= "</li><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . "</a>";
        }
    }
    $indice_old = $indice;
}

for ($n = 1; $n < $indice_old; $n++) {
    $li_submenus .= "</li></ul>";
}
$li_submenus .= "</li>";
if ($gm < 2) {
    $html_exit = "<li><a class=\"nohref\" onclick=\"fnjs_logout();\" >| " . ucfirst(_("salir")) . "</a></li>";
    $html_exit .= "<li><a class=\"nohref\"> (login as: " . $oUsuario->getUsuario() . '[' . ConfigGlobal::mi_region_dl() . "])</a></li>";

    $li_submenus .= $html_exit;
}
$li_submenus .= "</ul>";

$oHash = new Hash();
$oHash->setUrl(ConfigGlobal::getWeb() . '/apps/usuarios/controller/personal_update.php');
$oHash->setCamposForm('que!tabla!sPrefs');
$h = $oHash->linkSinVal();

// ------------- Html -------------------
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Orbix</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico"/>
    <!-- ULTIMATE DROP DOWN MENU Version 4.5 by Brothercake -->
    <!-- http://www.udm4.com/ -->
    <link rel="stylesheet" type="text/css"
          href="<?= ConfigGlobal::getWeb_scripts() ?>/udm4-php/udm-resources/udm-style.php?PHPSESSID=<?= session_id() ?>"
          media="screen, projection"/>
    <?php
    include_once(ConfigGlobal::$dir_estilos . '/todo_en_uno.css.php');
    include_once(ConfigGlobal::$dir_estilos . '/slickgrid_orbix.css.php');
    switch ($tipo_menu) {
        case "horizontal":
            include_once(ConfigGlobal::$dir_estilos . '/menu_horizontal.css.php');
            break;
        case "vertical":
            include_once(ConfigGlobal::$dir_estilos . '/menu_vertical.css.php');
            break;
    }
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
          href='<?= ConfigGlobal::getWeb_NodeScripts() . '/clockpicker/dist/jquery-clockpicker.css' ?>' />
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
    ?>
</head>
<body class="otro">
<script type="text/javascript">
    $(document).ready(function () {
        $('#cargando').hide();  // hide it initially
    });
    $(document).ajaxStart(function () {
        $('#cargando').show();
    });
    $(document).ajaxStop(function () {
        $('#cargando').hide();
    });

    function fnjs_slick_col_visible() {
        // columnas visibles
        colsVisible = {};
        ci = 0;
        v = "true";
        $(".slick-header-columns .slick-column-name").each(function (i) {
            ci++;
            // para saber el nombre
            name = $(this).text();
            // quito posibles espacios en el índice
            name_idx = name.replace(/ /g, '');
            //alert ("name: "+name+" vis: "+v);
            colsVisible[name_idx] = v;
        });
        if (ci === 0) {
            colsVisible = 'noCambia';
        }
        //alert (ci+'  cols: '+colsVisible);
        return colsVisible;
    }

    function fnjs_slick_search_panel(tabla) {
        // panel de búsqueda
        if ($("#inlineFilterPanel_" + tabla).is(":visible")) {
            panelVis = "si";
        } else {
            panelVis = "no";
        }
        //alert (panelVis);
        return panelVis;
    }

    function fnjs_slick_cols_width(tabla) {
        // anchura de las columnas
        colsWidth = {};
        $("#grid_" + tabla + " .slick-header-column").each(function (i) {
            //styl = $(this).attr("style");
            wid = $(this).css('width');
            //alert (wid);
            // quitar los 'px'
            //match = /width:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
            regExp = /(\d*)(px)*/;
            match = regExp.exec(wid);
            w = 0;
            if (match != null) {
                w = match[1];
                if (w === undefined) {
                    w = 0;
                }
            }
            //alert (w);
            // para saber el nombre
            let name = $(this).children(".slick-column-name").text();
            // quito posibles espacios en el índice
            let name_idx = name.replace(/ /g, '');
            colsWidth[name_idx] = w;
        });
        return colsWidth;
    }

    function fnjs_slick_grid_width(tabla) {
        // anchura de toda la grid
        var widthGrid = '';
        styl = $('#grid_' + tabla).attr('style');
        match = /(^|\s)width:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
        if (match != null) {
            w = match[2];
            if (w !== undefined) {
                widthGrid = w;
            }
        }
        return widthGrid;
    }

    function fnjs_slick_grid_height(tabla) {
        // altura de toda la grid
        var heightGrid = '';
        styl = $('#grid_' + tabla).attr('style');
        match = /(^|\s)height:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
        if (match != null) {
            h = match[2];
            if (h !== undefined) {
                heightGrid = h;
            }
        }
        return heightGrid;
    }

    function fnjs_def_tabla(tabla) {
        // si es la tabla por defecto, no puedo guardar las preferencias.
        if (tabla === 'uno') {
            alert("<?= _("no puedo grabar las preferencias de la tabla. No puede tener el nombre por defecto") ?>: " + tabla);
            return;
        }

        panelVis = fnjs_slick_search_panel(tabla);
        colsVisible = fnjs_slick_col_visible();
        //alert(JSON.stringify(colsVisible));
        colsWidth = fnjs_slick_cols_width(tabla);
        //alert(JSON.stringify(colsWidth));
        widthGrid = fnjs_slick_grid_width(tabla);
        heightGrid = fnjs_slick_grid_height(tabla);

        oPrefs = {
            "panelVis": panelVis,
            "colVisible": colsVisible,
            "colWidths": colsWidth,
            "widthGrid": widthGrid,
            "heightGrid": heightGrid
        };
        sPrefs = JSON.stringify(oPrefs);
        url = "<?= ConfigGlobal::getWeb() ?>/apps/usuarios/controller/personal_update.php";
        parametros = 'que=slickGrid&tabla=' + tabla + '&sPrefs=' + sPrefs + '<?= $h ?>';
        $.ajax({
            url: url,
            type: 'post',
            data: parametros,
            complete: function (rta) {
                rta_txt = rta.responseText;
                if (rta_txt != '' && rta_txt != '\n') {
                    alert(rta_txt);
                }
            }
        });
    }

    function fnjs_logout() {
        var parametros = 'logout=si&PHPSESSID=<?= session_id(); ?>';
        top.location.href = 'index.php?' + parametros;
    }

    function fnjs_windowopen(url) { //para poder hacerlo por el menu
        var parametros = '';
        window.open(url + '?' + parametros);
    }

    function fnjs_link_menu(id_grupmenu) {
        var parametros = 'id_grupmenu=' + id_grupmenu + '&PHPSESSID=<?= session_id(); ?>';

        if (id_grupmenu === 'web_externa') {
            top.location.href = 'http://www/exterior/cl/index.html';
        } else {
            top.location.href = 'index.php?' + parametros;
        }
        //cargar_portada(oficina);
    }

    function fnjs_link_submenu(url, parametros) {
        if (parametros) {
            parametros = parametros + '&PHPSESSID=<?= session_id() ?>';
        } else {
            parametros = 'PHPSESSID=<?= session_id() ?>';
        }
        if (!url) return false;
        // para el caso de editar webs
        if (url === "<?= ConfigGlobal::getWeb() ?>/programas/pag_html_editar.php") {
            window.open(url + '?' + parametros);
        } else {
            $('#main').attr('refe', url);
            $.ajax({
                url: url,
                type: 'post',
                data: parametros,
                complete: function (respuesta) {
                    fnjs_mostra_resposta(respuesta, '#main');
                },
                error: fnjs_procesarError
            });
        }
    }

    function fnjs_procesarError() {
        alert("<?= _("Error de página devuelta") ?>");
    }

    function fnjs_mostrar_atras(id_div, htmlForm) {
        fnjs_borrar_posibles_atras();
        var name_div = id_div.substring(1);

        if ($(id_div).length) {
            $(id_div).html(htmlForm);
        } else {
            html = '<div id="' + name_div + '" style="display: none;">';
            html += htmlForm;
            html += '</div>';
            $('#cargando').prepend(html);

        }
        fnjs_ir_a(id_div);
    }

    function fnjs_borrar_posibles_atras() {
        if ($('#ir_a').length) $('#ir_a').remove();
        if ($('#ir_atras').length) $('#ir_atras').remove();
        if ($('#ir_atras2').length) $('#ir_atras2').remove();
        if ($('#js_atras').length) $('#js_atras').remove();
        if ($('#go_atras').length) $('#go_atras').remove();
    }

    function fnjs_ir_a(id_div) {
        var url = $(id_div + " [name='url']").val();
        var parametros = $(id_div + " [name='parametros']").val();
        var bloque = $(id_div + " [name='id_div']").val();

        fnjs_left_side_hide();

        $(bloque).attr('refe', url);
        fnjs_borrar_posibles_atras();
        $.ajax({
            url: url,
            type: 'post',
            data: parametros,
            complete: function (resposta) {
                fnjs_mostra_resposta(resposta, bloque);
            },
            error: fnjs_procesarError
        });
        return false;
    }

    function fnjs_cambiar_link(id_div) {
        // busco si hay un id=ir_a que es para ir a otra página
        if ($('#ir_a').length) {
            fnjs_ir_a(id_div);
            return false;
        }
        if ($('#go_atras').length) {
            fnjs_ir_a(id_div);
            return false;
        }
        if ($('#ir_atras').length) {
            fnjs_left_side_show();
            return true;
        }
        if ($('#js_atras').length) {
            fnjs_ir_a(id_div);
            return true;
        }
        var base = $(id_div).attr('refe');
        if (base) {
            var selector = id_div + " a[href]";
            $(selector).each(function (i) {
                var aa = this.href;
                if ("<?= ConfigGlobal::mi_usuario() ?>" === "dani") {
                    //alert ("div: "+id_div+"\n base "+base+"\n selector "+selector+"\naa: "+aa );
                }
                // si tiene una ref a name(#):
                if (aa !== undefined && aa.indexOf("#") !== -1) {
                    part = aa.split("#");
                    this.href = "";
                    $(this).attr("onclick", "location.hash = '#" + part[1] + "'; return false;");
                } else {
                    url = fnjs_ref_absoluta(base, aa);
                    var path = aa.replace(/[\?#].*$/, ''); // borro desde el '?' o el '#'
                    var extension = path.substr(-4);
                    if (extension === ".php" || extension === "html" || extension === ".htm") { // documento web
                        this.href = "";
                        $(this).attr("onclick", "fnjs_update_div('" + id_div + "','" + url + "'); return false;");
                    } else {
                        this.href = url;
                    }
                }
            });
        }
    }

    function fnjs_cambiar_base_link() {
        // para el div oficina
        if ($('#main_oficina').length) {
            fnjs_cambiar_link('#main_oficina');
        }
        if ($('#main_todos').length) {
            fnjs_cambiar_link('#main_todos');
        }
        if ($('#main').length) {
            fnjs_cambiar_link('#main');
        }
    }

    function fnjs_update_div(bloque, ref) {
        fnjs_borrar_posibles_atras();
        var path = ref.replace(/\?.*$/, '');
        var pattern = /\?/;
        if (pattern.test(ref)) {
            parametros = ref.replace(/^[^\?]*\?/, '');
            parametros = parametros + '&PHPSESSID=<?= session_id(); ?>';
        } else {
            parametros = 'PHPSESSID=<?= session_id(); ?>';
        }
        //var web_ref=ref.gsub(/\/var\//,'http://');  // cambio el directorio físico (/var/www) por el url (http://www)
        $(bloque).attr('refe', path);
        $.ajax({
            url: path,
            type: 'post',
            data: parametros,
            complete: function (respuesta) {
                fnjs_mostra_resposta(respuesta, bloque);
            }
        });
        return false;
    }


    function fnjs_ref_absoluta(base, path) {
        var url = "";
        var inicio = "";
        var secure = <?php if (!empty($_SERVER["HTTPS"])) {
            echo 1;
        } else {
            echo 0;
        } ?> ;
        if (secure) {
            var protocol = 'https:';
        } else {
            var protocol = 'http:';
        }
        // El apache ya ha añadido por su cuenta protocolo+$web. Lo quito:
        ini = protocol + '<?= ConfigGlobal::getWeb() ?>';
        if (path.indexOf(ini) !== -1) {
            path = path.replace(ini, '');
        } else { // caso especial: http://www/exterior
            ini = protocol + '//www/exterior';
            if (path.indexOf(ini) !== -1) {
                url = path;
                return url;
            } else { // pruebo si ha subido un nivel, si ha subido más (../../../) no hay manera. El apache sube hasta nivel de servidor, no más.
                ini = protocol + '<?= ConfigGlobal::getWeb() ?>';
                if (path.indexOf(ini) !== -1) {
                    path = path.replace(ini, '');
                } else {
                    // si el path es una ref. absoluta, no hago nada
                    // si empieza por http://
                    if (path.match(/^http/)) {
                        url = path;
                        return url;
                    } else {
                        if ("<?= ConfigGlobal::mi_usuario() ?>" === "dani") {
                            alert("Este link no va ha funcionar bien, porque tiene una url relativa: ../../\n" + path);
                        }
                    }
                }
            }
        }
        // De la base. puede ser un directorio o una web:
        //   - cambio el directorio físico por su correspondiente web.
        //   - quito el documento.

        a = 0;
        if (base.match(/^<?= addcslashes(ConfigGlobal::$directorio, "/") ?>/)) {	// si es un directorio
            base = base.replace('<?= ConfigGlobal::$directorio ?>', '');
            inicio = protocol + '<?= ConfigGlobal::getWeb() ?>';
            a = 2;
        } else {
            if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_fotos, "/") ?>/)) {
                base = base.replace('<?= ConfigGlobal::$dir_fotos ?>', '');
                inicio = protocol + '<?= ConfigGlobal::$web_fotos ?>';
                a = 3;
            } else {
                if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_oficinas, "/") ?>/)) {
                    base = base.replace('<?= ConfigGlobal::$dir_oficinas ?>', '');
                    inicio = protocol + '<?= ConfigGlobal::$web_oficinas ?>';
                    a = 4;
                } else {
                    if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_web, "/") ?>/)) {
                        base = base.replace('<?= ConfigGlobal::$dir_web ?>', '');
                        inicio = protocol + '<?= ConfigGlobal::getWeb() ?>';
                        a = 5;
                    }
                }
            }
        }
        // si es una web:
        if (!inicio) {
            if (base.indexOf(protocol) != -1) {
                base = base.replace(protocol, '');
                inicio = protocol;
                a = 6;
            }
        }
        // le quito la página final (si tiene) y la barra (/)
        base = base.replace(/\/(\w+\.\w+$)|\/((\w+-)*(\w+ )*\w+\.\w+$)/, '');
        //elimino la base si ya existe en el path:
        path = path.replace(base, '');
        if ("<?= ConfigGlobal::mi_usuario() ?>" === "dani") {
        }
        // si no coincide con ninguno, dejo lo que había.
        if (!inicio) {
            url = path;
        } else {
            url = inicio + base + path;
        }
        //alert ('url: '+url);
        return url;
    }

    function fnjs_enviar_formulario(id_form, bloque) {
        fnjs_borrar_posibles_atras();
        if (!bloque) {
            bloque = '#main';
        }
        $(id_form).one("submit", function () { // catch the form's submit event
            $.ajax({ // create an AJAX call...
                data: $(this).serialize(), // get the form data
                type: 'post', // GET or POST
                url: $(this).attr('action'), // the file to call
                success: function (respuesta) {
                    fnjs_mostra_resposta(respuesta, bloque);
                }
            });
            return false; // cancel original event to prevent form submitting
        });
        $(id_form).trigger("submit");
        $(id_form).off();
    }

    function fnjs_enviar(evt, objeto) {
        var frm = objeto.id;
        if (evt.keyCode === 13 && evt.type === "keydown") {
            //alert ('hola33 '+evt.keyCode+' '+evt.type);
            // buscar el botón 'ok'
            var b = $('#' + frm + ' input.btn_ok');
            if (b[0]) {
                b[0].onclick();
            }
            evt.preventDefault(); // que no siga pasando el evento a submit.
            evt.stopPropagation();
            return false;
        }
    }

    function fnjs_mostra_resposta(respuesta, bloque) {
        switch (typeof respuesta) {
            case 'object':
                var myText = respuesta.responseText;
                break;
            case 'string':
                var myText = respuesta.trim();
                break;
        }
        $(bloque).empty();
        $(bloque).append(myText);
        fnjs_cambiar_link(bloque);
    }

    /*
      * funcion para comprobar que estan todos los campos necesarios antes de guardar.
      *@param object formulario
      *@param string tabla Nombre de la tabla de la base de datos.
      *@param string ficha 'si' o 'no' si viene de la presentación ficha.php
      *@param integer pau 0|1 si es de dossiers
      *@param string exterior 'si' o 'no' si está en la base de datos exterior o no.
      *@return strign 'ok'|'error'
      */
    fnjs_comprobar_campos = function (formulario, obj, ccpau, tabla) {
        if (tabla === undefined && obj === undefined) {
            return 'ok';
        } // sigue.
        var s = 0;
        if (tabla == undefined) tabla = 'x';
        if (obj == undefined) {
            obj = 'x';
        }
        //var parametros=$(formulario).serialize()+'&tabla='+tabla+'&ficha='+ficha+'&pau='+pau+'&exterior='+exterior+'&PHPSESSID=<?= session_id(); ?>';
        var parametros = $(formulario).serialize() + '&cc_tabla=' + tabla + '&cc_obj=' + obj + '&cc_pau=' + ccpau;

        url = 'apps/core/comprobar_campos.php';
        // pongo la opción async a 'false' para que espere, si no sigue con el código y devuelve siempre ok.
        $.ajax({
            async: false,
            url: url,
            type: 'post',
            data: parametros,
            dataType: 'html',
            success: function (rta_txt) {
                if (rta_txt.length > 3) {
                    alert("<?= _("error") ?>:\n" + rta_txt);
                    s = 1;
                } else {
                    s = 0;
                }
            }
        });
        if (s == 1) {
            return 'error';
        } else {
            return 'ok';
        }
    }

    function XMLtoString(elem) {

        var serialized;

        try {
            // XMLSerializer exists in current Mozilla browsers
            serializer = new XMLSerializer();
            serialized = serializer.serializeToString(elem);
        } catch (e) {
            // Internet Explorer has a different approach to serializing XML
            serialized = elem.xml;
        }

        return serialized;
    }

    function DOMtoString(doc) {
        // Vamos a convertir el árbol DOM en un String
        // Definimos el formato de salida: encoding, indentación, separador de línea,...
        // Pasamos doc como argumento para tener un formato de partida
        //OutputFormat
        // Definimos donde vamos a escribir. Puede ser cualquier OutputStream o un Writer
        //CharArrayWriter
        // Serializamos el arbol DOM
        //XMLSerializer
        serializer = new XMLSerializer();
        serializer.asDOMSerializer();
        serializer.serialize(doc);
        // Ya tenemos el XML serializado en el objeto salidaXML
        System.out.println(serializer.toString());
    }

    /* Estas variables han de ser globales, y las utiliza el dhtmlxScheduler (dibujar calendarios). */
    var _isFF = false;
    var _isIE = false;
    var _isOpera = false;
    var _isKHTML = false;
    var _isMacOS = false;
    var _isChrome = false;

    function fnjs_left_side_show() {
        if ($('#left_slide').length) {
            $('#left_slide').show();
        }
    }

    function fnjs_left_side_hide() {
        if ($('#left_slide').length) {
            $('#left_slide').hide();
        }
    }

    function fnjs_dani2() {
        $("#left_slide").hover(
            //on mouseover
            function () {
                $(this).animate({
                        height: '+=250' //adds 250px
                    }, 'slow' //sets animation speed to slow
                );
            }
            //on mouseout
            , function () {
                $(this).animate({
                        height: '-=250px' //substracts 250px
                    }, 'slow'
                );
            }
        );
    }

    function fnjs_restet_form() {
        $(this).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
    }

</script>
<?php
if ($gm > 1) {
    echo $html_barra;
}
?>
<!-- menu tree -->
<div id="submenu">
    <!-- PHP generated menu script [must come *before* any other modules or extensions] -->
    <script>
        <?php
        require_once(ConfigGlobal::$dir_scripts . "/udm4-php/udm-resources/udm-dom.php");
        ?>
    </script>
    <!-- keyboard navigation module -->
    <!-- <script type="text/javascript" src="/udm4-php/udm-resources/udm-mod-keyboard.js"></script> -->
    <ul id="udm" class="udm">
        <?= $li_submenus ?>
    </ul>
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
<div id="cargando"><?= _("Cargando...") ?></div>
<div id="left_slide" class="left-slide">
    <span class=handle onClick="fnjs_ir_a('#ir_atras');"></span>
</div>
<div id="main" refe="<?= $pag_ini ?>">
    <?php
    if ($_SESSION['session_auth']['expire'] == 1) {
        include("apps/usuarios/controller/usuario_form_pwd.php");
    } else if (!empty($pag_ini)) {
        include($pag_ini);
    } else {
        include("public/portada.php");
    }
    ?>
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
</body>
</html>
