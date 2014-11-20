<?php
use actividades\model as actividades;
use asistentes\model as asistentes;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_activ=strtok($_POST['sel'][0],"#");
}

if (!empty($_POST['go_to'])) {
	$go_to=urldecode($_POST['go_to']);
} else {
	empty($_POST['go_to'])? $go_to="" : $go_to=$_POST['go_to'];
}

if (!empty($id_activ)) { //caso de modificar
	$mod="editar";
	$oActividad = new actividades\Actividad(array('id_activ'=>$id_activ));
	$nom_activ=$oActividad->getNom_activ();
	$id_tabla=$oActividad->getId_tabla();
	if ($id_tabla == 'dl' OR $id_tabla == 'ex') { 
		switch ($_POST['obj_pau']) {
			case 'PersonaN':
			case 'PersonaNax':
			case 'PersonaAgd':
			case 'PersonaS':
			case 'PersonaSSSC':
			case 'PersonaDl':
				$oAsistente = new asistentes\AsistenteDl(array('id_activ'=>$id_activ,'id_nom'=>$_POST['id_pau']));
				break;
			case 'PersonaEx':
				$oAsistente = new asistentes\AsistenteEx(array('id_activ'=>$id_activ,'id_nom'=>$_POST['id_pau']));
				break;
		}
	} 
	if ($id_tabla == 'out') { 
		$oAsistente = new asistentes\AsistenteOut(array('id_activ'=>$id_activ,'id_nom'=>$_POST['id_pau']));
	} 
	$id_activ_real=$id_activ;
	$propio=$oAsistente->getPropio();
	$falta=$oAsistente->getFalta();
	$est_ok=$oAsistente->getEst_ok();
	$observ=$oAsistente->getObserv();
} else { //caso de nuevo asistente
	$mod="nuevo";
	if (empty($_POST['id_tipo'])) {
		$mi_sfsv = core\ConfigGlobal::mi_sfsv();
		$id_tipo='^'.$mi_sfsv;  //caso genérico para todas las actividades
	} else {
		empty($_POST['id_tipo'])? $id_tipo="" : $id_tipo='^'.$_POST['id_tipo'];
	}
	if (!empty($_POST['que_dl'])) { 
		$aWhere['dl_org']=$_POST['que_dl'];
	} else {
		$aWhere['dl_org']=core\ConfigGlobal::mi_dele();
		$aOperadores['dl_org']='!=';
	}
	
	$aWhere['id_tipo_activ'] = '^'.$id_tipo;
	$aOperadores['id_tipo_activ']='~';
	$aWhere['status']=2;
	$aWhere['_ordre']='f_ini';

	$oGesActividades = new actividades\GestorActividad();
	$cActividades = $oGesActividades->getActividades($aWhere,$aOperadores); 

	$propio="t"; //valor por defecto
	$falta="f"; //valor por defecto
	$est_ok="f"; //valor por defecto
	$observ=""; //valor por defecto
	/*
	  
	$sql_nom= "SELECT nom_activ, id_activ FROM a_actividades WHERE id_tipo_activ::text ~ '$id_tipo' AND status=2 $mis order by f_ini";
	$oDBSt_query_lista=$oDB->query($sql_nom);
	*/
}

$oHash = new web\Hash();
$camposForm = 'observ';
$oHash->setCamposNo('mod!propio!falta!est_ok');
$a_camposHidden = array(
		'id_nom' => $_POST['id_pau'],
		'obj_pau'=> $_POST['obj_pau'],
		'go_to'=> $go_to
		);
if (!empty($id_activ_real)) {
	$a_camposHidden['id_activ'] = $id_activ_real;
} else {
	$camposForm .= '!id_activ';
}
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

?>
<form id="frm_1301" name="frm_1301" action="apps/asistentes/controller/update_3101.php" method="POST">
<?= $oHash->getCamposHtml(); ?>
<input type="Hidden" id="mod" name="mod" value=<?= $mod ?>>
<table>
<tr class=tab><th class=titulo_inv colspan=2><?php echo ucfirst(_("Asistencia a una actividad")); ?></th></tr>
<?php
if (!empty($id_activ_real)) {
	echo "<tr><td class=etiqueta>".ucfirst(_("actividad")).":</td><td class=contenido>$nom_activ</td>";
} else {
	echo "<tr><td class=etiqueta>".ucfirst(_("actividad")).":</td><td><select class=contenido id='id_activ' name='id_activ'>";
	$i=0;
	foreach ($cActividades as $oActividad) {
		$i++;
		$id_activ=$oActividad->getId_activ();
		$nom_activ=$oActividad->getNom_activ();
		//$id_activ==$id_pau ? $chk="selected": $chk=""; 
		echo "<option value=$id_activ>$nom_activ</option>";
	
	}
	echo "</select></td></tr>";
}


$propio=="t" ? $chk="checked" : $chk="" ;
$chk_propio="<input type=\"Checkbox\" id=\"propio\" name=\"propio\" value=\"true\" $chk>";
$falta=="t" ? $chk="checked" : $chk="" ;
$chk_falta="<input type=\"Checkbox\" id=\"falta\" name=\"falta\" value=\"true\" $chk>";
$est_ok=="t" ? $chk="checked" : $chk="" ;
$chk_est_ok="<input type=\"Checkbox\" id=\"est_ok\" name=\"est_ok\" value=\"true\" $chk>";

echo "<tr><td class=etiqueta>"._("propio")."</td><td>$chk_propio</td></tr>";
echo "<tr><td class=etiqueta>"._("falta")."</td><td>$chk_falta</td></tr>";
echo "<tr><td class=etiqueta>"._("estudios confirmados")."</td><td>$chk_est_ok</td></tr>";

?>	
<tr><td class=etiqueta><?php echo ucfirst(_("observaciones")); ?></td><td class=contenido>
<textarea id="observ" name="observ" cols="40" rows="5"><?= htmlspecialchars($observ) ?></textarea></td></tr>
</table>
<br><input type="button" id="guardar" name="guardar" onclick="fnjs_enviar_formulario('#frm_1301');" value="<?php echo ucfirst(_("guardar")); ?>" align="MIDDLE">
