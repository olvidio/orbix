<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_nom = strtok($_POST['sel'][0],"#");
	$id_activ = strtok("#");
} else {
	$id_nom = (integer)  filter_input(INPUT_POST, 'id_nom');
	$id_activ = (integer)  filter_input(INPUT_POST, 'id_pau');
}
$id_activ = (integer)  filter_input(INPUT_POST, 'id_pau');

$oPersona = personas\model\Persona::NewPersona($id_nom);
if (!is_object($oPersona)) {
	$msg_err .= "<br>$oPersona con id_nom: $id_nom";
}

$nom=$oPersona->getNombreApellidos();
$lugar_nacimiento=$oPersona->getLugar_nacimiento();
$f_nacimiento=$oPersona->getF_nacimiento();
$txt_nacimiento = "$lugar_nacimiento ($f_nacimiento)";

$dl_origen = core\ConfigGlobal::mi_dele();
$dl_destino = $oPersona->getDl();

$oActividad = new actividades\model\Actividad($id_activ);
$nom_activ = $oActividad->getNom_activ();
$id_ubi = $oActividad->getId_ubi();
$f_ini = $oActividad->getF_ini();
$f_fin = $oActividad->getF_fin();
$oUbi = ubis\model\Ubi::NewUbi($id_ubi);
$lugar = $oUbi->getNombre_ubi();

$txt_actividad = "$lugar, $f_ini-$f_fin";


$GesMatriculas = new actividadestudios\model\GestorMatricula();
$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom'=>$id_nom, 'id_activ'=>$id_activ));
$matriculas=count($cMatriculas);
if ($matriculas > 0) {
	// para ordenar
	$aAsignaturasMatriculadas = array(); 
	foreach($cMatriculas as $oMatricula) {
		$id_asignatura=$oMatricula->getId_asignatura();
		$oAsignatura = new asignaturas\model\Asignatura($id_asignatura);
		$nombre_corto = $oAsignatura->getNombre_corto();
		//$nota = $oMatricula->getNota_txt();
		
		$GesNotas = new notas\model\GestorPersonaNota();
		$cNotas = $GesNotas->getPersonaNotas(array('id_nom'=>$id_nom,'id_asignatura'=>$id_asignatura));
		if ($cNotas !== FALSE && count($cNotas) > 0) {
			$oNota = $cNotas[0];
			$nota = $oNota->getNota_txt();
			$acta = $oNota->getActa();
			$f_acta = $oNota->getF_acta();
		} else {
			$nota = '';
			$acta = '';
			$f_acta = '';
		}
		$aAsignaturasMatriculadas[] = array('nom_asignatura' => $nombre_corto,
											'nota' => $nota,
											'f_acta' => $f_acta,
											'acta' => $acta);
	}
} else {
	$msg_err .= _("No hay ninguna matricula de esta persona");
}


if (!empty($msg_err)) { echo $msg_err; }
?>
<table><tr><td><?= $dl_destino ?></td><td><?= $dl_origen ?></td></tr></table>

<table border="1">
<thead>
	<tr><td><?= _("Nombre y apellidos"); ?>:</td><td><?= $nom ?></td></tr>
	<tr><td><?= _("Lugar y fecha de nacimiento"); ?>:</td><td><?= $txt_nacimiento ?></td></tr>
	<tr><td><?= _("Fecha y lugar del sem, ca o cv"); ?>:</td><td><?= $txt_actividad ?></td></tr>
</thead>
<tbody>
	<tr></tr>
	<tr><td><?=	strtoupper(_("asignatura")) ?> (1)</td>
		<td><?=	strtoupper(_("calificación")) ?></td>
		<td><?=	strtoupper(_("fecha del acta")) ?></td>
		<td><?=	strtoupper(_("nº del acta")) ?> (2)</td>
	</tr>
<?php
if ($matriculas > 0) {
	$i=0;
	foreach ($aAsignaturasMatriculadas as $key=>$aAsignaturas) {
		echo "<tr>";
		echo "<td>".$aAsignaturas['nom_asignatura']."</td>";
		echo "<td>".$aAsignaturas['nota']."</td>";
		echo "<td>".$aAsignaturas['f_acta']."</td>";
		echo "<td>".$aAsignaturas['acta']."</td>";
		echo "</tr>";
	}
}
?>	
</tbody></table>
<br>
(1) Deben anotare todas las asignaturas previstas, indicando en las observaciones los eventuales cambios en el plan de estudios.
<br>
(2) Rellenar después del ca, en la dl que organizó el ca, antes de enviar a la dl de procedencia del alumno.
<br>
(OBSERVACIONES AL DORSO)