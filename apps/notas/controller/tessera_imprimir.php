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
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once(core\ConfigGlobal::$dir_estilos.'/tessera.css.php'); 

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
} else {
	empty($_POST['id_nom'])? $id_nom="" : $id_nom=$_POST['id_nom'];
	empty($_POST['id_tabla'])? $id_tabla="" : $id_tabla=$_POST['id_tabla'];
}
if (empty($_POST['cara'])) $_POST['cara']="A";

$oPersona = personas\Persona::NewPersona($id_nom);
$nom_vernacula = $oPersona->getNom();
$apellidos = $oPersona->getApellidos();
$trato = $oPersona->getTrato();

// para el caso de nombre compuesto hay que hacer un bucle:
$nom_v_i=strtok($nom_vernacula," ");
$nom_lat='';
do {
	$oNombreLatin = new personas\NombreLatin($nom_v_i);
	$nom_lat_i=$oNombreLatin->getGenitivo();
	$nom_lat .= $nom_lat_i." ";
}
while ($nom_v_i=strtok(" ")) ;

$nom=$trato.$nom_lat.$apellidos;

function titulo($id_asignatura){
 $cabecera="<tr><td></td></tr><tr valign=\"bottom\"><td width=\"15\"></td>
			<td class=\"cabecera\" width=\"400\">".strtoupper(_("disciplin&#198;"))."</td>
			<td class=\"cabecera\" width=\"130\">".strtoupper(_("cum nota"))."</td>
			<td class=\"cabecera\" width=\"20\"></td>
			<td class=\"cabecera\" width=\"80\">".strtoupper(_("dies examinis"))."</td>
			<td class=\"cabecera\" width=\"20\"></td>
			<td class=\"cabecera\" width=\"80\">".strtoupper(_("numerus in actis"))."</td>
			<td width=\"10\"></td>
			</tr>";
 switch ($id_asignatura){
 	case 1101:
		?> <tr><td></td><td colspan="7" class="curso"><?php echo strtoupper(_("cursus institutionales philosophi&#198;")); ?></td></tr>
			 <?php echo $cabecera; ?>
			<tr><td></td><td colspan="7" class="any"><?php echo strtoupper(_("annus"))." I"; ?></td></tr>
			<?php
		break;
	case 1201:
		?> <tr><td></td></tr><tr><td></td><td colspan="7" class="any"><?php echo strtoupper(_("annus"))." II"; ?></td></tr> <?php
		break;
	case 2101:
		?> <tr><td></td></tr><tr><td><br></td></tr>
			<tr><td></td><td colspan="7" class="curso"><?php echo strtoupper(_("cursus institutionales s. theologi&#198;")); ?></td></tr>
			 <?php echo $cabecera; ?>
			<tr><td></td><td colspan="7" class="any"><?php echo strtoupper(_("annus"))." I"; ?></td></tr>
			<?php
		break;
	case 2108:
		?>
		<table class="A4" border=0 cellspacing="0" cellpadding="1">
		 <?php echo $cabecera; ?>
		<?php
		break;
	case 2201:
		?> <tr><td></td></tr><tr><td></td><td colspan="7" class="any"><?php echo strtoupper(_("annus"))." II"; ?></td></tr> <?php
		break;
	case 2301:
		?> <tr><td></td></tr><tr><td></td><td colspan="7" class="any"><?php echo strtoupper(_("annus"))." III"; ?></td></tr> <?php
		break;
	case 2401:
		?> <tr><td></td></tr><tr><td></td><td colspan="7" class="any"><?php echo strtoupper(_("annus"))." IV"; ?></td></tr> <?php
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

// -----------------------------

// -----------------------------  cabecera ---------------------------------
?>
<table class="no_print">
<tr>
<td class="atras">
<?= $oPosicion->atras2(); ?>
</td>
<td align="center"><span class=link onclick="fnjs_update_div('#main','apps/notas/controller/tessera_imprimir.php?cara=A&id_nom=<?= $id_nom; ?>&id_tabla=<?= $id_tabla; ?>')"><?= _("Cara A (delante)"); ?></span></td>
<td align="center"><span class=link onclick="fnjs_update_div('#main','apps/notas/controller/tessera_imprimir.php?cara=B&id_nom=<?= $id_nom; ?>&id_tabla=<?= $id_tabla; ?>')"><?= _("Cara B (detrás)"); ?></span></td>
</tr></table>
<table class="A4" border=0 cellspacing="0" cellpadding="1">
<col width=2%><col width=48%><col width=20%><col width=1%><col width=14%><col width=1%><col width=14%>
<?php 
if ($_POST['cara']=="A") {
	echo "<tr><td></td></tr><tr><td></td><td class=\"titulo\" colspan=6>".strtoupper(_("studium generale regionis")).": ".core\ConfigGlobal::$x_region."</td></tr>
	<tr><td></td><td class=\"subtitulo\" colspan=6>".strtoupper(_("tessera studiorum domini")).": $nom</td></tr>
	<tr><td><br></td></tr>";
}


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
	$aAprobadas[$n]['nombre_asig']= $oAsig->getNombre_asig();
	$aAprobadas[$n]['acta']= $acta;
	$aAprobadas[$n]['fecha']= $f_acta;
	//$oNota = new notas\Nota($id_situacion);
	//$aAprobadas[$n]['nota']= $oNota->getDescripcion();
	$nota = $oPersonaNota->getNota_txt();
	$aAprobadas[$n]['nota']= $nota;
}
ksort($aAprobadas);
$num_asig=count($cAsignaturas);

$a=0;
$j=0;
$i=0;
reset($aAprobadas);
while ( $a < count($cAsignaturas)) {
	$oAsignatura=$cAsignaturas[$a++];
	$row= current($aAprobadas);

 	// para imprimir sólo una cara:
	// cara A hasta la asignatura 2107
	if ($_POST['cara']=="A" && $oAsignatura->getId_nivel() > 2107 ) { continue ; }
	if ($_POST['cara']=="B" && $oAsignatura->getId_nivel() < 2108 ) { 
		while ( ($row["id_nivel"] < 2107) && ($j < $num_asig) ){
			$row= current($aAprobadas);
			next($aAprobadas);
			$j++;
		}
			prev($aAprobadas);
		continue ; 
	}
	while (($row['id_nivel_asig'] < $oAsignatura->getId_nivel()) && ($j < $num_asig)) {
		$row= current($aAprobadas);
		next($aAprobadas);
		$j++;
	}
	while (($oAsignatura->getId_nivel() < $row["id_nivel_asig"]) && ($row["id_nivel"] < 2434) ){
		$clase = "impar";
		$i % 2  ? 0: $clase = "par";
		$i++;
		titulo($oAsignatura->getId_nivel());
		$nombre_asig=str_replace ("ae", "&#230", $oAsignatura->getNombre_asig());
		?>
		<tr class="<?php echo $clase;?>" valign="bottom">
		<td></td>    
		<td><?php echo $nombre_asig;?>&nbsp;</td>
		<td class="dato">&nbsp;</td>
		<td>&nbsp;</td>
		<td class="dato">&nbsp;</td>
		<td>&nbsp;</td>
		<td class="dato">&nbsp;</td>
		<td></td>
		</tr>
		<?php
		$oAsignatura=$cAsignaturas[$a++];
		if ($_POST['cara']=="A" && $oAsignatura->getId_nivel() > 2107 )	{ continue 2; }
	}

	if ($oAsignatura->getId_nivel() == $row["id_nivel_asig"]) {
		$clase = "impar";
		$i % 2  ? 0: $clase = "par";
		$i++;
		titulo($oAsignatura->getId_nivel());
		// para las opcionales
		if ($row["id_asignatura"] > 3000 &&  $row["id_asignatura"] < 9000 ) {

			$nombre_asig=str_replace ("ae", "&#230", $row["nombre_asig"]);
			$algo=$oAsignatura->getNombre_asig()."<br>&nbsp;&nbsp;&nbsp;&nbsp;".$nombre_asig;
			?>
			<tr class="<?php echo $clase;?>" valign="bottom">
			<td></td>
			<td><?php echo $algo;?>&nbsp;</td>
			<td class="dato"><?php echo $row["nota"];?>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="dato"><?php echo $row["fecha"];?>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="dato"><?php echo $row["acta"];?>&nbsp;</td><td></td></tr>
			<?php 
		} else {
			?>
			<tr class="<?php echo $clase;?>">
			<td></td>
			<td><?php echo $oAsignatura->getNombre_asig();?>&nbsp;</td>
			<td class="dato"><?php echo $row["nota"];?>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="dato"><?php echo $row["fecha"];?>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="dato"><?php echo $row["acta"];?>&nbsp;</td><td></td></tr>
			<?php 
		}
		$num_asig ++;
	} else {
		if (!$row["id_nivel"] || ($j==$num_asig)) {
			$clase = "impar";
			$i % 2  ? 0: $clase = "par";
			$i++;
			titulo($oAsignatura->getId_asignatura());
			?>
			<tr class="<?php echo $clase;?>">
				<td></td>
				<td><?php echo $oAsignatura->getNombre_asig();?>&nbsp;</td>
				<td class="dato">&nbsp;</td>
				<td>&nbsp;</td>
				<td class="dato">&nbsp;</td>
				<td>&nbsp;</td>
				<td class="dato">&nbsp;</td><td></td></tr>
			<?php
		}
	}
}
?>
</tr>
<tr><td></td></tr>
</table>
