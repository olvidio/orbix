<?php
/**
* Esta página sirve para listar los profesores del stgr por departamentos.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		13/1/2017.
*		
*/

/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// tipos de profesores
$oGesProfesorTipo = new profesores\model\GestorProfesorTipo();
$cProfesorTipo = $oGesProfesorTipo->getProfesorTipos();
$cTipoProfesor = array();
foreach ($cProfesorTipo as $oProfesorTipo) {
	$id_tipo = $oProfesorTipo->getId_tipo_profesor();
	$tipo = $oProfesorTipo->getTipo_profesor();
	$cTipoProfesor[$id_tipo] = $tipo;
}
//lista de departamentos.
$oGesDepartamentos = new asignaturas\model\GestorDepartamento();
$cDepartamentos = $oGesDepartamentos->getDepartamentos(array('_ordre'=>'departamento'));


//por cada departamento:
// orden alfabetico personas.
$aClaustro = array();
foreach ($cDepartamentos as $oDepartamento) {
	$id_departamento = $oDepartamento->getId_departamento();
	$departamento = $oDepartamento->getDepartamento();
	// director.
	$oGesProfesorDirector = new profesores\model\GestorProfesorDirector();
	$cProfesorDirector = $oGesProfesorDirector->getProfesoresDirectores(array('id_departamento'=>$id_departamento),  array('f_cese' => 'IS NULL'));
	$aProfesores =array();
	$aDirs =array();
	foreach ($cProfesorDirector as $oProfesorDirector) {
		$id_nom = $oProfesorDirector->getId_nom();
		$oPersonaDl = new personas\model\PersonaDl($id_nom);
		$ap_orden = $oPersonaDl->getApellido1().$oPersonaDl->getApellido2().$oPersonaDl->getNom();
		$ap_nom = $oPersonaDl->getApellidosNombre() ." (". $oPersonaDl->getCentro_o_dl() .")";
		$aDirs[$ap_orden] = $ap_nom;
	}
	ksort($aDirs);
	$aProfesores['director'] = $aDirs;
	// tipo de profesor: ayudante, encargado...
	$oGesProfesor = new profesores\model\GestorProfesor();
	$aProfes =array();
	foreach ($cTipoProfesor as $id_tipo => $tipo) {
		//$aProfesores[$tipo] = $oGesProfesor->getListaProfesoresDepartamento($id_departamento);
		$cProfesores = $oGesProfesor->getProfesores(array('id_departamento'=>$id_departamento,'id_tipo_profesor' => $id_tipo),array('f_cese'=>'IS NULL'));
		foreach ($cProfesores as $oProfesor) {
			$id_nom = $oProfesor->getId_nom();
			$oPersonaDl = new personas\model\PersonaDl($id_nom);
			$ap_orden = $oPersonaDl->getApellido1().$oPersonaDl->getApellido2().$oPersonaDl->getNom();
			$ap_nom = $oPersonaDl->getApellidosNombre() ." (". $oPersonaDl->getCentro_o_dl() .")";
			$aProfes[$ap_orden] = $ap_nom;
		}
		ksort($aProfes); 
		$aProfesores[$tipo] = $aProfes;
	}
	$aClaustro[] = array('id_departamento' => $id_departamento,
						'departamento' => $departamento,
						'profesores' => $aProfesores
						);
}

//------------------------------ html --------------------------------
?>

<?php
$html = '';
foreach ($aClaustro as $aDepartamento){
	$titulo = $aDepartamento['departamento'];
	$html .=  "<h3>$titulo</h3>";
	$html .=  "<table>";
	$aProfesores = $aDepartamento['profesores'];
	foreach ($aProfesores as $tipo => $aNoms) {
		$html .= "<tr><td>$tipo</td><td></td></tr>";
		foreach($aNoms as $id => $ap_nom) {
			$html .= "<tr><td></td><td>$ap_nom</td></tr>";
		}
	}
	$html .=  "</table>";
}
echo $html;