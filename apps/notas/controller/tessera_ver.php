<?php
use asignaturas\model as asignaturas;
use notas\model as notas;
use personas\model as personas;
/**
* Esta página sirve para la tessera de una persona.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		22/11/02.
*		
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once(core\ConfigGlobal::$dir_estilos.'/tessera.css.php'); 

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_sel=$_POST['sel'];
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
	$oPosicion->addParametro('id_sel',$id_sel);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id);
} else {
	exit('nose de que va');
}

$ini = core\ConfigGlobal::$est_inicio;
$fin = core\ConfigGlobal::$est_fin;
$any = date('Y');
$mes = date('m');

if ($mes>9) {
	$any2=$any-1;
	$inicio = "$ini/$any2";	
	$fin = "$fin/$any";
	$curso_txt = "$any2-$any";
} else {
	$any2=$any-2;
	$any--;
	$inicio = "$ini/$any2";	
	$fin = "$fin/$any";
	$curso_txt = "$any2-$any";
}
$oInicio = \DateTime::createFromFormat('d/m/Y', $inicio);
$oFin = \DateTime::createFromFormat('d/m/Y', $fin);

$oPersona = personas\Persona::NewPersona($id_nom);
$ap_nom = $oPersona->getApellidosNombre();
$centro = $oPersona->getCentro_o_dl();

function titulo($id_nivel) {
	$html = "";
	switch ($id_nivel) {
		case 1101:
			$html = '<tr><td colspan="3" align="CENTER"><h3>'
				.ucfirst(_("filosofía")).
				'</h3></td></tr> <tr><td colspan="3"><b>'
				.ucfirst(_("año")).' I</b></td></tr>
				';
			break;
		case 1201:
			$html = '<tr><td colspan="3"><b>'
				.ucfirst(_("año")).' II</b></td></tr>
				';
			break;
		case 2101:
			$html = '<tr><td><br></td></tr> <tr><td colspan="4" align="CENTER"><h3>'
				.ucfirst(_("teología")).
				'</h3></td></tr> <tr><td colspan="3"><b>'
				.ucfirst(_("año")).' I</b></td></tr>
				';
			break;
		case 2201:
			//pruebo de cerrar la tabla anidada -->
			$html ='</table></td>
			<td valign="TOP" width="50%">
			<table class="semi">';
			
			$html .= '<tr><td colspan="3" align="CENTER"><h3>'
				.ucfirst(_("teología")).
				'</h3></td></tr> <tr><td colspan="3"><b>'
				.ucfirst(_("año")).' II</b></td></tr>
				';
			break;
		case 2301:
			$html = '<tr><td colspan="3"><b>'
				.ucfirst(_("año")).' III</b></td></tr>
				';
			break;
		case 2401:
			$html = '<tr><td colspan="3"><b>'
				.ucfirst(_("año")).' IV</b></td></tr>
				';
			break;
	}
	return $html;
}

function data($data) {
	$fecha = explode("-",$data);
	$any = substr($fecha[0],2);
	$fechaok = $fecha[2] . "." . $fecha[1] . "." . $any;
	if ($fecha[1]==00) {$fechaok="";}
	echo "$fechaok";
}

// -----------------------------  cabecera ---------------------------------
?>
<table>
<tr>
<td class="atras no_print">
<?= $oPosicion->atras2(); ?>
</td>
<td style="vertical-align: bottom;">
<h3>
<?php echo ucfirst(sprintf(_("tessera de:  %s (%s)"),$ap_nom,$centro)); ?></h3></td>
</tr>
</table>
<table border=1  >
<?php
// Asignaturas posibles:
$GesAsignaturas = new asignaturas\GestorAsignatura();
$aWhere=array();
$aOperador=array();
$aWhere['status'] = 't';
$aWhere['id_nivel'] = '1100,2500';
$aOperador['id_nivel']='BETWEEN';
$aWhere['_ordre'] = 'id_nivel';
$cAsignaturas = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
// Asignaturas cursadas:
$GesNotas = new notas\GestorPersonaNota();
$aWhere=array();
$aOperador=array();
$aWhere['id_nom'] = $id_nom;
$aWhere['id_nivel'] = '1100,2500';
$aOperador['id_nivel']='BETWEEN';
$cNotas = $GesNotas->getPersonaNotas($aWhere,$aOperador);
$aAprobadas=array();
foreach ($cNotas as $oPersonaNota) {
	extract($oPersonaNota->getTot());
	$oAsig = new asignaturas\Asignatura($id_asignatura);
	if ($id_asignatura > 3000) {
		$id_nivel_asig = $id_nivel;
	} else {
		if ($oAsig->getStatus() != 't') continue;
		$id_nivel_asig = $oAsig->getId_nivel();
	}
	$n=$id_nivel_asig;
	$aAprobadas[$n]['id_nivel_asig']= $id_nivel_asig;
	$aAprobadas[$n]['id_nivel']= $id_nivel;
	$aAprobadas[$n]['id_asignatura']= $id_asignatura;
	$aAprobadas[$n]['nombre_corto']= $oAsig->getNombre_corto();
	$aAprobadas[$n]['fecha']= $f_acta;
	//$oNota = new notas\Nota($id_situacion);
	//$aAprobadas[$n]['nota']= $oNota->getDescripcion();
	$nota = $oPersonaNota->getNota_txt();
	$aAprobadas[$n]['nota']= $nota;
}
ksort($aAprobadas);

// Para saber el número total de asignaturas
$num_asig_total=count($cAsignaturas);
$num_creditos_total=0;

?>
<tr><td valign="TOP" width="50%">
<table class="semi" border="<?php echo $border;?>">
<?php
$a=0;
$i=0;
$numasig=0;
$numcred=0;
$numasig_year=0;
$numcred_year=0;
reset($aAprobadas);
while ( $a < count($cAsignaturas)) {
	$oAsignatura=$cAsignaturas[$a++];
	$num_creditos_total += $oAsignatura->getCreditos();
	$row= current($aAprobadas);
	next($aAprobadas);
	while ( ($oAsignatura->getId_nivel() < $row["id_nivel_asig"]) && ($row["id_nivel"] < 2434) ){
		$clase = "impar";
		$i % 2  ? 0: $clase = "par";
		$i++;
		echo titulo($oAsignatura->getId_nivel());
		?>
		<tr class="<?php echo $clase;?>">     
		<td class="tessera"><?php echo $oAsignatura->getNombre_corto();?>&nbsp;</td>
		<td class="tessera"><?php echo "<font color='Fuchsia'>"._("Pendiente")."</font>"; ?></td>
		<td></td>
		</tr>
		<?php
		$oAsignatura=$cAsignaturas[$a++];
		$num_creditos_total += $oAsignatura->getCreditos();
	}

	if ($oAsignatura->getId_nivel() == $row["id_nivel_asig"]) {
		$clase = "impar";
		$i % 2  ? 0: $clase = "par";
		$i++;
		echo titulo($oAsignatura->getId_nivel());
		// para las opcionales
		if ($row["id_asignatura"] > 3000 &&  $row["id_asignatura"] < 9000 ) {
			$algo=$oAsignatura->getNombre_corto()."<br>&nbsp;&nbsp;&nbsp;&nbsp;".$row["nombre_corto"];
			?>
			<tr class="<?php echo $clase;?>">
			<td class="tessera"><?php echo $algo;?>&nbsp;</td>
			<td class="tessera"><?php echo $row["nota"];?>&nbsp;</td>
			<td class="tessera"><?php echo $row["fecha"];?>&nbsp;</td></tr>
			<?php 
		} else {
			?>
			<tr class="<?php echo $clase;?>">
			<td class="tessera"><?php echo $oAsignatura->getNombre_corto();?>&nbsp;</td>
			<td class="tessera"><?php echo $row["nota"];?>&nbsp;</td>
			<td class="tessera"><?php echo $row["fecha"];?>&nbsp;</td></tr>
			<?php 
		}
		$numasig ++;
		$numcred += $oAsignatura->getCreditos(); 
		$oFActa = \DateTime::createFromFormat('d/m/Y', $row['fecha']);
			
		$startdate = new DateTime("2014-11-20");
		$enddate = new DateTime("2015-01-20");

		if($oInicio <= $oFActa && $oFActa <= $oFin) {
			$numasig_year ++;
			$numcred_year += $oAsignatura->getCreditos(); 
		}

	
	}

	if (!$row["id_nivel"]){
		$clase = "impar";
		$i % 2  ? 0: $clase = "par";
		$i++;
		echo titulo($oAsignatura->getId_nivel());
		?>
		<tr class="<?php echo $clase;?>">     
		<td class="tessera"><?php echo $oAsignatura->getNombre_corto();?>&nbsp;</td>
		<td class="tessera"><?php echo "<font color='Fuchsia'>"._("Pendiente")."</font>"; ?></td>
		<td class="tessera"></td>
		</tr>
		<?php
	}
}
?>
</table></td></tr>
<tr><td>
<?php
echo sprintf(_("Número de asignaturas hechas: %s (de %s)"),$numasig,$num_asig_total)."<br>";
echo sprintf(_("Número de créditos realizados: %s (de %s)"),$numcred,$num_creditos_total)."<br>";
?>
	</td><td>
<?php
echo sprintf(_("Número de asignaturas cursadas el curso %s: %s"),$curso_txt,$numasig_year)."<br>";
echo sprintf(_("Número de créditos realizados el curso %s: %s"),$curso_txt,$numcred_year)."<br>";
?>
</td></tr>
</table>
