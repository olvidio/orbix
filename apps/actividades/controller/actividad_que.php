<?php
use ubis\model as ubis;
/**
* Esta página muestra un formulario con las opciones para escoger la actividad.
*
* Se le pasan las var:
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*@ajax		21/8/2007.
*		
*/

/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	include_once('apps/web/func_web.php');

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$modo = empty($_POST['modo'])? '' : $_POST['modo'];

if (empty($_POST['dl_org'])) { $_POST['dl_org']=''; }
if (empty($_POST['listar_asistentes'])) $_POST['listar_asistentes']=""; 
if (empty($_POST['periodo'])) $_POST['periodo']=""; 
if (empty($_POST['year'])) $_POST['year']= date('Y'); 
if (empty($_POST['empiezamin'])) $_POST['empiezamin']='';
if (empty($_POST['empiezamax'])) $_POST['empiezamax']=''; 
if (empty($_POST['filtro_lugar'])) $_POST['filtro_lugar']=""; 
if (empty($_POST['id_ubi'])) $_POST['id_ubi']=""; 

$id_tipo_activ = empty($_POST['id_tipo_activ'])? '' : $_POST['id_tipo_activ']; 

$oGesDl = new ubis\GestorDelegacion();
$oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones();
$oDesplDelegacionesOrg->setNombre('dl_org');
$oDesplDelegacionesOrg->setOpcion_sel($_POST['dl_org']);
if ($modo == 'importar') {
	$mi_dele = core\ConfigGlobal::mi_dele();
	$oDesplDelegacionesOrg->setOpcion_no(array($mi_dele));
}
if ($modo == 'publicar') {
	$mi_dele = core\ConfigGlobal::mi_dele();
	$oDesplDelegacionesOrg->setOpciones(array($mi_dele=>$mi_dele));
	$oDesplDelegacionesOrg->setBlanco(false);
}

$oDesplDelegaciones = $oGesDl->getListaDlURegionesFiltro();
$oDesplDelegaciones->setAction('fnjs_lugar()');
$oDesplDelegaciones->setNombre('filtro_lugar');
$oDesplDelegaciones->setOpcion_sel($_POST['filtro_lugar']);

$aOpciones =  array(
					'tot_any' => _('todo el año'),
					'trimestre_1'=>_('primer trimestre'),
					'trimestre_2'=>_('segundo trimestre'),
					'trimestre_3'=>_('tercer trimestre'),
					'trimestre_4'=>_('cuarto trimestre'),
					'separador'=>'---------',
					'curso_ca'=>_('curso ca'),
					'curso_crt'=>_('curso crt'),
					'separador1'=>'---------',
					'otro'=>_('otro')
					);
$oFormP = new web\PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($_POST['periodo']);
$oFormP->setDesplAnysOpcion_sel($_POST['year']);
$oFormP->setEmpiezaMin($_POST['empiezamin']);
$oFormP->setEmpiezaMax($_POST['empiezamax']);

$opciones_orden=array("nombre_ubi"=>_("lugar"),
				"f_ini"=>_("empieza"),
				"f_fin"=>_("termina"),
				"apellido1"=>_("sacd"));

$filtro_lugar="";
$ctr=""; 
$ssfsv=""; 

$oHash = new web\Hash();
$oHash->setcamposForm('dl_org!empiezamax!empiezamin!filtro_lugar!iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!periodo!status!year');
$oHash->setcamposNo('id_ubi');
$a_camposHidden = array(
		'modo' => $modo,
		'listar_asistentes' => $_POST['listar_asistentes'],
		'que' => $_POST['que']
		);
$oHash->setArraycamposHidden($a_camposHidden);


