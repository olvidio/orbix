<?php

use web\Hash;

$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');
$Qdocumentos = (string)filter_input(INPUT_POST, 'documentos');

$oHash = new Hash();
$sCamposFrom = 'f_recibido!f_asignado!eliminado!f_eliminado!num_ini!num_fin';
$oHash->setCamposForm($sCamposFrom);
$sCamposNo = 'chk_f_recibido!chk_f_asignado!chk_eliminado!chk_f_eliminado!chk_num_ini!chk_num_fin';
$oHash->setCamposNo($sCamposNo);
$oHash->setArrayCamposHidden([
    'id_tipo_doc' => $Qid_tipo_doc,
    'documentos' => $Qdocumentos,
]);

$txt2="<tr><td>"._("modificar")."</td><td>"._("campo")."</td><td>"._("valor")."</td></tr>";
$txt2.="<tr><td><input type=checkbox id='chk_f_recibido' name='chk_f_recibido'></td>";
$txt2.="<td>"._("fecha recibido")."</td><td><input type=text class='fecha' size=12 id='f_recibido' name='f_recibido'></td></tr>";
$txt2.="<tr><td><input type=checkbox id='chk_f_asignado' name='chk_f_asignado'></td>";
$txt2.="<td>"._("fecha asignado")."</td><td><input type=text class='fecha' size=12 id='f_asignado' name='f_asignado'></td></tr>";
$txt2.="<tr><td><input type=checkbox id='chk_eliminado' name='chk_eliminado'></td>";
$txt2.="<td>"._("eliminado")."</td><td><input type=radio name='eliminado' value=1 checked>"._("si")."<input type=radio name='eliminado' value=2>"._("no")."</td>";
$txt2.="<tr><td><input type=checkbox id='chk_f_eliminado' name='chk_f_eliminado'></td>";
$txt2.="<td>"._("fecha eliminado")."</td><td><input type=text class='fecha' size=12 id='f_eliminado' name='f_eliminado'></td></tr>";
$txt2.="<tr><td><input type=checkbox id='chk_num_ini' name='chk_num_ini'></td>";
$txt2.="<td>"._("número inicial")."</td><td><input type=text size=12 name='num_ini'></td>";
$txt2.="<tr><td><input id='chk_num_fin' name='chk_num_fin' type=checkbox></td>";
$txt2.="<td>"._("número final")."</td><td><input type=text size=12 name='num_fin'></td></tr>";

$txt="<form id='frm_mod_bloque'>";
$txt.= $oHash->getCamposHtml();
$txt.='<h3>'._("campos a modificar en los documentos seleccionados").'</h3>';
$txt.="<table style='width: 400px'>";
$txt.=$txt2;
$txt.='</table>';
$txt.='<br><br>';
$txt.="<input type='button' value='". _('guardar') ."' onclick=\"fnjs_guardar('#frm_mod_bloque');\" >";
$txt.="<input type='button' value='". _('cancel') ."' onclick=\"fnjs_cerrar();\" >";
$txt.="</form> ";
echo $txt;