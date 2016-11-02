<?php
namespace asignaturas\model;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Para la función de comprobar campos:
	$_tabla='xa_asignaturas';
	$_exterior='no'; // localización de la base de datos

// segun sea la página que hace el include de esta.
if (isset($_POST['padre'])) $padre=$_POST['padre'];
switch ($padre) {
	case 'datos_sql':
		// para el datos_sql.php
		// Si se quiere listar una selcción, $_POST['k_buscar']
		if (!empty($_POST['k_buscar'])) {
			$aWhere['nombre_asig']= $_POST['k_buscar'];
			$aOperador['nombre_asig']='sin_acentos';
		}
		$aWhere['id_asignatura']= 3000;
		$aOperador['id_asignatura']='<';
		$aWhere['_ordre']='id_nivel';
		$oLista=new GestorAsignatura();
		$Coleccion=$oLista->getAsignaturas($aWhere,$aOperador);
		break;
	case 'datos_update':
		// para el update
		if ($_POST['mod'] == 'editar' || $_POST['mod'] == 'eliminar') {
			if (!empty($a_pkey)) $oFicha = new Asignatura($a_pkey);
		}
		if ($_POST['mod'] == 'nuevo') {
			$oFicha = new Asignatura();
		}
		break;
	case 'datos_form':
		// para el form
		if (!empty($a_pkey) && isset($_POST['mod']) && $_POST['mod'] == 'editar') {
			$oFicha = new Asignatura($a_pkey);
		}
		if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo') {
			$oFicha = new Asignatura();
		}
		break;
}

$tit_buscar=_("buscar una asignatura");
$tit_txt=_("asignaturas");
$explicacion_txt="";
?>
