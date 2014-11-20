<?php
use ubis\model as ubis;
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

//if (isset($id_nom)) $_POST['id_nom'] = $id_nom;

if (!empty($_POST['nuevo'])) {
	$obj_pau = $_POST['obj_pau'];
	$obj = 'personas\\model\\'.$_POST['obj_pau'];
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
} else {
	if (!empty($_POST['sel'])) { //vengo de un checkbox
		//$id_nom=$sel[0];
		$id_nom=strtok($_POST['sel'][0],"#");
		$id_tabla=strtok("#");
	} else {
		empty($_POST['id_nom'])? $id_nom="" : $id_nom=$_POST['id_nom'];
		empty($_POST['id_tabla'])? $id_tabla="" : $id_tabla=$_POST['id_tabla'];
	}

	$obj_pau = $_POST['obj_pau'];
	$obj = 'personas\\model\\'.$_POST['obj_pau'];
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
	$a_campos['nom_ctr'] = $oDesplCentroDl;
	$oDesplCentroDl->setAction("fnjs_act_ctr('ctr')");
	$oDesplCentroDl->setNombre("id_ctr");
}

$_POST['que'] = empty($_POST['que'])? '' : $_POST['que'];
$_POST['breve'] = empty($_POST['breve'])? '' : $_POST['breve'];
$_POST['es_sacd'] = empty($_POST['es_sacd'])? '' : $_POST['es_sacd'];

$ok=0;
$ok_txt=0;
$presentacion="persona.phtml";
switch ($_POST['obj_pau']){
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

if (empty($_POST['nuevo'])) {
	$ir_a_traslado=web\hash::link('apps/personas/controller/traslado_form.php?'.http_build_query(array('pau'=>'p','id_pau'=>$id_nom,'obj_pau'=>$obj_pau)));
	$a_campos['ir_a_traslado'] = $ir_a_traslado;
}


/*
$adossiers="programas/dossiers/dossiers_ver.php?pau=p&id_pau=$id_nom&obj_pau=".$_POST['obj_pau'];
$ahome="programas/dossiers/home_persona.php?id_nom=$id_nom&obj_pau=".$_POST['obj_pau']."&breve=".$_POST['breve']."&es_sacd=".$_POST['es_sacd'];


//$form_action=link_a($go_to,1);
$form_action='';
$alt=_("ver dossiers");
$dos=_("dossiers");
*/

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
		$('#frm2').submit(function() {
			$.ajax({
				data: $(this).serialize(),
				url: $(this).attr('action'),
				type: 'post',
				succes: function (rta_txt) {
					//rta_txt = rta.responseText;
					//if (rta_txt != '' && rta_txt != '\n') {
					if (rta_txt.search('id="ir_a"') != -1) {
						mostra_resposta (rta,'main'); 
					}
				}
			});
			return false;
		});
		$('#frm2').submit();
		$('#frm2').off();
	}
}

fnjs_nuevo=function(f,go){
   $('#onanar').val(f);
   $('#go_to').val(go);
   $('#frm2').attr('action',"programas/ficha_nueva.php");
   fnjs_enviar_formulario('#frm2');
}

fnjs_eliminar=function(){
	if (confirm("<?php echo _("¿Esta seguro que desea elimidar esta ficha?");?>") ) {
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
	
	echo '<div id="top_personas"  name="top_personas">';
   	include ("../view/titulo_persona.phtml"); 
	echo '</div>';
	echo '<div id="ficha_personas" name="ficha_personas">';
}
/**
* Dibuja la ficha
*/
$oView = new core\View('personas\controller');
echo $oView->render($presentacion,$a_campos);

if ($_POST['que'] == 'titulo') {
	echo "</div>";
}
?>
