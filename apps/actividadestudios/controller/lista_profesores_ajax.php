<?php
use profesores\model\entity as profesores;
/*
* Devuelvo un desplegable con los valores posibles segun el valor de entrada.
*
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

switch ($_POST['salida']) {
	case "asignatura":
		$id_asignatura = empty($_POST['id_asignatura'])? '' : $_POST['id_asignatura'];
		$GesProfesores = new profesores\GestorProfesor();
		$oDesplProfesores = $GesProfesores->getDesplProfesoresAsignatura($id_asignatura);
		
		$oDesplProfesores->setNombre('id_profesor');
		$oDesplProfesores->setBlanco('t');
		$oDesplProfesores->setOpcion_sel(-1);

		echo $oDesplProfesores->desplegable();
	 break;
	case "dl":
		$id_activ = empty($_POST['id_activ'])? '' : $_POST['id_activ'];
		$GesProfesores = new profesores\GestorProfesorActividad();
		$oDesplProfesores = $GesProfesores->getListaProfesoresActividad(array($id_activ));
		
		$oDesplProfesores->setNombre('id_profesor');
		$oDesplProfesores->setBlanco('t');
		$oDesplProfesores->setOpcion_sel(-1);

		echo $oDesplProfesores->desplegable();
	 break;
	case "todos":
		$GesProfesores = new profesores\GestorProfesor();
		$aOpciones = $GesProfesores->getListaProfesoresPub();
		
		$oDesplProfesores = new web\Desplegable('',$aOpciones,'',true);

		$oDesplProfesores->setNombre('id_profesor');
		$oDesplProfesores->setBlanco('t');
		$oDesplProfesores->setOpcion_sel(-1);

		echo $oDesplProfesores->desplegable();
	 break;
}