<?php
use ubis\model\entity as ubis;
/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ("apps/web/func_web.php");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$nuevo = (integer)  filter_input(INPUT_POST, 'nuevo');
$obj_pau = (string)  filter_input(INPUT_POST, 'obj_pau');
	
$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$id_activ = strtok($a_sel[0],"#");
	$id_asignatura=strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
}

if (!empty($nuevo)) {
	$obj = 'personas\\model\\entity\\'.$obj_pau;
	$oPersona = new $obj;
	$cDatosCampo = $oPersona->getDatosCampos();
	$oDbl = $oPersona->getoDbl();
	foreach ($cDatosCampo as $oDatosCampo) {
		$camp = $oDatosCampo->getNom_camp();
		$valor_predeterminado=$oDatosCampo->datos_campo($oDbl,'valor');
		$a_campos[$camp] = $valor_predeterminado;
	}
	$a_campos['f_situacion'] = date('j/m/Y');
	$a_campos['id_nom'] = '';
	$a_campos['obj'] = $oPersona;
	$a_campos['id_tabla'] = empty($_POST['id_tabla'])? '' : $_POST['id_tabla'];
} else {
	if (!empty($a_sel)) { //vengo de un checkbox
		$id_nom = strtok($a_sel[0],"#");
		$id_tabla=strtok("#");
		// el scroll id es de la página anterior, hay que guardarlo allí
		$oPosicion->addParametro('id_sel',$a_sel,1);
		$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
		$oPosicion->addParametro('scroll_id',$scroll_id,1);
	} else {
		empty($_POST['id_nom'])? $id_nom="" : $id_nom=$_POST['id_nom'];
		empty($_POST['id_tabla'])? $id_tabla="" : $id_tabla=$_POST['id_tabla'];
	}

	$obj = 'personas\\model\\entity\\'.$obj_pau;
	$oPersona = new $obj($id_nom);
	$a_campos = $oPersona->getTot();
	$a_campos['obj'] = $oPersona;
	// para el ctr hay que buscar el nombre
	if (!empty($a_campos['id_ctr'])) {
		$id_ctr = $a_campos['id_ctr'];
		$oCentroDl = new ubis\CentroDl($id_ctr);
		$a_campos['nom_ctr'] = $oCentroDl->getNombre_ubi();
	}
}

// para el ctr, si es nuevo o está vacio
if (empty($a_campos['nom_ctr'])) {
	//$id_ctr = $a_campos['id_ctr'];
	$GesCentroDl = new ubis\GestorCentroDl();
	$oDesplCentroDl = $GesCentroDl->getListaCentros();
	$a_campos['oDesplCentro'] = $oDesplCentroDl;
	$oDesplCentroDl->setAction("fnjs_act_ctr('ctr')");
	$oDesplCentroDl->setNombre("id_ctr");
}

$_POST['que'] = empty($_POST['que'])? '' : $_POST['que'];
$_POST['breve'] = empty($_POST['breve'])? '' : $_POST['breve'];
$_POST['es_sacd'] = empty($_POST['es_sacd'])? '' : $_POST['es_sacd'];

