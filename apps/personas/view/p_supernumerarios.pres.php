<?php
/**
* Esta es la página de presentación de la ficha de s. 
*
* Esta presentación se incluye dentro del programa de visualización de
* las fichas.
* Existen 4 tipos de textos:
*		- menu (<tr class=tab> y <a class=tab>)
*		- titulos (<th>)
*		- etiquetas (<span class=etiqueta>) son links a helps
*		- datos (class=datos) varios tipos: links, inputs
*
* @package	fichas
* @subpackage	sg
* @author	Josep Companys
* @since		2/1/03.
*
*/
?>
<script>
$(function() { $("#f_fichero_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_procede_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_ap_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_pa_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_ad_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_o_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_fl_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_pr_fl_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_ctr_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_ctr_cr_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_cel_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_estado_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_cfi_<?= $tabla[$f].$r ?>").datepicker(); });
$(function() { $("#f_num_hijos_<?= $tabla[$f].$r ?>").datepicker(); });

fnjs_ver_tab=function(elemento){
	var tabs=[ '#datospersonales', '#datosprocedencia', '#incorporaciones', '#centro', '#datosfamiliares'];
	$.each(tabs,function(i,item){
			$(item).hide()
			});
	$(elemento).show();
}
fnjs_act_ctr=function(camp){
	var el, centre, xx, camp, idCamp;
	var dDate = new Date();
	var mes=dDate.getMonth()+1;
	var fecha=dDate.getDate()+'/'+mes+'/'+dDate.getFullYear();
	var f;
	idCamp='id_'+camp;
	el=document.frm2[idCamp].selectedIndex;
	centre=$(idCamp).val();
	$(camp).val(centre);
	// también la fecha
	f='f_'+camp;
	$(f).val(fecha);
}
fnjs_ver_iframe=function(id_frame){
	$(id_frame).show();
}
fnjs_ocultar_iframe=function(id_frame){
	$(id_frame).hide();

}
</script>
<?php
if (!empty($id_ctr)) {
	$query_ctr="select u.nombre_ubi AS nom from u_centros_dl u WHERE u.id_ubi=$id_ctr";
	$oDBSt_q_ctr=$oDB->query($query_ctr);
	$nom_ctr=$oDBSt_q_ctr->fetchColumn();
}

if (!empty($id_ctr_cr)) {
	$query_ctr_cr="select u.nombre_ubi AS nom from u_centros_dl u WHERE u.id_ubi=$id_ctr_cr";
	$oDBSt_q_ctr_cr=$oDB->query($query_ctr_cr);
	$nom_ctr_cr=$oDBSt_q_ctr_cr->fetchColumn();
}

