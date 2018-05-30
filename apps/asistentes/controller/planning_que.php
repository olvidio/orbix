<?php 
use usuarios\model\entity as usuarios;
/**
* Página que presentará los formularios de los distintos plannings 
* Según sea el submenú seleccionado seleccionará el formulario
* correspondiente
*
*@package	delegacion
*@subpackage	actividades
*@author	Josep Companys
*@since		15/5/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ('apps/core/global_header.inc');
// Arxivos requeridos por esta url **********************************************
	

// Crea los objectos de uso global **********************************************
	require_once ('apps/core/global_object.inc');
// FIN de  Cabecera global de URL de controlador ********************************
$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv = core\ConfigGlobal::mi_sfsv();

if (date('m')>9) {
	$periodo_txt= _('(por defecto: periodo desde 1/10 hasta 31/5)'); 
} else {
	$periodo_txt= _('(por defecto: periodo desde 1/6 hasta 30/9)'); 
}
if (!isset($_POST['tipo'])) $_POST['tipo']="";
if (!isset($_POST['obj_pau'])) $_POST['obj_pau']="";
if (!isset($_POST['na'])) $_POST['na']="";

//personas
$oHash = new web\Hash();
$oHash->setcamposForm('nombre!apellido1!apellido2!centro!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash->setcamposNo('modelo');
$a_camposHidden = array(
		'tipo' => $_POST['tipo'],
		'obj_pau' => $_POST['obj_pau'],
		'na' => $_POST['na']
		);
$oHash->setArraycamposHidden($a_camposHidden);
// centros
$oHash1 = new web\Hash();
$oHash1->setcamposForm('sacd!ctr!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash1->setcamposNo('todos_n!todos_agd!modelo');
$a_camposHidden1 = array(
		'tipo' => $_POST['tipo'],
		'obj_pau' => $_POST['obj_pau'],
		);
$oHash1->setArraycamposHidden($a_camposHidden1);
//casas
$oHash2 = new web\Hash();
$oHash2->setcamposForm('cdc_sel!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash2->setcamposNo('id_cdc!sin_activ!modelo');
$a_camposHidden2 = array(
		'tipo' => $_POST['tipo'],
		'obj_pau' => $_POST['obj_pau']
		);
$oHash2->setArraycamposHidden($a_camposHidden2);

?>
<script>
fnjs_ver_planning = function(formulario,n) {
	periodo = $('#periodo').val();
	if (!periodo) {
		alert('<?= _("Falta definir un periodo") ?>');
	} else {
		$('#modelo').val(n);
		fnjs_enviar_formulario(formulario);
	}
}
</script>
<?php 
switch ($_POST['tipo']) {
	case 'planning':
	case 'p_de_paso':
	//cuando queramos visualizar el calendario de actividades de
	//1 persona de dlb o de paso
	?>		
	<form id="que" name="que" action="apps/asistentes/controller/planning_select.php" method="post" onkeypress="fnjs_enviar(event,this);">
	<input type="hidden" id="modelo" name="modelo" value="">
	<?= $oHash->getCamposHtml(); ?>
		<table>
		<tr><th class=titulo_inv colspan="2">
		<?= core\strtoupper_dlb(_('búsqueda de personas en la obj_pau')); ?></th></tr>
	    <tr>
		<td class=etiqueta><?= ucfirst(_('nombre')); ?></td> 
		<td><input class=contenido id="nombre" name="nombre" size="30"></td></tr>
		<tr>
		<td class=etiqueta><?= ucfirst(_('primer apellido')); ?></td>
		<td><input class=contenido id="apellido1" name="apellido1" size="40"></td></tr>
		<tr> 
		<td class=etiqueta><?= ucfirst(_('segundo apellido')); ?></td>
		<td><input class=contenido id="apellido2" name="apellido2" size="40"></td></tr>
		<?php if ($_POST['tipo']=='planning') { ?>
			<tr>
			<td class=etiqueta><?= ucfirst(_('centro')); ?></td>
			<td><input class=contenido id="centro" name="centro"></td>
			</tr>
		<?php }
		echo "</table>";
	break;
	case 'planning_ctr':
	//cuando queramos visualizar el calendario de actividades de
	//todas las personas de 1 ctr
	?>
	<form id="que" name="que" action="apps/asistentes/controller/planning_crida_calendari.php" method="post">
	<input type="hidden" id="modelo" name="modelo" value="">
	<?= $oHash1->getCamposHtml(); ?>
	<table>
		<tr><th class=titulo_inv colspan="4">
		<?= strtoupper(_('actividades de las personas de un centro')); ?></th></tr>
	    <tr>
		<td class=etiqueta><?= ucfirst(_('centro')); ?></td>
		<td><input class=contenido id="ctr" name="ctr"></td><td class=etiqueta colspan="1"><?= ucfirst(_('(por defecto saldrán todos los n y agd ordenados por ctr)')); ?></td>
		</tr>
		<tr><td class=etiqueta colspan=9>
		<input type="Checkbox" id="todos_n" name="todos_n" value="t" ><?= _('todos los ctr con n'); ?>
		<input type="Checkbox" id="todos_agd" name="todos_agd" value="t" ><?= _('todos los ctr con agd'); ?>	
		</td></tr>
		<tr><td class=etiqueta colspan=9><?= _('incluir sacd:'); ?><input type="Radio" id="sacd" name="sacd" value=0 checked><?= _('no'); ?><input type="Radio" name="sacd" value=1><?= _('si'); ?></td></tr>	
	</table>
	<?php 
	break;
	case 'planning_cdc':
		$oForm = new web\CasasQue();
		$oForm->setTitulo(core\strtoupper_dlb(_('búsqueda de casas cuyo planning interesa')));
		// miro que rol tengo. Si soy casa, sólo veo la mía
		$miRole=$oMiUsuario->getId_role();
		if ($miRole == 9) { //casa
			$id_pau=$oMiUsuario->getId_pau();
			$sDonde=str_replace(",", " OR id_ubi=", $id_pau);
			//formulario para casas cuyo calendario de actividades interesa 
			$donde="WHERE status='t' AND (id_ubi=$sDonde)";
			$oForm->setCasas('casa');
		} else {
			if ($_SESSION['oPerm']->have_perm('des') or $_SESSION['oPerm']->have_perm('vcsd')) {
				$oForm->setCasas('all');
				$donde="WHERE status='t'";
			} else {
				if ($miSfsv == 1) {
					$oForm->setCasas('sv');
					$donde="WHERE status='t' AND sv='t'";
				}
				if ($miSfsv == 2) {
					$oForm->setCasas('sf');
					$donde="WHERE status='t' AND sf='t'";
				}
			}
		}
		?>
		<form name="que" id="que" action="apps/asistentes/controller/planning_crida_calendari.php" method="post">
		<input type="hidden" id="modelo" name="modelo" value="">
		<?= $oHash2->getCamposHtml(); ?>
		<?php
		$oForm->setPosiblesCasas($donde);
		echo $oForm->getHtmlTabla();
		echo _('incluir casas sin actividad:');
		echo "<input type=\"Radio\" id=\"sin_activ\" name=\"sin_activ\" value=0 checked>"._('no');
		echo '<input type="Radio" name="sin_activ" value=1>'. _('si');
		break;
}//tanquem el switch
?>
<br>
<?php
	$aOpciones =  array(
						'tot_any' => _('todo el año'),
						'trimestre_1'=>_('primer trimestre'),
						'trimestre_2'=>_('segundo trimestre'),
						'trimestre_3'=>_('tercer trimestre'),
						'trimestre_4'=>_('cuarto trimestre'),
						'separador'=>'---------',
						'otro'=>_('otro')
						);
	$oFormP = new web\PeriodoQue();
	$oFormP->setFormName('que');
	$oFormP->setTitulo(core\strtoupper_dlb(_('periodo del planning actividades')));
	$oFormP->setPosiblesPeriodos($aOpciones);
	$oFormP->setDesplAnysOpcion_sel(date('Y'));
	echo $oFormP->getHtml();
?>
<br>
<table>
<tfoot>
<tr class=botones>
<?php 
if ($_POST['tipo']=='planning_ctr' || $_POST['tipo']=='planning_cdc' ) { ?>
<td><input TYPE="button" onclick="fnjs_ver_planning(this.form,1)" value="<?= ucfirst(_('planning vista tabla')); ?>" ></td>
<td><input TYPE="button" onclick="fnjs_ver_planning(this.form,3)" value="<?= ucfirst(_('planning vista grid')); ?>" ></td>
<td colspan=2><input TYPE="button" onclick="fnjs_ver_planning(this.form,2)" value="<?= _('Vista para imprimir'); ?>"></td>
<?php } else { ?>
<td><input TYPE="button" onclick="fnjs_ver_planning(this.form,0)" id="btn_ok" name="btn_ok" value="<?= ucfirst(_('buscar')); ?>" class="btn_ok" ></td>
<?php } ?>
</tr></tfoot>
