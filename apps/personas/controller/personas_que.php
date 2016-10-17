<?php
/**
* Esta página muestra un formulario con las opciones para escoger a una persona.
*
*@package	delegacion
*@subpackage	fichas
*@author	Daniel Serrabou
*@since		15/5/02.
*@ajax		8/2007.
*		
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (empty($_POST['es_sacd'])) $_POST['es_sacd']="";
if (empty($_POST['na'])) $_POST['na']="";
if (empty($_POST['breve'])) $_POST['breve']="";
if (empty($_POST['que'])) $_POST['que']="";
if (!empty($_POST['tabla'])) {
	empty($_POST['tabla'])? $tabla="" : $tabla=$_POST['tabla'];
	$nom_tabla=substr($tabla,2);
	if ($nom_tabla=="de_paso") $nom_tabla=$_POST['na']." ".$nom_tabla;
	if (!empty($_POST['es_sacd'])) $nom_tabla="sacd ".$nom_tabla;
} else {
	$tabla="personas";
	$nom_tabla=ucfirst(_("todos"));
}
if ($_POST['que']=="telf") {
	//$action= web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select_telf.php');
	$action= core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select_telf.php';
} else {
	//$action= web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select.php');
	$action= core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select.php';
}
$oHash = new web\Hash();
$oHash->setcamposForm('nombre!apellido1!apellido2!centro!exacto!cmb');
$oHash->setcamposNo('exacto!cmb');
$a_camposHidden = array(
		'tipo' => $_POST['tipo'],
		'tabla' => $tabla,
		'na' => $_POST['na'],
		'breve' => $_POST['breve'],
		'es_sacd' => $_POST['es_sacd'],
		'que' => $_POST['que']
		);
$oHash->setArraycamposHidden($a_camposHidden);

?>
<form id="frm_personas_que" action="<?= $action ?>" method="post" onkeypress="fnjs_enviar(event,this);" >
<?= $oHash->getCamposHtml(); ?>

<table>
<thead><th class=titulo_inv colspan=4><?= ucfirst(_("búsqueda de personas")); ?> (<?= $nom_tabla ?>)</th></thead>
<tfoot>
<tr>
	<td class=etiqueta colspan=2><?= _("realizar una búsqueda exacta:"); ?>
	<input type="Radio" name="exacto" value=0 checked><?= _("no"); ?>
	<input type="Radio" name="exacto" value=1><?= _("sí"); ?></td>
<td class=etiqueta colspan="2" align="RIGHT"><input type="checkbox" name="cmb"><?= _("buscar en fichero cmb"); ?></td></tr>
<tr>
	<th colspan=4><input type="button" id="ok" name="ok" onclick="fnjs_enviar_formulario('#frm_personas_que')" value="<?= ucfirst(_("buscar")); ?>" class="btn_ok">
	<input TYPE="reset" value="<?= ucfirst(_("borrar")); ?>"></th>
</tr>
</tfoot>
<tbody>
<tr>
<td class=etiqueta><?= ucfirst(_("nombre")); ?></td> 
<td><input class=contenido id="nombre" name="nombre" size="30"></td></tr>
<tr>
<td class=etiqueta><?= ucfirst(_("primer apellido")); ?></td>
<td><input class=contenido id="apellido1" name="apellido1" size="40"></td></tr>
<tr> 
<td class=etiqueta><?= ucfirst(_("segundo apellido")); ?></td>
<td><input class=contenido id="apellido2" name="apellido2" size="40"></td></tr>
<tr>
<td class=etiqueta>
<?php 
if (($tabla=='p_cp_amigos') OR ($tabla=='p_cp_ae_sssc')) {
echo ucfirst(_("centro del que depende")); 
} else {
echo ucfirst(_("centro")); 
}
?>
</b></td>
<td><input class=contenido id="centro" name="centro"></td>
</tr>
</tbody>
</table>
</form>