$condicionEncode=urlencode($condicion[$f]);
if (!empty($id_nom)) {
	// este go_to es para la foto
	$go_to_fot=urlencode("./ficha_ver.php?id_nom=$id_nom&tabla=$tabla[$f]&condicion=$condicionEncode|main");
	$ir_a_foto="programas/foto_grabar.php?id_nom=$id_nom&tabla=$tabla[$f]&go_to=$go_to_fot&PHPSESSID=".session_id();
	$file_foto=ConfigGlobal::$dir_fotos."/s/$id_nom.jpg";
	$foto=ConfigGlobal::$web_fotos."/s/$id_nom.jpg";
	//este es para el cambio de ctr.
	$go_to=urlencode("../ficha_ver.php?id_pau=$id_nom&tabla=$tabla[$f]&condicion=$condicionEncode");
	$ir_a_traslado="programas/dossiers/traslado_form.php?pau=p&id_pau=$id_nom&tabla_pau=$tabla[$f]&go_to=$go_to";
} else {
	$ir_a_foto="";
	$file_foto="";
	$ir_a_traslado="";
}
/**
*
* Esta función simplemente dibuja el menú en las fichas de personas.
*/
function menu(){
	print ("
		<table cellpadding=\"7\" cellspacing=\"0\" border=\"1\" frame=\"box\" rules=\"cols\">
		<tr class=tab>
			<td onclick=fnjs_ver_tab('#datospersonales')>"._("datos personales")."</td>
			<td onclick=fnjs_ver_tab('#datosprocedencia')>"._("datos procedencia")."</td>
			<td onclick=fnjs_ver_tab('#incorporaciones')>"._("incorporaciones")."</td>
			<td onclick=fnjs_ver_tab('#centro')>"._("centro y encargos")."</td>
			<td onclick=fnjs_ver_tab('#datosfamiliares')>"._("datos familiares")."</td>
		</tr>
		</table><br>
	");
}
?>
<!-- ------------------- DATOS PERSONALES ------------------------ -->
<?php menu();
$query_nacionalidad="select id_nacionalidad, nombre_nacionalidad from xp_nacionalidad order by nombre_nacionalidad";
$oDBSt_q_nacionalidad=$oDB->query($query_nacionalidad);
?>
<iframe id="iframe_foto" name="iframe_foto" src="<?= $ir_a_foto ?>" 
	style="display:none; position:fixed; z-index:5; background-color: #BBBBBB; width:600; height:350; scrolling:auto; border-style:solid;border-width:2; ">
</iframe>
<table id="datospersonales" border=1 >
<tr><td class=titulo colspan="5">
<?php print(strtoupper(_("datos personales"))); ?>
</td></tr>
<tr>
<?php 
if (!empty($id_nom)) {
	if (!empty($file_foto) && file_exists($file_foto)) {
		echo "<td rowspan=4 align=center valign=top><table style='border: 0; width:55px;'><tr><td>
				<img class=link onclick=\"fnjs_ver_iframe('#iframe_foto');\" src='$foto' width=72 height=85 border=0 align='rigth' alt='foto carnet'>
				</td></tr><tr><td><input type=hidden name='f_foto_$tabla[$f]$r' value='$f_foto'>$f_foto</td></tr></table>";
	} else {
		$txt_foto=_("introducir una foto");
		echo "<span class=link onclick=\"fnjs_ver_iframe('#iframe_foto');\">$txt_foto</span>";
	}
}
echo "</td>";
echo dibujar_campo("trato",7,1,0);
echo dibujar_campo("nom",20,1,2);
echo "</tr><tr><td></td>";
echo dibujar_campo("apel_fam",20,1,2);
echo "</tr><tr>";
echo dibujar_campo("nx1",7,1,0);
echo dibujar_campo("apellido1",25,1,2);
echo "</tr><tr>";
echo dibujar_campo("nx2",7,1,0);
echo dibujar_campo("apellido2",25,1,2);
echo "</tr><tr>";
echo "<td>";
//en el caso de nueva ficha, pongo el nombre del centro sin llenar el dossier de traslados.
	if (empty($id_nom)){
		$query_dl_es="select u.dl AS nom, u.id_dl, u.region FROM xp_dl u where region='H' ORDER BY nom";
		$query_dl_ex="SELECT u.dl AS nom, u.id_dl, u.region FROM xp_dl u where region!='H' AND u.dl!='cr' AND u.dl!='ro' ORDER BY nom";
		$query_dl_r="SELECT u.region AS nom FROM xu_region u ORDER BY nom";
		$oDBSt_q_dl_es=$oDB->query($query_dl_es);
		$oDBSt_q_dl_ex=$oDB->query($query_dl_ex);
		$oDBSt_q_r=$oDB->query($query_dl_r);
		$f_dl=date("d/m/Y");
		?>	
		Delegación de procedencia:		
		<select class=contenido name="dl_<?= $tabla[$f].$r; ?>" title="<?= $dl_help; ?>" >
		<option></option>
		<?php
		foreach ($oDBSt_q_dl_es->fetchAll() as $row_dl) {
			echo "<option value=\"${row_dl['nom']}\" >  ${row_dl['nom']} </option>";
		}
		?>
		<option> -------- </option>
		<?php
		foreach ($oDBSt_q_dl_ex->fetchAll() as $row_dl) {
			echo "<option value=\"${row_dl['nom']}\" >  ${row_dl['nom']} </option>";
		}
		?>
		<option> -------- </option>
		<?php
		foreach ($oDBSt_q_r->fetchAll() as $row_dl) {
			echo "<option value=\"${row_dl['nom']}\" >  ${row_dl['nom']} </option>";
		}
		?>
		</select></td>
		<?php
	} else {
		echo "<span class=link onclick=\"fnjs_update_div('#main','$ir_a_traslado');\">&nbsp;".ucfirst($dl_etiqueta).":&nbsp;</span>";
		echo "<b class=contenido >$dl</b>";
		echo "<input type=\"hidden\" name=\"dl_${tabla[$f]}$r\" value=\"$dl\"> ";
	} 
echo "</td>";
echo dibujar_campo("lengua",3,1,1);
?>
<td><span class=etiqueta ondblclick="fnjs_help('<?= $nacionalidad_help_ref; ?>')" ><?= ucfirst($nacionalidad_etiqueta); ?></span></td>
<td><select class=contenido name="nacionalidad_<?= $tabla[$f].$r; ?>" title="<?= $nacionalidad_help; ?>">
<option value=""></option>
<?php
$i=0;
foreach ($oDBSt_q_nacionalidad->fetchAll() as $row) {
	$nombre_nacionalidad=$row["nombre_nacionalidad"];
	if ($nacionalidad==$nombre_nacionalidad) { $sel="selected"; } else { $sel=""; } 
	echo "<option value=\"$nombre_nacionalidad\" $sel > $nombre_nacionalidad</option>";
	$i++;
}

echo "</select></td></tr>";
echo "<tr>";
echo dibujar_campo("santo",4,1,1);
?>
<td><span class=etiqueta ondblclick="fnjs_help('<?= $celebra_help_ref; ?>')" ><?= ucfirst($celebra_etiqueta); ?></span>
	<select class=contenido name="celebra_<?= $tabla[$f].$r; ?>" title="<?= $celebra_help; ?>">
		<option value="c" <?php if ($celebra=="c") { echo "selected"; } ?> >c</option>
		<option value="s" <?php if ($celebra=="s") { echo "selected"; } ?> >s</option>
	</select>
</td>
<?php
	echo dibujar_campo("felicitar",2,1,1);
	echo "</tr><tr>";
	echo dibujar_campo("f_nacimiento",11,1,1);
	echo "</tr>";
?>
</table>
<!-- ------------------- DATOS PROCEDENCIA ------------------------ -->
<?php
echo "<table id='datosprocedencia' border=1 style='display:none;'>";
echo "<tr><td class=titulo colspan='5'>".strtoupper(_("datos procedencia"))."</td></tr>";
echo "<tr>";
echo dibujar_campo("ciudad_n",30,1,3);
echo "</tr><tr>";
echo dibujar_campo("provincia_n",15,1,1);
echo "</tr><tr>";
echo dibujar_campo("f_fichero",11,1,1);
?>
	<td><span class=etiqueta ondblclick="fnjs_help('<?= $fichero_help_ref; ?>')" ><?= ucfirst($fichero_etiqueta); ?></span>
	</td>
	<td>
	<select class=contenido name="fichero_<?= $tabla[$f].$r; ?>" title="<?= $fichero_help; ?>">
			<?php
			$sql_t_fichero="SELECT u.fichero,u.nombre_fichero FROM xp_fichero u 
							ORDER BY u.fichero ";
			$oDBSt_t_fichero=$oDB->query($sql_t_fichero);
			foreach ($oDBSt_t_fichero->fetchAll() as $row_fichero) {
				if ($fichero==$row_fichero['fichero']) { $sel="selected"; } else { $sel=""; }
 				echo "<option value=\"${row_fichero['fichero']}\" $sel>  {$row_fichero['fichero']} </option>";
  				}
			?>
	</select></td>
<?php
	echo "</tr><tr>";
	echo dibujar_campo("estudios",65,1,3);
	echo "</tr><tr>";
	echo dibujar_campo("est_universidad",65,1,3);
	echo "</tr><tr>";
	/*- echo dibujar_campo("profesion",30,1,2); */
	/* --- campo profesion: para que sea readonly ------------- */
	$a_valores_campo=$GLOBALS['a_campos']['profesion'];

	$etiqueta=$a_valores_campo["etiqueta"];
	$valor=$a_valores_campo["valor"];
	$help=$a_valores_campo["help"];
	$help_ref=$a_valores_campo["help_ref"];
	//$name=$texto."_".$a_valores_campo["name"];
	$name=$a_valores_campo["name"];
		
	$dibujo= "<td colspan='1'>";
	$dibujo.= "<span class=etiqueta ondblclick=\"fnjs_help('$help_ref')\" >".ucfirst($etiqueta)."</span>";
	$dibujo.= "</td><td colspan='2'>";
	$dibujo.= "<input class=contenido size=30 name=\"$name\" value=\"$valor\" title=\"modificar el dossier\" readonly >";
	$dibujo.= "</td>";
	echo $dibujo;
	/* --- campo profesion: para que sea readonly ------------- */
	echo "</tr><tr>";
	echo dibujar_campo("ante_medicos",60,1,3);
	echo "</tr><tr>";
	echo dibujar_campo("procede",3,1,1);
	echo dibujar_campo("f_procede",11,1,2);
?>
</tr>
</table>
<!--
------------------- INCOPORACIONES ------------------------ 

<input class=contenido type="hidden" name="ctr_<?= $tabla[$f].$r; ?>" value="<?= $ctr ?>" title="<?= $ctr_help; ?>">
<input class=contenido type="hidden" name="ctr_cr_<?= $tabla[$f].$r; ?>" value="<?= $ctr_cr ?>" title="<?= $ctr_cr_help; ?>"></td></tr>
-->

<input class=contenido type="hidden" name="id_ctr_<?= $tabla[$f].$r; ?>" value="<?= $id_ctr ?>" title="<?= $id_ctr_help; ?>">
<input class=contenido type="hidden" name="id_ctr_cr_<?= $tabla[$f].$r; ?>" value="<?= $id_ctr_cr ?>" title="<?= $id_ctr_cr_help; ?>">

<table id="incorporaciones" border=1 style='display:none;'>
<tr><td class=titulo colspan="5"><?php print(strtoupper(_("incorporaciones"))); ?></td></tr>
<tr>
<?php
echo dibujar_campo("ctr_ap",20,1,1);
echo dibujar_campo("f_ap",11,1,1);
echo "</tr><tr>";
echo dibujar_campo("ctr_pa",20,1,1);
echo dibujar_campo("f_pa",11,1,1);
echo "</tr><tr>";
echo dibujar_campo("lugar_pa",40,1,1);
echo "</tr><tr>";
echo dibujar_campo("provincia_pa",40,1,1);
echo "</tr><tr>";
echo dibujar_campo("ctr_ad",20,1,1);
echo dibujar_campo("f_ad",11,1,1);
echo "</tr><tr>";
echo dibujar_campo("ctr_o",20,1,1);
echo dibujar_campo("f_o",11,1,1);
echo "</tr><tr>";
echo dibujar_campo("ctr_fl",20,1,1);
echo dibujar_campo("f_fl",11,1,1);
echo "</tr><tr><td></td><td></td>";
echo dibujar_campo("f_pr_fl",11,1,1);
echo "</tr><tr>";
echo "</table>";
?>
<!-- CENTRO Y ENCARGOS ----------------------------------------------->
<table id="centro" border=1 style='display:none;'> 
<tr><td class=titulo colspan="5"><?php print(strtoupper(_("centro y encargos"))); ?></td></tr>
<tr>
<td>
	<?php //en el caso de nueva ficha, pongo el nombre del centro sin llenar el dossier de traslados.
	$a_valores_campo=$GLOBALS['a_campos']["id_ctr"];
	if (empty($id_nom) || empty($id_ctr)){
		//$query_ctrs="select u.nombre_ubi AS nom, u.id_ubi from u_centros_dl u where status='t' and (tipo_ctr='s') ORDER BY nom";
		//cambiado oct-03 a petición de oficina sg
		$query_ctrs="select u.nombre_ubi AS nom, u.id_ubi from u_centros_dl u where status='t' ORDER BY tipo_ctr, nom";
		$oDBSt_q_ctrs=$oDB->query($query_ctrs);
		?>
		Centro-sede:
		<select class=contenido name="id_ctr_<?= $tabla[$f].$r; ?>" id="id_ctr" onchange="fnjs_act_ctr('ctr')">
		<option></option>
		<?php
		foreach ($oDBSt_q_ctrs->fetchAll() as $row) {
			echo "<option value=\"${row['id_ubi']}\" >  ${row['nom']} </option>";
		}
		$a_valores_campo=$GLOBALS['a_campos']["f_ctr"];
		?>
		</select></td>			
		<input class=contenido type="hidden" name="ctr_<?= $tabla[$f].$r; ?>" id="ctr" value="w">
		<td colspan="3"><span class=etiqueta ondblclick="fnjs_help('<?= $a_valores_campo["help_ref"]; ?>')" ><?= ucfirst($a_valores_campo["etiqueta"])."&nbsp;"; ?></span>
		<input class=fecha name="f_ctr_<?= $tabla[$f].$r; ?>" id="f_ctr" size=11 value="<?= $a_valores_campo["valor"] ?>" title="<?= $a_valores_campo["help"]; ?>"></td>
		<?php
	} else {
		echo "<span class=link onclick=\"fnjs_update_div('#main','$ir_a_traslado');\">&nbsp;Centro-sede:&nbsp;</span>";
		echo "<b class=contenido>$nom_ctr</b>";
		echo dibujar_campo("f_ctr",11,3,0);
	}
echo "</td>";
echo "</tr><tr>";
echo "<td>";
//en el caso de nueva ficha, pongo el nombre del centro sin llenar el dossier de traslados.
	if (empty($id_nom) || empty($id_ctr_cr)){
		//$query_ctrs="select u.nombre_ubi AS nom, u.id_ubi from u_centros_dl u where status='t' and (tipo_ctr='s') ORDER BY nom";
		//cambiado oct-03 a petición de oficina sg
		$query_ctrs="select u.nombre_ubi AS nom, u.id_ubi from u_centros_dl u where status='t' ORDER BY tipo_ctr, nom";
		$oDBSt_q_ctrs=$oDB->query($query_ctrs);
		?>
		Centro-cr:
		<select class=contenido name="id_ctr_cr_<?= $tabla[$f].$r; ?>" id="id_ctr_cr" onchange="fnjs_act_ctr('ctr_cr')">
		<option></option>
		<?php
		foreach ($oDBSt_q_ctrs->fetchAll() as $row) {
			echo "<option value=\"{$row['id_ubi']}\" >  ${row['nom']} </option>";
		}
		$a_valores_campo=$GLOBALS['a_campos']["f_ctr_cr"];
		?>
		</select></td>			
		<input class=contenido type="hidden" name="ctr_cr_<?= $tabla[$f].$r; ?>" id="ctr_cr" value="ee">
		<td colspan="3"><span class=etiqueta ondblclick="fnjs_help('<?= $a_valores_campo["help_ref"]; ?>')" ><?= ucfirst($a_valores_campo["etiqueta"])."&nbsp;"; ?></span>
		<input class=fecha name="f_ctr_cr_<?= $tabla[$f].$r; ?>" id="f_ctr_cr" size=11 value="<?= $a_valores_campo["valor"] ?>" title="<?= $a_valores_campo["help"]; ?>"></td>
		<?php
	} else {
		echo "<span class=link onclick=\"fnjs_update_div('#main','$ir_a_traslado');\">&nbsp;Centro-cr:&nbsp;</span>";
		echo "<b class=contenido>$nom_ctr_cr</b>";
		echo dibujar_campo("f_ctr_cr",11,3,0);
	}
echo "</tr><tr>";
$a_valores_campo=$GLOBALS['a_campos']["ce"];
echo "<td colspan='4'><span class=etiqueta ondblclick=\"fnjs_help('$ce_help_ref')\" >".ucfirst($a_valores_campo["etiqueta"])."&nbsp;</span>";
echo "<select class=contenido name=\"ce_${tabla[$f]}$r\" title=\"$ce_help\">";
?>
	<option value="0" <?php if ($ce=="0") { echo "selected"; } ?> >0</option>
	<option value="1" <?php if ($ce=="1") { echo "selected"; } ?> >1</option>
	<option value="2" <?php if ($ce=="2") { echo "selected"; } ?> >2</option>
	<option value="3" <?php if ($ce=="3") { echo "selected"; } ?> >3</option>
	<option value="4" <?php if ($ce=="4") { echo "selected"; } ?> >4</option>
</select>
<?php
echo dibujar_campo("lugar_ce",2,0,0);
echo "</td></tr><tr><td colspan='4'>";
echo dibujar_campo("ini_ce",5,0,0);
echo "&nbsp";
echo dibujar_campo("fin_ce",5,0,0); 
echo "</td></tr><tr>";
echo dibujar_campo("eap",20,2,0); 
echo dibujar_campo("desc_eap",20,2,0);
echo "</tr><tr>";
$a_valores_campo=$GLOBALS['a_campos']["cel"];
echo "<td><span class=etiqueta ondblclick=\"fnjs_help('$cel_help_ref')\" >".ucfirst($a_valores_campo["etiqueta"])."&nbsp;</span>";
echo "<select class=contenido name=\"cel_${tabla[$f]}$r\" title=\"$cel_help\" >";
?>
    <option></option>
	<option value="s" <?php if ($cel=="s") { echo "selected"; } ?> >s</option>
	<option value="w" <?php if ($cel=="w") { echo "selected"; } ?> >w</option>
</select></td>
<?php
echo dibujar_campo("f_cel",11,2,1);    
echo "</tr><tr>";
$a_valores_campo=$GLOBALS['a_campos']["cfi"];
echo "<td><span class=etiqueta ondblclick=\"fnjs_help('$cfi_help_ref')\" >".ucfirst($a_valores_campo["etiqueta"])."&nbsp;</span>";
?>
<input class=contenido name="cfi_<?= $tabla[$f].$r; ?>" type=checkbox   <?php if ($cfi=='t'){ echo "checked";} ?> title="<?= $cfi_help; ?>"></td>
<?php
echo dibujar_campo("f_cfi",11,2,1);
echo "</tr><tr>";
echo dibujar_campo("observ",65,1,4);    
echo "</tr></table>";
?>
<!-- DATOS FAMILIARES --------------------------------------------- -->
<?php
echo "<table id='datosfamiliares' border=1 style='display:none;'>";
echo "<tr><td class=titulo colspan='5'>".strtoupper(_("datos familiares"))."</td></tr>";
echo "<tr>";
echo dibujar_campo("direccion",50,1,1);
echo dibujar_campo("c_p",5,1,1);
echo "</tr><tr>";
echo dibujar_campo("poblacion",20,1,1);
echo dibujar_campo("provincia",15,1,1);
echo "</tr><tr>";
$a_valores_campo=$GLOBALS['a_campos']["estado"];
echo "<td colspan=2><span class=etiqueta ondblclick=\"fnjs_help('$estado_help_ref')\" >".ucfirst($a_valores_campo["etiqueta"])."&nbsp;</span>";
?>
	<select class=contenido name="estado_<?= $tabla[$f].$r; ?>" title="<?= $estado_help; ?>">
		<option selected></option>
		<option value="s" <?php if ($estado=="s") { echo "selected"; } ?> >s</option>
		<option value="c" <?php if ($estado=="c") { echo "selected"; } ?> >c</option>
		<option value="v" <?php if ($estado=="v") { echo "selected"; } ?> >v</option>
	</select></td>
<?php
echo dibujar_campo("f_estado",11,1,1);
echo "</tr><tr>";
echo dibujar_campo("mujer",50,1,3);
echo "</tr><tr>";
echo dibujar_campo("num_hijos",23,1,1);
echo dibujar_campo("f_num_hijos",11,1,1);
?>
</tr> 
</table>
