<?php
namespace orbix\core;
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
define('CONFIG_DIR','/var/www/orbix/config');
define('INCLUDES_DIR','/var/www/orbix/core');
$new_include_path = get_include_path().PATH_SEPARATOR.CONFIG_DIR.PATH_SEPARATOR.INCLUDES_DIR;
ini_set ('include_path', $new_include_path);


// INICIO Cabecera global de URL de controlador *********************************
	require_once ("model/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	include_once('classes/personas/ext_aux_usuarios.class');
	include_once('classes/personas/ext_aux_roles.class');
	require_once ("classes/personas/aux_menus_gestor.class");
	require_once ("classes/personas/ext_aux_menus_ext_gestor.class");
	require_once ("classes/personas/ext_web_preferencias_gestor.class");

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
$oGesPref = new GestorPreferencia();

$oUsuario = new Usuario(array('id_usuario'=>ConfigGlobal::id_usuario()));
$oRole = new Role($oUsuario->getId_role());
$interior = $oRole->getInterior();
$mi_oficina_menu=ConfigGlobal::mi_oficina_menu();

$oPermisoMenu = new PermisoMenu();

// ----------- Preferencias -------------------
$id_usuario = ConfigGlobal::id_usuario();

//Busco la página inicial en las preferencias:
// ----------- Página de inicio -------------------
$pag_ini = '';
$aPref = $oGesPref->getPreferencias(array('id_usuario'=>$id_usuario ,'tipo'=>'inicio'));
//$aPref = $oGesPref->getPreferencias(array('username'=>$username,'tipo'=>'inicio'));
if (is_array($aPref) && count($aPref) > 0) {
	$oPreferencia = $aPref[0];
	$preferencia = $oPreferencia->getPreferencia();
	list($inicio,$mi_oficina_menu) = preg_split('/#/',$preferencia);
} else {
	$inicio='';
	$mi_oficina_menu='';
}
if (ConfigGlobal::$ubicacion == 'int') {
	if (isset($primera)) {
		if ($mi_oficina_menu=="admin") $mi_oficina_menu="sistema";
		switch ($inicio) {
			case "oficina":
				$oficina=$mi_oficina_menu;
				break;
			case "personal":
				$oficina=$mi_oficina_menu;
				$pag_ini=ConfigGlobal::$directorio.'/inici/personal.php';
				break;
			case "avisos":
				$oficina=$mi_oficina_menu;
				$pag_ini=ConfigGlobal::$directorio."/sistema/avisos_generar.php";
				break;
			case "aniversarios":
				$oficina=$mi_oficina_menu;
				$pag_ini=ConfigGlobal::$directorio."/programas/aniversarios.php";
				break;
			case "public_home":
				$oficina="public_home";
				$pag_ini=ConfigGlobal::$directorio.'/public/public_home.php';
				break;
			case "armari_doc":
				$oficina="armari_doc";
				$pag_ini=ConfigGlobal::$dir_web.'/oficinas/scdl/File/todos/DOCUMENTS.htm';
				break;
			default:
				$oficina=$mi_oficina_menu;
				$pag_ini='';
		}
	} elseif (isset($_GET['oficina']) && $_GET['oficina']=="public_home") {
		$pag_ini=ConfigGlobal::$directorio.'/public/public_home.php';
	} elseif (isset($_GET['oficina']) && $_GET['oficina']=="armari_doc") {
		$pag_ini=ConfigGlobal::$dir_web.'/oficinas/scdl/File/todos/DOCUMENTS.htm';
	}
	if (ConfigGlobal::usuario() == 'auxiliar') { $pag_ini=''; }
} else { // ubicación exterior
	if ($mi_oficina_menu=="admin") $mi_oficina_menu="sistema";
	if ($mi_oficina_menu=="") $mi_oficina_menu="exterior";
	switch ($inicio) {
		case "avisos":
			$oficina="exterior";
			$pag_ini=ConfigGlobal::$directorio."/sistema/avisos_generar.php";
			break;
		case "exterior":
		default:
			$oficina=$mi_oficina_menu;
			$pag_ini=ConfigGlobal::$directorio.'/public/exterior_home.php';
	}
}

if (!isset($_GET['oficina'])) { $oficina=$mi_oficina_menu; } else { $oficina=$_GET['oficina']; }

// crec que ja està a dalt. $username= ConfigGlobal::usuario();
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

<script type="text/javascript" src="<?php echo ConfigGlobal::$web_scripts.'/omplir_limits_dates.js.php?PHPSESSID='.session_id(); ?>"></script>
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
	url="<?= ConfigGlobal::getWeb() ?>/inici/personal_update.php";
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


/*
$.datepicker.setDefaults({
	showOn: 'both',
	buttonImageOnly: true,
	buttonImage: 'calendar.gif',
	buttonText: 'Calendar' });
	*/
$.datepicker.setDefaults( $.datepicker.regional[ "ca" ] );

function fnjs_logout() {
	var parametros='logout=si&PHPSESSID=<?php echo session_id(); ?>'; 
	top.location.href='index.php?'+parametros;
	//cargar_portada(oficina);
}
function fnjs_link_menu(oficina) {
	var parametros='oficina='+oficina+'&PHPSESSID=<?php echo session_id(); ?>'; 
	
	if (oficina=='web_externa') {
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
	var url=$('#url').val();
	var parametros=$('#parametros').val();
	var bloque='#'+$('#id_div').val();
	
	if(parametros) {
		parametros=parametros+'&PHPSESSID=<?php echo session_id(); ?>'; 
	} else {
		parametros='PHPSESSID=<?php echo session_id(); ?>'; 
	}
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
	var base=$(id_div).attr('refe');
	if (base) {
		var selector=id_div+" a[href]";
		$(selector).each(function(i) {
				var aa=this.href;
		if ("<?= ConfigGlobal::usuario() ?>"=="dani") {
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
					if ("<?= ConfigGlobal::usuario() ?>"=="dani") {
						alert("Este link no va ha funcionar bien, porque tiene una url relativa: ../../\n"+path);
					}
				}
			}
		}
	}
	/* De la base. puede ser un directorio o una web:
	   - cambio el directorio físico por su correspondiente web.
	   - quito el documento.
	*/
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
	if ("<?= ConfigGlobal::usuario() ?>"=="dani") {
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
	/* No sirve, porque ya he puesto la ruta bien (entera) en los formularios
	var ref=$(id_form).attr('action');
	var base=$(bloque).attr('refe');
	if (base==undefined) {
		var url=ref;
	} else {
		var url=fnjs_ref_absoluta(base,ref);
	}
	var path=url.replace(/\?.*$/,'');
	$(bloque).attr('refe',path);
	*/
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
		var b=$('#'+frm+' input#ok');
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
/**
  * funcion para comprobar que estan todos los campos necesarios antes de guardar.
  *@param object formulario
  *@param string tabla Nombre de la tabla de la base de datos.
  *@param string ficha 'si' o 'no' si viene de la presentación ficha.php
  *@param integer pau 0|1 si es de dossiers
  *@param string exterior 'si' o 'no' si está en la base de datos exterior o no.
  *@return strign 'ok'|'error'
  */
fnjs_comprobar_campos=function(formulario,tabla,ficha,pau,exterior){
	if (tabla==undefined) { return 'ok'; } // sigue.
	var s=0;
	var rta_txt="";
	if (ficha==undefined) ficha='no';
	if (pau==undefined) pau=0;
	if (exterior==undefined) exterior='no';
	//var parametros=$(formulario).serialize()+'&tabla='+tabla+'&ficha='+ficha+'&pau='+pau+'&exterior='+exterior+'&PHPSESSID=<?php echo session_id(); ?>';
	var parametros=$(formulario).serialize()+'&tabla='+tabla+'&ficha='+ficha+'&pau='+pau+'&exterior='+exterior;

	url='programas/comprobar_campos.php';
	// pongo la oopcion async false para que espere, sino sigue con el codigo y devuelve siempre ok.
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

</script>
<?php
// sólo para el caso de la instalación en el interior de la dl.
if (ConfigGlobal::$ubicacion == 'int') {
	?>
<ul id="menu" class="menu">
	<li onclick="fnjs_link_menu('public_home');" <?php if ($oficina=="public_home") echo "class='selec'"; ?> title="<?= _("cop d'ull") ?>">Home</li>
	<li onclick="fnjs_link_menu('armari_doc');" <?php if ($oficina=="armari_doc") echo "class='selec'"; ?> title="<?= _("armario de documentos") ?>">Arm. doc.</li>
	<!--
	<li onclick="fnjs_link_menu('dir');"  <?php if ($oficina=="dir") echo "class='selec'"; ?>>vcd</li>
	<li onclick="fnjs_link_menu('dir');"  <?php if ($oficina=="dir") echo "class='selec'"; ?>>sd</li>
	-->
	<li onclick="fnjs_link_menu('vcsd');"  <?php if ($oficina=="vcsd") echo "class='selec'"; ?>>vcsd</li>
	<li onclick="fnjs_link_menu('scdl');"  <?php if ($oficina=="scdl") echo "class='selec'"; ?>>scdl</li>
	<li onclick="fnjs_link_menu('sm');"  <?php if ($oficina=="sm") echo "class='selec'"; ?>>vsm</li>
	<li onclick="fnjs_link_menu('sg');"  <?php if ($oficina=="sg") echo "class='selec'"; ?>>vsg</li>
	<li onclick="fnjs_link_menu('sr');"  <?php if ($oficina=="sr") echo "class='selec'"; ?>>vsr</li>
	<li onclick="fnjs_link_menu('est');"  <?php if ($oficina=="est") echo "class='selec'"; ?>>vest</li>
	<li onclick="fnjs_link_menu('adl');"  <?php if ($oficina=="adl") echo "class='selec'"; ?>>adl</li>
	<li onclick="fnjs_link_menu('agd');"  <?php if ($oficina=="agd") echo "class='selec'"; ?>>dagd</li>
	<li onclick="fnjs_link_menu('des');"  <?php if ($oficina=="des") echo "class='selec'"; ?>>dre</li>
	<li onclick="fnjs_link_menu('aop');"  <?php if ($oficina=="aop") echo "class='selec'"; ?>>aop</li>
	<li onclick="fnjs_link_menu('ocs');"  <?php if ($oficina=="ocs") echo "class='selec'"; ?>>ocs</li>
	<li onclick="fnjs_link_menu('soi');"  <?php if ($oficina=="soi") echo "class='selec'"; ?>>soi</li>
	<li onclick="fnjs_link_menu('sistema');"  <?php if ($oficina=="sistema") echo "class='selec'"; ?> title="<?= _("informatica") ?>">sys.</li>
	<li onclick="fnjs_link_menu('preferencias');"  <?php if ($oficina=="preferencias") echo "class='selec'"; ?> title="<?= _("preferencias") ?>">pref.</li>
	<li onclick="fnjs_link_menu('web_externa');"  <?php if ($oficina=="web_extrena") echo "class='selec'"; ?> title="<?= _("web externa") ?>">web</li>
	<li onclick="fnjs_link_menu('casa_moneders');"  <?php if ($oficina=="casa_moneders") echo "class='selec'"; ?> title="<?= _("web de la casa") ?>">Mon14</li>
	<li onclick="fnjs_logout();" >| <?= ucfirst(_('salir')) ?></li>
</ul>
<?php
}
?>
<!-- menu tree -->
<div id="submenu">
<?php
$aWhere = array('oficina'=>$oficina,'_ordre'=>'orden');
if (ConfigGlobal::$ubicacion == 'int') {
	$oLista=new GestorMenu();
	$oMenus=$oLista->getMenus($aWhere);
} else {
	$oLista=new GestorMenuExt();
	$oMenus=$oLista->getMenusExt($aWhere);
}
?>
<!-- PHP generated menu script [must come *before* any other modules or extensions] -->
<script type="text/javascript" src="<?= ConfigGlobal::$web_scripts ?>/udm4-php/udm-resources/udm-dom.php?PHPSESSID=<?= session_id() ?>"></script>
<!-- keyboard navigation module -->
<!-- <script type="text/javascript" src="/udm4-php/udm-resources/udm-mod-keyboard.js"></script> -->
<ul id="udm" class="udm">
	<li><a class="nohref" title="Utilitats de la web"><?= _("Utilidades") ?></a>
		<ul>
			<li><a class="nohref" title="Exportar el contingut de la pàgina">exportar</a>
				<ul>
					<li><a class="nohref" onclick=fnjs_exportar('rtf') title="">passar a rtf</a></li>
					<li><a class="nohref" onclick=fnjs_exportar('html') title="">passar a html</a></li>
					<li><a class="nohref" onclick=fnjs_exportar('html_zip') title="Guardar como html comprimido (zip)">zip (html)</a></li>
					<li><a class="nohref" onclick=fnjs_exportar('pdf') title="">passar a pdf</a></li>
					<li><a class="nohref" onclick=fnjs_exportar('odfc') title="Open Document Format full de càlcul">ODF calc</a></li>
					<li><a class="nohref" onclick=fnjs_exportar('odft') title="Open Document Format texte">ODF text</a></li>
				</ul>
			</li>
			<?php
			if (ConfigGlobal::$ubicacion == 'int') {
				?>
			<li><a class="nohref" title="Exportar llistats">llistats</a>
				<ul>
					<li><a class="nohref" onclick="fnjs_link_submenu('programas/aniversarios_exportar.php','');" >aniversaris</a></li>
					<li><a class='nohref' onclick="fnjs_link_submenu('sistema/avisos_generar.php','');" >avisos</a></li>
				</ul>
			</li>
			<?php
			if (ConfigGlobal::usuario() != 'auxiliar') {
				?>
				<li><a class="nohref" onclick=window.open('http://madrid') >Dades Madrid</a></li>
				<?php
				}
			}
			if (ConfigGlobal::id_role() != 8 && ConfigGlobal::id_role() != 16) { //centros
			?>
			<li><a class="nohref" title="Help">Ajuda</a>
				<ul>
					<li><a class="nohref" onclick=window.open('<?= ConfigGlobal::$web_server ?>/mediawiki') >ajuda (wiki)</a></li>
				</ul>
			</li>
			<?php
			}
			if (ConfigGlobal::$ubicacion == 'ext') {
				$url='sistema/usuario_form.php';
				$parametros='quien=usuario&id_usuario='.ConfigGlobal::id_usuario();
				echo "<li><a href='#' title='Help'>Prefrencias</a>
					<ul>
						<li><a class='nohref' onclick=\"fnjs_link_submenu('inici/personal.php','');\" >personales</a></li>";
				if (ConfigGlobal::id_role() != 8 && ConfigGlobal::id_role() != 16) { //centros
					echo "<li><a class='nohref' onclick=\"fnjs_link_submenu('$url','$parametros');\" >avisos</a></li>";
				}
				echo "	</ul>
				</li>";
			}
			?>
		</ul>
	</li>
	<?php
	$txt="";
	$indice=1;
	$indice_old=1;
	$num_menu_1="";
		$m=0;
		foreach ($oMenus as $oMenu) {
			$m++;
			extract($oMenu->getTot());
			//echo "m: $perm_menu,l: $perm_login, ".visible($perm_menu,$perm_login) ;
			// hago las rutas absolutas, en vez de relativas:
			if (!empty($url)) $url=ConfigGlobal::getWeb().'/'.$url;
			// quito las llaves "{}"
			$orden=substr($orden,1,-1);
			$array_orden=preg_split('/,/',$orden);
			$indice=count($array_orden);
			if ($array_orden[0]==$num_menu_1) { continue; }
			if ($indice==1 && !$oPermisoMenu->visible($perm_menu)) {
				$num_menu_1=$array_orden[0];
				continue;
			} else { 
				$num_menu_1="";
				if (!$oPermisoMenu->visible($perm_menu)) { continue; }
			}
			if ($indice==$indice_old) {
					if (!empty($url)) {
						$txt.="<li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$url','$parametros');\"  >"._($menu)."</a>";
					} else {
						$txt.="<li><a class=\"nohref\" >"._($menu)."</a>";
					}
			} elseif ($indice>$indice_old) {
					$txt.="<ul><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$url','$parametros');\"  >"._($menu)."</a>";
			} else {
				for ($n=$indice;$n<$indice_old;$n++) {
					$txt.="</li></ul>";
				}
					$txt.="</li><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$url','$parametros');\"  >"._($menu)."</a>";
			}
			$indice_old=$indice;
		}
	for ($n=1;$n<$indice_old;$n++) {
        $txt.="</li></ul>";
    }
	echo $txt;
	?>
	</li>
	<?php
	if (ConfigGlobal::$ubicacion == 'ext') {
	?>
		<li><a href="#" onclick="fnjs_logout();" >| <?= ucfirst(_('salir')) ?></a></li>
		<?php
	}
	?>
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

<div id="cargando" >Cargando...</div>
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
$(document).ready(fnjs_cambiar_base_link); 
</script>
</div>
</body>
</html>
