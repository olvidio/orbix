<?php

use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use asignaturas\model\entity as asignaturas;
use core\ConfigGlobal;
use personas\model\entity as personas;
use web\Hash;
use web\Lista;
use web\Posicion;
use function core\curso_est;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/* Pongo en la variable $curso el periodo del curso */
/*
	$mes=date('m');
$any=date('Y');
if ($mes>9) { $any=$any+1; } 
$inicurs = curso_est("inicio",$any);
$fincurs = curso_est("fin",$any);

$inicio = $inicurs;
$fin = $fincurs;
*/	
	
//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
} 
$oPosicion->recordar();

$aviso = '';
$form = '';
$traslados = '';
$Qmod = (string) \filter_input(INPUT_POST, 'mod');
$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
$Qfin = (string) \filter_input(INPUT_POST, 'fin');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');

//periodo
if (empty($Qperiodo) || $Qperiodo == 'otro') {
	$any=  core\ConfigGlobal::any_final_curs('est');
	$Qempiezamin=core\curso_est("inicio",$any,"est");
	$Qempiezamax=core\curso_est("fin",$any,"est");
	$Qperiodo = 'curso_ca';
	$inicio = empty($Qinicio)? $Qempiezamin : $Qinicio;
	$fin = empty($Qfin)? $Qempiezamax : $Qfin;
} else {
	$oPeriodo = new web\Periodo();
	$any=empty($Qyear)? date('Y')+1 : $Qyear;
	$oPeriodo->setAny($any);
	$oPeriodo->setPeriodo($Qperiodo);
	$inicio = $oPeriodo->getF_ini();
	$fin = $oPeriodo->getF_fin();
}

$aWhereActividad['f_ini'] = "'$inicio','$fin'";
$aOperadorActividad['f_ini'] = 'BETWEEN';

$gesActividades = new actividades\GestorActividad();
$a_IdActividades = $gesActividades->getArrayIds($aWhereActividad,$aOperadorActividad);

$str_actividades = "{".implode(', ',$a_IdActividades)."}";
$aWhere = ['id_activ' => $str_actividades];
$aOperador = ['id_activ' => 'ANY'];

$gesMatriculasDl = new actividadestudios\gestorMatriculaDl();
$cMatriculas = $gesMatriculasDl->getMatriculas($aWhere,$aOperador);

$titulo = _(sprintf("Lista de matrículas en el periodo: %s - %s.",$inicio,$fin)); 
$a_botones=array(
			array( 'txt' => _("ver asignaturas ca"), 'click' =>"fnjs_ver_ca(this.form)" ) ,
			array( 'txt' => _("borrar matrícula"), 'click' =>"fnjs_borrar(this.form)" ) 
);

$a_cabeceras=array(
					_("alumno"),
					_("ctr"),
					_("dl"),
					_("actividad"),
					_("asignatura"),
					_("preceptor"),
					_("nota")
	);

$i=0;
$a_valores=array();
$msg_err = '';
foreach ($cMatriculas as $oMatricula) {
	$i++;
	$id_nom=$oMatricula->getId_nom();
	$id_activ=$oMatricula->getId_activ();
	$id_asignatura=$oMatricula->getId_asignatura();
	$nota_num = $oMatricula->getNota_num();
	$nota_max = $oMatricula->getNota_max();
	$nota_txt = empty($nota_num)? '' : "$nota_num/$nota_max";
	$preceptor=$oMatricula->getPreceptor();
	if ($preceptor == "t") { 
		$preceptor="x"; 
		$id_preceptor=$oMatricula->getId_preceptor();
		if (!empty($id_preceptor)) {
			$oPersona = personas\Persona::newPersona($id_preceptor);
			if (!is_object($oPersona)) {
				$msg_err .= "<br>preceptor: $oPersona con id_nom: $id_preceptor en  ".__FILE__.": line ". __LINE__;
			} else {
				$preceptor = $oPersona->getApellidosNombre();
			}
		}
	} else {
		$preceptor="";
	}

	//echo "id_activ: $id_activ<br>";
	//echo "id_asignatura: $id_asignatura<br>";

	$oActividad = new actividades\Actividad($id_activ);
	$nom_activ = $oActividad->getNom_activ();
	$f_ini = $oActividad->getF_ini();
	
	$oPersona = personas\Persona::newPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
		continue;
	}
	$apellidos_nombre = $oPersona->getApellidosNombre();
	$ctr = $oPersona->getCentro_o_dl();
	$dl = $oPersona->getDl();
			
	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$nombre_corto = $oAsignatura->getNombre_corto();
	
	$a_valores[$i]['sel']="$id_activ#$id_asignatura#$id_nom";
	$a_valores[$i][1]=$apellidos_nombre;
	$a_valores[$i][2]=$ctr;
	$a_valores[$i][3]=$dl;
	$a_valores[$i][4]=$nom_activ;
	$a_valores[$i][5]=$nombre_corto;
	$a_valores[$i][6]=$preceptor;
	$a_valores[$i][7]=$nota_txt;

	$a_Nombre[$i] = $apellidos_nombre;
	$a_Fecha[$i] = $f_ini;
	$a_Asignatura[$i] = $nombre_corto;
}

// ordenar por alumno, asignatura:
if (!empty($a_valores)) {
	array_multisort(
			$a_Nombre, SORT_STRING,
			$a_Asignatura, SORT_STRING,
			$a_valores);
}
//OJO!! hay añadirlos después de ordenar. 
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }

$oHash = new Hash();
$oHash->setCamposNo('sel!mod!pau!scroll_id');
$a_camposHidden = array(
		'id_dossier' => 3005,
		'permiso' => 3,
		'obj_pau' => 'Actividad',
		'queSel' => 'asig',
		);
$oHash->setArraycamposHidden($a_camposHidden);

if (!empty($msg_err)) { echo $msg_err; }

$oTabla = new Lista();
$oTabla->setId_tabla('mtr_pdte');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$txt_eliminar = _("¿Está seguro que desea borrar todas las matrículas seleccionadas?"); 

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
$a_camposHiddenP = array(
		);
$oHashPeriodo->setArraycamposHidden($a_camposHiddenP);

$a_campos = ['oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'mod' => $Qmod,
			'oTabla' => $oTabla,
			'titulo' => $titulo,
			'aviso' => $aviso,
			'txt_eliminar' => $txt_eliminar,
			'oFormP' => $oFormP,
			'oHashPeriodo' => $oHashPeriodo,
			];

$oView = new core\View('actividadestudios/controller');
echo $oView->render('matriculas.phtml',$a_campos);