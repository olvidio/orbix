<?php
namespace usuarios\model;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	//require_once ("classes/personas/ext_aux_roles_gestor.class");
	//require_once ("classes/web/desplegable.class");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


// segun sea la página que hace el include de esta.
if (isset($_POST['padre'])) $padre=$_POST['padre'];
switch ($padre) {
	case 'datos_sql':
		// para el datos_sql.php
		// Si se quiere listar una selcción, $_POST['k_buscar']
		if (empty($_POST['k_buscar'])) {
			$aWhere=array();
			$aOperador=array();
		} else {
			$aWhere['role'] = $_POST['k_buscar'];
			$aOperador['role'] = 'sin_acentos';
		}
		$aWhere['id_role'] = 3; // para asegurarme que no se borra.
		$aOperador['id_role'] = '>'; // para asegurarme que no se borra.
		$oLista=new GestorRole();
		$Coleccion=$oLista->getRoles($aWhere,$aOperador);
		break;
	case 'datos_update':
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new Role($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new Role();
		}
		break;
	case 'datos_form':
		// para el form
		if (isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			if (!empty($a_pkey)) $oFicha = new Role($a_pkey);
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new Role();
			$despl_depende = "<option></option>";
		}
		break;
}

$tit_buscar=_("rol a buscar");
$tit_txt=_("Tipos de rol que puede tener un usuario");
$explicacion_txt="";

?>
