<?php
use asignaturas\model\entity as asignaturas;
use notas\model\entity as notas;
use personas\model\entity as personas;
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

echo "<script>fnjs_left_side_hide()</script>";
include_once(core\ConfigGlobal::$dir_estilos.'/tessera.css.php'); 

// En el caso de actualizar la misma página (cara A-B) solo me quedo con la última.
$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack2 = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack2 != '') {
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack2)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack2);
		}
	}
}

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_nom= (integer) strtok($a_sel[0],"#");
	$id_tabla= (string) strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$id_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
	$id_tabla = (string) \filter_input(INPUT_POST, 'id_tabla');
}

$Qcara = (string) \filter_input(INPUT_POST, 'cara');
$Qcara = empty($Qcara)? "A" : $Qcara;

$oPersona = personas\Persona::NewPersona($id_nom);
if (!is_object($oPersona)) {
	$msg_err = "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
	exit($msg_err);
}
$nom = $oPersona->getNombreApellidos();
/* Ahora no hace falta el latín
$nom_vernacula = $oPersona->getNom();
$apellidos = $oPersona->getApellidos();
$trato = $oPersona->getTrato();
$oGesNomLatin = new personas\GestorNombreLatin();
$nom_lat = $oGesNomLatin->getVernaculaLatin($nom_vernacula);
$nom=$trato.$nom_vernacula.$apellidos;
*/

$region_latin = $_SESSION['oConfig']->getNomRegionLatin();

// conversion 
$replace  = config\model\Config::$replace;

function titulo($id_asignatura){
	$html = '';
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
			$html = " 
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"curso\">CURSUS INSTITUTIONALES FILOSOFI&#198;</td></tr>
				$cabecera
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS I</td></tr>
				<tr><td class=\"space\"></td></tr>
				";
			break;
		case 1201:
			$html = " 
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS II</td></tr>
				<tr><td class=\"space\"></td></tr>
				";
			break;
		case 2101:
			$html = " 
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"curso\">CURSUS INSTITUTIONALES S THEOLOGI&#198;</td></tr>
				$cabecera
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS I</td></tr>
				<tr><td class=\"space\"></td></tr>
				";
			break;
		case 2108:
			$html = "
			</table>
			</div>
			<div class=\"A4\">
			<table class=\"A4\">
			<col style=\"width: 2%\">
				<col style=\"width: 46%\">
				<col style=\"width: 25%\">
				<col style=\"width: 1%\">
				<col style=\"width: 10%\">
				<col style=\"width: 1%\">
				<col style=\"width: 10%\">
				<col style=\"width: 1%\">
				$cabecera
				<tr><td class=\"space\"></td></tr>
			";
			break;
		case 2201:
			$html = "
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS II</td></tr>
				<tr><td class=\"space\"></td></tr>
			";
			break;
		case 2301:
			$html = "
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS III</td></tr>
				<tr><td class=\"space\"></td></tr>
			";
			break;
		case 2401:
			$html = "
				<tr><td class=\"space\"></td></tr>
				<tr><td></td><td colspan=\"7\" class=\"any\">ANNUS IV</td></tr>
				<tr><td class=\"space\"></td></tr>
			";
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

// -----------------------------

// -----------------------------  cabecera ---------------------------------
$caraA = web\Hash::link('apps/notas/controller/tessera_imprimir.php?'.http_build_query(array('cara'=>'A','id_nom'=>$id_nom,'id_tabla'=>$id_tabla,'refresh'=>1)));
$caraB = web\Hash::link('apps/notas/controller/tessera_imprimir.php?'.http_build_query(array('cara'=>'B','id_nom'=>$id_nom,'id_tabla'=>$id_tabla,'refresh'=>1)));

$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb().'/apps/notas/controller/tessera_2_mpdf.php');
$oHash->setCamposForm('id_nom!id_tabla'); 
$h = $oHash->linkSinVal();

