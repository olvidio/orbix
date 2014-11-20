<?php
use asignaturas\model as asignaturas;
use notas\model as notas;
use personas\model as personas;
/**
* Esta página sirve para las actas.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		24/10/03.
*		
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	include_once(core\ConfigGlobal::$dir_estilos.'/actas.css.php'); 

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

function data($data) {
	list($dia,$mes,$any) = preg_split('/[\.\/-]/', $data ); //los delimitadores pueden ser /, ., -
	$mes_latin=array('ianuario','februario','martio','aprili','maio','iunio','iulio','augusto','septembri','octobri','novembri','decembri');
	$fecha_latin="die ".$dia." mense  ".$mes_latin[$mes-1]."  anno  ".$any;
	return $fecha_latin;
}

function num_latin($num) {
	$unidades=array('',I,II,III,IV,V,VI,VII,VIII,IX,X);
	$decenas=array('',X,XX,XXX,XL,L,LX,LXX,LXXX,XC,C);
	$centenas=array('',C,CC,CCC,CD,D,DC,DCC,DCCC,CM,M);
	$uni=substr($num,-1,1);
	if (strlen($num)>1) { $dec=substr($num,-2,1); } else { $dec=0;}
	if (strlen($num)>2) { $cen=substr($num,-3,1); } else { $cen=0;}
	$latin=$centenas[$cen].$decenas[$dec].$unidades[$uni];
	return $latin;
}	

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$acta=urldecode(strtok($_POST['sel'][0],"#"));
} else {
	empty($_POST['acta'])? $acta="" : $acta=urldecode($_POST['acta']);
}

// acta
$oActa = new notas\Acta($acta);
extract($oActa->getTot());

$oAsignatura = new asignaturas\Asignatura($id_asignatura);
$nombre_corto=$oAsignatura->getNombre_corto();
$nombre_asig=$oAsignatura->getNombre_asig();
$any=$oAsignatura->getYear();

switch (substr($id_asignatura, 0, 1)) {
	case 1:
		$curso="PHILOSOPHIAE";
		break;
	case 2:
		$curso="S. TEOLOGIAE";
		break;
	case 3:
		$op=substr($id_asignatura, 1, 1);
		if ($op=1) $curso="PHILOSOPHIAE";
		if ($op=2) $curso="S. TEOLOGIAE";
		if ($op=3) {
			if (stristr ($observ, "philo"))  $curso="PHILOSOPHIAE";
			if (stristr ($observ, "teolo"))  $curso="S. TEOLOGIAE";
		}
		break;
}

switch ($any) {
	case 1:
		$any="I";
		break;
	case 2:
		$any="II";
		break;
	case 3:
		$any="III";
		break;
	case 4:
		$any="IV";
		break;
	default:
		$any='';
}

// -----------------------------
// alumnos:

$GesPersonaNotas = new notas\GestorPersonaNota();
$aWhere['acta'] = $acta;
$cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhere);
$GesNotas  = new notas\GestorNota();
$aIdSuperadas = $GesNotas->getArrayNotasSuperadas();

// para ordenar
$aPersonasNotas = array(); 
foreach($cPersonaNotas as $oPersonaNota) {
	
	$id_situacion=$oPersonaNota->getId_situacion();
	$id_nom=$oPersonaNota->getId_nom();
	$oPersona = personas\Persona::NewPersona($id_nom);
	$apellidos=$oPersona->getApellidos();
	$trato=$oPersona->getTrato();
	$nom_v=$oPersona->getNom();
	
	// para el caso de nombre compuesto hay que hacer un bucle:
	$nom_v_i=strtok($nom_v,' ');
	$nom_lat='';
	do {
		$oNomLatin = new personas\NombreLatin(array('nom'=>$nom_v_i));
		$nom_lat .= $oNomLatin->getNominativo()." ";
	} while ($nom_v_i=strtok(" ")) ;

	//Ni la función del postgresql ni la del php convierten los acentos.
	$apellidos=core\strtoupper_dlb($apellidos);
	$nom=trim($apellidos).", ".$trato.$nom_lat;
		
	//$oNota = new notas\Nota($id_situacion);
	//$nota=$oNota->getDescripcion();
	$nota = $oPersonaNota->getNota_txt();
	$aPersonasNotas[$nom] = $nota;
}
uksort($aPersonasNotas, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.

$num_alumnos=count($aPersonasNotas);

// tribunal:
$GesTribunal = new notas\GestorActaTribunalDl();
$cTribunal = $GesTribunal->getActasTribunalesDl(array('acta'=>$acta,'_ordre'=>'orden')); 
$num_examinadores=count($cTribunal);

// Definición del número de lineas de las páginas y los numeros de alumnos----------------
$lin_A4=45;										// número máximo de lineas en un A4
$lin_encabezado=16;								// número de lineas del encabezado asignatura + pie
$lin_encabezado_tribunal=4;						// número de lineas del encabezado tribunal
$lin_tribunal=$lin_encabezado_tribunal+2*$num_examinadores;  // número de lineas del tribunal

$lin_max_cara_A=$lin_A4 - $lin_encabezado - 2; 	// número máximo de lineas en la cara A 

if ($num_alumnos > $lin_max_cara_A) { $alum_cara_A=$lin_max_cara_A; } else { $alum_cara_A=$num_alumnos; }
$alum_cara_B=$num_alumnos-$alum_cara_A;

$caraA = web\Hash::link('apps/notas/controller/acta_imprimir.php?'.http_build_query(array('cara'=>'A','acta'=>$acta)));
$caraB = web\Hash::link('apps/notas/controller/acta_imprimir.php?'.http_build_query(array('cara'=>'B','acta'=>$acta)));

// ---------------------------------------------------------------------------------------
if (empty($_POST['cara'])) { $cara="A"; } else { $cara=$_POST['cara']; }
?>
<table class="no_print"><tr>
<td align="center"><span class=link onclick="fnjs_update_div('#main','<?= $caraA ?>')"><?= _("Cara A (delante)"); ?></span></td>
<td align="center"><span class=link onclick="fnjs_update_div('#main','<?= $caraB ?>')"><?= _("Cara B (detrás)"); ?></span></td>
</tr></table>

<div class="A4" >
<?php if ($cara=="A") { ?>
   <cabecera><?php echo str_replace ("AE", "&#198&nbsp;", "PRAELATURA SANCTAE CRUCIS ET OPERIS DEI"); ?></cabecera>
   <region><?php echo str_replace ("AE", "&#198", "STUDIUM GENERALE REGIONIS: &nbsp;HISPANIAE"); ?></region>
   <curso><?php echo str_replace ("AE", "&#198",sprintf("CURSUS INSTITUTIONALES:&nbsp;&nbsp;  %s &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ANNUS: %s",$curso,$any)); ?></curso>
   <curso><?php echo str_replace ("ae", "&#230", "DISCIPLINA: &nbsp;&nbsp;&nbsp;&nbsp;$nombre_asig"); ?></curso>
   <intro>Hisce litteris, quas propria uniuscuiusque subsignatione firmamus, fidem facimus hodierna die, coram infrascriptis Iudicibus, periculum de hac disciplina sequentes alumnos rite superasse:</intro>
<table class="alumni" height="<?= $alum_cara_A ?>">
<tr><td width=65% class=alumni>ALUMNI</td><td  width=10%>&nbsp;</td><td width=25% class=alumni>CUM NOTA</td></tr>
<?php
	$i=0;
	foreach ($aPersonasNotas as $nom => $nota) {
		$i++;
		if ($i > $alum_cara_A) continue;
		?>
		<tr class=alumno>
		<td class=alumno><?php echo $nom; ?>
		</td>
		<td>&nbsp;</td>
		<td class=nota><?php echo $nota; ?></td>
		</tr>
		<?php
	}
	// linea final y linea de salto
	if ($num_alumnos>$alum_cara_A) {
		//echo "<tr><td colspan=3 class=linea >------------------------------------------------------------------------------------------------------------(.../...)</td></tr>";
		echo "<tr><td colspan=2 class=linea ><hr></td><td>(.../...)</td></tr>";
	} else {
		//echo "<tr><td colspan=3 class=linea >------------------------------------------------------------------------------------------------------------</td></tr>";
		echo "<tr><td colspan=3 class=linea ><hr></td></tr>";
	}
	echo "</table>";

}
if ($cara=="B" && $alum_cara_B > 0 ) {
	echo "<tbody><tr height=$alum_cara_B% ><td colspan=3 >";
	echo "<table class=alumni>";
	echo "<tr><td width=65% class=alumni></td><td  width=10%></td><td width=25%></td></tr>";
	//echo "<tr><td colspan=3 class=linea >(.../...)------------------------------------------------------------------------------------------------------------</td></tr>";
	echo "<tr><td colspan=3>(.../...)<hr></td></tr>";
	$i = 0;
	foreach ($aPersonasNotas as $nom => $nota) {
		$i++;
		if ($i <= $lin_max_cara_A) continue;
		?>
		<tr class=alumno>
		<td class=alumno><?php echo $nom; ?>
		</td>
		<td>&nbsp;</td>
		<td class=nota><?php echo $nota; ?></td>
		</tr>
		<?php
	}
	// linea final y linea de salto
	echo "<tr><td colspan=3 class=linea ><hr></td></tr>";
	echo "</tbody></table>";
}

// tribunal -----------------
if ($cara=="A" && $num_alumnos+$lin_tribunal<$lin_max_cara_A) $tribunal=1;
if ($cara=="A" && $num_alumnos+$lin_tribunal>$lin_max_cara_A) $tribunal=0;
if ($cara=="B" && $num_alumnos+$lin_tribunal>=$lin_max_cara_A) $tribunal=1;

if (!empty($tribunal)){
?>
	<tribunal>TRIBUNAL:</tribunal>
	<?php
	$i=0;
foreach ($cTribunal as $oTribunal) {
		$i++;

		$examinador=$oTribunal->getExaminador();
		echo "<examinador>$examinador</examinador>";
	}
	$fecha=$lugar.",  ".data($f_acta);
	echo "<fecha>$fecha</fecha>";
	echo "<sello>L.S.<br>Studii Generalis</sello>";
}
if ($cara=="A") {
?>
</div>
<pie>
<libro>
<b>Reg.</b> StgrH &nbsp;
<b>lib.</b> <?php echo $libro; ?> &nbsp;
<b>pág.</b>  <?php echo $pagina; ?>
<b> n.</b> <?php echo $linea; ?>
</libro>
<acta>(N <?php echo $acta; ?>)</acta>
</pie>
<f7>F7</f7>
<?php
} else {
	echo "</div>";
}
