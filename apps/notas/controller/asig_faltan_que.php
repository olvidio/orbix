<?php
/**
* Esta página muestra un formulario con las opciones para escoger a una persona.
*
*@package	delegacion
*@subpackage	fichas
*@author	Daniel Serrabou
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


$oHash = new web\Hash();
$oHash->setcamposChk('personas_n!personas_agd!c1!c2!lista');
$oHash->setcamposForm('numero!b_c');

?>
<form id="frm_asig_altan" action="apps/notas/controller/asig_faltan_select.php" method="post" onkeypress="fnjs_enviar(event,this);">
<?= $oHash->getCamposHtml(); ?>
<table >
<thead><th class=titulo_inv colspan=4><?php echo ucfirst(_("búsqueda de personas por número de asignaturas")); ?></th></thead>
<tfoot>
<tr>
	<th colspan=4><input type="button" onclick="fnjs_enviar_formulario('#frm_asig_altan')" id="ok" name="ok" value="<?php echo ucfirst(_("buscar")); ?>"  class="btn_ok">
	<input TYPE="reset" value="<?php echo ucfirst(_("borrar")); ?>"></th>
</tr>
</tfoot>
<tbody>
<tr>
	<td class=etiqueta ><input type="Checkbox" name="personas_n" value="n" checked><?php echo _("numerarios"); ?></td>
	<td class=etiqueta ><input type="Checkbox" name="personas_agd" value="agd" ><?php echo _("agregados"); ?></td>
</tr>
	<td class=etiqueta ><input type="Radio" name="b_c" value=b ><?php echo _("bienio"); ?></td>
	<td class=etiqueta ><input type="Radio" name="b_c" value=c checked><?php echo _("cuadrienio"); ?></td>
	<td class=etiqueta align="RIGHT"><input type="checkbox" name="c1" checked><?php echo _("año I"); ?></td>
	<td class=etiqueta align="RIGHT"><input type="checkbox" name="c2" checked><?php echo _("año II-IV"); ?></td>
</tr>
<tr>
	<td class=etiqueta><b><?php echo ucfirst(_("número de asignaturas que faltan")); ?></b></td> 
	<td><input class=contenido name="numero" size="3"></td>
</tr>
<tr>
<td class=etiqueta colspan="2"><input type="checkbox" name="lista"><?php echo _("incluir lista de asignaturas"); ?></td>
</tr>
</tbody>
</table>
</form>
