<?php
use usuarios\model\entity as usuarios;
use ubis\model\entity as ubis;
/**
* Es el frame inferior. Muestra la ficha de los ubis
*
* Se incluye la página ficha.php que contiene la función ficha.
* Esta página sirve para definir los parámetros que se le pasan a la función ficha.
*
*@package	delegacion
*@subpackage	ubis
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
$Qmod = (string) \filter_input(INPUT_POST, 'mod');
$Qobj_dir = (string) \filter_input(INPUT_POST, 'obj_dir');
// id_direccion es string, porque puede ser una lista de varios separados por coma
$Qid_direccion = (string) \filter_input(INPUT_POST, 'id_direccion');
$Qid_direccion = urldecode($Qid_direccion);

switch ($Qobj_dir) {
	case 'DireccionCdc': // tipo dl pero no de la mia
		$obj_x = 'ubis\\model\\entity\\CdcxDireccion';
		$obj_ubi = 'ubis\\model\\entity\\Casa';
		break;
	case 'DireccionCdcDl':
		$obj_x = 'ubis\\model\\entity\\CdcDlxDireccion';
		$obj_ubi = 'ubis\\model\\entity\\CasaDl';
		break;
	case 'DireccionCdcEx':
		$obj_x = 'ubis\\model\\entity\\CdcExxDireccion';
		$obj_ubi = 'ubis\\model\\entity\\CasaEx';
		break;
	case 'DireccionCtr': // tipo dl pero no de la mia
		$obj_x = 'ubis\\model\\entity\\CtrxDireccion';
		$obj_ubi = 'ubis\\model\\entity\\Centro';
		break;
	case 'DireccionCtrDl':
		$obj_x = 'ubis\\model\\entity\\CtrDlxDireccion';
		$obj_ubi = 'ubis\\model\\entity\\CentroDl';
		break;
	case 'DireccionCtrEx':
		$obj_x = 'ubis\\model\\entity\\CtrExxDireccion';
		$obj_ubi = 'ubis\\model\\entity\\CentroEx';
		break;
}
$obj = 'ubis\\model\\entity\\'.$Qobj_dir;

if ($Qmod == 'nuevo') {
	$oUbi = new $obj_ubi($Qid_ubi);
	$sf = $oUbi->getSf();
	$dl = $oUbi->getDl();
	$tipo_ubi = $oUbi->getTipo_ubi();

	$oDireccion = new $obj();
	$cDatosCampo = $oDireccion->getDatosCampos();
	$oDbl = $oDireccion->getoDbl();
	foreach ($cDatosCampo as $oDatosCampo) {
		$camp = $oDatosCampo->getNom_camp();
		$valor_predeterminado=$oDatosCampo->datos_campo($oDbl,'valor');
		$a_campos[$camp] = $valor_predeterminado;

	}
	$idx = 'nuevo';
	$id_direccion = '';
	$golistadir = '';
	$nom_sede = '';
	$direccion = '';
	$a_p = '';
	$c_p = '';
	$poblacion = '';
	$provincia = '';
	$pais = '';
	$observ = '';
	$f_direccion = '';
	$latitud = '';
	$longitud = '';
	$id_direccion_actual = '';
	$mas = '';
	$menos = '';
	$h = '';
} else {
	// puede haber más de una dirección
	$a_id_direccion = explode(',',$Qid_direccion);
	$num_dir = count($a_id_direccion);
	$idx = (integer) \filter_input(INPUT_POST, 'idx');
	$inc = (string) \filter_input(INPUT_POST, 'inc');

	if ($inc == 'mas' & $idx < $num_dir-1) $idx++;
	if ($inc == 'menos' & $idx > 0) $idx--;

	$id_direccion_actual = $a_id_direccion[$idx];
	$oDireccion = new $obj($a_id_direccion[$idx]);

	$xDireccion = new $obj_x(array('id_ubi'=>$Qid_ubi,'id_direccion'=>$a_id_direccion[$idx]));

	$nom_sede = $oDireccion->getNom_sede(); 
	$direccion = $oDireccion->getDireccion(); 
	$a_p = $oDireccion->getA_p(); 
	$c_p = $oDireccion->getC_p(); 
	$cp_dcha = $oDireccion->getCp_dcha(); 
	$poblacion = $oDireccion->getPoblacion(); 
	$provincia = $oDireccion->getProvincia(); 
	$pais = $oDireccion->getPais(); 
	$observ = $oDireccion->getObserv(); 
	$f_direccion = $oDireccion->getF_direccion()->getFromLocal(); 
	$latitud = $oDireccion->getLatitud(); 
	$longitud = $oDireccion->getLongitud(); 
	$propietario = $xDireccion->getPropietario();
	$principal = $xDireccion->getPrincipal();

	$oUbi = new $obj_ubi($Qid_ubi);
	$sf = $oUbi->getSf();
	$dl = $oUbi->getDl();
	$tipo_ubi = $oUbi->getTipo_ubi();

	$mas = ($idx < $num_dir-1)? 1 : 0; 
	$menos = ($idx < 1)? 0 : 1; 
	$idx = $idx;
	$id_direccion = $Qid_direccion;
	$id_direccion_actual = $id_direccion_actual;

	$golistadir = web\Hash::link('apps/ubis/controller/direcciones_que.php?'.http_build_query(array('id_ubi'=>$Qid_ubi,'id_direccion'=>$Qid_direccion,'obj_dir'=>$Qobj_dir)));
	
	$oHashPlano = new web\Hash();
	$oHashPlano->setUrl('apps/ubis/controller/plano_bytea.php');
	$oHashPlano->setCamposForm('obj_dir!act!id_direccion');
	$h = $oHashPlano->linkSinVal();

	$oHashDir = new web\Hash();
	$oHashDir->setUrl('apps/ubis/controller/direcciones_editar.php');
	$oHashDir->setCamposNo('inc');
	$aCamposHidden = [ 'id_ubi' => $Qid_ubi,
	                   'id_direccion' => $Qid_direccion,
	                   'obj_dir' => $Qobj_dir,
	                   'idx' => $idx,
	                   'refresh' => 1,
                     ];
    $oHashDir->setArrayCamposHidden($aCamposHidden);
    $go_dir = $oHashDir->linkConVal();
	//$go_dir = web\Hash::link('apps/ubis/controller/direcciones_editar.php?'.http_build_query(array('id_ubi'=>$Qid_ubi,'id_direccion'=>$Qid_direccion,'obj_dir'=>$Qobj_dir,'idx'=>$idx,'hno'=>'inc')));
	
}

//----------------------------------Permisos según el usuario

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv=core\ConfigGlobal::mi_sfsv();

$botones = 0;
/*
1: guardar cambios
2: eliminar
3: eliminar
4: quitar direccion
*/
if (strstr($Qobj_dir,'Dl')) {
	if ($dl == core\ConfigGlobal::mi_dele()) {
		// ----- sv sólo a scl -----------------
		if ($_SESSION['oPerm']->have_perm("scdl")) {
					$botones= "1,4,5";
		}
	}
} else if (strstr($Qobj_dir,'Ex')) {
	// ----- sv sólo a scl -----------------
	if ($_SESSION['oPerm']->have_perm("scdl")) {
				$botones= "1,4,5";
	}
}
$a_campos['botones'] = $botones;
//------------------------------------------------------------------------

