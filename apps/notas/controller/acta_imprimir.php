<?php
use asignaturas\model\entity as asignaturas;
use core\ConfigGlobal;
use notas\model\entity as notas;
use personas\model\entity as personas;
use web\Hash;
use function core\strtoupper_dlb;

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

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Arxivos requeridos por esta url **********************************************
	include_once(ConfigGlobal::$dir_estilos.'/actas.css.php'); 

// FIN de  Cabecera global de URL de controlador ********************************

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


$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$acta=urldecode(strtok($a_sel[0],"#"));
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$Qacta = (string) \filter_input(INPUT_POST, 'acta');
	$acta = empty($Qacta)? '' : urldecode($Qacta);
}

$Qcara = (string) \filter_input(INPUT_POST, 'cara');
$cara = empty($Qcara)? 'A' : $Qcara;

// conversion
$replace  = array(
    'AE' => '&#198;',
    'Ae' => '&#198;',
    'ae' => '&#230;',
    'OE' => '&#140;',
    'Oe' => '&#140;',
    'oe' => '&#156;'
);
$region_latin = $_SESSION['oConfig']->getNomRegionLatin();
$nombre_prelatura = strtr("PRAELATURA SANCTAE CRUCIS ET OPERIS DEI", $replace);
$reg_stgr = "Stgr".ConfigGlobal::mi_region();

// acta
$oActa = new notas\Acta($acta);
$id_asignatura = $oActa->getId_asignatura();
$id_activ = $oActa->getId_activ();
$oF_acta = $oActa->getF_acta();
$libro = $oActa->getLibro();
$pagina = $oActa->getPagina();
$linea = $oActa->getLinea();
$lugar = $oActa->getLugar();
$observ = $oActa->getObserv();

$oAsignatura = new asignaturas\Asignatura($id_asignatura);
$nombre_corto=$oAsignatura->getNombre_corto();
$nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
$any=$oAsignatura->getYear();

$id_tipo=$oAsignatura->getId_tipo();
$oAsignaturaTipo = new asignaturas\AsignaturaTipo($id_tipo);
$curso = strtr($oAsignaturaTipo->getTipo_latin(), $replace);

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
$aWhere = [];
$aOperador = [];

$GesNotas  = new notas\GestorNota();
$aIdSuperadas = $GesNotas->getArrayNotasSuperadas();
$superadas_txt = "{".implode(', ',$aIdSuperadas)."}";

$aWhere['id_situacion'] = $superadas_txt;
$aOperador['id_situacion'] = 'ANY';
$aWhere['acta'] = $acta;

$GesPersonaNotas = new notas\GestorPersonaNota();
$cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhere,$aOperador);

// para ordenar
$errores = '';
$aPersonasNotas = array(); 
$oGesNomLatin = new personas\GestorNombreLatin();
foreach($cPersonaNotas as $oPersonaNota) {
	$id_situacion=$oPersonaNota->getId_situacion();
	$id_nom=$oPersonaNota->getId_nom();
	$oPersona = personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$errores .= "<br>".sprintf(_("existe una nota de la que no se tiene acceso al nombre (id_nom = %s): es de otra dl o 'de paso' borrado."),$id_nom);
		$errores .= " " . _("no aparece en la lista");
		continue;
	}
	$apellidos=$oPersona->getApellidos();
	$trato=$oPersona->getTrato();
	$nom_v=$oPersona->getNom();
	$nom_lat = $oGesNomLatin->getVernaculaLatin($nom_v);

	//Ni la función del postgresql ni la del php convierten los acentos.
	$apellidos = trim($apellidos);

	$apellidos = empty($apellidos)? '????' : $apellidos;
	$apellidos=strtoupper_dlb($apellidos);
	$nom = $apellidos.", ".$trato.$nom_lat;
		
	//echo "<br>$id_nom, $apellidos";
	$nota = $oPersonaNota->getNota_txt();
	$aPersonasNotas[$nom] = $nota;
}
uksort($aPersonasNotas, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.

$num_alumnos=count($aPersonasNotas);

// tribunal:
//$GesTribunal = new notas\GestorActaTribunalDl();
$GesTribunal = new notas\GestorActaTribunal();
$cTribunal = $GesTribunal->getActasTribunales(array('acta'=>$acta,'_ordre'=>'orden')); 
$num_examinadores=count($cTribunal);

// Definición del número de lineas de las páginas y los numeros de alumnos----------------
$lin_A4=45;										// número máximo de lineas en un A4
$lin_encabezado=16;								// número de lineas del encabezado asignatura + pie
$lin_encabezado_tribunal=4;						// número de lineas del encabezado tribunal
$lin_tribunal=$lin_encabezado_tribunal+2*$num_examinadores;  // número de lineas del tribunal

$lin_max_cara_A=$lin_A4 - $lin_encabezado - 2; 	// número máximo de lineas en la cara A 

if ($num_alumnos > $lin_max_cara_A) { $alum_cara_A=$lin_max_cara_A; } else { $alum_cara_A=$num_alumnos; }
$alum_cara_B=$num_alumnos-$alum_cara_A;

$caraA = Hash::link('apps/notas/controller/acta_imprimir.php?'.http_build_query(array('cara'=>'A','acta'=>$acta,'refresh'=>1)));
$caraB = Hash::link('apps/notas/controller/acta_imprimir.php?'.http_build_query(array('cara'=>'B','acta'=>$acta,'refresh'=>1)));

$oHash = new Hash();
$oHash->setUrl(ConfigGlobal::getWeb().'/apps/notas/controller/acta_2_mpdf.php');
$oHash->setCamposForm('acta');
$h = $oHash->linkSinVal();

$lugar_fecha = $lugar.",  ".$oF_acta->getFechaLatin();

$a_campos = [
			'oPosicion' => $oPosicion,
			'h' => $h,
			'cara' => $cara,
			'caraA' => $caraA,
			'caraB' => $caraB,
			'acta' => $acta,
			'errores' => $errores,
			'curso' => $curso,
			'any' => $any,
            'region_latin' => $region_latin,
            'nombre_prelatura' => $nombre_prelatura,
            'reg_stgr' => $reg_stgr,
			'nombre_asignatura' => $nombre_asignatura,
			'alum_cara_A' => $alum_cara_A,
			'alum_cara_B' => $alum_cara_B,
			'aPersonasNotas' => $aPersonasNotas,
			'num_alumnos' => $num_alumnos,
			'lin_max_cara_A' => $lin_max_cara_A,
			'lin_tribunal' => $lin_tribunal,
			'cTribunal' => $cTribunal,
			'lugar' => $lugar,
			'lugar_fecha' => $lugar_fecha,
			'libro' => $libro,
			'pagina' => $pagina,
			'linea' => $linea,
			];

$oView = new core\View('notas/controller');
echo $oView->render('acta_imprimir.phtml',$a_campos);
