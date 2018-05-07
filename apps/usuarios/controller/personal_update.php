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

$_POST['que'] = empty($_POST['que'])? '' : $_POST['que'];
switch ($_POST['que']) {
	case "slickGrid":
		$idioma= core\ConfigGlobal::mi_Idioma();
		$tipo = 'slickGrid_'.$_POST['tabla'].'_'.$idioma;
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>$tipo));
		$sPrefs = $_POST['sPrefs'];
		// si no se han cambiado las columnas visibles, pongo las actuales (sino las borra).
		$aPrefs = json_decode($sPrefs, true);
		if ($aPrefs['colVisible'] == 'noCambia') {
			$sPrefs_old = $oPref->getPreferencia();
			$aPrefs_old = json_decode($sPrefs_old, true);
			$aPrefs['colVisible'] = empty($aPrefs_old['colVisible'])? '' : $aPrefs_old['colVisible'];
			$sPrefs = json_encode($aPrefs, true);
		}

		$oPref->setPreferencia($sPrefs);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		break;
	default:
		$_POST['oficina'] = empty($_POST['oficina'])? 'exterior' : $_POST['oficina'];
		// Guardar página de inicio:
		$inicio=$_POST['inicio']."#".$_POST['oficina'];
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'inicio'));
		$oPref->setPreferencia($inicio);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		// Guardar estilo:
		$estilo=$_POST['estilo_color']."#".$_POST['tipo_menu'];
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'estilo'));
		$oPref->setPreferencia($estilo);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		// Guardar presentacion tablas:
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'tabla_presentacion'));
		$oPref->setPreferencia($_POST['tipo_tabla']);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		// Guardar presentacion nombre Apellidos:
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'ordenApellidos'));
		$oPref->setPreferencia($_POST['ordenApellidos']);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		// Guardar idioma:
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'idioma'));
		$oPref->setPreferencia($_POST['idioma_nou']);
		if ($oPref->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}

		// volver a la página de configuración
		$location=web\Hash::link(core\ConfigGlobal::getWeb().'/index.php?'.http_build_query(array('PHPSESSID'=>session_id())));
		echo "<body onload=\"$location\";></body>";
}
?>
