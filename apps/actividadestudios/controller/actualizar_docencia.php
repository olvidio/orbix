<?php
use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use notas\model\entity as notas;
use personas\model\entity as personas;
use profesores\model\entity as profesores;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/*
 * Esta página actualiza los datos del dossier "d_docencia_stgr"
 * con la información que se tiene de los ca.
 * Se cogen los ca marcados como terminados (así se copia el acta...)
 */

$continuar = (integer)  filter_input(INPUT_POST, 'continuar');

 if (empty($continuar)) {
	$aQuery = array('continuar'=> 1);
	$pagina=web\Hash::link('apps/actividadestudios/controller/actualizar_docencia.php?'.http_build_query($aQuery));
	$a_campos = array('mod'=>'inicio',
					'pagina' => $pagina
 				);
 } else {
	// seleccionar las posibles actividades:
	$any=date("Y");
	$mes=date("m");
	if ($mes>9) {
		$any1=$any+1; 
	} else { 
		$any1=$any-1;
	}
	$inicurs_ca=core\curso_est("inicio",$any1);
	$fincurs_ca=core\curso_est("fin",$any1);
	
	$aWhere['status'] = 3;
	$aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
	$aOperadores['f_ini'] = 'BETWEEN';
	$mi_sfsv = core\ConfigGlobal::mi_sfsv();
	$id_tipo='^'.$mi_sfsv.'[123][23]';  
	$aWhere['id_tipo_activ'] = $id_tipo;
	$aOperadores['id_tipo_activ'] = '~';
	$GesActividades = new actividades\GestorActividadDl();	
	$cActividades = $GesActividades->getActividades($aWhere,$aOperadores);
	// busco los profesores que han dado alguna asignatura en actividad.	
	foreach ($cActividades as $oActividad) {
		$id_activ = $oActividad->getId_activ();
		$f_ini = $oActividad->getF_ini();
		list($ini_d,$ini_m,$ini_a) = preg_split('/[:\/\.-]/', $f_ini ); //los delimitadores pueden ser /, ., -, :
		$GesAsignaturasCa = new actividadestudios\GestorActividadAsignaturaDl();
		$cActivAsignaturas = $GesAsignaturasCa->getActividadAsignaturas(array('id_activ'=>$id_activ),array('id_profesor'=>'IS NOT NULL'));
		
		foreach ($cActivAsignaturas as $oActividadAsignatura) {
			$id_asignatura = $oActividadAsignatura->getId_asignatura();
			$id_profesor = $oActividadAsignatura->getId_profesor();
			$tipo = $oActividadAsignatura->getTipo();

			$GesActas = new notas\GestorActa();
			$cActas = $GesActas->getActas(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura));
			if (is_array($cActas) && count($cActas) == 1) {
				$oActa = $cActas[0];
				$acta=$oActa->getActa();
			} else {
				$acta = '';	
			}
			
			$oProfesorDocencia = new profesores\ProfesorDocenciaStgr();
			$oProfesorDocencia->setId_activ($id_activ);
			$oProfesorDocencia->setId_asignatura($id_asignatura);
			$oProfesorDocencia->setId_nom($id_profesor);
			$oProfesorDocencia->setCurso_inicio($ini_a);
			$oProfesorDocencia->setTipo($tipo);
			$oProfesorDocencia->setActa($acta);
			$oProfesorDocencia->DBGuardar();
		}	
	}

	$a_campos = array('mod'=>'fin',
				);
}

$oView = new core\View('actividadestudios/controller');
echo $oView->render('actualizar_docencia.phtml',$a_campos);