if (empty($Qmod) & empty($Qid_direccion)) {
 	?>
	<table><tr><td><?= _("este ubi no dispone de una dirección. Compruebe primero si existe, en este caso, asígnesela. En caso contrario cree una nueva.") ?></td></tr></table>
	<br>
	<span class="link" onclick="fnjs_update_div('#ficha','<?= $golistadir ?>');">
	<?=  mb_strtoupper(_("asignar una dirección")) ?>
	</span>
	<?php
	die();
}

$chk_dcha = (!empty($cp_dcha) && $cp_dcha=="t")? 'checked' : '';
$chk_propietario = (!empty($propietario) && $propietario=="t")? 'checked' : '';
$chk_principal = (!empty($principal) && $principal=="t")? 'checked' : '';

$campos_chk = 'cp_dcha!propietario!principal';

$oHash = new web\Hash();
$oHash->setcamposForm('a_p!c_p!direccion!f_direccion!latitud!longitud!nom_sede!observ!pais!poblacion!provincia!que');
$oHash->setcamposNo('que!inc'.$campos_chk);
$a_camposHidden = array(
		'campos_chk'=>$campos_chk,
		'obj_dir'=>$Qobj_dir,
		'id_direccion'=>$id_direccion,
		'idx'=>$idx,
		'id_ubi'=>$Qid_ubi
		);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['oPosicion' => $oPosicion,
		'oHash' => $oHash,
		'id_ubi' => $Qid_ubi,
		'id_direccion' => $id_direccion,
		'obj' => $obj,
		'obj_dir' => $Qobj_dir,
		'idx' => $idx,
		'nom_sede' => $nom_sede,
		'chk_propietario' => $chk_propietario,
		'direccion' => $direccion,
		'chk_principal' => $chk_principal,
		'a_p' => $a_p,
		'chk_dcha' => $chk_dcha,
		'c_p' => $c_p,
		'poblacion' => $poblacion,
		'provincia' => $provincia,
		'pais' => $pais,
		'observ' => $observ,
		'f_direccion' => $f_direccion,
		'latitud' => $latitud,
		'longitud' => $longitud,
		'botones' => $botones,
		'id_direccion_actual' => $id_direccion_actual,
		'golistadir' => $golistadir,
		'go_dir' => $go_dir,
		'mas' => $mas,
		'menos' => $menos,
		'h' => $h,
	];

$oView = new core\View('ubis\controller');
echo $oView->render('direccion_form.phtml',$a_campos);
