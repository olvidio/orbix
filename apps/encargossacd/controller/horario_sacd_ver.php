<?php 
use encargossacd\model\EncargoFunciones;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use personas\model\entity\PersonaDl;

/**
* Esta página muestra un formulario para crear una nuevo horario de encargo.
*
*
*@package	delegacion
*@subpackage	encargos
*@author	Daniel Serrabou
*@since		24/2/06.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
//
$oDbl = $GLOBALS['oDBE'];

$oEncargoFunciones = new EncargoFunciones();

$Qid_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
$Qid_enc = (integer) \filter_input(INPUT_POST, 'id_enc');
$Qmod = (integer) \filter_input(INPUT_POST, 'mod');
$Qfiltro_sacd = (string) \filter_input(INPUT_POST, 'filtro_sacd');
$Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
$Qdesc_enc = (string) \filter_input(INPUT_POST, 'desc_enc');




$oPersona = new PersonaDl($Qid_nom);
$ap_nom = $oPersona->getApellidosNombre();
/*
$GesEncargosSacdHorario = new GestorEncargoSacdHorario();
$cTareasHorario = $GesEncargosSacdHorario->getTareaHorariosSacd(array('id_nom'=>$Qid_nom));

$GesEncargos = new GestorEncargo();
$cEncargos = $GesEncargos->getEncargos(array('id_enc'));
*/

$sql_h="SELECT t.desc_enc,hs.id_item
		FROM encargos t LEFT JOIN encargo_sacd_horario hs USING(id_enc)
		WHERE t.id_enc=$Qid_enc AND hs.id_nom=$Qid_nom";
$oDBSt_q_h=$oDbl->query($sql_h);
$h=0;
foreach ($oDBSt_q_h->fetchAll() as $row_h) {
	$h++;
	$id_item=$row_h['id_item'];
	$desc_enc=$row_h['desc_enc'];
}

if (!empty($id_item)) { //significa que no es nuevo
    /*
       if (!empty($sel_nom)) { //vengo de un checkbox
	$id_item_h=strtok($sel_nom[0],"#");
    }
    */
    $query="SELECT hs.f_ini, hs.f_fin, hs.dia_ref, hs.dia_num, hs.mas_menos, hs.dia_inc, hs.h_ini, hs.h_fin, t.desc_enc
	    FROM encargo_sacd_horario hs, encargos t , encargos_sacd d
	    WHERE hs.id_item=$id_item AND hs.id_enc=t.id_enc AND d.id_enc=hs.id_enc AND d.id_nom=hs.id_nom";
    //echo "query: $query<br>";
    $oDBSt_q=$oDbl->query($query);
    $row=$oDBSt_q->fetch(PDO::FETCH_ASSOC);

    extract($row);
} else {   //es nuevo
    $titulo=_("nuevo")." "; 
    // cojo los valores por defecto de f_ini, f_fin del dossier de tareas
    $query="SELECT f_ini, f_fin
	    FROM encargos_sacd d
	    WHERE d.id_enc=$Qid_enc AND d.id_nom=$Qid_nom";
    //echo "query: $query<br>";
    $oDBSt_q=$oDbl->query($query);
    $row=$oDBSt_q->fetch(PDO::FETCH_ASSOC);

    extract($row);
}
$titulo=_("horario de").": ".$desc_enc; 
?>                           
<script>
$(function() { $( "#f_ini" ).datepicker(); });
$(function() { $( "#f_fin" ).datepicker(); });

fnjs_guardar_horario=function(tipo){
	var err=0;
	var formulario=$('#modifica');
	var f_ini=$('#f_ini').val();
	var h_ini=$('#h_ini').val();
	var h_fin=$('#h_fin').val();
	var dia=$('#dia').val();
	
	if (!f_ini) { alert("Debe llenar el campo fecha inicio"); err=1; }
	if (!h_ini) { alert("Debe llenar el hora de inicio"); err=1; }
	if (!h_fin) { alert("Debe llenar el hora de finalización"); err=1; }
	if (!dia) { alert("Debe llenar el campo dia"); err=1; }

	var inc=0;
	var dia_sem=dia;
	var dia_ref=$('#dia_ref').val();
	var mas_menos=$('#mas_menos').val();
	
	//Tengo que multiplicar por 1 (*1) para que me coja los valores como números
	if (dia_ref) {
	    if (mas_menos=="-"){
		if (dia_ref > dia_sem) inc=dia_ref*1-dia_sem*1;
		if (dia_ref < dia_sem) inc=dia_ref*1+(7-dia_sem*1);
	    }
	    if (mas_menos=="+"){
		if (dia_ref > dia_sem) inc= (7-dia_ref*1)+dia_sem*1;
		if (dia_ref < dia_sem) inc= dia_sem*1-dia_ref*1;
	    }
	    //alert ("mas: "+mas_menos+" el incremento es: "+inc);
	    $('#dia_inc').val(inc);
	}

	if (err!=1) {
	    switch (tipo) {
		case 4:
		    if (confirm("<?= _("¿Esta seguro que desea borrar este horario?") ?>") ) {
		         formulario.mod.value="eliminar";
		         formulario.attr('action','des/tareas/horario_sacd_update.php');
		    }
		    break;
		case 5:
		    formulario.mod.value=tipo;
		    formulario.attr('action','des/tareas/horario_sacd_ex_ver.php');
		    break;
		default:
		    formulario.mod.value=tipo;
		    formulario.attr('action','des/tareas/horario_sacd_update.php');
	    } 
	    fnjs_enviar_formulario(formulario,'#ficha');
	}
}
</script>
<form id="modifica" name="modifica" action="">
<input type="hidden" name="filtro_sacd" value="<?= $Qfiltro_sacd ?>">
<input type="hidden" name="id_nom" value="<?= $Qid_nom ?>">
<input type="hidden" name="id_enc" value="<?= $Qid_enc ?>">
<input type="hidden" name="id_item" value="<?= $Qid_item ?>">
<input type="hidden" name="desc_enc" value="<?= $Qdesc_enc ?>">
<input type="hidden" name="mod" value="<?= $Qmod ?>">
<table>
<tr><th class="titulo_inv"><?php echo ucfirst($ap_nom); ?></th></tr>
<tr><th class="titulo_inv"><?php echo ucfirst($titulo); ?></th></tr>
</table>
<br>
<table>
<tr>

