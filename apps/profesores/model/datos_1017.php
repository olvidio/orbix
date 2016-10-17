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
		$oLista=new GestorProfesorTituloEst();
		$Coleccion=$oLista->getTitulosEst(array('id_nom'=>$_POST['id_pau']));
		break;
	case 'datos_update':
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new ProfesorTituloEst($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new ProfesorTituloEst(array('id_nom'=>$_POST['id_pau']));
		}
		break;
	case 'datos_form':
		// para el form
		if (!empty($a_pkey) && isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			$oFicha = new ProfesorTituloEst($a_pkey);
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new ProfesorTituloEst();
			$despl_depende = "<option></option>";
		}
		break;
}

$explicacion_txt='';
$tit_txt=_("dossier de títulos de postgrado");
$eliminar_txt=_("¿Está seguro que desea eliminar este titulo?");
?>
