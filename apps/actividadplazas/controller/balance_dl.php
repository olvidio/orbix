<?php
//use ubis\model as ubis;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Sólo las del tipo...
$Qid_tipo_activ = empty($_POST['Qid_tipo_activ'])? '' : $_POST['Qid_tipo_activ'];

$oTipoActiv= new web\TiposActividades($Qid_tipo_activ);
$sactividad = $oTipoActiv->getActividadText();

$dlA = core\ConfigGlobal::mi_dele();
if (empty($_POST['dl'])) {
	exit();
} else {
	$dlB = $_POST['dl'];
}
// no puedo compararme conmigo mismo:
if ($dlA == $dlB) {	exit(); }

//periodo
switch ($sactividad) {
	case 'ca':
	case 'cv':
		$any=  core\ConfigGlobal::any_final_curs('est');
		$inicurs=core\curso_est("inicio",$any,"est");
		$fincurs=core\curso_est("fin",$any,"est");
		break;
	case 'crt':
		$any=  core\ConfigGlobal::any_final_curs('crt');
		$inicurs=core\curso_est("inicio",$any,"crt");
		$fincurs=core\curso_est("fin",$any,"crt");
		break;
}

$status = 2; //actual

// Seleccionar los id_dl del mismo grupo de estudios
$esquema = core\ConfigGlobal::mi_region();
$a_reg = explode('-',$esquema);
$mi_dl = substr($a_reg[1],0,-1); // quito la v o la f.
$aWhere =array('region'=>$a_reg[0],'dl'=>$mi_dl);
$oMiDelegacion = new ubis\model\Delegacion($aWhere);
$grupo_estudios = $oMiDelegacion->getGrupo_estudios();

$gesDelegacion = new ubis\model\GestorDelegacion();
$cDelegaciones = $gesDelegacion->getDelegaciones(array('grupo_estudios'=>$grupo_estudios,'_ordre'=>'region,dl'));

$gesActividadPlazas = new actividadplazas\model\GestorActividadPlazas();
// Seleccionar actividades exportadas de los id_dl

$a_grupo = array();
$cActividades = array();
$gesActividades = new actividades\model\GestorActividad();
/*
$k = 0;
foreach ($cDelegaciones as $oDelegacion) {
	$k++;
	$dl = $oDelegacion->getDl();
	$id_dl = $oDelegacion->getId_dl();
	$a_grupo[$dl] = $id_dl;
	$aWhere =array('dl_org'			=>$dl,
					'id_tipo_activ'	=>$id_tipo_activ,
					'status' 		=> $status,
					'f_ini' 		=> "'$inicurs','$fincurs'",
					'_ordre'		=>'f_ini');
	$aOperador = array('id_tipo_activ'=>'~', 'f_ini'=>'BETWEEN');
	$cActividades1 = $gesActividades->getActividades($aWhere,$aOperador);
	$cActividades =  array_merge($cActividades,$cActividades1);

}
*/
function PlazasAB_por_actividad($dlA,$dlB,$clase) {
	global $mi_dl,$Qid_tipo_activ,$status,$inicurs,$fincurs;
	global $gesDelegacion;
	global $gesActividades;
	global $gesActividadPlazas;

	$gesAsistentes = new asistentes\model\GestorAsistente();
	
	$cDelegaciones = $gesDelegacion->getDelegaciones(array('dl'=>$dlA));
	$oDelegacionA = $cDelegaciones[0];
	$id_dlA = $oDelegacionA->getId_dl();
	$cDelegaciones = $gesDelegacion->getDelegaciones(array('dl'=>$dlB));
	$oDelegacionB = $cDelegaciones[0];
	$id_dlB = $oDelegacionB->getId_dl();
	
	$aWhereA =array('dl_org'	=> $dlA,
				'id_tipo_activ'	=> '^'.$Qid_tipo_activ,
				'status' 		=> $status,
				'f_ini' 		=> "'$inicurs','$fincurs'",
				'_ordre'		=>'f_ini');
	$aOperador = array('id_tipo_activ'=>'~', 'f_ini'=>'BETWEEN');
	$cActividadesA = $gesActividades->getActividades($aWhereA,$aOperador);
	$i = 0;
	$a_valores = array();
	$sumaConcedidasA = 0;
	$sumaConcedidasB = 0;
	$dlA_c = $dlA.'-c';
	$dlA_l = $dlA.'-l';
	$dlB_c = $dlB.'-c';
	$dlB_l = $dlB.'-l';
	foreach ($cActividadesA as $oActividad) {
		$i++;
		//$id_tipo_activ = $oActividad->getId_tipo_activ();
		$id_activ = $oActividad->getId_activ();
		$nom = $oActividad->getNom_activ();
		$dl_org = $oActividad->getDl_org();
		
		$a_valores[$i]['id'] = $id_activ;
		$a_valores[$i]['actividad'] = $nom;
		$a_valores[$i]['dlorg'] = $dl_org;
		
		$libresA = 0;
		$concedidasA = 0;
		$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_dl'=>$id_dlA,'id_activ'=>$id_activ));
		foreach ($cActividadPlazas as $oActividadPlazas) {
			$dl_tabla = $oActividadPlazas->getDl_tabla();
			if ($dl_org == $dl_tabla) {
				$concedidasA = $oActividadPlazas->getPlazas();
			}
		}
		// ocupadas A
		$ocupadasA = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$dlA);
		if ($ocupadasA < 0) { // No se sabe
			$libresA = '-';
		} else {
			$libresA = $concedidasA - $ocupadasA;
		}
		$sumaConcedidasA +=	$concedidasA;
		
		
		$libresB = 0;
		$concedidasB = 0;
		$txtB = '';
		$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_dl'=>$id_dlB,'id_activ'=>$id_activ));
		foreach ($cActividadPlazas as $oActividadPlazas) {
			$dl_tabla = $oActividadPlazas->getDl_tabla();
			if ($dl_org == $dl_tabla) {
				$concedidasB = $oActividadPlazas->getPlazas();
			}
		}
		// ocupadas B
		$ocupadasB = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$dlB);
		if ($ocupadasB < 0) { // No se sabe
			$libresB = '-';
		} else {
			$libresB = $concedidasB - $ocupadasB;
		}
		$sumaConcedidasB +=	$concedidasB;
		
		$txtB = (empty($concedidasB) && empty($libresB))? '' : "$concedidasB ($libresB libres)";

		if ($dlA == $mi_dl) {
			$a_valores[$i][$dlA_c] = array('editable' => 'true', 'valor' => $concedidasA);
			$a_valores[$i][$dlA_l] = array('editable' => 'false', 'valor' => $libresA);
			//$a_valores[$i][4] = array('editable' => 'true', 'valor' => $txtB);
			$a_valores[$i][$dlB_c] = array('editable' => 'true', 'valor' => $concedidasB);
			$a_valores[$i][$dlB_l] = array('editable' => 'false', 'valor' => $libresB);
		} else {
			//$a_valores[$i][3] = array('editable' => 'false', 'valor' => $txtB);
			$a_valores[$i][$dlB_c] = array('editable' => 'false', 'valor' => $concedidasB);
			$a_valores[$i][$dlB_l] = array('editable' => 'false', 'valor' => $libresB);
			$a_valores[$i][$dlA_c] = array('editable' => 'true', 'valor' => $concedidasA);
			$a_valores[$i][$dlA_l] = array('editable' => 'false', 'valor' => $libresA);
		}
		$a_valores[$i]['clase'] = $clase;
	}
	return array('plazasA'=>$sumaConcedidasA, 'plazasB'=>$sumaConcedidasB, 'a_valores'=>$a_valores);
}

