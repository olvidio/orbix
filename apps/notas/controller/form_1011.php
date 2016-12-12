<?php
use actividades\model as actividades;
use asignaturas\model as asignaturas;
use notas\model as notas;
use personas\model as personas;
use profesores\model as profesores;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$obj = 'notas\\model\\PersonaNota';

$pau = empty($_POST['pau'])? '' : $_POST['pau'];
$id_pau = empty($_POST['id_pau'])? '' : $_POST['id_pau'];
$obj_pau = empty($_POST['obj_pau'])? '' : $_POST['obj_pau'];
$permiso = empty($_POST['permiso'])? '' : $_POST['permiso'];
		
if (!empty($_POST['sel'])) { //vengo de un checkbox
	if ($pau=="p") { 
		$id_nivel_real=strtok($_POST['sel'][0],"#"); 
		$id_asignatura_real=strtok("#");
	}
} else {
	if (!empty($_POST['mod']) && $_POST['mod']!='nuevo') {
		$id_asignatura_real=$_POST['id_asignatura_real']; 
	} else {
		$id_asignatura_real='';
	}
}

$_POST['id_nivel'] = empty($_POST['id_nivel'])? 'n': $_POST['id_nivel'];
$_POST['opcional'] = empty($_POST['opcional'])? 'n': $_POST['opcional'];

$GesNotas = new notas\GestorNota();
$oDesplNotas = $GesNotas->getListaNotas();
$oDesplNotas->setNombre('id_situacion');

$GesProfes = new profesores\GestorProfesor();
$cProfesores= $GesProfes->getProfesores();
$aProfesores=array();
foreach ($cProfesores as $oProfesor) {
	$id_nom=$oProfesor->getId_nom();
	$oPersona = personas\Persona::NewPersona($id_nom);
	$ap_nom=$oPersona->getApellidosNombre();
	$aProfesores[$id_nom]=$ap_nom;
}
asort($aProfesores);
$oDesplProfesores = new web\Desplegable();
$oDesplProfesores->setOpciones($aProfesores);
$oDesplProfesores->setBlanco(1);
$oDesplProfesores->setNombre('id_preceptor');

$GesActividades = new actividades\GestorActividad();
$GesAsignaturas = new asignaturas\GestorAsignatura();

if (!empty($id_asignatura_real)) { //caso de modificar
	$mod="editar";
	$id_asignatura=$id_asignatura_real;
	$_POST['opcional']='n';
	$aWhere['id_nom'] = $id_pau;
	$aWhere['id_asignatura'] = $id_asignatura_real;
	$GesPersonaNotas = new notas\GestorPersonaNota();
	$cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhere);

	$oPersonaNota = $cPersonaNotas[0]; // solo debeeria existir una.

	$id_situacion_real=$oPersonaNota->getId_situacion();
	$nota_num_real=$oPersonaNota->getNota_num();
	$nota_max_real=$oPersonaNota->getNota_max();
	$acta_real=$oPersonaNota->getActa();
	$f_acta_real=$oPersonaNota->getF_acta();
	$preceptor_real=$oPersonaNota->getPreceptor();
	$id_preceptor_real=$oPersonaNota->getId_preceptor();
	$detalle_real=$oPersonaNota->getDetalle();
	$epoca_real=$oPersonaNota->getEpoca();
	$id_activ_real=$oPersonaNota->getId_activ();

	$oAsignatura = new asignaturas\Asignatura($id_asignatura_real);
	$nombre_corto=$oAsignatura->getNombre_corto();
	if ($oPersonaNota->getId_asignatura() > 3000) {
		$id_nivel=$oPersonaNota->getId_nivel();
	} else {
		$id_nivel=$oAsignatura->getId_nivel();
	}

} else { //caso de nueva asignatura
	$mod="nuevo";
	// todas las asignaturas
	$aWhere=array();
	$aOperador=array();
	$aWhere['status']='t';
	$aWhere['id_nivel']=3000;
	$aOperador['id_nivel']='<';
	$aWhere['_ordre']='id_nivel';
	$cAsignaturas = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
	// todas las opcionales 
	$aWhere=array();
	$aOperador=array();
	$aWhere['status']='t';
	$aWhere['id_nivel']='3000,5000';
	$aOperador['id_nivel']='BETWEEN';
	$aWhere['_ordre']='id_nivel';
	$cOpcionales = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
	// Asignaturas superadas
	$GesNotas = new notas\GestorNota();
	$cSuperadas = $GesNotas->getNotas(array('superada'=>'t'));
	$cond='';
	$c=0;
	foreach ($cSuperadas as $Nota) {
		if ($c >0 ) $cond.='|';
		$c++;
		$cond.=$Nota->getId_situacion();
	}
	$aWhere=array();
	$aOperador=array();
	$aWhere['id_situacion']=$cond;
	$aOperador['id_situacion']='~';
	$aWhere['id_nom']=$id_pau;
	$aWhere['id_nivel']=3000;
	$aOperador['id_nivel']='<';
	$aWhere['_ordre']='id_nivel';
	$GesPersonaNotas = new notas\GestorPersonaNota();
	$cAsignaturasSuperadas = $GesPersonaNotas->getPersonaNotas($aWhere,$aOperador);
	$aSuperadas=array();
	foreach($cAsignaturasSuperadas as $oAsignatura) {
		$id_nivel = $oAsignatura->getId_nivel();
		$id_asignatura = $oAsignatura->getId_asignatura();
		$aSuperadas[$id_nivel]=$id_asignatura;
	}
	// asignaturas posibles
	$aFaltan=array();
	foreach ($cAsignaturas as $oAsignatura) {
		$id_nivel = $oAsignatura->getId_nivel();
		$id_asignatura = $oAsignatura->getId_asignatura();
		$nombre_corto = $oAsignatura->getNombre_corto();
		if (array_key_exists($id_nivel,$aSuperadas)) continue;
		$aFaltan[$id_nivel]=$nombre_corto;
	}
}

