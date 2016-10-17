<?php
use actividades\model as actividades;
use actividadestudios\model as actividadestudios;
use asignaturas\model as asignaturas;
use notas\model as notas;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$id_asignatura_real = '';

if (!empty($_POST['sel'])) { //vengo de un checkbox
	if ($_POST['pau']=="p") {
	   	$id_activ = strtok($_POST['sel'][0],'#'); 
		$id_activ = !empty($_POST['id_activ'])? $_POST['id_activ'] : $id_activ;
		$id_asignatura_real = strtok('#'); 
		$id_nom = strtok('#'); 
		$id_nom = !empty($_POST['id_pau'])? $_POST['id_pau'] : $id_nom;
   	}
}

$go_to = empty($_POST['go_to'])? '' : $_POST['go_to'];

$_POST['opcional'] = empty($_POST['opcional'])? '': $_POST['opcional'];

$oActividad = new actividades\Actividad($_POST['id_activ']);
$nom_activ = $oActividad->getNom_activ();

if (!empty($id_asignatura_real)) { //caso de modificar
	$mod="editar";
	$oMatricula = new actividadestudios\Matricula(array('id_nom'=>$_POST['id_pau'],'id_activ'=>$_POST['id_activ'],'id_asignatura'=>$id_asignatura_real));
	$id_situacion=$oMatricula->getId_situacion();
	$preceptor=$oMatricula->getPreceptor();
	$oAsignatura = new asignaturas\Asignatura($id_asignatura_real);
	$nombre_corto=$oAsignatura->getNombre_corto();
	$id_nivel=$id_asignatura_real;
	$id_asignatura=$id_asignatura_real;
	$primary_key_s=serialize(array('id_nom'=>$_POST['id_pau'],'id_activ'=>$_POST['id_activ'],'id_asignatura'=>$id_asignatura_real));
} else { //caso de nueva asignatura
	$mod="nuevo";	
	// asignaturas posibles
	$GesAsignaturas = new asignaturas\GestorAsignatura();
	$cAsignaturas = $GesAsignaturas->getAsignaturas(array('id_nivel'=>3000,'status'=>'t','_ordre'=>'id_nivel'),array('id_nivel'=>'<'));
	// quito las ya cursadas
	$GesNotas = new notas\GestorNota();
	$cNotas = $GesNotas->getNotas(array('superada'=>'t'));
	$aSuperadas = array();
	foreach ($cNotas as $oNota) {
		$aSuperadas[] = $oNota->getId_situacion();
	}
	$ac=0;
	foreach ($cAsignaturas as $oAsignatura) {
		$id_asignatura = $oAsignatura->getId_asignatura();
		$id_nivel = $oAsignatura->getId_nivel();
		$GesPersonaNota = new notas\GestorPersonaNota();
		$cPersonaNota = $GesPersonaNota->getPersonaNotas(array('id_nom'=>$_POST['id_pau'],'id_nivel'=>$id_nivel));
		if (is_array($cPersonaNota) && count($cPersonaNota) == 1) {
			$id_situacion = $cPersonaNota[0]->getId_situacion();
			if (in_array($id_situacion,$aSuperadas)) {
				// la borro de la lista
				unset($cAsignaturas[$ac]);
			}
		}
		$ac++;
	}
	// quito las ya matriculadas
	$GesMatriculas = new actividadestudios\GestorMatriculaDl();
	$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom'=>$_POST['id_pau'],'id_activ'=>$_POST['id_activ']));
	//lista ids asignaturas posibles
	$a_PosiblesAsignaturas = array();
	foreach($cAsignaturas as $n=>$oAsignatura){
		$id_nivel = $oAsignatura->getId_nivel();
		$a_PosiblesAsignaturas[$id_nivel] = $n;
	}
	foreach ($cMatriculas as $oMatricula) {
		$id_asignatura=$oMatricula->getId_asignatura();
		$id_nivel = $oMatricula->getId_nivel();
		// la borro de la lista
		if (array_key_exists($id_nivel,$a_PosiblesAsignaturas)) {
			$n = $a_PosiblesAsignaturas[$id_nivel];
			unset($cAsignaturas[$n]);
		}
	}
	// Lo mismo para las opcionales
	if (!empty($_POST['opcional'])) {
		$GesAsignaturasOp = new asignaturas\GestorAsignatura();
		$cAsignaturasOp = $GesAsignaturasOp->getAsignaturas(array('id_nivel'=>'3000,5000','status'=>'t','_ordre'=>'nombre_corto'),array('id_nivel'=>'BETWEEN'));
		// quito las ya cursadas
		$GesNotas = new notas\GestorNota();
		$cNotas = $GesNotas->getNotas(array('superada'=>'t'));
		$aSuperadas = array();
		foreach ($cNotas as $oNota) {
			$aSuperadas[] = $oNota->getId_situacion();
		}
		$acop=0;
		foreach ($cAsignaturasOp as $oAsignatura) {
			$id_asignatura = $oAsignatura->getId_asignatura();
			$oPersonaNota = new notas\PersonaNota(array('id_nom'=>$_POST['id_pau'],'id_asignatura'=>$id_asignatura));
			if (is_object($oPersonaNota)) {
				$id_situacion = $oPersonaNota->getId_situacion();
				if (in_array($id_situacion,$aSuperadas)) {
					// la borro de la lista
					unset($cAsignaturasOp[$acop]);
				}
			}
			$acop++;
		}
		// quito las ya matriculadas
		//lista ids asignaturas posibles
		$a_PosiblesAsignaturas = array();
		foreach($cAsignaturasOp as $n=>$oAsignatura){
			$id_asignatura = $oAsignatura->getId_asignatura();
			$a_PosiblesAsignaturas[$id_asignatura] = $n;
		}
		foreach ($cMatriculas as $oMatricula) {
			$id_asignatura=$oMatricula->getId_asignatura();
			// la borro de la lista
			if (array_key_exists($id_asignatura,$a_PosiblesAsignaturas)) {
				$n = $a_PosiblesAsignaturas[$id_asignatura];
				unset($cAsignaturasOp[$n]);
			}
		}
	}
}