$ok=0;
$ok_txt=0;
$presentacion="persona.phtml";
switch ($obj_pau){
	case "PersonaAgd":
		$a_campos['id_tabla'] = 'a';
		if ($_SESSION['oPerm']->have_perm("agd")) { $ok=1; } 
		if (($_SESSION['oPerm']->have_perm("agd") or $_SESSION['oPerm']->have_perm("dtor")) and ($_POST['breve']!="true")) {
			//$presentacion="p_agregados.phtml";
			$presentacion="persona.phtml";
			$ok_txt=1;
		} else {
			$presentacion="p_public_personas.phtml";
		}
		break;
	case "PersonaN":
		$a_campos['id_tabla'] = 'n';
		if ($_SESSION['oPerm']->have_perm("sm")) { $ok=1; } 
		if (($_SESSION['oPerm']->have_perm("sm") or $_SESSION['oPerm']->have_perm("dtor")) and ($_POST['breve']!="true")) { 
			//$presentacion="p_numerarios.phtml";
			$presentacion="persona.phtml";
			$ok_txt=1;
		} else {
			$presentacion="p_public_personas.phtml";
		}
		break;
	case "PersonaNax":
		$a_campos['id_tabla'] = 'x';
		if ($_SESSION['oPerm']->have_perm("sm")) { $ok=1; } 
		if (($_SESSION['oPerm']->have_perm("sm") or $_SESSION['oPerm']->have_perm("dtor")) and ($_POST['breve']!="true")) { 
			//$presentacion="p_numerarios.phtml";
			$presentacion="persona.phtml";
			$ok_txt=1;
		} else {
			$presentacion="p_public_personas.phtml";
		}
		break;
	case "PersonaS":
		$a_campos['id_tabla'] = 's';
		if ($_SESSION['oPerm']->have_perm("sg")) { $ok=1; } 
		if (($_SESSION['oPerm']->have_perm("sg") or $_SESSION['oPerm']->have_perm("dtor")) and ($_POST['breve']!="true")) { 
			//$presentacion="p_supernumerarios.phtml";
			$presentacion="persona.phtml";
			$ok_txt=1;
		} else {
			$presentacion="p_public_personas.phtml";
		}
		break;
	case "PersonaSSSC":
		$a_campos['id_tabla'] = 'sssc';
		if ($_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("vcsd")) { $ok=1; } 
		if (($_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("dtor")) and ($_POST['breve']!="true")) { 
			//$presentacion="p_sssc.phtml";
			$presentacion="persona.phtml";
			$ok_txt=1;
		} else {
			$presentacion="p_public_personas.phtml";
		}
		break;
	case "PersonaEx":
		if (empty($a_campos['id_tabla'])) $a_campos['id_tabla'] = 'pn';
		$presentacion="persona_de_paso.phtml";
		if ($_SESSION['oPerm']->have_perm("agd") or $_SESSION['oPerm']->have_perm("sm") or $_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("est")) { $ok=1; } 
		$ok_txt=1;
		break;
}
$a_campos['obj_pau'] = $obj_pau;

if (empty($nuevo)) {
	$ir_a_traslado=web\hash::link('apps/personas/controller/traslado_form.php?'.http_build_query(array('pau'=>'p','id_pau'=>$id_nom,'obj_pau'=>$obj_pau)));
	$a_campos['ir_a_traslado'] = $ir_a_traslado;
}

$botones = 0;
/*
1: guardar cambios
2: eliminar
3: formato texto
*/
if (!$_POST['breve'] && $ok==1) {	
	$botones = '1';
	// de momento se lo permito a los de paso i cp
	if ($obj_pau == 'PersonaEx') {
		$botones .= ',2';
	}
}
if 	($ok_txt==1) {
	//$botones .= ',3'; // de momento no lo pongo
}

$a_campos['botones'] = $botones;
//------------------------------------------------------------------------

echo $oPosicion->mostrar_left_slide(1);
?>
<script>

fnjs_act_ctr=function (camp) {
	var centre, camp, idCamp;
	var dDate = new Date();
	var mes=dDate.getMonth()+1;
	var fecha=dDate.getDate()+'/'+mes+'/'+dDate.getFullYear();
	var f;
	idCamp='#id_'+camp;
	centre=$(idCamp).val();
	$(camp).val(centre);
	// también la fecha
	//f='f_'+camp;
	//$(f).val(fecha);
}

fnjs_guardar=function(){
	var rr=fnjs_comprobar_campos('#frm2','<?= addslashes($obj) ?>');
	//alert ("EEE "+rr);
	if (rr=='ok') {
		$('#que').val('guardar');
		$('#frm2').attr('action',"apps/personas/controller/personas_update.php");
		fnjs_enviar_formulario('#frm2');
	}
}

fnjs_eliminar=function(){
	if (confirm("<?= _("¿Esta seguro que desea eliminar esta ficha?");?>") ) {
		$('#que').val('eliminar');
		$('#frm2').attr('action',"apps/personas/controller/personas_update.php");
		fnjs_enviar_formulario('#frm2');
	}
}
</script>
<?php
if ($_POST['que'] == 'titulo') {
	$gohome=web\Hash::link('apps/personas/controller/home_persona.php?'.http_build_query(array('id_nom'=>$id_nom,'obj_pau'=>$obj_pau))); 
	$godossiers=web\Hash::link('apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>'p','id_pau'=>$id_nom,'obj_pau'=>$obj_pau)));
	$dos=_("dossiers");
	$alt=_("ver dossiers");
	$titulo = $oPersona->getNombreApellidos();
	
	echo '<div id="top"  name="top">';
   	include ("../view/titulo_persona.phtml"); 
	echo '</div>';
	echo '<div id="ficha" name="ficha">';
}
/**
* Dibuja la ficha
*/
$oView = new core\View('personas\controller');
echo $oView->render($presentacion,$a_campos);

if ($_POST['que'] == 'titulo') {
	echo "</div>";
}