<?php
use asignaturas\model as asignaturas;
use actividadestudios\model as actividadestudios;
use notas\model as notas;
use personas\model as personas;
/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


if ($_POST['que']==3) { //paso las matrículas a notas definitivas (Grabar e imprimir)
	$aNivelOpcionales = array(1230,1231,1232,2430,2431,2432,2433,2434);
	$error = '';
	$GesNotas  = new notas\GestorNota();
	//$aIdSuperadas = $GesNotas->getArrayNotasSuperadas();
	// miro el acta
	$GesActas = new notas\GestorActa();
	$cActas = $GesActas->getActas(array('id_activ'=>$_POST['id_activ'],'id_asignatura'=>$_POST['id_asignatura']));

	if (is_array($cActas) && count($cActas) == 1) {
		$acta=$cActas[0]->getActa();
		$f_acta=$cActas[0]->getF_acta();
		if (!$acta || !$f_acta) $error .= sprintf(_("debe introducir los datos del acta")."\n");
	} else {
		$error .= sprintf(_("debe introducir los datos del acta. No se ha guardado nada.")."\n");
	}
	if (!empty($error)) exit($error);

	$GesMatriculas = new actividadestudios\GestorMatricula();
	$cMatriculados = $GesMatriculas->getMatriculas(array('id_asignatura'=>$_POST['id_asignatura'], 'id_activ'=>$_POST['id_activ']));
	$i=0;
	$msg_err = '';
	foreach ($cMatriculados as $oMatricula) {
		$i++;
		$id_nom=$oMatricula->getId_nom();
		// para saber a que schema pertenece la persona
		$oPersona = personas\Persona::NewPersona($id_nom);
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_nom";
			continue;
		}
		$id_schema = $oPersona->getId_schema();
		$id_situacion=$oMatricula->getId_situacion();
		$preceptor=$oMatricula->getPreceptor();
		$nota_num=$oMatricula->getNota_num();
		$nota_max=$oMatricula->getNota_max();
		
		// Sólo grabo si está superada.
		//if (!in_array($id_situacion,$aIdSuperadas)) continue;
		if ($nota_num/$nota_max < 0.6) {
			$nn = $nota_num/$nota_max * 10;
			$error .= sprintf(_("nota no guardada para %s porque la nota (%s) no llega al mínimo: 6"),$oPersona->getNombreApellidos(),$nn);
			continue;
		}
				
		if (!empty($preceptor)) { //miro cuál
			$oActividadAsignatura = new actividadestudios\ActividadAsignatura(array('id_activ'=>$_POST['id_activ'],'id_asignatura'=>$_POST['id_asignatura'])); 
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
			$GesPersonaNotas = new notas\GestorPersonaNota();
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
				//if (in_array($id_situacion,$aIdSuperadas)) $aOpSuperadas[$j] = $id_op;
				if ($nota_num/$nota_max >= 0.85)  $aOpSuperadas[$j] = $id_op;
			}
			for ($op=$op_min;$op<=$op_max;$op++) {
				$id_nivel = $aNivelOpcionales[$op];
				if (!in_array($id_nivel,$aOpSuperadas)) break;
			}
			//if ($nivel > $aNivelOpcionales[$op_max]) { $error.=sprintf (_("ha cursado una opcional que no tocaba (id_nom=%s)")."\n",$id_nom); continue; }
		} else {
			$oAsignatura = new asignaturas\Asignatura($_POST['id_asignatura']);
			$id_nivel = $oAsignatura->getId_nivel();
		}
			
		$oPersonaNota = new notas\PersonaNota(array('id_nom'=>$id_nom,'id_asignatura'=>$_POST['id_asignatura']));
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
			if ($nota_num > 1) $id_situacion = 10;
			// guardo los datos
			$oPersonaNota->setId_schema($id_schema);
			$oPersonaNota->setId_nivel($id_nivel);
			$oPersonaNota->setId_situacion($id_situacion);
			$oPersonaNota->setActa($acta);
			$oPersonaNota->setF_acta($f_acta);
			$oPersonaNota->setId_activ($_POST['id_activ']);
			$oPersonaNota->setPreceptor($preceptor);
			$oPersonaNota->setId_preceptor($id_preceptor);
			$oPersonaNota->setNota_num($nota_num);
			$oPersonaNota->setNota_max($nota_max);
			if ($oPersonaNota->DBGuardar() === false) {
				echo _('Hay un error, no se ha guardado');
			}
		}
	}
	$go_to=core\ConfigGlobal::getWeb()."/apps/notas/controller/acta_imprimir.php?acta=$acta|main";
}

if ($_POST['que']==1) { // Grabar las notas en la matricula
	for ($n=0;$n<$_POST['matriculados'];$n++) {
		if (!empty($_POST['form_preceptor'][$n]) && $_POST['form_preceptor'][$n]=="p") { $preceptor="t"; } else { $preceptor="f"; }
		$oMatricula = new actividadestudios\Matricula(array('id_asignatura'=>$_POST['id_asignatura'],'id_activ'=>$_POST['id_activ'],'id_nom'=>$_POST['id_nom'][$n]));
		$oMatricula->setPreceptor($preceptor);
		$oMatricula->setNota_num($_POST['nota_num'][$n]);
		$oMatricula->setNota_max($_POST['nota_max'][$n]);
		if ($_POST['nota_num'][$n] > 1) $oMatricula->setId_situacion(10);
		if ($oMatricula->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
	}
	//$go_to="acta_notas.php?id_asignatura=".$_POST['id_asignatura']."&id_activ=".$_POST['id_activ'];
	$go_to = '';
}

if (!empty($msg_err)) { echo $msg_err; }
//vuelve a la presentacion de la ficha.
if (empty($error)) {
   if (!empty($go_to)) {
		$go_to=urlencode($go_to);
		//echo "gou: $go_to<br>";
		echo $oPosicion->ir_a($go_to);
   }
} else {
	echo $error;
	echo "<br>";
	
	$go_avant = web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/acta_imprimir.php?'.http_build_query(array('acta'=>$acta)));
	echo "<input type='button' onclick=fnjs_update_div('#main','".$go_avant."') value="._('continuar').">";


}
?>