// miro cuales son las opcionales genéricas, para la funcion actualizar() de java.
// la condicion es que tengan id_sector=0
$GesAsignaturasOpG = new asignaturas\GestorAsignatura();
$cAsignaturasOpG = $GesAsignaturasOpG->getAsignaturas(array('id_nivel'=>'3000','id_sector'=>0,'status'=>'t'),array('id_nivel'=>'<'));
$condicion='';
foreach ($cAsignaturasOpG as $oAsignaturaOp) {
	$id_nivel_j = $oAsignaturaOp->getId_nivel();
	$condicion.="id==".$id_nivel_j." || ";
}
$condicion=substr($condicion,0,-4);

$oHash = new web\Hash();
$camposForm = '';
$oHash->setCamposNo('mod!opcional!preceptor');
$a_camposHidden = array(
		'id_pau' => $_POST['id_pau'],
		'id_activ' => $_POST['id_activ'],
		'go_to' => $go_to
		);
if (!empty($id_asignatura_real)) {
	$a_camposHidden['id_asignatura'] = $id_asignatura;
	$a_camposHidden['id_nivel'] = $id_nivel;
	//$a_camposHidden['primary_key_s'] = $primary_key_s;
} else {
	$camposForm .= 'id_asignatura!id_nivel';
}
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);



?>
<script>
fnjs_actualizar=function(){
	var id=document.f_1303.id_nivel.value;
	if (<?php echo $condicion; ?>) {
		$('#opcional').val(1);
	} else {
		$('#opcional').val(0);
	}
	$('#f_1303').attr('action',"apps/actividadestudios/controller/form_1303.php");
	fnjs_enviar_formulario('#f_1303','#ficha_personas');
}

fnjs_guardar=function(){
	if ($('#id_asignatura').value=="") {
		$('#id_asignatura').value=document.f_1303.id_nivel.value;
	}
	$('#f_1303').attr('action',"apps/actividadestudios/controller/update_3103.php");
	fnjs_enviar_formulario('#f_1303','#ficha_personas');
}
</script>
<form id="f_1303" name="f_1303" action="t" method="POST">
<?= $oHash->getCamposHtml(); ?>
<input type="Hidden" id="mod" name="mod" value="<?= $mod ?>">
<input type="Hidden" id="opcional" name="opcional" value="">
<table>
<thead><tr><th colspan=4><?= _("matricula de asignaturas") ?></th></tr></thead>
<tbody>
<tr><td><?= ucfirst(_("actividad")) ?>:</td><td class=contenido colspan=3><?= $nom_activ ?></td></tr>
<?php
if (!empty($id_asignatura_real)) { //caso de modificar
?>
	<tr><td><?= ucfirst(_("asignatura")) ?>:</td><td class=contenido><?= $nombre_corto ?></td></tr>
<?php
} else {
	//niveles posibles (los no aprobados)
	echo "<tr><td>".ucfirst(_("asignatura")).":</td>";
	echo "<td><select id='id_nivel' name='id_nivel' onchange='fnjs_actualizar()'><option />";
	$i=0;
	foreach ($cAsignaturas as $oAsignatura) {
		$i++;
		$asignatura=$oAsignatura->getNombre_corto();
		$list_id_nivel=$oAsignatura->getId_nivel();
		if (!empty($_POST['id_nivel']) && $list_id_nivel==$_POST['id_nivel']) { $chk="selected"; } else { $chk=""; }
		echo "<option value=$list_id_nivel $chk>$asignatura</option>";
	}
	echo "</select></td>";
	
	// opcionales posibles
	if (!empty($_POST['opcional'])) {
		echo "<td>".ucfirst(_("opcional")).":</td>";
		echo "<td><select id='id_asignatura' name='id_asignatura' >";
		$i=0;
		foreach ($cAsignaturasOp as $oAsignatura) {
			$i++;
			$asignatura=$oAsignatura->getNombre_corto();
			$id_asignatura=$oAsignatura->getId_asignatura();
			if (!empty($_POST['id_asignatura']) && $_POST['id_asignatura']==$id_asignatura_real) { $chk="selected"; } else { $chk=""; }
			echo "<option value=$id_asignatura $chk>$asignatura</option>";
		}
		echo "</select></td>";
	} else { //si no es opcional 
		if (!empty($_POST['id_nivel'])) {
			$GesAsigaturas = new asignaturas\GestorAsignatura();
			$cAsignatura = $GesAsignaturas->getAsignaturas(array('id_nivel'=>$_POST['id_nivel']));
			$id_asignatura=$cAsignatura[0]->getId_asignatura();
		} else {
			$id_asignatura='';
		}
		echo "<input type=\"Hidden\" id=\"id_asignatura\" name=\"id_asignatura\" value=\"$id_asignatura\">";
	}
	echo "</tr>";
}
if (!empty($preceptor) && $preceptor=="t") { $chk_tipo="selected"; } else { $chk_tipo=""; }
?>	
<tr><td><?= _("tipo") ?></td>
	<td><select id="preceptor" name="preceptor">
		<option />
		<option value="t" <?= $chk_tipo ?> ><?= _("preceptor") ?></option>
	</select>
	</td></tr>
</tbody></table>
<br><input type="button" value="<?= ucfirst(_("guardar")); ?>" onclick="fnjs_guardar()">
