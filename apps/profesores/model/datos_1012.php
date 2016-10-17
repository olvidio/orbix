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
		$oLista=new GestorProfesorPublicacion();
		$Coleccion=$oLista->getProfesorPublicaciones(array('id_nom'=>$_POST['id_pau']));
		break;
	case 'datos_update':
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new ProfesorPublicacion($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new ProfesorPublicacion(array('id_nom'=>$_POST['id_pau']));
		}
		break;
	case 'datos_form':
		// para el form
		if (!empty($a_pkey) && isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			$oFicha = new ProfesorPublicacion($a_pkey);
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new ProfesorPublicacion();
			$despl_depende = "<option></option>";
		}
		break;
}

$tit_txt=_("dossier de publicaciones");
$eliminar_txt=_("¿Está seguro que desea eliminar esta publicación?");
$explicacion_txt='';
?>
