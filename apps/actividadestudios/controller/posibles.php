<?php
use personas\model as personas;
use ubis\model as ubis;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Grupo de estudios
$mi_dele = core\ConfigGlobal::mi_dele();
$GesGrupoEst = new ubis\GestorDelegacion();
$cMiDl = $GesGrupoEst->getDelegaciones(array('dl'=>$mi_dele));
if (is_array($cMiDl) && !empty($cMiDl)) {
	$grupo_estudios = $cMiDl[0]->getGrupo_estudios();
	$cDelegaciones = $GesGrupoEst->getDelegaciones(array('grupo_estudios'=>$grupo_estudios));
	$mi_grupo = '';
	foreach ($cDelegaciones as $oDelegacion) {
		$mi_grupo .= empty($mi_grupo)? '' : ',';
		$mi_grupo .= $oDelegacion->getDl();
	}
} else {
	$mi_grupo = _("No encuentro el grupo de estudios al que pertenece la dl");
}

// centros donde hay numerarios, aunque sean de agd
$GesPersonas = new personas\GestorPersonaN();
$aListaCtr = $GesPersonas->getListaCtr();
$aCentrosN = array();
$aCentrosOrden = array();
foreach($aListaCtr as $id_ubi) {
	$oCentro = new ubis\CentroDl(array('id_ubi'=>$id_ubi));
	$nombre_ubi = $oCentro->getNombre_ubi();
	$aCentrosOrden[$nombre_ubi] = array($id_ubi => $nombre_ubi);
}
uksort($aCentrosOrden,"core\strsinacentocmp");
// No encuentro la manera de añadir las opciones sin desordenar el array de indice numérico
$aCentrsoNExt = array();
$aCentrosNExt[1] = _("todos los ctr");
$aCentrosNExt[2] = "----------";
foreach ($aCentrosOrden as $aCentro) {
	$key = key($aCentro);
	$value = current($aCentro);
	$aCentrosNExt[$key] = $value;
}

$oDesplCtrN = new web\Desplegable();
$oDesplCtrN->setNombre('id_ctr_n');
$oDesplCtrN->setOpciones($aCentrosNExt);
$oDesplCtrN->setBlanco(1);
$oDesplCtrN->setAction("fnjs_n_a('n')");

// centros donde hay agregados, aunque sean de n
$GesPersonas = new personas\GestorPersonaAgd();
$aListaCtr = $GesPersonas->getListaCtr();
$aCentrosAgd = array();
$aCentrosOrden = array();
foreach($aListaCtr as $id_ubi) {
	$oCentro = new ubis\CentroDl(array('id_ubi'=>$id_ubi));
	$nombre_ubi = $oCentro->getNombre_ubi();
	$aCentrosOrden[$nombre_ubi] = array($id_ubi => $nombre_ubi);
}
uksort($aCentrosOrden,"core\strsinacentocmp");
// No encuentro la manera de añadir las opciones sin desordenar el array de indice numérico
$aCentrsoAgdExt = array();
$aCentrosAgdExt[1] = _("todos los ctr");
$aCentrosAgdExt[2] = "----------";
foreach ($aCentrosOrden as $aCentro) {
	$key = key($aCentro);
	$value = current($aCentro);
	$aCentrosAgdExt[$key] = $value;
}

$oDesplCtrAgd = new web\Desplegable();
$oDesplCtrAgd->setNombre('id_ctr_agd');
$oDesplCtrAgd->setOpciones($aCentrosAgdExt);
$oDesplCtrAgd->setBlanco(1);
$oDesplCtrAgd->setAction("fnjs_n_a('agd')");


$oHash = new web\Hash();
$oHash->setcamposForm('id_ctr_agd!id_ctr_n!texto!empiezamax!empiezamin!periodo!ref!iactividad_val!iasistentes_val!year');
$oHash->setCamposNo('na!todos');
$a_camposHidden = array(
		'asistentes_val' => 1,
		'actividades_val' => 2
		);
$oHash->setArraycamposHidden($a_camposHidden);

?>
<script>
fnjs_n_a=function(dd){
	$('#na').val(dd);
	if (dd=='agd') {
		$('#id_ctr_n').val('');
	} else {
		$('#id_ctr_agd').val('');
	}
}
</script>
<form id="modifica" name="modifica" action="apps/actividadestudios/controller/ca_posibles.php" method="POST">
<?= $oHash->getCamposHtml(); ?>
<input type="hidden" id="na" name="na" value="">
<!-- Selección de centros -->
<table>
	<tr>
		<th colspan=2><?php echo ucfirst(_("ver cuadro de posibles ca")); ?></th>
	</tr>
	<tr>
		<td colspan=2><?php echo _("Nota: Para que salgan los ca en el cuadro deben tener introducidas las asignaturas y el campo de nivel de stgr"); ?></td>
	</tr>
	<tr>
	<td>
		<table style="width:250px">
			<tr>
			<th colspan=2><?php echo ucfirst(_("numerarios")); ?></th>
			</tr>
			<tr>
			<td colspan=2 align=center>
			<br>
			</td></tr>
			<tr>
			<td><b><?= _("centro") ?></b></td>
			<td><?= $oDesplCtrN->desplegable(); ?></td></tr>
			</td></tr>
		</table>
	</td>
	<td>
		<table style="width:250px">
			<tr>
			<th colspan=2><?php echo ucfirst(_("agregados")); ?></th>
			</tr>
			<tr>
			<td colspan=2 align=center>
			<br>
			</td></tr>
			<tr>
			<td><b><?= _("centro") ?></b></td>
			<td><?= $oDesplCtrAgd->desplegable(); ?></td></tr>
		</table>
	</td></tr>
</table>
<!-- Selección de periodo -->
<?php
	$aOpciones =  array(
						'verano'=>_('verano'),
						'curso_ca'=>_('curso'),
						'separador'=>'---------',
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
	$oFormP->setTitulo(core\strtoupper_dlb(_("periodo de las actividades")));
	$oFormP->setPosiblesPeriodos($aOpciones);
	$oFormP->setDesplAnysOpcion_sel(date('Y'));
	echo $oFormP->getHtml();
?>
<table>
<tr><td><br></td></tr>
<tr>
<th colspan=5><?php echo _("selección por delegaciones"); ?></th>
</tr>
<tr><td colspan=4><input type="radio" id="todos" name="todos" value=<?= $grupo_estudios?> checked><?= $mi_grupo ?>    <input type="radio" name="todos" value=1 ><?= _("todos") ?></td></tr>
<tr><td><br></td></tr>
<tr>
<th colspan=5><?php echo _("formato del cuadro"); ?></th>
</tr>
<tr><td><?php echo ucfirst(_("escrito de referencia")).":"; ?><input type=text name=ref></td></tr>
<tr><td><input type="Radio" name="texto" value="text"><?php echo ucfirst(_("texto cabezera horizontal (excel)")); ?></td>
<td colspan=4><input type="Radio" name="texto" value="image" checked><?php echo ucfirst(_("texto cabezera vertical (imprimir)")); ?></td></tr>
<tr><td>
<?php
	$btn1="<input name='btn1' type=button onclick=fnjs_enviar_formulario('#modifica') value='"._("ver cuadro")."' >";
	 echo $btn1;
?>
</td></tr>
</table>