if ($mod == 'nuevo') {
	// Valores por defecto
	$max = core\ConfigGlobal::nota_max();
	$max = empty($max)? '': $max;
	$situacion = 10;

	$acta=empty($_POST['acta'])? '': $_POST['acta'];
	$f_acta=empty($_POST['f_acta'])? '': $_POST['f_acta'];
	$epoca=empty($_POST['epoca'])? '': $_POST['epoca'];
	$detalle=empty($_POST['detalle'])? '': $_POST['detalle'];
	$id_activ=empty($_POST['id_activ'])? '': $_POST['id_activ'];
	$precep=empty($_POST['precep'])? 'no' : $_POST['precep'];
	$id_preceptor=empty($_POST['id_preceptor'])? '': $_POST['id_preceptor'];
	$id_situacion=empty($_POST['id_situacion'])? $situacion : $_POST['id_situacion'];
	$nota_num=empty($_POST['nota_num'])? '': $_POST['nota_num'];
	$nota_max=empty($_POST['nota_max'])? $max: $_POST['nota_max'];
} else {
	$acta=!isset($_POST['acta'])? $acta_real : $_POST['acta'];
	$f_acta=!isset($_POST['f_acta'])? $f_acta_real : $_POST['f_acta'];
	$epoca=!isset($_POST['epoca'])? $epoca_real : $_POST['epoca'];
	$detalle=!isset($_POST['detalle'])? $detalle_real : $_POST['detalle'];
	$id_activ=!isset($_POST['id_activ'])? $id_activ_real : $_POST['id_activ'];
	$precep=!isset($_POST['precep'])? '' : $_POST['precep'];
	if (empty($precep)) $precep= empty($preceptor_real)? 'no' : 'si';
	$id_preceptor=!isset($_POST['id_preceptor'])? $id_preceptor_real : $_POST['id_preceptor'];
	$id_situacion=!isset($_POST['id_situacion'])? $id_situacion_real : $_POST['id_situacion'];
	$nota_num=!isset($_POST['nota_num'])? $nota_num_real : $_POST['nota_num'];
	$nota_max=!isset($_POST['nota_max'])? $nota_max_real : $_POST['nota_max'];
}
$oDesplProfesores->setOpcion_sel($id_preceptor);
$oDesplNotas->setOpcion_sel($id_situacion);

