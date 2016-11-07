<?php
use ubis\model as ubis;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/***************  datos  **********************************/

// dossier="1001";

	
switch ($_POST['obj_pau']) {
	case 'Casa': // tipo dl pero no de la mia
		$obj_ges_tel = 'ubis\\model\\GestorTelecoCdc';
		$obj_ubi = 'ubis\\model\\Casa';
		break;
	case 'CasaDl':
		$obj_ges_tel = 'ubis\\model\\GestorTelecoCdcDl';
		$obj_ubi = 'ubis\\model\\CasaDl';
		break;
	case 'CasaEx':
		$obj_ges_tel = 'ubis\\model\\GestorTelecoCdcEx';
		$obj_ubi = 'ubis\\model\\CentroEx';
		break;
	case 'Centro': // tipo dl pero no de la mia
		$obj_ges_tel = 'ubis\\model\\GestorTelecoCtr';
		$obj_ubi = 'ubis\\model\\Centro';
		break;
	case 'CentroDl':
		$obj_ges_tel = 'ubis\\model\\GestorTelecoCtrDl';
		$obj_ubi = 'ubis\\model\\CentroDl';
		break;
	case 'CentroEx':
		$obj_ges_tel = 'ubis\\model\\GestorTelecoCtrEx';
		$obj_ubi = 'ubis\\model\\CentroEx';
		break;
}

$oLista=new $obj_ges_tel();
$Coleccion=$oLista->getTelecos(array('id_ubi'=>$_POST['id_ubi']));

$botones = 0;
/*
1: modificar,eliminar,nuevo
*/
if (strstr($_POST['obj_pau'],'Dl')) {
	$oUbi = new $obj_ubi($_POST['id_ubi']);
	$dl = $oUbi->getDl();
	if ($dl == core\ConfigGlobal::mi_dele()) {
		// ----- sv sólo a scl -----------------
		if ($_SESSION['oPerm']->have_perm("scdl")) {
			$botones= "1";
		}
	}
} else if (strstr($_POST['obj_pau'],'Ex')) {
	// ----- sv sólo a scl -----------------
	if ($_SESSION['oPerm']->have_perm("scdl")) {
			$botones= "1";
	}
}

$tit_txt=_("Telecomunicaciones de un centro o casa");
$ficha="ficha_ubis";
?>
<script>
fnjs_nuevo=function(formulario){
	$('#mod').val("nuevo");
	$(formulario).attr('action',"apps/ubis/controller/teleco_editar.php");
  	fnjs_enviar_formulario(formulario,'#<?= $ficha ?>');
}
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("editar");
		$(formulario).attr('action',"apps/ubis/controller/teleco_editar.php");
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
			$('#mod').val("eliminar_teleco");
			$(formulario).attr('action',"apps/ubis/controller/teleco_update.php");
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
if ($botones == 1) {
	$a_botones=array( array( 'txt' => _('modificar'), 'click' =>"fnjs_modificar(\"#seleccionados\")" ) ,
				array( 'txt' => _('eliminar'), 'click' =>"fnjs_eliminar(\"#seleccionados\")" ) 
				);
} else {
	$a_botones = array();
}

$a_cabeceras=array();
$a_valores=array();
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
$oHash->setcamposForm('mod!sel');
$oHash->setcamposNo('mod!sel!scroll_id');
$a_camposHidden = array(
		'id_ubi'=>$_POST['id_ubi'],
		'obj_pau'=>$_POST['obj_pau'],
		);
$oHash->setArraycamposHidden($a_camposHidden);

/* ---------------------------------- html --------------------------------------- */
?>
<h3 class=subtitulo><?= ucfirst($tit_txt) ?></h3>
<form id='seleccionados' id='seleccionados' name='seleccionados' action='' method='post'>
<?= $oHash->getCamposHtml(); ?>
<input type="hidden" id="mod" name="mod" value="" >
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('telecos_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
// ---------- BOTON DE NUEVO ----------
if ($botones == 1) {
	?>
	<br><table cellspacing=3  class=botones><tr class=botones>
	<td class=botones><input name="btn_new" type="button" value="<?= _("nuevo") ?>" onclick="fnjs_nuevo('#seleccionados');"></td>
	</tr></table>
	<?php
}
?>
