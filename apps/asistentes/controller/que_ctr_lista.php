<?php 
use ubis\model as ubis;
/**
* Formulario para ctr de los listados de profesión y de los asistentes a actividades
*
* Debe pasársele, mediante menú, el contenido de $lista para que haga el link
* correspondiente
*
*@package	delegacion
*@subpackage	personas
*@author	Josep Companys
*@since		15/5/02.
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

$tipo = empty($_POST['tipo'])? '' : $_POST['tipo']; 
$ssfsv = empty($_POST['ssfsv'])? '' : $_POST['ssfsv']; 

switch ($_POST['lista']) {
	case "profesion" :
		$tituloGros=ucfirst(_("listado de profesiones por centros"));
		$titulo=ucfirst(_("buscar en uno ó varios centros"));
		$nomUbi=ucfirst(_("nombre del centro"));
		$action="programas/sm-agd/lista_profesion.php";
		$inputs="<input type=\"Hidden\" id=\"tipo\" name=\"tipo\" value=\"$tipo\" >"; 
		break;
	case "ctrex" :
	case "list_activ" :
		$titulo=ucfirst(_("actividades de personas por centros de la delegación"));
		$tituloGros=ucfirst(_("qué centro interesa?"));
		$nomUbi=ucfirst(_("nombre del centro"));
		$action="apps/asistentes/controller/lista_activ_ctr.php";
		
		$a_camposHidden = array(
			'tipo' => $tipo,
			'ssfsv' => $ssfsv,
			'sasistentes' => $_POST['sasistentes'],
			'sactividad' => $_POST['sactividad']
		);
		break;
	case "list_est" :
		$titulo=ucfirst(_("estudios en actividades de personas por centros de la delegación"));
		$tituloGros=ucfirst(_("qué centro interesa?"));
		$nomUbi=ucfirst(_("nombre del centro"));
		$action="programas/sm-agd/lista_est_ctr.php";
		$inputs = "<input type=\"Hidden\" id=\"tipo\" name=\"tipo\" value=\"$tipo\" >"; 
		$inputs .= "<input type=\"Hidden\" id=\"ssfsv\" name=\"ssfsv\" value=\"$ssfsv\" >";
		$inputs .= "<input type=\"Hidden\" id=\"sasistentes\" name=\"sasistentes\" value=\"${_POST['sasistentes']}\" >";
		$inputs .= "<input type=\"Hidden\" id=\"sactividad\" name=\"sactividad\" value=\"${_POST['sactividad']}\" >";
		break;
}


$n='';
$nj='';
$nm='';
$a='';
$sss='';
$nax='';

switch ($_POST['n_agd']) {
	case "n":
		$n="checked";
		break;
	case "nj":
		$nj="checked";
		break;
	case "nm":
		$nm="checked";
		break;
	case "a":
		$a="checked";
		break;
	case "sss":
		$sss="checked";
		break;
	case "nax":
		$nax="checked";
		break;
}

$oGesCentros= new ubis\GestorCentroDl();
$oDesplCentros = $oGesCentros->getListaCentros("WHERE status = 't' AND tipo_ctr ~ '^a|^n' ");
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setBlanco(true);
$oDesplCentros->setAction('fnjs_otro(1)');

$oHash = new web\Hash();
$oHash->setcamposForm('n_agd!empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
$oHash->setcamposNo('id_ubi');
$oHash->setArraycamposHidden($a_camposHidden);

?>
<script>
fnjs_buscar=function(form){
	var err=0;
	// Hay opciones que no muestran el periodo.
	if ($(form).periodo) {
		// comprobar que tiene el periodo.
		var periodo = $(form).periodo.value;
		if (!periodo) { err=1; }
		if (periodo == 'otro') {
			var min = $(form).empiezamin.value;
			var max = $(form).empiezamax.value;
			if (min && max) {
				err=0;
			} else {
				err=1;
			}
		}
	}
	if (err==1) {
		alert ("<?= _("debe introducir un periodo") ?>");
	} else {
		fnjs_enviar_formulario(form);
	}
}

fnjs_otro=function(v){
	if (v==1) {
		$('#oro').show();
		$('#n_agd_4').checked="true";
	} else {
		$('#oro').hide();	
	}
 }
// por defecto escondido.
$('#oro').hide();	
</script>
<div>
<h2 class="subtitulo"><?= $tituloGros ?></h2>
<form id="modifica" name="modifica" action="<?= $action ?>" method="POST">
<?= $oHash->getCamposHtml(); ?>
<table>
<thead><th class="titulo_inv" colspan=6><?= $titulo ?></th></thead>
<tbody>
<tr><td class="etiqueta" onclick="fnjs_otro(0);">
	<input type="radio" id="n_agd_1" name="n_agd" value="n" <?= $n ?> ><?= ucfirst(_("todos los numerarios"))?></td>
	<td class="etiqueta"><input type="radio" id="n_agd_11" name="n_agd" value="nj" <?= $nj ?> ><?= ucfirst(_("todos ctr numerarios jóvenes"))?></td>
	<td class="etiqueta"><input type="radio" id="n_agd_12" name="n_agd" value="nm" <?= $nm ?> ><?= ucfirst(_("todos ctr numerarios mayores"))?></td>
</tr>
<tr><td class="etiqueta" onclick="fnjs_otro(0);">
<input type="radio" id="n_agd_2" name="n_agd" value="a" <?= $a ?> ><?= ucfirst(_("todos los agregados"))?></td></tr>
<?php 
if (core\ConfigGlobal::mi_sfsv() == 1) { ?>
<tr><td class="etiqueta" onclick="fnjs_otro(0);">
<input type="radio" id="n_agd_3" name="n_agd" value="sss" <?= $sss ?> ><?= ucfirst(_("todos los de sss+"))?></td></tr>
<?php } else { ?>
<tr><td class="etiqueta" onclick="fnjs_otro(0);">
<input type="radio" id="n_agd_3" name="n_agd" value="nax" <?= $nax ?> ><?= ucfirst(_("todos los de nax"))?></td></tr>
<?php } ?>

<tr><td class="etiqueta"><input type="radio" id="n_agd_4" name="n_agd" value="c" onclick="fnjs_otro(1);"><?= ucfirst(_("otro..."))?></td>
<td><span id="oro" class=etiqueta " ><?php echo $oDesplCentros->desplegable(); ?></span></td>

</tbody>
</table>
<?php
if ($_POST['lista']=="list_activ" || $_POST['lista']=="list_est") {
	$aOpciones =  array(
						'curso_ca'=>_('curso ca'),
						'curso_crt'=>_('curso crt'),
						'tot_any' => _('todo el año'),
						'separador'=>'---------',
						'otro'=>_('otro')
						);
	$oFormP = new web\PeriodoQue();
	$oFormP->setFormName('modifica');
	$oFormP->setTitulo(core\strtoupper_dlb(_('período de inicio o finalización de las actividades')));
	$oFormP->setPosiblesPeriodos($aOpciones);
	switch ($_POST['sactividad']) {
		case 'ca':
			$oFormP->setDesplPeriodosOpcion_sel('curso_ca');
			break;
		case 'crt':
			$oFormP->setDesplPeriodosOpcion_sel('curso_crt');
			break;
		default:
			$oFormP->setDesplPeriodosOpcion_sel('tot_any');
			break;
	}
	$oFormP->setDesplAnysOpcion_sel(date('Y'));
	echo $oFormP->getHtml();
}
?>
<table>
<tfoot>
<tr>
	<th colspan=6><input type="button" id="ok" name="ok" onclick="fnjs_buscar('#modifica');" value="<?= ucfirst(_("buscar")) ?>">
	<input TYPE="reset" value="<?= ucfirst(_("borrar")) ?>"></th>
</tr>
</tfoot>
</table>
