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

$oGesNomLatin = new personas\GestorNombreLatin();
$nom_lat = $oGesNomLatin->getVernaculaLatin($nom_vernacula);
$nom=$trato.$nom_lat.$apellidos;

// conversion 
$replace  = array(
 'AE' => '&#198;',
 'Ae' => '&#198;',
 'ae' => '&#230;',
 'OE' => '&#140;',
 'Oe' => '&#140;',
 'oe' => '&#156;'
 );

function titulo($id_asignatura){
	$cabecera='<tr><td class="space"></td></tr>
	 		<tr valign="bottom"><td style="width: 2%"></td>
			<td class="cabecera" style="width: 46%">DISCIPLIN&#198;</td>
			<td class="cabecera" style="width: 25%">CUM NOTA</td>
			<td class="cabecera" style="width: 1%"></td>
			<td class="cabecera" style="width: 10%">DIES EXAMINIS</td>
			<td class="cabecera" style="width: 1%"></td>
			<td class="cabecera" style="width: 10%">NUMERUS IN ACTIS</td>
			<td style="width: 1%"></td>
			</tr>';
	switch ($id_asignatura){
		case 1101:
				?> 
				<tr><td class="space"></td></tr>
				<tr><td></td><td colspan="7" class="curso">CURSUS INSTITUTIONALES FILOSOFI&#198;</td></tr>
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
				<tr><td></td><td colspan="7" class="curso">CURSUS INSTITUTIONALES S THEOLOGI&#198;</td></tr>
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
			<div class="A4">
			<table class="A4">
			<col style="width: 2%">
				<col style="width: 46%">
				<col style="width: 25%">
				<col style="width: 1%">
				<col style="width: 10%">
				<col style="width: 1%">
				<col style="width: 10%">
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
$caraA = web\Hash::link('apps/notas/controller/tessera_imprimir.php?'.http_build_query(array('cara'=>'A','id_nom'=>$id_nom,'id_tabla'=>$id_tabla)));
$caraB = web\Hash::link('apps/notas/controller/tessera_imprimir.php?'.http_build_query(array('cara'=>'B','id_nom'=>$id_nom,'id_tabla'=>$id_tabla)));

$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb().'/apps/notas/controller/tessera_2_mpdf.php');
$oHash->setCamposForm('id_nom!id_tabla'); 
$h = $oHash->linkSinVal();

?>
<table class="no_print">
<tr>
<td class="atras">
<?= $oPosicion->atras2(); ?>
</td>
<td align="center"><span class=link onclick="fnjs_update_div('#main','<?= $caraA ?>')"><?= _("Cara A (delante)"); ?></span></td>
<td align="center"><span class=link onclick="fnjs_update_div('#main','<?= $caraB ?>')"><?= _("Cara B (detrás)"); ?></span></td>
<td align="center"><span class=link onclick='window.open("<?= core\ConfigGlobal::getWeb() ?>/apps/notas/controller/tessera_2_mpdf.php?id_nom=<?= $id_nom ?>&id_tabla=<?= $id_tabla ?><?= $h ?>&PHPSESSID=<?php echo session_id(); ?>", "sele");' >
<?= _("PDF"); ?></span></td>
</tr></table>
<table class="A4">
<col style="width: 2%">
<col style="width: 46%">
<col style="width: 25%">
<col style="width: 1%">
<col style="width: 10%">
<col style="width: 1%">
<col style="width: 10%">
<col style="width: 1%">
<?php 
if ($_POST['cara']=="A") {
?>
<tr><td class="space"></td></tr>
<tr><td></td><td class="titulo" colspan="6">STUDIUM GENERALE REGIONIS: <?= core\ConfigGlobal::$x_region ?></td></tr>
<tr><td></td><td class="subtitulo" colspan="6">TESSERA STUDIORUM DOMINI:  <?= $nom ?></td></tr>
<?php
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
		$nombre_asig = strtr($oAsignatura->getNombre_asig(), $replace);
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

			$nombre_asig=strtr ($row["nombre_asig"], $replace);
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
			$nombre_asig = strtr($oAsignatura->getNombre_asig(), $replace);
			?>
			<tr class="<?php echo $clase;?>">
			<td></td>
			<td><?= $nombre_asig; ?>&nbsp;</td>
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
			$nombre_asig = strtr($oAsignatura->getNombre_asig(), $replace);
			?>
			<tr class="<?php echo $clase;?>">
				<td></td>
				<td><?= $nombre_asig; ?>&nbsp;</td>
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
</table>