if (!empty($f_acta_real)) { // 3 meses cerca de la fecha del acta.
	$oData = DateTime::createFromFormat('j/m/Y',$f_acta_real);
	$oData2 = clone $oData;
	$oData->add(new \DateInterval('P3M'));
	$f_fin = $oData->format('d/m/Y');
	$oData2->sub(new \DateInterval('P3M'));
	$f_ini = $oData2->format('d/m/Y');
} else { // desde hoy, 10 meses antes.
	$oData = new \DateTime();
	$oData2 = clone $oData;
	$oData->add(new \DateInterval('P1M'));
	$f_fin = $oData->format('d/m/Y');
	$oData2->sub(new \DateInterval('P10M'));
	$f_ini = $oData2->format('d/m/Y');
}
$aWhere=array();
$aOperador=array();
$aWhere['f_ini'] = "'$f_ini','$f_fin'";
$aOperador['f_ini']='BETWEEN';
$aWhere['id_tipo_activ'] = '^1(12|33)';
$aOperador['id_tipo_activ'] = '~';
$aWhere['_ordre'] = 'f_ini';
$cActividades = $GesActividades->getActividades($aWhere,$aOperador);
$aActividades=array();
foreach ($cActividades as $oActividad) {
	$id_actividad=$oActividad->getId_activ();
	$nom_activ=$oActividad->getNom_activ();
	$aActividades[$id_actividad]=$nom_activ;
}
$oDesplActividades = new web\Desplegable();
$oDesplActividades->setOpciones($aActividades);
$oDesplActividades->setBlanco(1);
$oDesplActividades->setNombre('id_activ');
$oDesplActividades->setOpcion_sel($id_activ);

// miro cuales son las opcionales genéricas, para la funcion actualizar() de java.
// la condicion es que tengan id_sector=1
$aWhere=array();
$aOperador=array();
$aWhere['status']='t';
$aWhere['id_sector']=1;
$aWhere['id_nivel']=3000;
$aOperador['id_nivel']='<';
$aWhere['_ordre']='id_nivel';
$cOpcionalesGenericas = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
$condicion='';
$lista_nivel_op='';
foreach ($cOpcionalesGenericas as $oOpcional) {
	$id_nivel_j = $oOpcional->getId_nivel();
	$condicion.="id==".$id_nivel_j." || ";
	$lista_nivel_op.=$id_nivel_j.",";
}
$condicion=substr($condicion,0,-4);

$go_to_1 = web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau,'id_dossier'=>1011,'permiso'=>$permiso)));
//$go_to_1="apps/dossiers/controller/dossiers_ver.php?id_dossier=1011&pau=$pau&id_pau=$id_pau&obj_pau=$obj_pau&permiso=$permiso";

$go_to = empty($_POST['go_to'])? $go_to_1 : $_POST['go_to'];

$campos_chk = '!preceptor';

$oHash = new web\Hash();
$camposForm = 'precep!opcional!nota_num!nota_max!id_situacion!acta!f_acta!preceptor!id_preceptor!epoca!id_activ!detalle';
$camposNo = 'go_to_que!id_preceptor!id_activ'.$campos_chk;
$a_camposHidden = array(
		'campos_chk'=>$campos_chk,
		'mod' => $mod,
		'pau' => $pau,
		'id_pau' => $id_pau,
		'obj_pau' => $obj_pau,
		'permiso' => $permiso,
		'go_to' => $go_to
		);

if (!empty($id_asignatura_real)) { //caso de modificar
	$a_camposHidden['id_asignatura_real'] = $id_asignatura_real;
	$a_camposHidden['id_asignatura'] = $id_asignatura_real;
	$a_camposHidden['id_nivel'] = $id_nivel;
} else {
	$camposForm .= '!id_nivel';
	$camposNo .= '!id_nivel';
	if ($_POST['opcional'] == 'n') {
		//si no es opcional el id_asignatura es el mismo que id_nivel
		$a_camposHidden['id_asignatura'] = '1';
	} else {
		$camposForm .= '!id_asignatura';
	}
}
$oHash->setcamposForm($camposForm);
$oHash->setcamposNo($camposNo);
$oHash->setArraycamposHidden($a_camposHidden);
?>
<script>
$(function() { $( "#f_acta" ).datepicker(); });

