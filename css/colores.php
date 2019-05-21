<?php
namespace core;
use usuarios\model\entity as usuarios;
if (empty($estilo_color)) {
	// INICIO Cabecera global de URL de controlador *********************************
		require_once ("apps/core/global_header.inc");
	// Arxivos requeridos por esta url **********************************************
		//require_once ("classes/personas/ext_web_preferencias_gestor.class");

	// Crea los objectos de uso global **********************************************
		require_once ("apps/core/global_object.inc");
	// FIN de  Cabecera global de URL de controlador ********************************
	$oGesPref = new usuarios\GestorPreferencia();

	$id_usuario= ConfigGlobal::mi_id_usuario();
	$aPref = $oGesPref->getPreferencias(array('id_usuario'=>$id_usuario,'tipo'=>'estilo'));
	if (count($aPref) > 0) {
		$oPreferencia = $aPref[0];
		$preferencia = $oPreferencia->getPreferencia();
		list($estilo_color,$tipo_menu) = preg_split('/#/',$preferencia);
	} else {
		// valores por defecto
		$estilo_color='azul';
		$tipo_menu='horizontal';
	}
}

$gris_claro="#EEEEEE";
switch ($estilo_color) {
	case  "navy":
		$border=0;
		
		$medio="#000066";
		//$claro="#5D91F1";
		$claro="#FFFFFF";
		$oscuro="#000000";
	
		$fondo_oscuro="#000066";
		$fondo_claro="beige";
		$letras="black";
		$letras_link="navy";
		$letras_hover="#00CCFF";
		$lineas="#CCCCCC";
		$cru="#FFFCF2";
		$fondo_uno="#CCCCCC";
		$fondo_dos="#DDDDDD";
		$fondo_tres="#EECCCC";
		
		$fondo_menu="#AAAAFF";
		$udm_flecha="right-navblue.gif";
		$tono1 = "#ccccff";
		$tono2 = "#b3b3ff";
		$tono3 = "#9999ff";
		$tono4 = "#8080ff";
		$tono5 = "#6666ff";
		$tono6 = "#4d4dff";
		$tono7 = "#3333ff";
		break;
	case  "azul":
		$border=0;
		
		$medio="#5482D4";
		//$claro="#5D91F1";
		$claro="#FFFFFF";
		$oscuro="#325081";
	
		$fondo_oscuro="#000066";
		//$fondo_claro="white";
		$fondo_claro="beige";
		$letras="black";
		$letras_link="navy";
		$letras_hover="#00CCFF";
		$lineas="#CCCCCC";
		$cru="#FFFCF2";
		$fondo_uno="#CCCCCC";
		$fondo_dos="#DDDDDD";
		$fondo_tres="#EECCCC";
		
		$fondo_menu="#AAAAFF";
		$udm_flecha="right-navblue.gif";
		$tono1 = "#ebf0fa";
		$tono2 = "#d6e1f5";
		$tono3 = "#c2d3f0";
		$tono4 = "#adc4eb";
		$tono5 = "#99b5e6";
		$tono6 = "#85a6e0";
		$tono7 = "#7097db";

		break;
	case "verde":
		$border=0;
		$fondo_menu="#AAFFAA";

		$oscuro="#363";
		$claro="#F8FBD0";
		//$oscuro2="#64915E";
		$medio="#699F62";

		$fondo_oscuro=$oscuro;
		$fondo_claro=$claro;
		$letras=$oscuro;
		$letras_link="navy";
		$letras_hover="#00FF00";
		$lineas="#CCCCCC";
		$cru="#FFFCF2";
		$fondo_uno="#A0F0A0";
		$fondo_dos="#AFFFAF";
		$fondo_tres="#EECCCC";
		
		$udm_flecha="right-navgreen.gif";
		$tono1 = "#f0f5ef";
		$tono2 = "#e1ecdf";
		$tono3 = "#d2e2d0";
		$tono4 = "#c3d8c0";
		$tono5 = "#b4cfb0";
		$tono6 = "#a4c5a0";
		$tono7 = "#95bb90";

		break;
	case "naranja":
		$border=0;

		$medio="#FF6600";
		$claro="white";
		$oscuro="#C73800";

		$fondo_oscuro="#FF6600";
		$fondo_claro="oldlace";
		$letras="black";
		$letras_link="navy";
		$letras_hover="#FF6600";
		$lineas="#CCCCCC";
		$cru="#FFFCF2";
		$fondo_uno="#FFCC99";
		$fondo_dos="#FFDDAA";
		$fondo_tres="#EECCCC";
		
		$fondo_menu="#FFAAAA";
		$udm_flecha="right-navorange.gif";
		$tono1 = "#fff0e6";
		$tono2 = "#ffe0cc";
		$tono3 = "#ffd1b3";
		$tono4 = "#ffc299";
		$tono5 = "#ffb380";
		$tono6 = "#ffa366";
		$tono7 = "#ff944d";

	break;
}
if (ConfigGlobal::$web_path == '/pruebas' OR ConfigGlobal::$web_path == '/pruebassf') {
		$fondo_claro="aquamarine";
}
?>
