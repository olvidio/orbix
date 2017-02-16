<?php
use actividades\model as actividades;
use actividadestudios\model as actividadestudios;
use notas\model as notas;
use personas\model as personas;
use profesores\model as profesores;
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

$continuar = (string)  filter_input(INPUT_POST, 'continuar');
 if (empty($continuar)) {
	 $html = "<br><h3>"._("Actualización de los datos de docencia")."</h3>";
	 $html .=  "<p>";
	 $html .= _("Esta acción llenará el dossier de actividad docente con los datos de los cursos anuales. Sólo se tendrán en cuenta los ca que se han marcado como terminados. Así nos aseguramos de copiar todos los datos (actas...)");
	 $html .=  "</p>";
	 $html .=  "<span class=link onclick=fnjs_update_div('main','apps/actividadestudios/controller/actualizar_docencia.php?continuar=1')>";
	 $html .= _("continuar");
	 $html .= "</span></p>";
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
		$GesAsignaturasCa = new actividadestudios\GestorActividadAsignatura();
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
			
			$oProfesorDocencia = new profesores\ProfesorDocenciaStgr(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura,'id_nom'=>$id_profesor));
			$oProfesorDocencia->DBCarregar();
			$oProfesorDocencia->setCurso_inicio($ini_a);
			$oProfesorDocencia->setTipo($tipo);
			$oProfesorDocencia->setActa($acta);
			$oProfesorDocencia->DBGuardar();
		}	
	}
	$html = _("ya está");
}

echo $html;