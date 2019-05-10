<?php
use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use notas\model\entity as notas;
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

$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
$Qfin = (string) \filter_input(INPUT_POST, 'fin');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$continuar = (integer)  filter_input(INPUT_POST, 'continuar');

 if (empty($continuar)) {
	//Periodo
	$boton = "<input type='button' value='"._("buscar")."' onclick='fnjs_buscar()' >";
	$aOpciones =  array(
						'tot_any' => _("todo el año"),
						'trimestre_1'=>_("primer trimestre"),
						'trimestre_2'=>_("segundo trimestre"),
						'trimestre_3'=>_("tercer trimestre"),
						'trimestre_4'=>_("cuarto trimestre"),
						'separador'=>'---------',
						'curso_ca'=>_("curso ca"),
						'separador1'=>'---------',
						'otro'=>_("otro")
						);
	$oFormP = new web\PeriodoQue();
	$oFormP->setFormName('que');
	$oFormP->setTitulo(core\strtoupper_dlb(_("periodo de selección de actividades")));
	$oFormP->setPosiblesPeriodos($aOpciones);
	$oFormP->setDesplAnysOpcion_sel($Qyear);
	$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
	$oFormP->setBoton($boton);
	$oHashPeriodo = new web\Hash();
	$oHashPeriodo->setCamposForm('empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
	$oHashPeriodo->setCamposNo('!refresh');
	$a_camposHiddenP = array( 'continuar' => 1,
			);
	$oHashPeriodo->setArraycamposHidden($a_camposHiddenP);

	$a_campos = array('mod'=>'inicio',
					'oFormP' => $oFormP,
					'oHashPeriodo' => $oHashPeriodo,
 				);

 } else {
	//periodo
	if (empty($Qperiodo) || $Qperiodo == 'otro') {
		$any=  $_SESSION['oConfig']->any_final_curs('est');
		$Qempiezamin=core\curso_est("inicio",$any,"est")->format('Y-m-d');
		$Qempiezamax=core\curso_est("fin",$any,"est")->format('Y-m-d');
		$Qperiodo = 'curso_ca';
		$inicio = empty($Qinicio)? $Qempiezamin : $Qinicio;
		$fin = empty($Qfin)? $Qempiezamax : $Qfin;
	} else {
		$oPeriodo = new web\Periodo();
		$any=empty($Qyear)? date('Y')+1 : $Qyear;
		$oPeriodo->setAny($any);
		$oPeriodo->setPeriodo($Qperiodo);
		$inicio = $oPeriodo->getF_ini_iso();
		$fin = $oPeriodo->getF_fin_iso();
	}

	$aWhere = [];
	$aOperador = [];
	$aWhere['f_ini'] = "'$inicio','$fin'";
	$aOperador['f_ini'] = 'BETWEEN';
	$aWhere['status'] = 3;
	
	$mi_sfsv = core\ConfigGlobal::mi_sfsv();
	//$id_tipo='^'.$mi_sfsv.'[123][23]';   // OJO AÑADO sem inv.
	$id_tipo='^'.$mi_sfsv.'[123][23]5*';
	$id_tipo_inv='^'.$mi_sfsv.'[123][23]5';
	$aWhere['id_tipo_activ'] = $id_tipo;
	$aOperador['id_tipo_activ'] = '~';
	$GesActividades = new actividades\GestorActividadDl();	
	$cActividades = $GesActividades->getActividades($aWhere,$aOperador);
    $ini_d = $_SESSION['oConfig']->getDiaIniStgr();
    $ini_m = $_SESSION['oConfig']->getMesIniStgr();
	// busco los profesores que han dado alguna asignatura en actividad.	
	$GesProfesorDocencia = new profesores\GestorProfesorDocenciaStgr();
	foreach ($cActividades as $oActividad) {
		$id_activ = $oActividad->getId_activ();
		$id_tipo_activ = $oActividad->getId_tipo_activ();
		$oFini = $oActividad->getF_ini();
		$mes = $oFini->format('m');
		$any = $oFini->format('Y');
		if ($mes < $ini_m) {
			$ini_a = $any - 1;
		} else {
			$ini_a = $any;
		}
		$GesAsignaturasCa = new actividadestudios\GestorActividadAsignaturaDl();
		$cActivAsignaturas = $GesAsignaturasCa->getActividadAsignaturas(array('id_activ'=>$id_activ),array('id_profesor'=>'IS NOT NULL'));
		
		foreach ($cActivAsignaturas as $oActividadAsignatura) {
			$id_asignatura = $oActividadAsignatura->getId_asignatura();
			$id_profesor = $oActividadAsignatura->getId_profesor();
			if (empty($id_profesor)) {
				continue;
			}
			$tipo = $oActividadAsignatura->getTipo();
			// si no es con preceptor, pongo ca o inv
			if (empty($tipo)) {
				$tipo = actividadestudios\ActividadAsignatura::TIPO_CA;
				if (preg_match("/$id_tipo_inv/", $id_tipo_activ)) { // semestre de invierno (ca agd)
					$tipo = actividadestudios\ActividadAsignatura::TIPO_INV;
				}
			}

			$GesActas = new notas\GestorActa();
			$cActas = $GesActas->getActas(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura));
			if (is_array($cActas) && count($cActas) > 0) {
				$acta = '';
				foreach ($cActas as $oActa) {
					$acta .= empty($acta)? '' : ', ';
					$acta .= $oActa->getActa();
				}
			} else {
				$acta = '';	
			}
			
			// Puede que ya lo tenga:
			$aWhereDocencia = ['id_nom'=>$id_profesor, 'id_activ'=>$id_activ, 'id_asignatura' => $id_asignatura];
			$cProfesorDocencia = $GesProfesorDocencia->getProfesorDocenciasStgr($aWhereDocencia);
			if (is_array($cProfesorDocencia) && count($cProfesorDocencia) > 0) {
				$oProfesorDocencia = $cProfesorDocencia[0];
				$oProfesorDocencia->setCurso_inicio($ini_a);
				$oProfesorDocencia->setTipo($tipo);
				$oProfesorDocencia->setActa($acta);
				$oProfesorDocencia->DBGuardar();
			} else {
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
	}

	$a_campos = array('mod'=>'fin',
				);
}

$oView = new core\View('actividadestudios/controller');
echo $oView->render('actualizar_docencia.phtml',$a_campos);