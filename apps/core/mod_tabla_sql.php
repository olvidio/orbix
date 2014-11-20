<?php
namespace core;
use web;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (empty($_POST['datos_buscar'])) $_POST['datos_buscar']="";
if (empty($_POST['aSerieBuscar'])) $_POST['aSerieBuscar']="";
if (empty($_POST['k_buscar'])) $_POST['k_buscar']="";
if (empty($ficha)) $ficha="main";
if (empty($eliminar_txt)) $eliminar_txt="";
/***************  datos  **********************************/
$padre='datos_sql'; // para indicarle al $dir_datos lo que quiero.

$_POST['datos_tabla'] = urldecode($_POST['datos_tabla']);
$_POST['datos_buscar'] = urldecode($_POST['datos_buscar']);
$_POST['aSerieBuscar'] = urldecode($_POST['aSerieBuscar']);
$_POST['k_buscar'] = urldecode($_POST['k_buscar']);
include(ConfigGlobal::$directorio.'/'.$_POST['datos_tabla']);
// En el caso de aop, la base de datos és distinta. Debo incluir en $_POST['datos_tabla'] la conexión:: $oDB;
/*************** fin datos  **********************************/
$a_dataUrl = array('datos_tabla'=>$_POST['datos_tabla'],'datos_buscar'=>$_POST['datos_buscar'],'aSerieBuscar'=>$_POST['aSerieBuscar'],'k_buscar'=>$_POST['k_buscar']);
$go_to=web\Hash::link(ConfigGlobal::getWeb()."/apps/core/mod_tabla_sql.php?".http_build_query($a_dataUrl));
?>
<script>
fnjs_nuevo=function(formulario){
	$('#mod').val("nuevo");
	$(formulario).attr('action',"apps/core/mod_tabla_form.php");
  	fnjs_enviar_formulario(formulario,'#<?= $ficha ?>');
}
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("editar");
		$(formulario).attr('action',"apps/core/mod_tabla_form.php");
	  	fnjs_enviar_formulario(formulario,'#<?= $ficha ?>');
	}
}
fnjs_eliminar=function(formulario){
	var err;
	var eliminar;
	eliminar="<?= $eliminar_txt ?>";

	if (!eliminar) eliminar="<?= _("¿Está seguro que desea eliminar este registro?") ?>";
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		if (confirm(eliminar) ) {
			go=$('#go_to').val();
			$('#mod').val("eliminar");
			$(formulario).attr('action',"apps/core/mod_tabla_update.php");
			$(formulario).submit(function() {
				$.ajax({
					data: $(this).serialize(),
					url: $(this).attr('action'),
					type: 'post',
					complete: function (rta) {
						rta_txt=rta.responseText;
						if (rta_txt != "") {
							alert(rta_txt); 
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
$a_botones=array( array( 'txt' => _('modificar'), 'click' =>"fnjs_modificar(\"#seleccionados\")" ) ,
				array( 'txt' => _('eliminar'), 'click' =>"fnjs_eliminar(\"#seleccionados\")" ) 
				);

$a_cabeceras=array();
$a_valores=array();
$c=0;
if (is_array($Coleccion)) { // para el caso de estar vacío
foreach ($Coleccion as $oFila) {
	$v=0;	
	$pks=urlsafe_b64encode(serialize($oFila->getPrimary_key()));
	$a_valores[$c]['sel']=$pks;
	//$a_valores[$c]['sel']='a';
	foreach ($oFila->getDatosCampos() as $oDatosCampo) {
		if ($c==0) $a_cabeceras[]=ucfirst($oDatosCampo->getEtiqueta());
		$v++;
		$nom_camp=$oDatosCampo->getNom_camp();	
		$valor_camp=$oFila->$nom_camp;	
		$var_1=$oDatosCampo->getArgument();
		$var_2=$oDatosCampo->getArgument2();
		switch($oDatosCampo->getTipo()) {
			case "array":
				$alista=$oDatosCampo->getLista();
				$a_valores[$c][$v]=$alista[$valor_camp];
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
}

$oHash = new web\Hash();
$oHash->setcamposForm('k_buscar');
$a_camposHidden = array(
		'datos_tabla' => $_POST['datos_tabla'],
		'datos_buscar' => $_POST['datos_buscar'],
		'aSerieBuscar' => $_POST['aSerieBuscar']
		);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new web\Hash();
$oHash1->setcamposForm('sel');
$oHash1->setCamposNo('mod!sel');
$a_camposHidden1 = array(
		'datos_tabla' => $_POST['datos_tabla'],
		'datos_buscar' => $_POST['datos_buscar'],
		'aSerieBuscar' => $_POST['aSerieBuscar'],
		'k_buscar' => $_POST['k_buscar'],
		'go_to' => $go_to
		);
$oHash1->setArraycamposHidden($a_camposHidden1);
/* ---------------------------------- html --------------------------------------- */
if (!empty($_POST['datos_buscar'])) {
	include(ConfigGlobal::$directorio.'/'.$_POST['datos_buscar']);
} else { ?>
<form id="frm_buscar" name="frm_buscar" action="<?= ConfigGlobal::getWeb() ?>/apps/core/mod_tabla_sql.php" method="post" onkeypress="fnjs_enviar(event,this);" >
<?= $oHash->getCamposHtml(); ?>
<table>
<thead>
<th class=titulo_inv colspan=4><?= ucfirst($tit_buscar) ?>
&nbsp;&nbsp;&nbsp;<input class=contenido id="frm_buscar_nom" name="k_buscar" size="25" value="<?= $_POST['k_buscar'] ?>"></th>
<th colspan=4><input type="button" id="ok" name="ok" onclick="fnjs_enviar_formulario(this.form);" value="<?php echo ucfirst(_("buscar")); ?>" class="btn_ok"></th>
</thead>
</table>
</form>
<?php } ?>
<h3 class=subtitulo><?= ucfirst($tit_txt) ?></h3>
<form id='seleccionados' name='seleccionados' action='' method='post'>
<?= $oHash1->getCamposHtml(); ?>
<input type="hidden" id="mod" name="mod" value="" >
<?php
$oTabla = new web\Lista();
// para el id_tabla, convierto los posibles '/' en '_' i tambien quito '.php'
$id_tabla = str_replace('/','_',$_POST['datos_tabla']); 
$id_tabla = str_replace('.php','',$id_tabla); 
$id_tabla = 'mod_tabla_sql_'.$id_tabla;
$oTabla->setId_tabla($id_tabla);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
// ---------- BOTON DE NUEVO ----------
?>
<br><table cellspacing=3  class=botones><tr class=botones>
<td class=botones><input name="btn_new" type="button" value="<?= _("nuevo") ?>" onclick="fnjs_nuevo('#seleccionados');"></td>
</tr></table>
