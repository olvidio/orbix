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

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$stack = (integer)  filter_input(INPUT_POST, 'stack');
//Si vengo de vuelta y le paso la referecia del stack donde está la información.
if (!empty($stack)) {
	$oPosicion->goStack($stack);
	$Qmodo = $oPosicion->getParametro('modo');
	$Qque = $oPosicion->getParametro('que');
	$Qstatus = $oPosicion->getParametro('status');
	$Qid_tipo_activ = $oPosicion->getParametro('id_tipo_activ');
	$Qfiltro_lugar = $oPosicion->getParametro('filtro_lugar');
	$Qid_ubi= $oPosicion->getParametro('id_ubi');
	$Qperiodo=$oPosicion->getParametro('periodo');
	$Qinicio=$oPosicion->getParametro('inicio');
	$Qfin=$oPosicion->getParametro('fin');
	$Qyear=$oPosicion->getParametro('year');
	$Qdl_org=$oPosicion->getParametro('dl_org');
	$Qempiezamin=$oPosicion->getParametro('empiezamin');
	$Qempiezamax=$oPosicion->getParametro('empiezamax');
	$Qlistar_asistentes=$oPosicion->getParametro('listar_asistentes');
	$oPosicion->olvidar($stack);
} else { //si tengo los parametros en el $_POST
	$Qmodo = empty($_POST['modo'])? '' : $_POST['modo'];
	$Qque = empty($_POST['que'])? '' : $_POST['que'];
	$Qstatus = empty($_POST['status'])? 2 : $_POST['status'];
	$Qid_tipo_activ = empty($_POST['id_tipo_activ'])? '' : $_POST['id_tipo_activ'];
	$Qfiltro_lugar = empty($_POST['filtro_lugar'])? '' : $_POST['filtro_lugar'];
	$Qid_ubi = empty($_POST['id_ubi'])? '' : $_POST['id_ubi'];
	$Qperiodo = empty($_POST['periodo'])? '' : $_POST['periodo'];
	$Qinicio = empty($_POST['inicio'])? '' : $_POST['inicio'];
	$Qfin = empty($_POST['fin'])? '' : $_POST['fin'];
	$Qyear = empty($_POST['year'])? (integer) date('Y') : $_POST['year'];
	$Qdl_org = empty($_POST['dl_org'])? '' : $_POST['dl_org'];
	$Qempiezamax = empty($_POST['empiezamax'])? '' : $_POST['empiezamax'];
	$Qempiezamin = empty($_POST['empiezamin'])? '' : $_POST['empiezamin'];
	$Qlistar_asistentes = empty($_POST['listar_asistentes'])? '' : $_POST['listar_asistentes'];
}

//para la página actividad_tipo_que.php
$id_tipo_activ = $Qid_tipo_activ;

$oGesDl = new ubis\GestorDelegacion();
$oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones();
$oDesplDelegacionesOrg->setNombre('dl_org');
$oDesplDelegacionesOrg->setOpcion_sel($Qdl_org);
if ($Qmodo == 'importar') {
	$mi_dele = core\ConfigGlobal::mi_dele();
	$oDesplDelegacionesOrg->setOpcion_no(array($mi_dele));
}
if ($Qmodo == 'publicar') {
	$mi_dele = core\ConfigGlobal::mi_dele();
	$oDesplDelegacionesOrg->setOpciones(array($mi_dele=>$mi_dele));
	$oDesplDelegacionesOrg->setBlanco(false);
}

$oDesplDelegaciones = $oGesDl->getListaDlURegionesFiltro();
$oDesplDelegaciones->setAction('fnjs_lugar()');
$oDesplDelegaciones->setNombre('filtro_lugar');
$oDesplDelegaciones->setOpcion_sel($Qfiltro_lugar);

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
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);

$filtro_lugar="";
$ctr=""; 
$ssfsv=""; 

$oHash = new web\Hash();
$oHash->setcamposForm('dl_org!empiezamax!empiezamin!filtro_lugar!iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!periodo!status!year');
$oHash->setcamposNo('id_ubi');
$a_camposHidden = array(
		'modo' => $Qmodo,
		'listar_asistentes' => $Qlistar_asistentes,
		'que' => $Qque
		);
$oHash->setArraycamposHidden($a_camposHidden);


$oHash1 = new web\Hash();
$oHash1->setUrl(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php');
$oHash1->setCamposForm('salida!entrada!opcion_sel!isfsv'); 
$h = $oHash1->linkSinVal();

switch ($Qmodo) {
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
	var opcion_sel='<?= $Qid_ubi ?>';
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
fnjs_left_side_hide();
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
	<td><input type="Radio" name="status" value="1" <?php if ($Qstatus==1) { echo "checked='true'";} ?>><?php echo _("proyecto"); ?></td>
	<td><input type="Radio" name="status" value="2" <?php if ($Qstatus==2) { echo "checked='true'";} ?>><?php echo _("actual"); ?></td>
	<td><input type="radio" name="status" value="3" <?php if ($Qstatus==3) { echo "checked='true'";} ?>><?php echo _("terminada"); ?></td>
	<td><input type="radio" name="status" value="4" <?php if ($Qstatus==4) { echo "checked='true'";} ?>><?php echo _("borrable"); ?></td>
	<td><input type="radio" name="status" value="5" <?php if ($Qstatus==5) { echo "checked='true'";} ?>><?php echo _("cualquiera"); ?></td>

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

switch ($Qque) {
case "list_activ" :
case "list_activ_compl" :
	$act=core\ConfigGlobal::getWeb().'/apps/actividades/controller/lista_activ.php';
	/*es el caso de querer sacar tablas 
	de un grupo de actividades*/
break;
case "list_cjto" :
	$act=core\ConfigGlobal::getWeb().'/apps/asistentes/controller/lista_asis_conjunto_activ.php';
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
<input TYPE="reset" VALUE="borrar" onclick="fnjs_reset_form();"> 
</form>
<script>
fnjs_lugar(); 
</script>
