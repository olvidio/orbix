<?php
use asignaturas\model\entity as asignaturas;
use actividadestudios\model\entity as actividadestudios;
use notas\model\entity as notas;
use personas\model\entity as personas;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qid_asignatura = (integer) \filter_input(INPUT_POST, 'id_asignatura');
$Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');

if ($Qque==3) { //paso las matrículas a notas definitivas (Grabar e imprimir)
	$aNivelOpcionales = array(1230,1231,1232,2430,2431,2432,2433,2434);
	$error = '';
	$GesNotas  = new notas\GestorNota();
	//$aIdSuperadas = $GesNotas->getArrayNotasSuperadas();
	// miro el acta
	$GesActas = new notas\GestorActa();
	$cActas = $GesActas->getActas(array('id_activ'=>$Qid_activ,'id_asignatura'=>$Qid_asignatura));

	$GesMatriculas = new actividadestudios\GestorMatricula();
	$cMatriculados = $GesMatriculas->getMatriculas(array('id_asignatura'=>$Qid_asignatura, 'id_activ'=>$Qid_activ));
	$i=0;
	$msg_err = '';
	foreach ($cMatriculados as $oMatricula) {
		$i++;
		$id_nom=$oMatricula->getId_nom();
		// para saber a que schema pertenece la persona
		$oPersona = personas\Persona::NewPersona($id_nom);
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
			continue;
		}
		$id_schema = $oPersona->getId_schema();
		$id_situacion=$oMatricula->getId_situacion();
		$preceptor=$oMatricula->getPreceptor();
		$nota_num=$oMatricula->getNota_num();
		$nota_max=$oMatricula->getNota_max();
		$acta=$oMatricula->getActa();

		if (empty($nota_max)) {
			$nota_max = 10;
		}
		// Si es con precptor no se acepta cursado o examinado.
		if ($preceptor)	{
			// Acepto nota_num=0 para borrar.
			if (!empty($nota_num) && $nota_num/$nota_max < 0.6) {
				$nn = $nota_num/$nota_max * 10;
				// Ahora si la gurado como examinado
				$error .= sprintf(_("nota no guardada para %s porque la nota (%s) no llega al mínimo: 6"),$oPersona->getNombreApellidos(),$nn)."\n";
				continue;
			}
			if ($acta == 2 ) {
				$error .= sprintf(_("no se puede definir cursada con preceptor")."\n");
				exit($error);
			}
			if (!empty($nota_num)) { // Si esta vacio, es para borrar, no tiene acta.
                $oActa =new notas\Acta($acta);
                $f_acta=$oActa->getF_acta()->getFromLocal();
                if (!$acta || !$f_acta) {
                    $error .= sprintf(_("debe introducir los datos del acta. No se ha guardado nada.")."\n");
                    exit($error);
                }
			}
		} else {
			// para las cursadas o examinadas no aprobadas
			if ($id_situacion == 2 OR $id_situacion == 12 OR empty($id_situacion)) {
				//conseguir una fecha para poner como fecha acta. las cursadas se guardan durante 2 años
				$f_acta = $cActas[0]->getF_acta()->getFromLocal();
			} else {
				if (!$acta) {
					$error .= sprintf(_("falta definir el acta para alguna nota")."\n");
					exit($error);
				}
				$oActa =new notas\Acta($acta);
				$f_acta=$oActa->getF_acta()->getFromLocal();
				if (!$acta || !$f_acta) {
					$error .= sprintf(_("debe introducir los datos del acta. No se ha guardado nada.")."\n");
					exit($error);
				}
			}
			// Acepto nota_num=0 para borrar.
			if (!empty($nota_num) && $nota_num/$nota_max < 0.6) {
				$nn = $nota_num/$nota_max * 10;
				$id_situacion = 12; // examinado
			}
		}
				
		if (!empty($preceptor)) { //miro cuál
			$oActividadAsignatura = new actividadestudios\ActividadAsignaturaDl(array('id_activ'=>$Qid_activ,'id_asignatura'=>$Qid_asignatura)); 
			$id_preceptor = $oActividadAsignatura->getId_profesor();
		} else {
			$id_preceptor = '';
		}
		
		//Si es una opcional miro el id nivel para cada uno
		if ($Qid_asignatura > 3000) {
			switch (substr($Qid_asignatura,1,1)) {
			    /* Ahora las opcionales son indiferentes a bienio/cuadrienio
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
                */
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
				$id_asignatura = $oPersonaNota->getId_asignatura();
				if ($id_asignatura ==$Qid_asignatura) { // ya está la que intento meter => actualizar
					$id_nivel = $id_op;
					break;
				}
				// compruebo que corresponde a 'superada'
				if ($nota_num/$nota_max >= 0.6)  $aOpSuperadas[$j] = $id_op;
			}
			if (empty($id_nivel)) {
				for ($op=$op_min;$op<=$op_max;$op++) {
					$id_nivel = $aNivelOpcionales[$op];
					if (!in_array($id_nivel,$aOpSuperadas)) break;
				}
			}
			if ($id_nivel > $aNivelOpcionales[$op_max]) { $error.=sprintf (_("ha cursado una opcional que no tocaba (id_nom=%s)")."\n",$id_nom); continue; }
		} else {
			$oAsignatura = new asignaturas\Asignatura($Qid_asignatura);
			$id_nivel = $oAsignatura->getId_nivel();
		}
			
		//compruebo que no existe ya la nota:
		//	- si existe y es en mismo id_activ, actualizo
		//  - si existe en otro id_activ, AVISO!!
		//
		$id_activ_old = 0;
		$oGesPersonaNota = new notas\GestorPersonaNota();
		$cBuscarPersonaNotas = $oGesPersonaNota->getPersonaNotas(array('id_nom'=>$id_nom,'id_asignatura'=>$Qid_asignatura));
		if (!empty($cBuscarPersonaNotas)) {
			$oPersonaNota = $cBuscarPersonaNotas[0];
			$id_activ_old = $oPersonaNota->getId_activ();
		}

		if (!empty($id_activ_old) && ($Qid_activ != $id_activ_old)) {
			//aviso
			$error.=sprintf (_("está intentando poner una nota que ya existe (id_nom=%s)")."\n",$id_nom);
			continue;
		} else {
			// para borrar	(empty($nota_num))
			if (!empty($id_activ_old) && ($Qid_activ == $id_activ_old) && empty($nota_num)) {
				if ($id_situacion != 2 && $id_situacion != 12) {
					$oPersonaNota->DBEliminar();
					continue;
				}
			} 
			// Ahora guardo si la ha cursado o examinado
			if (empty($id_situacion)) {
				if (empty($nota_num)) {
					$id_situacion = 2;
				}
				if (!empty($nota_num) && $nota_num/$nota_max < 0.6) {
					$id_situacion = 12;
				} else {
					$id_situacion = 10;
				}
			}
			$oPersonaNota = new notas\PersonaNota(array('id_nom'=>$id_nom,'id_asignatura'=>$Qid_asignatura));
			// guardo los datos
			$oPersonaNota->setId_schema($id_schema);
			$oPersonaNota->setId_nivel($id_nivel);
			$oPersonaNota->setId_situacion($id_situacion);
			$oPersonaNota->setActa($acta);
			$oPersonaNota->setF_acta($f_acta);
			$oPersonaNota->setId_activ($Qid_activ);
			$oPersonaNota->setPreceptor($preceptor);
			$oPersonaNota->setId_preceptor($id_preceptor);
			$oPersonaNota->setNota_num($nota_num);
			$oPersonaNota->setNota_max($nota_max);
			$oPersonaNota->setTipo_acta(notas\PersonaNota::FORMATO_ACTA);
			if ($oPersonaNota->DBGuardar() === false) {
				echo _("hay un error, no se ha guardado");
			}
		}
	}
	$go_to=core\ConfigGlobal::getWeb()."/apps/notas/controller/acta_imprimir.php?acta=$acta|main";
}

