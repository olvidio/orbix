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

include_once(core\ConfigGlobal::$dir_estilos.'/tessera_pdf.css'); 

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
} else {
	$id_nom = empty($_POST['id_nom'])? '' : $_POST['id_nom'];
	$id_tabla = empty($_POST['id_tabla'])? '' : $_POST['id_tabla'];
}
if (empty($_POST['cara'])) $_POST['cara']="A";

	$id_nom = empty($_GET['id_nom'])? '' : $_GET['id_nom'];
	$id_tabla = empty($_GET['id_tabla'])? '' : $_GET['id_tabla'];

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
 $cabecera='<tr><td class="space"></td></tr>
	 		<tr valign="bottom"><td style="width: 2%"></td>
			<td class="cabecera" style="width: 38%">DISCIPLINAE</td>
			<td class="cabecera" style="width: 25%">CUM NOTA</td>
			<td class="cabecera" style="width: 1%"></td>
			<td class="cabecera" style="width: 14%">DIES EXAMINIS</td>
			<td class="cabecera" style="width: 1%"></td>
			<td class="cabecera" style="width: 14%">NUMERUS IN ACTIS</td>
			<td style="width: 1%"></td>
			</tr>';
 switch ($id_asignatura){
 	case 1101:
			?> 
			<tr><td class="space"></td></tr>
			<tr><td></td><td colspan="7" class="curso">CURSUS INSTITUTIONALES FILOSOFIAE</td></tr>
			<?php echo $cabecera; ?>
			<tr><td class="space"></td></tr>
			<tr><td></td><td colspan="7" class="any">ANNUS I</td></tr>
			<tr><td class="space"></td></tr>
			<?php
		break;
	case 1201:
			?> 
			<tr><td class="space"></td></tr>
			<tr><td></td><td colspan="7" class="any">ANNUS II</td></tr>
			<tr><td class="space"></td></tr>
			<?php
		break;
	case 2101:
			?> 
			<tr><td class="space"></td></tr>
			<tr><td></td><td colspan="7" class="curso">CURSUS INSTITUTIONALES S THEOLOGIAE</td></tr>
			<?php echo $cabecera; ?>
			<tr><td class="space"></td></tr>
			<tr><td></td><td colspan="7" class="any">ANNUS I</td></tr>
			<tr><td class="space"></td></tr>
			<?php
		break;
	case 2108:
		?>
		</table>
		</div>	
		</page>
		<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm">
		<div class="A4" style="border:solid green 2px; ">
		<table class="A4">
		<col style="width: 2%">
			<col style="width: 38%">
			<col style="width: 25%">
			<col style="width: 1%">
			<col style="width: 14%">
			<col style="width: 1%">
			<col style="width: 14%">
			<col style="width: 1%">
		 	<?php echo $cabecera; ?>
			<tr><td class="space"></td></tr>
		<?php
		break;
	case 2201:
			?> 
			<tr><td class="space"></td></tr>
			<tr><td></td><td colspan="7" class="any">ANNUS II</td></tr>
			<tr><td class="space"></td></tr>
			<?php
		break;
	case 2301:
			?> 
			<tr><td class="space"></td></tr>
			<tr><td></td><td colspan="7" class="any">ANNUS III</td></tr>
			<tr><td class="space"></td></tr>
			<?php
		break;
	case 2401:
			?> 
			<tr><td class="space"></td></tr>
			<tr><td></td><td colspan="7" class="any">ANNUS IV</td></tr>
			<tr><td class="space"></td></tr>
			<?php
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
<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm">
<div class="A4" style="border:solid green 2px; ">
<table class="A4">
<col style="width: 2%">
<col style="width: 38%">
<col style="width: 25%">
<col style="width: 1%">
<col style="width: 14%">
<col style="width: 1%">
<col style="width: 14%">
<col style="width: 1%">
<tr><td class="space"></td></tr>
<tr><td></td><td class="titulo" colspan="6">STUDIUM GENERALE REGIONIS: <?= core\ConfigGlobal::$x_region ?></td></tr>
<tr><td></td><td class="subtitulo" colspan="6">TESSERA STUDIORUM DOMINI:  <?= $nom ?></td></tr>
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
reset($aAprobadas);
while ( $a < count($cAsignaturas)) {
	$oAsignatura=$cAsignaturas[$a++];
	$row= current($aAprobadas);
	while (($row['id_nivel_asig'] < $oAsignatura->getId_nivel()) && ($j < $num_asig)) {
		$row= current($aAprobadas);
		next($aAprobadas);
		$j++;
	}
	while (($oAsignatura->getId_nivel() < $row["id_nivel_asig"]) && ($row["id_nivel"] < 2434) ){
		titulo($oAsignatura->getId_nivel());
		$nombre_asig=str_replace ("ae", "&#230", $oAsignatura->getNombre_asig());
		?>
		<tr>
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
	}

	if ($oAsignatura->getId_nivel() == $row["id_nivel_asig"]) {
		titulo($oAsignatura->getId_nivel());
		// para las opcionales
		if ($row["id_asignatura"] > 3000 &&  $row["id_asignatura"] < 9000 ) {

			$nombre_asig=str_replace ("ae", "&#230", $row["nombre_asig"]);
			$algo=$oAsignatura->getNombre_asig()."<br>&nbsp;&nbsp;&nbsp;&nbsp;".$nombre_asig;
			?>
			<tr>
			<td></td>
			<td class="opcional"><?php echo $algo;?>&nbsp;</td>
			<td class="dato opcional"><?php echo $row["nota"];?>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="dato opcional"><?php echo $row["fecha"];?>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="dato opcional"><?php echo $row["acta"];?>&nbsp;</td><td></td></tr>
			<tr><td class="space"></td></tr>
			<?php 
		} else {
			?>
			<tr>
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
			titulo($oAsignatura->getId_asignatura());
			?>
			<tr>
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
</table>
</div>
</page>
