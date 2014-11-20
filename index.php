<?php
namespace core;
use web;
/**
* llama a la plantilla de inicio con el nombre de la oficina
*
*@package	delegacion
*@subpackage	menus
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

// Para salir de la sesión.
if (isset($_REQUEST['logout']) && $_REQUEST['logout'] == 'si') {
	session_start();
	// Destruir todas las variables de sesión.
	$_SESSION = array();
	$GLOBALS = array();
	// Si se desea destruir la sesión completamente, borre también la cookie de sesión.
	// Nota: ¡Esto destruirá la sesión, y no la información de la sesión!
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}
	// Finalmente, destruir la sesión.
	session_regenerate_id();
	session_destroy();
    header("Location: index.php");
    die();
}

//use orbix\config\configglobal as ConfigGlobal;

/*
define('CONFIG_DIR','/var/www/orbix/config');
define('INCLUDES_DIR','/var/www/orbix/core');
$new_include_path = get_include_path().PATH_SEPARATOR.CONFIG_DIR.PATH_SEPARATOR.INCLUDES_DIR;
ini_set ('include_path', $new_include_path);
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//$oUsuario = new Usuario(array('id_usuario'=>113));
//print_r($oUsuario);
use usuarios\model as usuarios;
use menus\model as menus;

$oGesPref = new usuarios\GestorPreferencia();

$id_usuario = ConfigGlobal::mi_id_usuario();
$oUsuario = new usuarios\Usuario(array('id_usuario'=>$id_usuario));
$id_role = $oUsuario->getId_role();
$oRole = new usuarios\Role($id_role);
//$mi_oficina_menu=ConfigGlobal::mi_oficina_menu();

$oPermisoMenu = new menus\PermisoMenu();

// ----------- Preferencias -------------------
//Busco la página inicial en las preferencias:
// ----------- Página de inicio -------------------
$pag_ini = '';
$aPref = $oGesPref->getPreferencias(array('id_usuario'=>$id_usuario ,'tipo'=>'inicio'));
//$aPref = $oGesPref->getPreferencias(array('username'=>$username,'tipo'=>'inicio'));
if (is_array($aPref) && count($aPref) > 0) {
	$oPreferencia = $aPref[0];
	$preferencia = $oPreferencia->getPreferencia();
	list($inicio,$mi_id_grupmenu) = preg_split('/#/',$preferencia);
} else {
	$inicio='';
	$GesGMR = new menus\GestorGrupMenuRole();
	$cGMR = $GesGMR->getGrupMenuRoles(array('id_role'=>$id_role));
	$mi_id_grupmenu=$cGMR[0]->getId_grupmenu();
}

if (isset($primera)) {
	if ($mi_id_grupmenu=="admin") $mi_id_grupmenu="sistema";
	switch ($inicio) {
		case "oficina":
			$id_grupmenu=$mi_id_grupmenu;
			break;
		case "personal":
			$id_grupmenu=$mi_id_grupmenu;
			$pag_ini=ConfigGlobal::$directorio.'/inici/personal.php';
			break;
		case "avisos":
			$id_grupmenu=$mi_id_grupmenu;
			$pag_ini=ConfigGlobal::$directorio."/sistema/avisos_generar.php";
			break;
		case "aniversarios":
			$id_grupmenu=$mi_id_grupmenu;
			//$pag_ini=ConfigGlobal::$directorio."/programas/aniversarios.php";
			$pag_ini=ConfigGlobal::$directorio."/public/aniversarios.php";
			break;
		case "exterior":
			$oficina=$mi_oficina_menu;
			$pag_ini=ConfigGlobal::$directorio.'/public/exterior_home.php';
			break;
		default:
			$id_grupmenu=$mi_id_grupmenu;
			$pag_ini='';
	}
} elseif (isset($_GET['id_grupmenu']) && $_GET['id_grupmenu']=="public_home") {
	$pag_ini=ConfigGlobal::$directorio.'/public/public_home.php';
} elseif (isset($_GET['id_grupmenu']) && $_GET['id_grupmenu']=="armari_doc") {
	$pag_ini=ConfigGlobal::$dir_web.'/oficinas/scdl/File/todos/DOCUMENTS.htm';
}
if (ConfigGlobal::mi_usuario() == 'auxiliar') { $pag_ini=''; }

if (!isset($_GET['id_grupmenu'])) { $id_grupmenu=$mi_id_grupmenu; } else { $id_grupmenu=$_GET['id_grupmenu']; }

// crec que ja està a dalt. $username= ConfigGlobal::mi_usuario();
$aPref = $oGesPref->getPreferencias(array('id_usuario'=>$id_usuario,'tipo'=>'estilo'));
if (is_array(($aPref)) && count($aPref) > 0) {
	$oPreferencia = $aPref[0];
	$preferencia = $oPreferencia->getPreferencia();
	list($estilo_color,$tipo_menu) = preg_split('/#/',$preferencia);
} else {
	// valores por defecto
	$estilo_color='azul';
	$tipo_menu='horizontal';
}

$aWhere = array('id_role'=>$oUsuario->getId_role());
$gesGMR=new menus\GestorGrupMenuRole();
$cGrupMenuRoles=$gesGMR->getGrupMenuRoles($aWhere);
$html_barra = "<ul id=\"menu\" class=\"menu\">";
$gm = 0;
$html_gm = array();
foreach ($cGrupMenuRoles as $oGrupMenuRole) {
	$gm++;
	$id_gm = $oGrupMenuRole->getId_grupmenu();
	// comprobar que tiene algún submenú.
	$gesMenuDb = new menus\GestorMenuDb();
	$cMenuDbs=$gesMenuDb ->getMenuDbs(array('id_grupmenu'=>$id_gm));
	if (is_array($cMenuDbs) && count($cMenuDbs) < 1) continue;
	$oGrupMenu = new menus\GrupMenu($id_gm);
	$grup_menu = $oGrupMenu->getGrup_menu();
	$iorden = $oGrupMenu->getOrden();
	if ($iorden < 1) continue;
	$clase = ($id_gm == $id_grupmenu)? "class='selec'": '';
	$html_gm[$iorden] = "<li onclick=\"fnjs_link_menu('$id_gm');\" $clase >$grup_menu</li>";
}
// ordenar la barra de grupmenus
ksort($html_gm);
$html_barra .= implode($html_gm);
$html_barra .= "<li onclick=\"fnjs_logout();\" >| ".ucfirst(_('salir'))."</li>";
$html_barra .= "<li> (login as: ".$oUsuario->getUsuario().'['.configGlobal::mi_region()."])</li>";
$html_barra .= "</ul>";
if ($gm == 1) { 
	//asegurarme que el id_grupmenu seleccionado (pref) es el que se ve.
	$id_grupmenu = $id_gm;
}

// El grupmenu 'Utilidades' es el 1, lo pongo siempre.
$aWhere = array();
$aOperador = array();
$aWhere['id_grupmenu'] = "^1$|^$id_grupmenu$";
$aOperador['id_grupmenu'] = "~";
$aWhere['_ordre'] = 'orden';
$oLista=new menus\GestorMenuDb();
$oMenuDbs=$oLista->getMenuDbs($aWhere,$aOperador);
$li_submenus="";
$indice=1;
$indice_old=1;
$num_menu_1="";
	$m=0;
	foreach ($oMenuDbs as $oMenuDb) {
		$m++;
		extract($oMenuDb->getTot());
		$oMetamenu = new menus\Metamenu($id_metamenu);
		$url = $oMetamenu ->getUrl();
		//echo "m: $perm_menu,l: $perm_login, ".visible($perm_menu,$perm_login) ;
		// primero si està instalado:
		if (!empty($url)) {
			$rta=preg_match('@apps/(.+?)/@',$url, $matches);
			if ($rta === false) {
				echo _("error no hay menu");
			} else {
				if ($rta == 1) {
					$url_app = $matches[1];
					if(!ConfigGlobal::is_app_installed($url_app)) continue;
				} else {
					//echo " | ". _("url invàlida en $menu");
				}
			}
		}

		// hago las rutas absolutas, en vez de relativas:
		$full_url = '';
		if (!empty($url)) $full_url=ConfigGlobal::getWeb().'/'.$url;
		//$parametros = web\Hash::param($full_url,$parametros);
		$parametros = web\Hash::add_hash($parametros,$full_url);
		// quito las llaves "{}"
		$orden=substr($orden,1,-1);
		$array_orden=preg_split('/,/',$orden);
		$indice=count($array_orden);
		if ($array_orden[0]==$num_menu_1) { continue; }
		if ($indice==1 && !$oPermisoMenu->visible($menu_perm)) {
			$num_menu_1=$array_orden[0];
			continue;
		} else { 
			$num_menu_1="";
			if (!$oPermisoMenu->visible($menu_perm)) { continue; }
		}
		if ($indice==$indice_old) {
				if (!empty($full_url)) {
					if (strstr($url,'fnjs') !== false) {
						$li_submenus.="<li><a class=\"nohref\" onclick=\"$url;\"  >"._($menu)."</a>";
					} else {
						$li_submenus.="<li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >"._($menu)."</a>";
					}
				} else {
					$li_submenus.="<li><a class=\"nohref\" >"._($menu)."</a>";
				}
		} elseif ($indice>$indice_old) {
				if (strstr($url,'fnjs') !== false) {
					$li_submenus.="<ul><li><a class=\"nohref\" onclick=\"$url;\"  >"._($menu)."</a>";
				} else {
					$li_submenus.="<ul><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >"._($menu)."</a>";
				}
		} else {
			for ($n=$indice;$n<$indice_old;$n++) {
				$li_submenus.="</li></ul>";
			}
			if (strstr($url,'fnjs') !== false) {
				$li_submenus.="</li><li><a class=\"nohref\" onclick=\"$url;\"  >"._($menu)."</a>";
			} else {
				$li_submenus.="</li><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >"._($menu)."</a>";
			}
		}
		$indice_old=$indice;
	}
for ($n=1;$n<$indice_old;$n++) {
	$li_submenus.="</li></ul>";
}
$li_submenus.="</li>";
if ($gm < 2) {
	$li_submenus.="<li><a href=\"#\" onclick=\"fnjs_logout();\" >| <?= ucfirst(_('salir')) ?></a></li>";
}

// ------------- Html -------------------
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Pàgina inicial de la web de la dl</title>
	<!-- ULTIMATE DROP DOWN MENU Version 4.5 by Brothercake -->
	<!-- http://www.udm4.com/ -->
 	<link rel="stylesheet" type="text/css" href="<?= ConfigGlobal::$web_scripts ?>/udm4-php/udm-resources/udm-style.php?PHPSESSID=<?= session_id() ?>" media="screen, projection" /> 
<?php
include_once(ConfigGlobal::$dir_estilos.'/todo_en_uno.css.php');
echo "<style>";
switch ($tipo_menu) {
	case "horizontal":
		include_once(ConfigGlobal::$dir_estilos.'/menu_horizontal.css');
		break;
	case "vertical":
		include_once(ConfigGlobal::$dir_estilos.'/menu_vertical.css');
		break;
}
?>
img.calendar:hover { cursor: pointer; }
</style>
<!-- jQuery -->
<script type="text/javascript" src='<?php echo ConfigGlobal::$web_scripts.'/jquery-ui-latest/js/jquery-1.7.1.min.js'; ?>'></script>
<script type="text/javascript" src='<?php echo ConfigGlobal::$web_scripts.'/jquery-ui-latest/js/jquery-ui-1.8.17.custom.min.js'; ?>'></script>
<script type="text/javascript" src='<?php echo ConfigGlobal::$web_scripts.'/jquery-ui-latest/development-bundle/ui/i18n/jquery.ui.datepicker-ca.js'; ?>'></script>
<link type="text/css" rel='stylesheet' href='<?php echo ConfigGlobal::$web_scripts.'/jquery-ui-latest/css/smoothness/jquery-ui-1.8.17.custom.css'; ?>' />
<!-- history.js -->
<script type="text/javascript" src='<?php echo ConfigGlobal::$web_scripts.'/history.js/scripts/bundled/html4+html5/jquery.history.js'; ?>'></script>

<!-- Slick -->
<link type='text/css' rel='stylesheet' href='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/slick.grid.css'; ?>' />
<link type='text/css' rel='stylesheet' href='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/controls/slick.pager.css'; ?>' />
<link type='text/css' rel='stylesheet' href='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/controls/slick.columnpicker.css'; ?>' />
<!-- <link type='text/css' rel='stylesheet' href='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/examples/examples.css'; ?>' /> -->

<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/lib/firebugx.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/lib/jquery.event.drag-2.0.min.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/slick.core.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/plugins/slick.autotooltips.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/plugins/slick.rowselectionmodel.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/plugins/slick.checkboxselectcolumn.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/slick.formatters.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/slick.editors.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/slick.grid.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/slick.dataview.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/controls/slick.pager.js'; ?>'></script>
<script type='text/javascript' src='<?php echo ConfigGlobal::$web_scripts.'/SlickGrid/controls/slick.columnpicker.js'; ?>'></script>

<script type="text/javascript" src="<?php echo ConfigGlobal::$web_scripts.'/fechas.js.php?'.rand(); ?>"></script>
<script type="text/javascript" src="<?php echo ConfigGlobal::$web_scripts.'/selects.js.php?'.rand(); ?>"></script>
<script type="text/javascript" src="<?php echo ConfigGlobal::$web_scripts.'/exportar.js?'.rand(); ?>"></script>
</head>
<body class="otro">
<script type="text/javascript">
$(document).ready(function() {
	$('#cargando').hide();  // hide it initially
});
$(document).ajaxStart(function() {$('#cargando').show(); });
$(document).ajaxStop(function() {$('#cargando').hide(); });

function fnjs_slick_col_visible() {
	// columnas vivibles
	colsVisible={};
	ci = 0;
	$('.slick-columnpicker input').each(function(i){
		ci++;
		id = $(this).attr('id');
		var pattern=/columnpicker/;
		if (pattern.test(id)) {
			v = $(this).attr('checked');
			if (v==undefined) {
				v ="false";
			} else {
				v ="true";
			}
			// para saber el nombre
			name=$(this).siblings('[for="'+id+'"]').text();
			name_idx = name.replace(/ /g,''); // quito posibles espacios en el indice
			//(alert ("name: "+name+" vis: "+v);
			colsVisible[name_idx]=v;
		}
	});
	if (ci == 0) { colsVisible = 'noCambia'; }
	//alert (ci+'  cols: '+cols);
	return colsVisible;
}

function fnjs_slick_search_panel(tabla) {
	// panel de búsqueda
	if ($("#inlineFilterPanel_"+tabla).is(":visible")) {
		panelVis = "si";
	} else {
		panelVis = "no";
	}
	//alert (panelVis);
	return panelVis;
}
function fnjs_slick_cols_width(tabla) {
	// anchura de las columnas
	colsWidth={};
	$("#grid_"+tabla+" .slick-header-column").each(function(i){
		styl = $(this).attr("style");
		//alert("styl "+styl);
		match = /width:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
		w=0;
		if (match != null) {
			w = match[1];
			if (w==undefined) {
				w=0;
			}
		}
		// para saber el nombre
		name=$(this).children(".slick-column-name").text();
		name_idx = name.replace(/ /g,''); // quito posibles espacios en el indice
		colsWidth[name_idx]=w;
	});
	return colsWidth;
}
function fnjs_slick_grid_width(tabla) {
	// anchura de toda la grid
	var widthGrid;
	styl = $('#grid_'+tabla).attr('style');
	match = /(^|\s)width:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
	if (match != null) {
		w = match[2];
		if (w!=undefined) {
			widthGrid=w;
		}
	}
	return widthGrid;
}
function fnjs_def_tabla(tabla) {
	// si es la tabla por defecto, no puedo guardar las preferencias.
	if (tabla=='uno') { alert('no puedo grabar las preferencias de la tabla. No puede tener el nombre por defecto: '+tabla); return; }

	panelVis=fnjs_slick_search_panel(tabla);
	colsVisible=fnjs_slick_col_visible();
	//alert(JSON.stringify(colsVisible));
	colsWidth=fnjs_slick_cols_width(tabla);
	//alert(JSON.stringify(colsWidth));
	widthGrid=fnjs_slick_grid_width(tabla);

	oPrefs = { "panelVis": panelVis, "colVisible": colsVisible, "colWidths": colsWidth, "widthGrid": widthGrid };
	sPrefs = JSON.stringify(oPrefs);
	url="<?= ConfigGlobal::getWeb() ?>/apps/usuarios/controller/personal_update.php";
	parametros='que=slickGrid&tabla='+tabla+'&sPrefs='+sPrefs+'&PHPSESSID=<?php echo session_id(); ?>'; 
	$.ajax({
			url: url,
			type: 'post',
			data: parametros,
			complete: function (rta) {
				rta_txt=rta.responseText;
				if (rta_txt != '' && rta_txt != '\n') {
					alert (rta_txt);
				}
			}
	});
}


$.datepicker.setDefaults( $.datepicker.regional[ "ca" ] );

function fnjs_logout() {
	var parametros='logout=si&PHPSESSID=<?php echo session_id(); ?>'; 
	top.location.href='index.php?'+parametros;
}
function fnjs_windowopen(url) { //para poder hacerlo por el menu
	var parametros='';
	window.open(url+'?'+parametros);
}
function fnjs_link_menu(id_grupmenu) {
	var parametros='id_grupmenu='+id_grupmenu+'&PHPSESSID=<?php echo session_id(); ?>'; 
	
	if (id_grupmenu=='web_externa') {
		top.location.href='http://www/exterior/cl/index.html';
	} else {
		top.location.href='index.php?'+parametros;
	}
	//cargar_portada(oficina);
}
function fnjs_link_submenu(url,parametros) {
	if(parametros) {
		parametros=parametros+'&PHPSESSID=<?php echo session_id(); ?>'; 
	} else {
		parametros='PHPSESSID=<?php echo session_id(); ?>'; 
	}
	if (!url) return false;
	// para el caso de editar webs
	if(url=="<?= ConfigGlobal::getWeb() ?>/programas/pag_html_editar.php") {
		window.open(url+'?'+parametros);
	} else {
		$('#main').attr('refe',url);
		$.ajax({
				url: url,
				type: 'post',
				data: parametros,
				complete: function (resposta) { fnjs_mostra_resposta (resposta,'#main'); },
				error: fnjs_procesarError
				});
	}
}
function fnjs_procesarError() {
	alert('Error de pagina retornada');
}

function fnjs_ir_a() {
	var atras='';
	var url=$('#url').val();
	var parametros=$('#parametros').val();
	var bloque='#'+$('#id_div').val();
	
	if(parametros) {
		if ($('#ir_atras').length) { // atras=1; 
			if (parametros.indexOf("&atras") != -1) {
				parametros=parametros.replace(/&atras=(0|1)?/,'&atras=1');
			} else {
				parametros=parametros+'&atras=1'; 
			}
		} else {
			parametros=parametros.replace(/&atras=(0|1)?/,'');
		}
	} else {
		if ($('#ir_atras').length) { // atras=1; 
			parametros='&atras=1'; 
		}
	}
	if (parametros.indexOf("PHPSESSID") == -1) {
		parametros=parametros+'&PHPSESSID=<?php echo session_id(); ?>'; 
	}
	
	if ($('#left_slide').length) { $('#left_slide').hide(); } 

	$(bloque).attr('refe',url);
	$.ajax({
			url: url,
			type: 'post',
			data: parametros,
			complete: function (resposta) { fnjs_mostra_resposta (resposta,bloque); },
			error: fnjs_procesarError
			}) ;
	return false;
}

function fnjs_cambiar_link(id_div) {
	// busco si hay un id=ir_a que es para ir a otra página
	if ($('#ir_a').length) { fnjs_ir_a(); return false; } 
	if ($('#ir_atras').length) { fnjs_dani(); return true; } 
	var base=$(id_div).attr('refe');
	if (base) {
		var selector=id_div+" a[href]";
		$(selector).each(function(i) {
				var aa=this.href;
		if ("<?= ConfigGlobal::mi_usuario() ?>"=="dani") {
			//alert ("div: "+id_div+"\n base "+base+"\n selector "+selector+"\naa: "+aa );
		}
				// si tiene una ref a name(#):
				if (aa != undefined && aa.indexOf("#") != -1) {
					part=aa.split("#");
					this.href="";
					$(this).attr("onclick","location.hash = '#"+part[1]+"'; return false;");
				} else {
					url=fnjs_ref_absoluta(base,aa);
					var path=aa.replace(/[\?#].*$/,''); // borro desde el '?' o el '#'
					var extension=path.substr(-4);
					if (extension==".php" || extension=="html" || extension==".htm" ) { // documento web
						this.href="";
						$(this).attr("onclick","fnjs_update_div('"+id_div+"','"+url+"'); return false;");
					} else {
						this.href=url;
					}
				}
		});
	}
}

function fnjs_cambiar_base_link() {
	// para el div oficina
	if ( $('#main_oficina').length ) { fnjs_cambiar_link('#main_oficina'); }
	if ( $('#main_todos').length ) { fnjs_cambiar_link('#main_todos'); }
	if ( $('#main').length ) { fnjs_cambiar_link('#main'); }
}

function fnjs_update_div(bloque,ref) {
	var path=ref.replace(/\?.*$/,'');
	var pattern=/\?/;
	if (pattern.test(ref)) {
		parametros=ref.replace(/^[^\?]*\?/,'');
		parametros=parametros+'&PHPSESSID=<?php echo session_id(); ?>'; 
	} else {
		parametros='PHPSESSID=<?php echo session_id(); ?>'; 
	}
	//var web_ref=ref.gsub(/\/var\//,'http://');  // cambio el directorio físico (/var/www) por el url (http://www)
	$(bloque).attr('refe',path);
		$.ajax({
				url: path,
				type: 'post',
				data: parametros,
				complete: function (resposta) { fnjs_mostra_resposta (resposta,bloque); }
				});
	return false;
}


function fnjs_ref_absoluta(base,path) {
	var url="";
	var inicio="";
	var base1= base;
	var path1= path;
	var secure = <?php if (!empty($_SERVER["HTTPS"])) { echo 1; } else {echo 0;} ?> ;
	if (secure) {
		var protocol = 'https:';
	} else {
		var protocol = 'http:';
	}
	// El apache ya ha añadido por su cuenta protocolo+$web. Lo quito:
	ini=protocol+'<?= ConfigGlobal::getWeb() ?>';
	if (path.indexOf(ini) != -1) {
		path=path.replace(ini,'');
	} else { // caso especial: http://www/exterior
		ini=protocol+'//www/exterior';
		if (path.indexOf(ini) != -1) {
				url=path;
				return url;
		} else { // pruebo si ha subido un nivel, si ha subido más (../../../) no hay manera. El apache sube hasta nivel de servidor, no más.
			ini=protocol+'<?= ConfigGlobal::$web_server ?>';
			if (path.indexOf(ini) != -1) {
				path=path.replace(ini,'');
			} else {
				// si el path es una ref. absoluta, no hago nada
				// si empieza por http://
				if  ( path.match(/^http/) ) {
					url=path;
					return url;
				} else {
					if ("<?= ConfigGlobal::mi_usuario() ?>"=="dani") {
						alert("Este link no va ha funcionar bien, porque tiene una url relativa: ../../\n"+path);
					}
				}
			}
		}
	}
	// De la base. puede ser un directorio o una web:
	//   - cambio el directorio físico por su correspondiente web.
	//   - quito el documento.
	
	a=0;
	if ( base.match(/^<?= addcslashes(ConfigGlobal::$directorio,"/") ?>/) ) {	// si es un directorio
		base=base.replace('<?= ConfigGlobal::$directorio ?>','');
		inicio=protocol+'<?= ConfigGlobal::getWeb() ?>';
	a=2;
	} else { if ( base.match(/^<?= addcslashes(ConfigGlobal::$dir_fotos,"/") ?>/) ){
		base=base.replace('<?= ConfigGlobal::$dir_fotos ?>','');
		inicio=protocol+'<?= ConfigGlobal::$web_fotos ?>';
	a=3;
		} else { if ( base.match(/^<?= addcslashes(ConfigGlobal::$dir_oficinas,"/") ?>/) ){
			base=base.replace('<?= ConfigGlobal::$dir_oficinas ?>','');
			inicio=protocol+'<?= ConfigGlobal::$web_oficinas ?>';
	a=4;
			} else { if ( base.match(/^<?= addcslashes(ConfigGlobal::$dir_web,"/") ?>/) ){
				base=base.replace('<?= ConfigGlobal::$dir_web ?>','');
				inicio=protocol+'<?= ConfigGlobal::$web_server ?>';
	a=5;
				}
			}
		}
	}
	// si es una web:
	if (!inicio) {
		if (base.indexOf(protocol) != -1) {
			base=base.replace(protocol,'');
			inicio=protocol;
	a=6;
		}
	}
	// le quito la página final (si tiene) y la barra (/)
	base=base.replace(/\/(\w+\.\w+$)|\/((\w+-)*(\w+ )*\w+\.\w+$)/,''); 
	//elimino la base si ya existe en el path:
	path=path.replace(base,'');
	if ("<?= ConfigGlobal::mi_usuario() ?>"=="dani") {
		//alert ("base1: "+base1+"\npath1: "+path1+"\npath: "+path+"\nAA: "+a +" base: "+base);	
	}
	// sino coincide con niguno, dejo lo que había.
	if (!inicio) {
		url=path;
	} else {
		url=inicio+base+path;
	}
	//alert ('url: '+url);
	return url;
}
function fnjs_enviar_formulario(id_form,bloque) {
	if (!bloque) { bloque='#main'; }
	$(id_form).submit(function() { // catch the form's submit event
			$.ajax({ // create an AJAX call...
				data: $(this).serialize(), // get the form data
				type: 'post', // GET or POST
				url: $(this).attr('action'), // the file to call
				success:function (resposta) { fnjs_mostra_resposta (resposta,bloque); }
			});
			return false; // cancel original event to prevent form submitting
		});
	$(id_form).submit();
	$(id_form).off();
}

function fnjs_enviar(evt,objeto){
	var frm=objeto.id;
	if (evt.keyCode==13 && evt.type=="keypress") {
		//alert ('hola33 '+evt.keyCode+' '+evt.type);
		// buscar el boton 'ok'
		var b=$('#'+frm+' input.btn_ok');
		if (b[0]) {
			b[0].onclick();
		}
		evt.preventDefault(); // que no siga pasando el evento a submit.
		evt.stopPropagation();
		return false;
	}
}
function fnjs_mostra_resposta(resposta,bloque) {
	switch (typeof resposta) {
		case 'object':
			var myText=resposta.responseText;
			break;
		case 'string':
			var myText=resposta;
			break;
	}
	//$(bloque).load(myText);
	$(bloque).html(myText);
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
fnjs_comprobar_campos=function(formulario,obj,ccpau,tabla){
	if (tabla==undefined && obj==undefined) { return 'ok'; } // sigue.
	var s=0;
	var rta_txt="";
	if (tabla==undefined) tabla='x';
	if (obj==undefined)  { obj='x'; }
	//var parametros=$(formulario).serialize()+'&tabla='+tabla+'&ficha='+ficha+'&pau='+pau+'&exterior='+exterior+'&PHPSESSID=<?php echo session_id(); ?>';
	var parametros=$(formulario).serialize()+'&cc_tabla='+tabla+'&cc_obj='+obj+'&cc_pau='+ccpau;

	url='apps/core/comprobar_campos.php';
	// pongo la opcion async a false para que espere, sino sigue con el codigo y devuelve siempre ok.
	$.ajax({
		async: false,
		url: url,
		type: 'post',
		data: parametros,
		dataType: 'html',
		success: function (rta_txt) {
		if (rta_txt.length > 3) {
			alert ("<?= _("error") ?>:\n"+rta_txt);
			s=1;
		} else {
			s=0;
		}
	  }
	});
	if (s==1) {
		return 'error';
	} else {
		return 'ok';
	}
}

function XMLtoString(elem){
	
	var serialized;
	
	try {
		// XMLSerializer exists in current Mozilla browsers
		serializer = new XMLSerializer();
		serialized = serializer.serializeToString(elem);
	} 
	catch (e) {
		// Internet Explorer has a different approach to serializing XML
		serialized = elem.xml;
	}
	
	return serialized;
}

function DOMtoString(doc) {
	// Vamos a convertir el arbol DOM en un String  
	// Definimos el formato de salida: encoding, identación, separador de línea,...  
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

function fnjs_dani() {
  $("#left_slide").show();
}
function fnjs_dani2() {
  $("#left_slide").hover(
    //on mouseover
    function() {
      $(this).animate({
        height: '+=250' //adds 250px
        }, 'slow' //sets animation speed to slow
      );
    }
    //on mouseout
    ,function() {
      $(this).animate({
        height: '-=250px' //substracts 250px
        }, 'slow'
      );
    }
  );
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
<script type="text/javascript" src="<?= ConfigGlobal::$web_scripts ?>/udm4-php/udm-resources/udm-dom.php?PHPSESSID=<?= session_id() ?>"></script>
<!-- keyboard navigation module -->
<!-- <script type="text/javascript" src="/udm4-php/udm-resources/udm-mod-keyboard.js"></script> -->
<ul id="udm" class="udm">
				<!--
					<li><a class="nohref" onclick=fnjs_exportar('rtf') title="">passar a rtf</a></li>
					<li><a class="nohref" onclick=fnjs_exportar('html') title="">passar a html</a></li>
					<li><a class="nohref" onclick=fnjs_exportar('html_zip') title="Guardar como html comprimido (zip)">zip (html)</a></li>
					<li><a class="nohref" onclick=fnjs_exportar('pdf') title="">passar a pdf</a></li>
					<li><a class="nohref" onclick=fnjs_exportar('odfc') title="Open Document Format full de càlcul">ODF calc</a></li>
					<li><a class="nohref" onclick=fnjs_exportar('odft') title="Open Document Format texte">ODF text</a></li>
			<li><a class="nohref" title="Help">Ajuda</a>
				<ul>
					<li><a class="nohref" onclick=window.open('<?= ConfigGlobal::$web_server ?>/mediawiki') >ajuda (wiki)</a></li>
				</ul>
			</li>
			-->
	<?= $li_submenus; ?>
</ul>
</div>
<div id="iframe_export" name="iframe_export" style="display:none;">
	<form id="frm_export" method="POST" action="export.php">
	<input type="hidden" id="frm_export_orientation" name="frm_export_orientation" />
	<input type="hidden" id="frm_export_ref" name="frm_export_ref" />
	<input type="hidden" id="frm_export_titulo" name="frm_export_titulo" />
	<input type="hidden" id="frm_export_modo" name="frm_export_modo" />
	<input type="hidden" id="frm_export_tipo" name="frm_export_tipo" />
	<input type="hidden" id="frm_export_ex" name="frm_export_ex" />
	</form>
</div>
<div id="cargando" ><?= _('Cargando...') ?></div>
<div id="left_slide" class="left-slide">
<span class=handle onClick="fnjs_ir_a();" style="display: none;">cccc</span>
</div>
<div id="main" refe="<?= $pag_ini ?>">
<?php
if ($_SESSION['session_auth']['expire'] == 1) {
	include ("sistema/usuario_form_pwd.php");
} else {
	if (!empty($pag_ini)) {
		include ($pag_ini);
	} else {
		include ("public/portada.php");
	} 
}
?>
<script>
/* $(document).ready */
$(function() {
	fnjs_cambiar_base_link();
	$('#left_slide').hide();  // hide it initially
})
</script>
</div>
</body>
</html>