$oHash1 = new web\Hash();
$oHash1->setUrl(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php');
$oHash1->setCamposForm('salida!entrada!opcion_sel!isfsv'); 
$h = $oHash1->linkSinVal();

switch ($modo) {
	case 'importar':
		$titulo = ucfirst(_("buscar actividad de otras dl para importar"));
		break;
	case 'publicar':
		$titulo = ucfirst(_("buscar actividades de mi dl para publicar"));
		break;
	default:
		$titulo = ucfirst(_("buscar actividad"));
}
?>
<script>
fnjs_buscar=function(act){
	/* genero el id_tipo_actividad */
	var isfsv=$('#isfsv_val').val();
	var iasistentes=$('#iasistentes_val').val();
	var iactividad=$('#iactividad_val').val();
	var inom_tipo=$('#inom_tipo_val').val();
	if (!isfsv) isfsv=".";
	if (!iasistentes) iasistentes=".";
	if (!iactividad) iactividad=".";
	if (!inom_tipo) inom_tipo="...";
	var id=isfsv+iasistentes+iactividad+inom_tipo;

	$('#id_tipo_activ').val(id);
	$('#modifica').attr('action',act);
	fnjs_enviar_formulario('#modifica');
}
fnjs_lugar=function(){
	var opcion_sel='<?= $_POST['id_ubi'] ?>';
	var isfsv=$('#isfsv_val').val();
	var filtro_lugar=$('#filtro_lugar').val();
	var url='<?= core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php' ?>';
	var parametros='salida=lugar&entrada='+filtro_lugar+'&opcion_sel='+opcion_sel+'&isfsv='+isfsv+'<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';
	$.ajax({
		data: parametros,
		url: url,
		type: 'post',
		dataType: 'html',
		complete: function (rta) {
			rta_txt=rta.responseText;
			$('#lst_lugar').html(rta_txt);
		}
	});
}

fnjs_mas_orden=function(){
	var num=$('#orden_num');
	var orden=$('#mas_orden');
	var id_orden=orden.value;
					
	var n=num.value;
	var txt;
	txt='<select id=orden['+n+'] name=orden['+n+'] class=contenido onchange=comprobar_orden(\'orden['+n+']\') ><option />';
	txt += '<?=	web\options_var($opciones_orden,"",0); ?>';
	txt += '</select>';

	/* antes del desplegable de añadir */
    $('#span_orden').append(txt);
	/* selecciono el valor del desplegable */
	var nom='orden['+n+']';
	$(nom).val(id_orden);
	$('#mas_orden').val(0);
														
	//ir_a('ref_prot_num['+n+']');
	num.value=++n;
}

fnjs_comprobar_orden=function(orden){
	var id_orden=$(orden).val();
	if (!id_orden) {
		$(orden).hide();
	} 
}
fnjs_lugar();
</script>
<div id="exportar" export_modo="formulario">
<form id="modifica"name="modifica" action="" method="post" onkeypress="fnjs_enviar(event,this);" >
<?= $oHash->getCamposHtml(); ?>
<h3 class=subtitulo><?= $titulo; ?></h3>
<table>
<tr><th colspan=3 class=titulo_inv><?php  echo ucfirst(_("escoger el tipo de actividad")); ?></th></tr>
</table>
<?php
include_once("actividad_tipo_que.php");
?>
<table>
<tr>
<td class=etiqueta><?php echo ucfirst(_("estado")); ?>:</td>
	<td><input type="Radio" name="status" value="1" <?php if ($_POST['status']==1) { echo "checked='true'";} ?>><?php echo _("proyecto"); ?></td>
	<td><input type="Radio" name="status" value="2" <?php if ($_POST['status']==2) { echo "checked='true'";} ?>><?php echo _("actual"); ?></td>
	<td><input type="radio" name="status" value="3" <?php if ($_POST['status']==3) { echo "checked='true'";} ?>><?php echo _("terminada"); ?></td>
	<td><input type="radio" name="status" value="4" <?php if ($_POST['status']==4) { echo "checked='true'";} ?>><?php echo _("borrable"); ?></td>
	<td><input type="radio" name="status" value="5" <?php if ($_POST['status']==5) { echo "checked='true'";} ?>><?php echo _("cualquiera"); ?></td>

<td><input type='hidden' id='id_tipo_activ' name='id_tipo_activ'> </td>
</tr>
</table>

<br>
	
<table>
<tr><th colspan=6 class=titulo_inv><?php  echo ucfirst(_("escoger la actividad")); ?></th></tr>
<?php
if (core\ConfigGlobal::mi_id_role() != 8 && core\ConfigGlobal::mi_id_role() != 16) { //centros
?>
<tr>
	<td class=etiqueta><?php echo _("lugar según país o dl"); ?>:</TD>
	<td colspan=3>
	<?php echo $oDesplDelegaciones->desplegable() ?>
	</TD>
	<td class=etiqueta><?php echo _("lugar"); ?></TD>
	<td id='lst_lugar' colspan=1>
	</TD>
</TR>
<tr>
	<td class=etiqueta><?php echo _("organiza"); ?>:</TD>
	<td colspan=3>
	<?php echo $oDesplDelegacionesOrg->desplegable(); ?>
	</TD>
</TR>
<?php
}
?>
<tr><td class=etiqueta><?php echo _("periodo"); ?>:</TD>
<?php echo $oFormP->getTd(); ?>
</tr>
</table>
<?php
/* a continuación distinguimos el caso habitual en que 
vamos a la página actividad_select.php
de los casos particulares de algunos listados, 
en que vamos directamente a
las páginas que los generan*/

switch ($_POST['que']) {
case "list_activ" :
case "list_activ_compl" :
	$act=core\ConfigGlobal::getWeb().'/apps/actividades/controller/lista_activ.php';
	/*es el caso de querer sacar tablas 
	de un grupo de actividades*/
break;
case "list_cjto" :
	$act=core\ConfigGlobal::getWeb().'/apps/actividades/controller/lista_asis_conjunto_activ.php';
	/*es el caso de querer sacar 
	los asistentes o cargos 
	de un conjunto de actividades*/
break;
default;
	$act=core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_select.php';	
	/*es el caso de todo el resto 
	de listados que pasan por un listado 
	previo con los links */
break;		
}
//echo "act:$act<br>";
?>
<br>
<input TYPE="button" onclick="fnjs_buscar('<?php echo $act; ?>')" id="ok" name="ok" value="<?= ucfirst(_("buscar")); ?>" class="btn_ok">
<input TYPE="reset" VALUE="borrar"> 
</form>
<script>
fnjs_lugar(); 
</script>