<td class=etiqueta><?php echo ucfirst(_("activo desde")); ?>: </td>
<td><input class="fecha" size="11" id="f_ini" id="f_ini" name="f_ini" value="<?= $f_ini ?>">
<td class=etiqueta><?php echo ucfirst(_("hasta")); ?>: </td>
<td><input class="fecha" size="11" id="f_fin" id="f_fin" name="f_fin" value="<?= $f_fin ?>">
</tr>
<tr><td class=etiqueta><?php echo ucfirst(_("dia")); ?>:</td>
<td><select class=contenido id="dia" name="dia">
<option \>
<?php
    $dia=$oEncargoFunciones->calcular_dia($mas_menos,$dia_ref,$dia_inc);
    reset($opciones_dia_semana);
    while(list($key,$d_semana)=each($opciones_dia_semana)){
	if ($dia==$key) { $selected="selected"; } else { $selected=""; }
	echo "<option value=\"$key\" $selected>".ucfirst($d_semana)."</option>";
    }
    ?>
    </select>
</td>
<td>
<select class=contenido id="mas_menos" name="mas_menos">
<?php 
if ($mas_menos=="-") { $sel_menos="selected"; $sel_mas="";}
if ($mas_menos=="+") { $sel_mas="selected"; $sel_menos="";}
echo "<option value=0></option>";
echo "<option value=\"-\" $sel_menos >"._("antes del")."</option>";
echo "<option value=\"+\" $sel_mas >"._("después del")."</option>";
?>
</select>
</td>
<td>
<select class=contenido id="dia_num" name="dia_num">
<option />
<?php
reset($opciones_ordinales);
    while(list($key,$d_ord)=each($opciones_ordinales)){
	if ($dia_num==$key) { $selected="selected"; } else { $selected=""; }
	echo "<option value=\"$key\" $selected>".$d_ord."</option>";
    }
?>
</select>
</td>
<td><select class=contenido id="dia_ref" name="dia_ref">
<option />
<?php
    reset($opciones_dia_ref);
    while(list($key,$d_ref)=each($opciones_dia_ref)){
	if ($dia_ref==$key) { $selected="selected"; } else { $selected=""; }
	echo "<option value=\"$key\" $selected>".ucfirst($d_ref)."</option>";
    }
    ?>
    </select>
</td>
<tr>
<td class=etiqueta><?php echo ucfirst(_("hora inicio")); ?>: </td><td><input class=contenido size="11" id="h_ini" name="h_ini" value="<?php echo $h_ini ?>">
<td class=etiqueta><?php echo ucfirst(_("hora fin")); ?>: </td><td><input class=contenido size="11" id="h_fin" name="h_fin" value="<?php echo $h_fin ?>">
</tr>
<tr>
<td><input type=hidden id="dia_inc"  name="dia_inc">
</td></tr>


</table>
<?php
if ( $id_item) {
	echo "<input TYPE=\"button\" VALUE=\"".ucfirst(_("guardar horario"))."\" onclick=\"javascript:guardar_horario(2)\"> ";
	echo "<input TYPE=\"button\" VALUE=\"".ucfirst(_("añadir horario"))."\" onclick=\"javascript:guardar_horario(3)\"> ";
	echo "<input TYPE=\"button\" VALUE=\"".ucfirst(_("eliminar horario"))."\" onclick=\"javascript:guardar_horario(4)\"> ";
} else {
	echo "<input TYPE=\"button\" VALUE=\"".ucfirst(_("crear horario"))."\" onclick=\"javascript:guardar_horario(1)\"> ";
}
// si NO es para uno nuevo, miro si tinen excepciones:
if (!empty($id_item)) {
   $sql_ex="SELECT * FROM t_horario_sacd_excepcion WHERE id_item_h=$id_item"; 
   //echo "query: $sql_ex<br>";
   $oDBSt_q=$oDB->query($sql_ex);
   if ($oDBSt_q->rowCount()>0) {
	echo "</form>";
	include("horario_sacd_ex_select.php");
    } else {
	echo "<input TYPE=\"button\" VALUE=\"".ucfirst(_("generar excepciones"))."\" onclick=\"javascript:guardar_horario(5)\"> ";
    }
}
?>
</form>