?>
<table class="no_print">
<tr>
<td class="atras">
<?= $oPosicion->mostrar_back_arrow(1); ?>
</td>
<td align="center"><span class=link onclick="fnjs_update_div('#main','<?= $caraA ?>')"><?= _("Cara A (delante)"); ?></span></td>
<td align="center"><span class=link onclick="fnjs_update_div('#main','<?= $caraB ?>')"><?= _("Cara B (detrás)"); ?></span></td>
<td align="center"><span class=link onclick='window.open("<?= core\ConfigGlobal::getWeb() ?>/apps/notas/controller/tessera_2_mpdf.php?id_nom=<?= $id_nom ?>&id_tabla=<?= $id_tabla ?><?= $h ?>&PHPSESSID=<?= session_id(); ?>", "sele");' >
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
if ($Qcara=="A") {
?>
<tr><td class="space"></td></tr>
<tr><td></td><td class="titulo" colspan="6">STUDIUM GENERALE REGIONIS: <?= $region_latin ?></td></tr>
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
	$id_asignatura = $oPersonaNota->getId_asignatura();
	$id_nivel = $oPersonaNota->getId_nivel();
	$acta = $oPersonaNota->getActa();
	$f_acta = $oPersonaNota->getF_acta()->getFromLocal();
	
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
	$aAprobadas[$n]['nombre_asignatura']= $oAsig->getNombre_asignatura();
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
$row= current($aAprobadas);
while ( $a < count($cAsignaturas)) {
	$oAsignatura=$cAsignaturas[$a++];

 	// para imprimir sólo una cara:
	// cara A hasta la asignatura 2107
	if ($Qcara=="A" && $oAsignatura->getId_nivel() > 2107 ) { 
		$row= current($aAprobadas);
		continue ;
	}
	if ($Qcara=="B" && $oAsignatura->getId_nivel() < 2108 ) { 
		while ( ($row["id_nivel"] < 2107) && ($j < $num_asig) ){
			$row= current($aAprobadas);
			next($aAprobadas);
			$j++;
		}
		continue ; 
		prev($aAprobadas);
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
		echo titulo($oAsignatura->getId_nivel());
		$nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
		?>
		<tr class="<?= $clase;?>" valign="bottom">
		<td></td>    
		<td><?= $nombre_asignatura;?>&nbsp;</td>
		<td class="dato">&nbsp;</td>
		<td>&nbsp;</td>
		<td class="dato">&nbsp;</td>
		<td>&nbsp;</td>
		<td class="dato">&nbsp;</td>
		<td></td>
		</tr>
		<?php
		$oAsignatura=$cAsignaturas[$a++];
		if ($Qcara=="A" && $oAsignatura->getId_nivel() > 2107 )	{ continue 2; }
	}

	if ($oAsignatura->getId_nivel() == $row["id_nivel_asig"]) {
		$clase = "impar";
		$i % 2  ? 0: $clase = "par";
		$i++;
		echo titulo($oAsignatura->getId_nivel());
		// para las opcionales
		if ($row["id_asignatura"] > 3000 &&  $row["id_asignatura"] < 9000 ) {

			$nombre_asignatura=strtr ($row["nombre_asignatura"], $replace);
			$algo=$oAsignatura->getNombre_asignatura()."<br>&nbsp;&nbsp;&nbsp;&nbsp;".$nombre_asignatura;
			?>
			<tr class="<?= $clase;?>" valign="bottom">
			<td></td>
			<td><?= $algo;?>&nbsp;</td>
			<td class="dato"><?= $row["nota"];?>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="dato"><?= $row["fecha"];?>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="dato"><?= $row["acta"];?>&nbsp;</td><td></td></tr>
			<?php 
		} else {
			$nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
			?>
			<tr class="<?= $clase;?>">
			<td></td>
			<td><?= $nombre_asignatura; ?>&nbsp;</td>
			<td class="dato"><?= $row["nota"];?>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="dato"><?= $row["fecha"];?>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="dato"><?= $row["acta"];?>&nbsp;</td><td></td></tr>
			<?php 
		}
		$num_asig ++;
	} else {
		if (!$row["id_nivel"] || ($j==$num_asig)) {
			$clase = "impar";
			$i % 2  ? 0: $clase = "par";
			$i++;
			echo titulo($oAsignatura->getId_asignatura());
			$nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
			?>
			<tr class="<?= $clase;?>">
				<td></td>
				<td><?= $nombre_asignatura; ?>&nbsp;</td>
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
