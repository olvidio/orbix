<?php
namespace ubis\model;
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
		// Si se quiere listar una selcción, $_POST['k_buscar']
		if (empty($_POST['k_buscar'])) {
			$aWhere=array('_ordre'=>'region');
			$aOperador='';
		} else {
			$aWhere=array('region'=> $_POST['k_buscar']);
			$aOperador=array('region'=>'sin_acentos');
		}
		$oLista=new GestorRegion();
		$Coleccion=$oLista->getRegiones($aWhere,$aOperador);
		break;
	case 'datos_update':
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new Region($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new Region();
		}
		break;
	case 'datos_form':
		// para el form
		if (!empty($a_pkey) && isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			$oFicha = new Region($a_pkey);
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new Region();
		}
		break;
}

$tit_buscar=_("buscar una región (sigla)");
$tit_txt=_("regiones");
$explicacion_txt="";
?>
