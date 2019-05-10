<?php
/**
 * Muestra un formulario para introducir/cambiar las notas de una persona
 * 
 *
 * @package	orbix
 * @subpackage	notas
 * @author	Daniel Serrabou
 * @since		15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 *
 * @param string $_POST['pau']  para el controlador dossiers_ver
 * @param integer $_POST['id_pau']  para el controlador dossiers_ver
 * @param string $_POST['obj_pau']  para el controlador dossiers_ver
 * @param integer $_POST['id_dossier']  para el controlador dossiers_ver
 * @param string $_POST['mod']  para el controlador dossiers_ver
 * En el caso de modificar:
 * @param integer $_POST['permiso'] valores 1, 2, 3
 * @param integer $_POST['scroll_id'] 
 * @param array $_POST['sel'] con id_activ#id_asignatura
 * 
 */

use actividades\model\entity as actividades;
use asignaturas\model\entity as asignaturas;
use notas\model\entity as notas;
use personas\model\entity as personas;
use profesores\model\entity as profesores;
use web\Hash;
use actividades\model\entity\Actividad;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$obj = 'notas\\model\\entity\\PersonaNota';

$Qpau = (string) \filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer) \filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string) \filter_input(INPUT_POST, 'obj_pau');
$Qpermiso = (integer) \filter_input(INPUT_POST, 'permiso');
$Qmod = (string) \filter_input(INPUT_POST, 'mod');
		
$sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($sel)) { //vengo de un checkbox
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
	if ($Qpau=="p") { 
		$id_nivel_real=strtok($sel[0],"#"); 
		$Qid_asignatura_real=strtok("#");
	}
} else {
	if (!empty($Qmod) && $Qmod != 'nuevo') {
		$Qid_asignatura_real = (string) \filter_input(INPUT_POST, 'id_asignatura_real');
	} else {
		$Qid_asignatura_real='';
	}
}

$GesNotas = new notas\GestorNota();
$oDesplNotas = $GesNotas->getListaNotas();
$oDesplNotas->setNombre('id_situacion');

$cNotas = $GesNotas->getNotas(array('superada' => 'f'));
$lista_situacion_no_acta = '"11"'; // Para el caso de 'exento', es superada pero sin acta.
foreach ($cNotas as $oNota) {
	$id_situacion = $oNota->getId_situacion(); 
	$lista_situacion_no_acta .= ',"'.$id_situacion.'"' ;
}
		

$GesActividades = new actividades\GestorActividad();
$GesAsignaturas = new asignaturas\GestorAsignatura();

