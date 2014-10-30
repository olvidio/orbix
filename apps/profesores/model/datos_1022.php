<?php
namespace profesores\model;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// segun sea la página que hace el include de esta.
if (isset($_POST['padre'])) $padre=$_POST['padre'];
switch ($padre) {
	case 'datos_sql':
		// para el datos_sql.php
		$oLista=new GestorProfesorLatin();
		$Coleccion=$oLista->getProfesoresLatin(array('id_nom'=>$_POST['id_pau']));
		break;
	case 'datos_update':
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new ProfesorLatin($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new ProfesorLatin(array('id_nom'=>$_POST['id_pau']));
		}
		break;
	case 'datos_form':
		// para el form
		if (!empty($a_pkey) && isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			$oFicha = new ProfesorLatin($a_pkey);
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new ProfesorLatin();
		}
		break;
}

$tit_txt=_("dossier profesores de latín");
$eliminar_txt=_("¿Está seguro que desea eliminar esto?");
$explicacion_txt="";
?>
