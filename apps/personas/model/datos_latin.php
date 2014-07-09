<?php
namespace personas\model;
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
			$aWhere=array('_ordre'=>'nom');
			$aOperador='';
		} else {
			$aWhere=array('nom'=> $_POST['k_buscar']);
			$aOperador=array('nom'=>'sin_acentos');
		}
		$oLista=new GestorNombreLatin();
		$Coleccion=$oLista->getNombresLatin($aWhere,$aOperador);
		break;
	case 'datos_update':
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new NombreLatin($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new NombreLatin();
		}
		break;
	case 'datos_form':
		// para el form
		if (!empty($a_pkey) && isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			$oFicha = new NombreLatin($a_pkey);
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new NombreLatin();
		}
		break;
}

$tit_buscar=_("buscar un nombre");
$tit_txt=_("nombres en latín");
$explicacion_txt="";
?>