if (!empty($Qid_asignatura_real)) { //caso de modificar
	$mod="editar";
	$id_asignatura=$Qid_asignatura_real;
	$aWhere['id_nom'] = $Qid_pau;
	$aWhere['id_asignatura'] = $Qid_asignatura_real;
	$GesPersonaNotas = new notas\GestorPersonaNota();
	$cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhere);

	$oPersonaNota = $cPersonaNotas[0]; // solo deberia existir una.
	$id_situacion=$oPersonaNota->getId_situacion();
	$nota_num=$oPersonaNota->getNota_num();
	$nota_max=$oPersonaNota->getNota_max();
	$acta=$oPersonaNota->getActa();
	$tipo_acta=$oPersonaNota->getTipo_acta();
	$oF_acta=$oPersonaNota->getF_acta();
	$f_acta=$oF_acta->getFromLocal();
	$f_acta_iso=$oF_acta->format('Y-m-d');
	$preceptor=$oPersonaNota->getPreceptor();
	$id_preceptor=$oPersonaNota->getId_preceptor();
	$detalle=$oPersonaNota->getDetalle();
	$epoca=$oPersonaNota->getEpoca();
	$id_activ=$oPersonaNota->getId_activ();

	$oAsignatura = new asignaturas\Asignatura($Qid_asignatura_real);
	$nombre_corto=$oAsignatura->getNombre_corto();
	if ($oPersonaNota->getId_asignatura() > 3000) {
		$id_nivel=$oPersonaNota->getId_nivel();
	} else {
		$id_nivel=$oAsignatura->getId_nivel();
	}
	
	$GesProfes = new profesores\GestorProfesor();
	$cProfesores= $GesProfes->getProfesores();
	$aProfesores=array();
	$msg_err = '';
	foreach ($cProfesores as $oProfesor) {
		$id_nom=$oProfesor->getId_nom();
		$oPersona = personas\Persona::NewPersona($id_nom);
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
			continue;
		}
		$ap_nom=$oPersona->getApellidosNombre();
		$aProfesores[$id_nom]=$ap_nom;
	}
	uasort($aProfesores,'core\strsinacentocmp');

	$oDesplProfesores = new web\Desplegable();
	$oDesplProfesores->setNombre('id_preceptor');
	$oDesplProfesores->setOpciones($aProfesores);
	$oDesplProfesores->setOpcion_sel($id_preceptor);
	$oDesplProfesores->setBlanco(1);
	
	$cOpcionales = array();
	$aFaltan=array();
	$oDesplNiveles = array();
} else { //caso de nueva asignatura
	$mod="nuevo";
	$id_situacion='';
	$nota_num='';
	$nota_max='';
	$acta='';
	$tipo_acta='';
	$f_acta='';
	$f_acta_iso='';
	$preceptor='';
	$id_preceptor='';
	$detalle='';
	$epoca='';
	$id_activ='';
	$oDesplProfesores =array();
	// todas las asignaturas
	$aWhere=array();
	$aOperador=array();
	$aWhere['status']='t';
	$aWhere['id_nivel']=3000;
	$aOperador['id_nivel']='<';
	$aWhere['_ordre']='id_nivel';
	$cAsignaturas = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
	// todas las opcionales 
	$aWhere=array();
	$aOperador=array();
	$aWhere['status']='t';
	$aWhere['id_nivel']='3000,5000';
	$aOperador['id_nivel']='BETWEEN';
	$aWhere['_ordre']='nombre_corto';
	$cOpcionales = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
	// Asignaturas superadas
	$GesNotas = new notas\GestorNota();
	$cSuperadas = $GesNotas->getNotas(array('superada'=>'t'));
	$cond='';
	$c=0;
	foreach ($cSuperadas as $Nota) {
		if ($c >0 ) $cond.='|';
		$c++;
		$cond.=$Nota->getId_situacion();
	}
	$aWhere=array();
	$aOperador=array();
	$aWhere['id_situacion']=$cond;
	$aOperador['id_situacion']='~';
	$aWhere['id_nom']=$Qid_pau;
	$aWhere['id_nivel']=3000;
	$aOperador['id_nivel']='<';
	$aWhere['_ordre']='id_nivel';
	$GesPersonaNotas = new notas\GestorPersonaNota();
	$cAsignaturasSuperadas = $GesPersonaNotas->getPersonaNotas($aWhere,$aOperador);
	$aSuperadas=array();
	foreach($cAsignaturasSuperadas as $oAsignatura) {
		$id_nivel = $oAsignatura->getId_nivel();
		$id_asignatura = $oAsignatura->getId_asignatura();
		$aSuperadas[$id_nivel]=$id_asignatura;
	}
	// asignaturas posibles
	$aFaltan=array();
	foreach ($cAsignaturas as $oAsignatura) {
		$id_nivel = $oAsignatura->getId_nivel();
		$id_asignatura = $oAsignatura->getId_asignatura();
		$nombre_corto = $oAsignatura->getNombre_corto();
		if (array_key_exists($id_nivel,$aSuperadas)) continue;
		$aFaltan[$id_nivel]=$nombre_corto;
	}
	// Añado Fin Bienio y Fin Cuadrienio
	$aFaltan[9997]='---------';
	$aFaltan[9998]=_("fin cuadrienio");
	$aFaltan[9999]=_("fin bienio");

	$oDesplNiveles = new web\Desplegable();
	$oDesplNiveles->setNombre('id_nivel');
	$oDesplNiveles->setOpciones($aFaltan);
	$oDesplNiveles->setBlanco(1);
	$oDesplNiveles->setAction('fnjs_cmb_opcional()');
}

// Valores por defecto
$nota_max_default = $_SESSION['oConfig']->getNota_max();
$nota_max = empty($nota_max)? $nota_max_default : $nota_max;
$id_situacion = empty($id_situacion)? 10 : $id_situacion;

if (!empty($preceptor)) {
	$chk_preceptor = "checked";
} else {
	$chk_preceptor = "";
}
$oDesplNotas->setOpcion_sel($id_situacion);

if (!empty($tipo_acta)) {
	if ($tipo_acta==notas\PersonaNota::FORMATO_ACTA) { $chk_acta="checked"; } else { $chk_acta=""; }
	if ($tipo_acta==notas\PersonaNota::FORMATO_CERTIFICADO) { $chk_certificado="checked"; } else { $chk_certificado=""; }
} else {
	$chk_acta="checked";
	$chk_certificado="";
}

if (!empty($epoca)) {
	if ($epoca==notas\PersonaNota::EPOCA_CA) { $chk_epoca_ca="checked"; } else { $chk_epoca_ca=""; }
	if ($epoca==notas\PersonaNota::EPOCA_INVIERNO) { $chk_epoca_inv="checked"; } else { $chk_epoca_inv=""; }
	if ($epoca==notas\PersonaNota::EPOCA_OTRO) { $chk_epoca_otro="checked"; } else { $chk_epoca_otro=""; }
} else {
	$chk_epoca_ca="checked";
	$chk_epoca_inv="";
	$chk_epoca_otro="";
}

