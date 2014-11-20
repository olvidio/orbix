<?php
/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ('classes/actividades/d_asignaturas_activ.class');
	require_once ('classes/actividades/e_actas_gestor.class');
	require_once ('classes/activ-personas/xa_asignaturas.class');
	require_once ('classes/activ-personas/d_matriculas_activ_gestor.class');
	require_once ('classes/personas/e_notas_gestor.class');
	require_once ('classes/personas/e_notas_situacion_gestor.class');

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once(ConfigGlobal::$dir_programas.'/func_web.php');


if ($_POST['que']==3) { //paso las matrículas a notas definitivas
	$aNivelOpcionales = array(1230,1231,1232,2430,2431,2432,2433,2434);
	$GesNotas  = new GestorNota();
	$aIdSuperadas = $GesNotas->getArrayNotasSuperadas();
	// miro el acta
	$GesActas = new GestorActa();
	$cActas = $GesActas->getActas(array('id_activ'=>$_POST['id_activ'],'id_asignatura'=>$_POST['id_asignatura']));

	if (is_array($cActas) && count($cActas) == 1) {
		$acta=$cActas[0]->getActa();
		$f_acta=$cActas[0]->getF_acta();
		if (!$acta || !$f_acta) $error=sprintf(_("debe introducir los datos del acta")."\n");
	} else {
		$error=sprintf(_("debe introducir los datos del acta. No se ha guardado nada.")."\n");
	}
	if (!empty($error)) exit($error);

	$GesMatriculas = new GestorMatricula();
	$cMatriculados = $GesMatriculas->getMatriculas(array('id_asignatura'=>$_POST['id_asignatura'], 'id_activ'=>$_POST['id_activ']));
	$i=0;
	foreach ($cMatriculados as $oMatricula) {
		$i++;
		$id_nom=$oMatricula->getId_nom();
		$id_situacion=$oMatricula->getId_situacion();
		$preceptor=$oMatricula->getPreceptor();
		
		// Sólo grabo si está superada.
		if (!in_array($id_situacion,$aIdSuperadas)) continue;
				
		if (!empty($preceptor)) { //miro cuál
			$oActividadAsignatura = new ActividadAsignatura(array('id_activ'=>$_POST['id_activ'],'id_asignatura'=>$_POST['id_asignatura'])); 
			$id_preceptor = $oActividadAsignatura->getId_profesor();
		} else {
			$id_preceptor = '';
		}
		
		//Si es una opcional miro el id nivel para cada uno
		if ($_POST['id_asignatura'] > 3000) {
			switch (substr($_POST['id_asignatura'],1,1)) {
				case 1:	// sólo de bienio
					$aWhere['id_nivel'] = "123.";
					$aOperadores['id_nivel'] = '~';
					//$cc="id_nivel::text ~ '123.'";
					$op_min=0;
					$op_max=2;
					break;
				case 2:	// sólo de caudrienio
					$aWhere['id_nivel'] = "243.";
					$aOperadores['id_nivel'] = '~';
					//$cc="id_nivel::text ~ '243.'";
					$op_min=3;
					$op_max=7;
					break;
				default:
					$aWhere['id_nivel'] = "[12|24]3.";
					$aOperadores['id_nivel'] = '~';
					//$cc="id_nivel::text ~ '[12|24]3.'";
					$op_min=0;
					$op_max=7;
			}
			$GesPersonaNotas = new GestorPersonaNota();
			$aWhere['id_nom'] = $id_nom;
			$aWhere['_ordre'] = 'id_nivel DESC';
			$cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhere,$aOperadores);
			$id_op = '';
			$aOpSuperadas = array();
			$j=0;
			foreach ($cPersonaNotas as $oPersonaNota) {
				$j++;
				$id_op = $oPersonaNota->getId_nivel();
				$id_situacion = $oPersonaNota->getId_situacion();
				// compruebo que el id_situacion corresponde a 'superada'
				if (in_array($id_situacion,$aIdSuperadas)) $aOpSuperadas[$j] = $id_op;
			}
			for ($op=$op_min;$op<=$op_max;$op++) {
				$id_nivel = $aNivelOpcionales[$op];
				if (!in_array($id_nivel,$aOpSuperadas)) break;
			}
			//if ($nivel > $aNivelOpcionales[$op_max]) { $error.=sprintf (_("ha cursado una opcional que no tocaba (id_nom=%s)")."\n",$id_nom); continue; }
		} else {
			$oAsignatura = new Asignatura($_POST['id_asignatura']);
			$id_nivel = $oAsignatura->getId_nivel();
		}
			
		$oPersonaNota = new PersonaNota(array('id_nom'=>$id_nom,'id_asignatura'=>$_POST['id_asignatura']));
		
		//compruebo que no existe ya la nota:
		//	- si existe y es en mismo id_activ, actualizo
		//  - si existe en otro id_activ, AVISO!!
		//
		$id_activ_old = $oPersonaNota->getId_activ();
		if (!empty($id_activ_old) && ($_POST['id_activ'] != $id_activ_old)) {
			//aviso
			$error.=sprintf (_("está intentando poner una nota que ya existe (id_nom=%s)")."\n",$id_nom);
			continue;
		} else {
			// guardo los datos
			$oPersonaNota->setId_nivel($id_nivel);
			$oPersonaNota->setId_situacion($id_situacion);
			$oPersonaNota->setActa($acta);
			$oPersonaNota->setF_acta($f_acta);
			$oPersonaNota->setId_activ($_POST['id_activ']);
			$oPersonaNota->setPreceptor($preceptor);
			$oPersonaNota->setId_preceptor($id_preceptor);
			if ($oPersonaNota->DBGuardar() === false) {
				echo _('Hay un error, no se ha guardado');
			}
		}
	}
	$go_to="acta_imprimir.php?acta=$acta|main";
}

if ($_POST['que']==1) {
	for ($n=0;$n<$_POST['matriculados'];$n++) {
		if (!empty($_POST['form_preceptor'][$n]) && $_POST['form_preceptor'][$n]=="p") { $preceptor="t"; } else { $preceptor="f"; }
		$oMatricula = new Matricula(array('id_asignatura'=>$_POST['id_asignatura'],'id_activ'=>$_POST['id_activ'],'id_nom'=>$_POST['id_nom'][$n]));
		$oMatricula->setId_situacion($_POST['id_situacion'][$n]);
		$oMatricula->setPreceptor($preceptor);
		if ($oMatricula->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
	}
	//$go_to="acta_notas.php?id_asignatura=".$_POST['id_asignatura']."&id_activ=".$_POST['id_activ'];
	$go_to = '';
}

//vuelve a la presentacion de la ficha.
if (empty($error)) {
   if (!empty($go_to)) {
		$go_to=urlencode($go_to);
		//echo "gou: $go_to<br>";
		ir_a($go_to);
   }
} else {
	echo $error;
}
?>