fnjs_nota=function(){
	var num;
	var max;
	var sit;
	
	num = $('#nota_num').val();
	max = $('#nota_max').val();
	sit = $('#id_situacion').val();
	if (!num)  $('#id_situacion').val('0');
	num = parseFloat(num);
	if (typeof num == 'number' && num > 1) {
 		$('#id_situacion').val(10);
	}
	max_default = <?= core\ConfigGlobal::nota_max(); ?>;
	if (!max)  $('#nota_max').val(max_default);
}

fnjs_actualizar=function(){
	var p=document.f_1011.preceptor.checked;
	if (p) {
		$('#precep').val("si");
	} else {
		$('#precep').val("no");
	}
	var id=document.f_1011.id_nivel.value;
	if (<?php echo $condicion; ?>) {
		$('#opcional').val('s');
	} else {
		$('#opcional').val('n');
	}
	$('#f_1011').attr('action',"apps/notas/controller/form_1011.php");
	fnjs_enviar_formulario('#f_1011','#ficha_personas');
}

fnjs_guardar=function(formulario){
	var err=0;
	var mod=document.f_1011.mod.value;
	var acta=document.f_1011.acta.value;
	var f_acta=document.f_1011.f_acta.value;
	var situacion=document.f_1011.id_situacion.value;

	if (situacion == 10) { // comprobar la nota numérica
		var num = $('#nota_num').val();
		var max = $('#nota_max').val();
		num = parseFloat(num);
		max = parseFloat(max);
		if (isNaN(num)) { alert ('<?= _("valor de nota no válido") ?>'); err=1; } 
		if (num < 0 || num > max) { alert ('<?= _("nota fuera de rango") ?>'); err=1; } 
	}
	
	// situacion = 2 es para cursada
	if (!acta && situacion != 2) { alert("Debe llenar el campo del acta"); document.f_1011.acta.focus(); err=1; }
	if (f_acta) {
		if (!fnjs_comprobar_fecha('#f_acta')) { err=1; }
	}
	if ($('#id_asignatura').val()=="") {
		$('#id_asignatura').val(document.f_1011.id_nivel.value);
	}
	
	var rr=fnjs_comprobar_campos(formulario,'<?= addslashes($obj) ?>');
	//alert ("EEE "+rr);
	if (rr=='ok' && err!=1) {
		go=$('#go_to').val();
		$(formulario).attr('action',"apps/notas/controller/update_1011.php");
		$(formulario).submit(function() {
			$.ajax({
				data: $(this).serialize(),
				url: $(this).attr('action'),
				type: 'post',
				complete: function (rta) {
					rta_txt=rta.responseText;
					if (rta_txt.search('id="ir_a"') != -1) {
						fnjs_mostra_resposta(rta,'#main'); 
					} else {
						if (go) { 
							fnjs_update_div('#main',go);
						} else {
							alert ('no se donde ir');
						}
					}
				}
			});
			return false;
		});
		$(formulario).submit();
		$(formulario).off();
	}
}
</script>
<form id="f_1011" name="f_1011" action="" method="POST">
<?= $oHash->getCamposHtml(); ?>
<input type="hidden" id="precep" name="precep" value="<?= $precep ?>">
<input type="hidden" id="go_to_que" name="go_to_que" value="">
<input type="hidden" id="opcional" name="opcional" value="<?= $_POST['opcional'] ?>">
<table>
<thead><tr><th colspan=4><?= _("asignaturas aprobadas") ?></th></tr></thead>
<tbody>
<?php
if (!empty($id_asignatura_real)) { //caso de modificar
	?>
	<tr><td><?= ucfirst(_("asignatura")) ?>:</td><td class=contenido><?= $nombre_corto ?></td></tr>
	<?php
} else {
	//niveles posibles (los no aprobados)
	echo "<tr><td>".ucfirst(_("asignatura")).":</td>";
	echo "<td><select id='id_nivel' name='id_nivel' onchange='fnjs_actualizar()'><option></option>";
	$ninguno_sel=1;
	foreach ($aFaltan as $list_id_nivel=>$nombre_corto) {
		if ($list_id_nivel==$_POST['id_nivel']) { $chk="selected"; $ninguno_sel=0; } else { $chk=""; }
		echo "<option value=$list_id_nivel $chk>$nombre_corto</option>";
	}
	echo "</select></td>";
	
	// Si la primera vez que se muestra la página, y la primera asignatura es una opcional,
	// hay que tenerlo en cuenta, sino las opcionales sólo se muestran al seleccionar una asignatura.
	if ($ninguno_sel==1) {
		reset($aFaltan);
		$id=key($aFaltan);
		if (strstr($lista_nivel_op, $id)) { $_POST['opcional']='s'; } else { $_POST['opcional']='n'; }
	}
	
	// opcionales posibles
	if (!empty($_POST['opcional']) && $_POST['opcional'] == 's') {
		echo "<td>".ucfirst(_("opcional")).":</td>";
		echo "<td><select id='id_asignatura' name='id_asignatura' >";
		$i=0;
		foreach ($cOpcionales as $oOpcional) {
			$i++;
			$asignatura=$oOpcional->getNombre_corto();
			$id_asignatura_list=$oOpcional->getId_asignatura();
			if ($id_asignatura_list==$_POST['id_asignatura']) { $chk="selected"; } else { $chk=""; }
			echo "<option value=$id_asignatura_list $chk>$asignatura</option>";
		}
		echo "</select></td>";
	} else { //si no es opcional el id_asignatura es el mismo que id_nivel
		//echo "<input type=\"hidden\" id=\"id_asignatura\" name=\"id_asignatura\" value=\"nueva\">";
	}
	echo "</tr>";
}

