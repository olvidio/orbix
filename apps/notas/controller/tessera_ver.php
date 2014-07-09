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
	//$id_nom=$sel[0];
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
} else {
	exit('nose de que va');
}

$oPersona = personas\Persona::NewPersona($id_nom);
$ap_nom = $oPersona->getApellidosNombre();
$centro = $oPersona->getCentro_o_dl();

function titulo($id_nivel){
 switch ($id_nivel){
 	case 1101:
		?> <tr><td colspan="3" align="CENTER"><h3><?php echo ucfirst(_("filosofía")); ?></h3></td></tr>
			<tr><td colspan="3"><b><?php echo ucfirst(_("año"))." I"; ?></b></td></tr> <?php
		break;
	case 1201:
		?> <tr><td colspan="3"><b><?php echo ucfirst(_("año"))." II"; ?></b></td></tr> <?php
		break;
	case 2101:
		?> <tr><td><br></td></tr>	<tr><td colspan="4" align="CENTER"><h3><?php echo ucfirst(_("teología")); ?></h3></td></tr>
			<tr><td colspan="3"><b><?php echo ucfirst(_("año"))." I"; ?></b></td></tr> <?php
		break;
	case 2201:
		?> <!-- pruebo de cerrar la tabla anidada -->
		</table></td>
		<td valign="TOP" width="50%">
		<table class="semi">
			<tr><td colspan="3" align="CENTER"><h3><?php echo ucfirst(_("teología")); ?></h3></td></tr>
			<tr><td colspan="3"><b><?php echo ucfirst(_("año"))." II"; ?></b></td></tr> <?php
		break;
	case 2301:
		?> <tr><td colspan="3"><b><?php echo ucfirst(_("año"))." III"; ?></b></td></tr> <?php
		break;
	case 2401:
		?> <tr><td colspan="3"><b><?php echo ucfirst(_("año"))." IV"; ?></b></td></tr> <?php
		break;
 }
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
	if ($oAsig->getStatus() != 't') continue;
	if ($id_asignatura > 3000) {
		$id_nivel_asig = $id_nivel;
	} else {
		$id_nivel_asig = $oAsig->getId_nivel();
	}
	$n=$id_nivel_asig;
	$aAprobadas[$n]['id_nivel_asig']= $id_nivel_asig;
	$aAprobadas[$n]['id_nivel']= $id_nivel;
	$aAprobadas[$n]['id_asignatura']= $id_asignatura;
	$aAprobadas[$n]['nombre_corto']= $oAsig->getNombre_corto();
	$aAprobadas[$n]['fecha']= $f_acta;
	$oNota = new notas\Nota($id_situacion);
	$aAprobadas[$n]['nota']= $oNota->getDescripcion();
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
		titulo($oAsignatura->getId_nivel());
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
		titulo($oAsignatura->getId_nivel());
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
	}

	if (!$row["id_nivel"]){
		$clase = "impar";
		$i % 2  ? 0: $clase = "par";
		$i++;
		titulo($oAsignatura->getId_nivel());
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
<tr><td colspan="2">
<?php
echo sprintf(_("Número de asignaturas hechas: %s (de %s)"),$numasig,$num_asig_total)."<br>";
echo sprintf(_("Número de créditos realizados: %s (de %s)"),$numcred,$num_creditos_total)."<br>";
?>
</td></tr>
</table>
