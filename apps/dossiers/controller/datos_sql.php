<?php
use dossiers\model as dossiers;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/***************  datos  **********************************/
$padre='datos_sql'; // para indicarle al $dir_datos lo que quiero.

$sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($sel)) { //vengo de un checkbox
	// el scroll id es de la página anterior, hay que guardarlo allí
 	$id_sel=$sel;
	$oPosicion->addParametro('id_sel',$id_sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
}
$oPosicion->recordar();

$oTipoDossier = new dossiers\TipoDossier($_POST['id_dossier']);
$app=$oTipoDossier->getApp();

$dir_datos=core\ConfigGlobal::$dir_web."/apps/$app/model/datos_${_POST['id_dossier']}.php";
include($dir_datos);

// para una persona: id_nom=id_pau
// define el <div> que tiene que actualizar. Si lo paso como parámetro no hago nada.
if (!empty($_POST['bloque'])) {
	empty($_POST['bloque'])? $ficha="" : $ficha=$_POST['bloque'];
} else {
	switch ($_POST['pau']) {
		case "p":
			$ficha="ficha_personas";
			break;
		case "u":
			$ficha="ficha_ubis";
			break;
		case "a":
			$ficha="ficha_activ";
			break;
		default:
			$ficha="main";
	}
}
//if (empty($tabla_dossier)) $tabla_dossier=$_POST['tabla_dossier'];
$tabla_dossier = empty($_POST['tabla_dossier'])? '' : $_POST['tabla_dossier'];

if (empty($_POST['go_to'])) {
	$go_to=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$_POST['pau'],'id_pau'=>$_POST['id_pau'],'obj_pau'=>$_POST['obj_pau'],'id_dossier'=>$_POST['id_dossier'],'permiso'=>$_POST['permiso'],'depende'=>$_POST['depende'])));
} else {
	$go_to = urldecode($_POST['go_to']);
}

echo $oPosicion->mostrar_left_slide(1);
?>
<script>
fnjs_nuevo=function(formulario){
	$('#mod').val("nuevo");
	$(formulario).attr('action',"apps/dossiers/controller/datos_form.php");
  	fnjs_enviar_formulario(formulario,'#<?= $ficha ?>');
}
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("editar");
		$(formulario).attr('action',"apps/dossiers/controller/datos_form.php");
	  	fnjs_enviar_formulario(formulario,'#<?= $ficha ?>');
	}
}
fnjs_eliminar=function(formulario){
	var err;
	var eliminar;
	eliminar="<?php if (empty($eliminar_txt)) { echo ''; } else { echo $eliminar_txt; } ?>";

	if (!eliminar) eliminar="<?= _("¿Está seguro que desea eliminar este registro?") ?>";
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		if (confirm(eliminar) ) {
			go=$('#go_to').val();
			$('#mod').val("eliminar");
			$(formulario).attr('action',"apps/dossiers/controller/datos_update.php");
			$(formulario).submit(function() {
				$.ajax({
					data: $(this).serialize(),
					url: $(this).attr('action'),
					type: 'post',
					complete: function (rta) {
						rta_txt=rta.responseText;
						if (rta_txt.search('id="ir_a"') != -1) {
							fnjs_mostra_resposta(rta,'#main'); 
						} else {
							if (go) fnjs_update_div('#main',go); 
						}
					}
				});
				return false;
			});
			$(formulario).submit();
			$(formulario).off();
		}
  	}
}
</script>
<?php

$a_botones=array();
if ($_POST['permiso']==3) {
	$a_botones=array( array( 'txt' => _('modificar'), 'click' =>"fnjs_modificar(\"#seleccionados\")" ) ,
				array( 'txt' => _('eliminar'), 'click' =>"fnjs_eliminar(\"#seleccionados\")" ) 
				);
}


$a_cabeceras=array();
$a_valores=array();
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }
$c=0;
foreach ($Coleccion as $oFila) {
	$v=0;	
	$pks=core\urlsafe_b64encode(serialize($oFila->getPrimary_key()));
	//$pks=str_replace('"','\"',$pks);
	//echo "sel: $pks<br>";
	$a_valores[$c]['sel']=$pks;
	foreach ($oFila->getDatosCampos() as $oDatosCampo) {
		if ($c==0) $a_cabeceras[]=ucfirst($oDatosCampo->getEtiqueta());
		$v++;
		$nom_camp=$oDatosCampo->getNom_camp();	
		$valor_camp=$oFila->$nom_camp;	
		if (!$valor_camp) {
			$a_valores[$c][$v]='';
			continue;
		}
		$var_1=$oDatosCampo->getArgument();
		$var_2=$oDatosCampo->getArgument2();
		switch($oDatosCampo->getTipo()) {
			case "array":
				$a_valores[$c][$v]=$var_1[$valor_camp];
				break;
			case 'depende':
			case 'opciones':
				$oRelacionado = new $var_1($valor_camp);
				$var=$oRelacionado->$var_2;
				if (empty($var)) $var=$valor_camp;
				$a_valores[$c][$v]=$var;	
				break;
			case "check":
				if ($valor_camp=="t") { $a_valores[$c][$v]= _("si"); } else { $a_valores[$c][$v] = _("no"); }
				break;
			default:
				$a_valores[$c][$v]=$valor_camp;
		}
	}
	$c++;
}

$oHash = new web\Hash();
$oHash->setCamposForm('mod');
$oHash->setCamposNo('sel!scroll_id!mod');
$a_camposHidden = array(
		'pau' => $_POST['pau'],
		'id_pau' => $_POST['id_pau'],
		'obj_pau' => $_POST['obj_pau'],
		'id_dossier' => $_POST['id_dossier'],
		'tabla_dossier' => $tabla_dossier,
		'permiso' => $_POST['permiso'],
		'depende' => $_POST['depende'],
		'go_to' => $go_to
		);
$oHash->setArraycamposHidden($a_camposHidden);

/* ---------------------------------- html --------------------------------------- */
?>
<h3 class=subtitulo><?= ucfirst($tit_txt) ?></h3>
<form id='seleccionados' id='seleccionados' name='seleccionados' action='' method='post'>
<?= $oHash->getCamposHtml(); ?>
<input type='hidden' id='mod' name='mod' value=''>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('datos_sql'.$_POST['id_dossier']);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
// ---------- BOTON DE NUEVO ----------
if ($_POST['permiso']==3) {
	?>
	<br><table cellspacing=3  class=botones><tr class=botones>
	<td class=botones><input name="btn_new" type="button" value="<?= _("nuevo") ?>" onclick="fnjs_nuevo('#seleccionados');"></td>
	</tr></table>
	<?php
}