if ($Qque==1) { // Grabar las notas en la matricula
	$Qform_preceptor = (array) \filter_input(INPUT_POST, 'form_preceptor', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	$Qid_nom = (array) \filter_input(INPUT_POST, 'id_nom', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	$Qnota_num = (array) \filter_input(INPUT_POST, 'nota_num', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	$Qnota_max = (array) \filter_input(INPUT_POST, 'nota_max', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	$Qacta = (array) \filter_input(INPUT_POST, 'acta_nota', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

	$num_alumnos = count($Qid_nom);
	$num_alumnos = empty($num_alumnos)? 0 : $num_alumnos;
	
	for ($n=0;$n<$num_alumnos;$n++) {
		if (!empty($Qform_preceptor[$n]) && $Qform_preceptor[$n]=="p") { 
			$preceptor="t";
		} else {
			$preceptor="f";
		}
		$oMatricula = new actividadestudios\Matricula(array('id_asignatura'=>$Qid_asignatura,'id_activ'=>$Qid_activ,'id_nom'=>$Qid_nom[$n]));
		$oMatricula->setPreceptor($preceptor);
		// admitir coma y punto como separador decimal
		$nn = str_replace(',', '.', $Qnota_num[$n]);
		$oMatricula->setNota_num($nn);
		$oMatricula->setNota_max($Qnota_max[$n]);
		$oMatricula->setActa($Qacta[$n]);
		// cursada o examinada para el caso sin preceptor
		if ($preceptor == 'f') {
			if ($Qacta[$n] == 2) {
				$oMatricula->setId_situacion(2);
				// examinada
				if ($Qnota_num[$n] > 1) $oMatricula->setId_situacion(12);
			} elseif ($Qnota_num[$n] > 1) {
				if (!empty($Qnota_num[$n]) && $Qnota_num[$n]/$Qnota_max[$n] < 0.6) {
					// examinado
					$oMatricula->setId_situacion(12);
				} else {
					// aprobada
					$oMatricula->setId_situacion(10);
				}
			}
		} else {
			if ($Qacta[$n] == 2 && $preceptor == true) {
				$error = sprintf(_("no se puede definir cursada con preceptor")."\n");
				exit($error);
			}
			if (empty($Qnota_num[$n])) {
				$oMatricula->setId_situacion(0);
			} else {
				$oMatricula->setId_situacion(10);
			}
		}
		if ($oMatricula->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
		}
	}
	$go_to = '';
}

if (!empty($msg_err)) { echo $msg_err; }
//vuelve a la presentacion de la ficha.
if (empty($error)) {
   if (!empty($go_to)) {
		$go_to=urlencode($go_to);
		//echo "gou: $go_to<br>";
//		echo $oPosicion->ir_a($go_to);
   }
} else {
	echo $error;
	echo "\n";
	
//	$go_avant = web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/acta_imprimir.php?'.http_build_query(array('acta'=>$acta)));
//	echo "<input type='button' onclick=fnjs_update_div('#main','".$go_avant."') value="._("continuar").">";

}