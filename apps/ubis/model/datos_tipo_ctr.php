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
			$aWhere=array('_ordre'=>'nombre_tipo_ctr');
			$aOperador='';
		} else {
			$aWhere=array('nombre_tipo_ctr'=> $_POST['k_buscar']);
			$aOperador=array('nombre_tipo_ctr'=>'sin_acentos');
		}
		$oLista=new GestorTipoCentro();
		$Coleccion=$oLista->getTiposCentro($aWhere,$aOperador);
		break;
	case 'datos_update':
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new TipoCentro($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new TipoCentro();
		}
		break;
	case 'datos_form':
		// para el form
		if (!empty($a_pkey) && isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			$oFicha = new TipoCentro($a_pkey);
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new TipoCentro();
		}
		break;
}

$tit_buscar=_("buscar un tipo de centro");
$tit_txt=_("tipos de centro");
$explicacion_txt="";
?>