echo "<tr><td>"._("nota")."</td><td>";
echo "<input type=\"text\" id=\"nota_num\" name=\"nota_num\" value=\"$nota_num\" size=2 onchange='fnjs_nota()'>";
echo ' ' . _("sobre") . ' ';
echo "<input type=\"text\" id=\"nota_max\" name=\"nota_max\" value=\"$nota_max\" size=2>";
echo "<td>"._("situación")."</td><td>";
echo $oDesplNotas->desplegable();
echo "</td></tr>";
echo "<tr><td>"._("acta").'</td>';
echo "<td colspan=3><input type=\"text\" id=\"acta\" name=\"acta\" value=\"$acta\" size=20>";
echo '  ("?": '._("significa inventado").')   '._("Formato") .': "dlx nn/aa" o "dlx" o "region" o "?"'.'</td></tr>';
echo "<tr><td>"._("fecha acta")."</td><td><input type=\"text\" id=\"f_acta\" name=\"f_acta\" value=\"$f_acta\" size=12></td></tr>";
switch ($precep) { 
	case "si":
		$chk="checked";
		echo "<tr><td>"._("preceptor")."</td>";
		echo "<td><input type=\"Checkbox\" id=\"preceptor\" name=\"preceptor\" value=\"true\" $chk onclick='fnjs_actualizar()'></td>";
		echo "<td colspan=2 class=contenido>";
		echo $oDesplProfesores->desplegable();
		echo "</td>";
		break;
	case "no":
		$chk="";
		echo "<tr><td>"._("preceptor")."</td>";
		echo "<td><input type=\"Checkbox\" id=\"preceptor\" name=\"preceptor\" value=\"true\" $chk onclick='fnjs_actualizar()'></td>";
		break;
}
echo "</tr><tr><td>"._("epoca")."</td><td><input type=\"text\" id=\"epoca\" name=\"epoca\" value=\"$epoca\" size=2></td></tr>";
echo "<tr><td>"._("cursada en")."</td>";
echo "<td class=contenido colspan=3>";
echo $oDesplActividades->desplegable();
echo "</td>";
echo "<tr><td>"._("detalle")."</td><td><input type=\"text\" id=\"detalle\" name=\"detalle\" value=\"$detalle\" ></td></tr>";
?>	
</tbody></table>
<br><input type="button" value="<?php echo ucfirst(_("guardar")); ?>" onclick="fnjs_guardar(this.form)">
</form>