if (!empty($id_activ)) {
    $oActividad = new Actividad($id_activ);
    $nom_activ = $oActividad->getNom_activ();
} else {
    $nom_activ = '';
}


// miro cuales son las opcionales genéricas, para la funcion
//  fnjs_cmb_opcional de javascript.
// la condicion es que tengan id_sector=1
$aWhere=array();
$aOperador=array();
$aWhere['status']='t';
$aWhere['id_sector']=1;
$aWhere['id_nivel']=3000;
$aOperador['id_nivel']='<';
$aWhere['_ordre']='nombre_corto';
$cOpcionalesGenericas = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
$condicion='';
$lista_nivel_op='';
foreach ($cOpcionalesGenericas as $oOpcional) {
	$id_nivel_j = $oOpcional->getId_nivel();
	$condicion.="id==".$id_nivel_j." || ";
	$lista_nivel_op.=$id_nivel_j.",";
}
$condicion_js=substr($condicion,0,-4);


$oHash = new web\Hash();
$campos_chk = '!preceptor';
$camposForm = 'preceptor!nota_num!nota_max!id_situacion!acta!tipo_acta!f_acta!preceptor!id_preceptor!epoca!id_activ!detalle';
$camposNo = 'refresh!id_preceptor!id_activ'.$campos_chk;
$a_camposHidden = array(
		'campos_chk'=>$campos_chk,
		'mod' => $mod,
		'pau' => $Qpau,
		'id_pau' => $Qid_pau,
		'obj_pau' => $Qobj_pau,
		'permiso' => $Qpermiso,
        'id_activ' => $id_activ,
		);

if (!empty($Qid_asignatura_real)) { //caso de modificar
	$a_camposHidden['id_asignatura_real'] = $Qid_asignatura_real;
	$a_camposHidden['id_asignatura'] = $Qid_asignatura_real;
	$a_camposHidden['id_nivel'] = $id_nivel;
} else {
	$camposForm .= '!id_nivel!id_asignatura';
	$camposNo .= '!id_nivel!id_asignatura';
}
$oHash->setcamposForm($camposForm);
$oHash->setcamposNo($camposNo);
$oHash->setArraycamposHidden($a_camposHidden);

$url_ajax = core\ConfigGlobal::getWeb().'/apps/notas/controller/notas_ajax.php';
$oHash1 = new Hash();
$oHash1->setUrl($url_ajax);
$oHash1->setCamposForm('que!id_nom'); 
//$oHash1->setCamposNo('id_nom'); 
$h1 = $oHash1->linkSinVal();
$oHash2 = new Hash();
$oHash2->setUrl($url_ajax);
$oHash2->setCamposForm('que'); 
$h2 = $oHash2->linkSinVal();


$oHashMod = new Hash();
$oHashMod->setUrl($url_ajax);
$oHashMod->setcamposForm('dl_org!f_acta_iso!que');
$h_modificar = $oHashMod->linkSinVal();

$oHashActa = new Hash();
$oHashActa->setUrl($url_ajax);
$oHashActa->setcamposForm('acta!que');
$h_acta = $oHashActa->linkSinVal();

$op_genericas = $GesAsignaturas->getListaOpGenericas('json');

$a_campos = [
			'obj' => $obj, //sirve para comprobar campos
			'oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'url_ajax' => $url_ajax,
			'h1' => $h1,
			'h2' => $h2,
            'h_modificar' => $h_modificar,
            'h_acta' => $h_acta,
            'op_genericas' => $op_genericas,
			'condicion_js' => $condicion_js,
			'Qid_asignatura_real' => $Qid_asignatura_real,
			'nombre_corto' => $nombre_corto,
			'oDesplNiveles' => $oDesplNiveles,
			'nota_num' => $nota_num,
			'nota_max' => $nota_max,
			'nota_max_default' => $nota_max_default,
			'oDesplNotas' => $oDesplNotas,
			'chk_acta' => $chk_acta,
			'chk_certificado' => $chk_certificado,
			'acta' => $acta,
			'f_acta' => $f_acta,
			'f_acta_iso' => $f_acta_iso,
			'chk_preceptor' => $chk_preceptor,
			'id_preceptor' => $id_preceptor,
			'oDesplProfesores' => $oDesplProfesores,
			'epoca' => $epoca,
			'chk_epoca_ca' => $chk_epoca_ca,
			'chk_epoca_inv' => $chk_epoca_inv,
			'chk_epoca_otro' => $chk_epoca_otro,
            'nom_activ' => $nom_activ,
			'detalle' => $detalle,
			'lista_situacion_no_acta' => $lista_situacion_no_acta,
			];

$oView = new core\View('notas/model');
echo $oView->render('form_1011.phtml',$a_campos);