$a_plazasA = PlazasAB_por_actividad($dlA,$dlB,'tono1');
$concedidasA2B = $a_plazasA['plazasB'];
$a_valoresA = $a_plazasA['a_valores'];

$a_plazasB = PlazasAB_por_actividad($dlB,$dlA,'tono2');
$concedidasB2A = $a_plazasB['plazasB'];
$a_valoresB = $a_plazasB['a_valores'];

$a_valores = array_merge($a_valoresA,$a_valoresB);

$txt = "<table>";
$txt .= "<tr><td></td><td> de $dlA a $dlB: $concedidasA2B</td></tr>";
$txt .= "<tr><td></td><td> de $dlB a $dlA: $concedidasB2A</td></tr>";
$txt .= "</table>";

$a_cabeceras=array( 
		array('field'=>'id','name'=>_("id_activ"),'visible'=>'no'),
		array('field'=>'actividad','name'=>ucfirst(_("actividad")),'width'=>100,'formatter'=>'clickFormatter'),
		array('field'=>'dlorg','name'=>_("dl org"),'width'=>10),
		);

$childrenA = array(
		array('name'=>_("concedidas"),'field'=>$dlA."-c",'width'=>15,'editor'=>'Slick.Editors.Integer'),
		array('name'=>_("libres"),'field'=>$dlA."-l",'width'=>15,'editor'=>'Slick.Editors.Integer')
	);
$a_cabeceras[] = array('field'=>$dlA,'name'=>$dlA,'children'=>$childrenA);
$childrenB = array(
		array('name'=>_("concedidas"),'field'=>$dlB."-c",'width'=>15,'editor'=>'Slick.Editors.Integer'),
		array('name'=>_("libres"),'field'=>$dlB."-l",'width'=>15,'editor'=>'Slick.Editors.Integer')
	);
$a_cabeceras[] = array('field'=>$dlB,'name'=>$dlB,'children'=>$childrenB);

//foreach ($a_grupo as $dl => $id_dl) {
//	$a_cabeceras[] = array('name'=>$dl,'width'=>10,'editor'=>'Slick.Editors.Integer');
//}
$a_botones =array();

$oTabla = new web\TablaEditable();
//$oTabla = new web\Lista();
$oTabla->setId_tabla('gestion_plazas');
$UpdateUrl = core\ConfigGlobal::getWeb().'/apps/actividadplazas/controller/gestion_plazas_ajax.php';
$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

echo $txt;
echo $oTabla->mostrar_tabla();
?>