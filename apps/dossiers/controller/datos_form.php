<?php
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//include_once("./func_dossiers.php");
//include_once(core\core\ConfigGlobal::$dir_programas.'/func_web.php');

// las claves primarias se usan para crear el objeto en el include $dir_datos.
// También se pasan por formulario al update.
if (!empty($_POST['sel'])) { //vengo de un checkbox
	$s_pkey=explode('#',$_POST['sel'][0]);
	// he cambiado las comillas dobles por simples. Deshago el cambio.
	$s_pkey = str_replace("'",'"',$s_pkey[0]);
	$a_pkey=unserialize(core\urlsafe_b64decode($s_pkey));
} else { // si es nuevo
	$s_pkey='';
}

/***************  datos  **********************************/
$padre='datos_form'; // para indicarle al $dir_datos lo que quiero.

$dir_datos=core\ConfigGlobal::$dir_web."/apps/dossiers/model/datos_${_POST['id_dossier']}.php";
$web_datos=core\ConfigGlobal::getWeb()."/apps/dossiers/model/datos_${_POST['id_dossier']}.php";
include($dir_datos);

$formulario="";
$camposForm = '';
$camposNo = '';
$oFicha->DBCarregar();
$clasname = get_class($oFicha);
foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
	$tabla=$oDatosCampo->getNom_tabla();	// Para usarlo a la hora de comprobar los campos.
	$nom_camp=$oDatosCampo->getNom_camp();	
	$camposForm .= empty($camposForm)? $nom_camp : '!'.$nom_camp; 
	$valor_camp=$oFicha->$nom_camp;	
	$var_1=$oDatosCampo->getArgument();
	$eti=$oDatosCampo->getEtiqueta();
	$formulario.="<tr><td class=etiqueta>".ucfirst($eti)."</td>";
	switch($oDatosCampo->getTipo()) {
		case "ver":
			if ($_POST['mod'] == 'nuevo') { // si es nuevo lo muestro como texto
				$size= isset($var_1)? $var_1 : '';
				$formulario.="<td class=contenido><input type='text' name='$nom_camp' value=\"".htmlspecialchars($valor_camp)."\" size='$size'></td></tr>";
			} else {
				$formulario.="<td class=contenido>".htmlspecialchars($valor_camp)."</td></tr>";
				$formulario.="<input type='hidden' name='$nom_camp' value=\"".htmlspecialchars($valor_camp)."\"></td></tr>";
			}
			break;
		case "texto":
			$size=$var_1;
			$formulario.="<td class=contenido><input type='text' name='$nom_camp' value=\"".htmlspecialchars($valor_camp)."\" size='$size'></td></tr>";
			break;
		case "fecha":
			$formulario.="<td class=contenido><input class='fecha' type='text' id='$nom_camp' name='$nom_camp' value='$valor_camp' 
							onchange='fnjs_comprobar_fecha(\"#$nom_camp\")'>";	
			break;
		case "opciones":
			$acc=$oDatosCampo->getAccion();
			$var_3=$oDatosCampo->getArgument3();
			$gestor=preg_replace('/\\\(\w*)$/', '\Gestor\1', $var_1);
			$oRelacionado = new $gestor();
			$oDesplegable=$oRelacionado->$var_3();
			$oDesplegable->setOpcion_sel($valor_camp);

			$accion = empty($acc)? '' : "onchange=\"fnjs_actualizar_depende('$nom_camp','$acc');\" ";
			$formulario.="<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\" $accion>";
			$formulario.= $oDesplegable->options();
			$formulario.="</select></td></tr>";
			break;
		case "depende":
			$formulario.="<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\">";
			$formulario.= $despl_depende;
			$formulario.="</select></td></tr>";
			break;
		case "array":
			$oDespl = new web\Desplegable();
			$oDespl->setOpciones($var_1);
			$oDespl->setOpcion_sel($valor_camp);
			$formulario.="<td class=contenido><select name=\"$nom_camp\">";
			$formulario.= $oDespl->options();
			$formulario.="</select></td></tr>";
			break;
		case "check":
			if ($valor_camp=="t") { $chk="checked"; } else { $chk=""; }
			$formulario.="<td class=contenido><input type='checkbox' name='$nom_camp' $chk><td width=70%></td>";
			//los check a falso no se pueden comprobar.
			$camposNo .= empty($camposNo)? $nom_camp : '!'.$nom_camp; 
			break;
	}
}

$oHash = new web\Hash();
$oHash->setcamposForm($camposForm);
$oHash->setCamposNo('sel!'.$camposNo);
$a_camposHidden = array(
		'pau' => $_POST['pau'],
		'id_pau' => $_POST['id_pau'],
		's_pkey' => $s_pkey,
		'obj_pau' => $_POST['obj_pau'],
		'id_dossier' => $_POST['id_dossier'],
		'tabla_dossier' => $_POST['tabla_dossier'],
		'permiso' => $_POST['permiso'],
		'depende' => $_POST['depende'],
		"mod" => $_POST['mod'],
		'go_to' => $_POST['go_to']
		);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new web\Hash();
$oHash1->setUrl($web_datos);
$oHash1->setCamposForm('padre!id_dossier!acc!valor_depende'); 
$h = $oHash1->linkSinVal();


?>
<script>
fnjs_grabar=function(formulario){
	var rr=fnjs_comprobar_campos(formulario,'<?= addslashes($clasname) ?>',1);
	if (rr=='ok') {
		go=$('#go_to').val();
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
fnjs_ir=function(form){
  $(form).attr('action',"<?= $_POST['go_to'] ?>");
  fnjs_enviar_formulario(form); 
}
fnjs_actualizar_depende=function(camp,acc){
	var valor_depende=$('#'+camp).val();
	var parametros='padre=datos_form&id_dossier='+<?= $_POST['id_dossier'] ?>+'&acc='+acc+'&valor_depende='+valor_depende+'<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>'; 
	var url='<?= $web_datos ?>';
	$.ajax({
		data: parametros,
		url: url,
		type: 'post',
		dataType: 'html',
		complete: function (rta) {
			rta_txt=rta.responseText;
			$('#'+acc).html(rta_txt);
		}
	});
	return false;
}
$('#seleccionados').ready(function(){
	$('#seleccionados .fecha').each(function(i){
		$(this).datepicker();
	});
});
</script>
<form id='seleccionados' action='' method='POST' name='seleccionados'>
<?= $oHash->getCamposHtml(); ?>
<h3 class=subtitulo><?= ucfirst($tit_txt) ?></h3>

<h4><?= ucfirst($explicacion_txt) ?></h4>
<table>
<?php 
echo $formulario;
?>
</table>
<br>
<table><tr>
<td><input type="button" name="guardar" value="<?= ucfirst(_("guardar")) ?>" align="MIDDLE" onclick="fnjs_grabar('#seleccionados')"></td>
<td><input type="button" name="atras" value="<?= ucfirst(_("cancelar")) ?>" align="MIDDLE" onclick="fnjs_ir('#seleccionados')"></td></tr>
</table>
