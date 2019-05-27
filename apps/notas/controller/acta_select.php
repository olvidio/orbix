<?php
/**
* Esta página muestra una tabla con las actas.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		14/10/03.
*		
*/

use asignaturas\model\entity as asignaturas;
use core\ConfigGlobal;
use notas\model\entity as notas;
use web\Hash;
use web\Lista;
use function core\curso_est;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	
// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_dele = ConfigGlobal::mi_delef();
$mi_region = ConfigGlobal::mi_region();

$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
}

$Qtitulo = (string) \filter_input(INPUT_POST, 'titulo');
$Qacta = (string) \filter_input(INPUT_POST, 'acta');

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array (
				'titulo'=>$Qtitulo,
				'acta'=>$Qacta );
$oPosicion->setParametros($aGoBack,1);

/*miro las condiciones. Si es la primera vez muestro las de este año */
$aWhere = array();
$aOperador = array();
if (!empty($Qacta)) {
	$dl_acta = strtok($Qacta,' ');

	if ($dl_acta == $mi_dele || ($mi_dele == 'cr' && $dl_acta == $mi_region) || $dl_acta == "?") {
		if ($dl_acta == "?") $Qacta = "\?";
		$GesActas = new notas\GestorActaDl();
	} else {
		// si es número busca en la dl.
		$matches = [];
		preg_match ("/^(\d*)(\/)?(\d*)/", $Qacta, $matches);
		if (!empty($matches[1])) {
		    // Para regiones sin dl:
		    if ($mi_dele == 'cr') {
			    $Qacta = empty($matches[3])? "$mi_region ".$matches[1].'/'.date("y") : "$mi_region $Qacta";
		    } else {
			    $Qacta = empty($matches[3])? "$mi_dele ".$matches[1].'/'.date("y") : "$mi_dele $Qacta";
		    }
			$GesActas = new notas\GestorActaDl();
		} else {
			// Si es cr, se mira en todas:
			if (ConfigGlobal::soy_region) {
				$GesActas = new notas\GestorActa();
			} else {
				$GesActas = new notas\GestorActaEx();
			}
		}
	}

	$aWhere['_ordre'] = 'f_acta DESC';
	$aWhere['acta'] = $Qacta;
	$aOperador['acta'] = '~';
	$titulo = $Qtitulo;
} else {
	$mes=date('m');
	if ($mes>9) { $any=date('Y')+1; } else { $any=date("Y"); }
	$inicurs_ca=curso_est("inicio",$any)->format('Y-m-d');
	$fincurs_ca=curso_est("fin",$any)->format('Y-m-d');
	$txt_curso = "$inicurs_ca - $fincurs_ca";
	
	$aWhere['f_acta'] = "'$inicurs_ca','$fincurs_ca'";
	$aOperador['f_acta'] = 'BETWEEN';
	$aWhere['_ordre'] = 'f_acta DESC';
	
	$titulo=ucfirst(sprintf(_("lista de actas del curso %s"),$txt_curso));
	// Si es cr, se mira en todas:
	if (ConfigGlobal::soy_region) {
		$GesActas = new notas\GestorActa();
	} else {
		$GesActas = new notas\GestorActaDl();
	}
}

$cActas = $GesActas->getActas($aWhere,$aOperador);

$botones = 0; // para 'añadir acta'
$a_botones = [];
if ($_SESSION['oPerm']->have_perm("est")) {
	$a_botones[] = array( 'txt' => _("eliminar"), 'click' =>"fnjs_eliminar(\"#seleccionados\")");
	$a_botones[] = array( 'txt' => _("modificar"), 'click' =>"fnjs_modificar(\"#seleccionados\")");
	$botones = 1; // para 'añadir acta'
}
$a_botones[] = array( 'txt' => _("imprimir"), 'click' =>"fnjs_imprimir(\"#seleccionados\")" );

$a_cabeceras = array( array('name'=>ucfirst(_("acta")),'formatter'=>'clickFormatter'), 
		array('name'=>ucfirst(_("fecha")),'class'=>'fecha'),
		_("asignatura"));

$i=0;
$a_valores = array();
foreach ($cActas as $oActa) {
	$i++;
	$acta=$oActa->getActa();
	$f_acta=$oActa->getF_acta()->getFromLocal();
	$id_asignatura=$oActa->getId_asignatura();

	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$nombre_corto = $oAsignatura->getNombre_corto();

	$acta_2=urlencode($acta);
	//$pagina="apps/notas/controller/acta_ver.php?acta=$acta_2";
	$pagina=Hash::link('apps/notas/controller/acta_ver.php?'.http_build_query(array('acta'=>$acta)));
	$a_valores[$i]['sel']=$acta_2;
	if ($_SESSION['oPerm']->have_perm("est")) {
		$a_valores[$i][1]=array( 'ira'=>$pagina, 'valor'=>$acta);
	} else {
		$a_valores[$i][1]=$acta;
	}
	$a_valores[$i][2]=$f_acta;
	$a_valores[$i][3]=$nombre_corto;
}
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }

$oHash = new Hash();
$oHash->setcamposForm('acta');

$oHash1 = new Hash();
$oHash1->setcamposForm('sel!mod');
$oHash1->setCamposNo('sel!scroll_id!mod!refresh');

$oTabla = new Lista();
$oTabla->setId_tabla('acta_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$txt_eliminar = _("esto eliminará los datos del acta, pero no las notas que mantendrán el número de acta");
					
$a_campos = ['oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'oHash1' => $oHash1,
			'titulo' => $titulo,
			'oTabla' => $oTabla,
			'botones' => $botones,
			'txt_eliminar' => $txt_eliminar,
			];

$oView = new core\View('notas/controller');
echo $oView->render('acta_select.phtml',$a_campos);
