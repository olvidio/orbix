<?php
//namespace usuarios\controller;
use usuarios\model\entity as usuarios;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$id_usuario= core\ConfigGlobal::mi_id_usuario();

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qtabla = (string) \filter_input(INPUT_POST, 'tabla');
$Qoficina = (string) \filter_input(INPUT_POST, 'oficina');
$QsPrefs = (string) \filter_input(INPUT_POST, 'sPrefs');

switch ($Qque) {
	case "slickGrid":
		$idioma= core\ConfigGlobal::mi_Idioma();
		$tipo = 'slickGrid_'.$Qtabla.'_'.$idioma;
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>$tipo));
		// si no se han cambiado las columnas visibles, pongo las actuales (sino las borra).
		$aPrefs = json_decode($QsPrefs, true);
		if ($aPrefs['colVisible'] == 'noCambia') {
			$sPrefs_old = $oPref->getPreferencia();
			$aPrefs_old = json_decode($sPrefs_old, true);
			$aPrefs['colVisible'] = empty($aPrefs_old['colVisible'])? '' : $aPrefs_old['colVisible'];
			$QsPrefs = json_encode($aPrefs, true);
		}

		$oPref->setPreferencia($QsPrefs);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
	default:
		$Qoficina = empty($Qoficina)? 'exterior' : $Qoficina;
		$Qinicio = empty($Qinicio)? 'exterior' : $Qinicio;
		// Guardar página de inicio:
		$inicio=$Qinicio."#".$Qoficina;
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'inicio'));
		$oPref->setPreferencia($inicio);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		// Guardar estilo:
		$Qestilo_color = (string) \filter_input(INPUT_POST, 'estilo_color');
		$Qtipo_menu = (string) \filter_input(INPUT_POST, 'tipo_menu');
		$estilo=$Qestilo_color."#".$Qtipo_menu;
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'estilo'));
		$oPref->setPreferencia($estilo);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		// Guardar presentacion tablas:
		$Qtipo_tabla = (string) \filter_input(INPUT_POST, 'tipo_tabla');
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'tabla_presentacion'));
		$oPref->setPreferencia($Qtipo_tabla);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		// Guardar presentacion nombre Apellidos:
		$QordenApellidos = (string) \filter_input(INPUT_POST, 'ordenApellidos');
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'ordenApellidos'));
		$oPref->setPreferencia($QordenApellidos);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		// Guardar idioma:
		$Qidioma_nou = (string) \filter_input(INPUT_POST, 'idioma_nou');
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'idioma'));
		$oPref->setPreferencia($Qidioma_nou);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		// volver a la página de configuración
		$location=web\Hash::link(core\ConfigGlobal::getWeb().'/index.php?'.http_build_query(array('PHPSESSID'=>session_id())));
		echo "<body onload=\"$location\";></body>";
}