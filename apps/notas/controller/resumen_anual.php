<?php
/**
* Esta página sirve para comprobar las notas de la tabla e_notas.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		22/11/02.
*		
*/

/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$go['comprobar_n']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/comprobar_notas.php?'.http_build_query(array('id_tabla'=>'n')));
$go['comprobar_a']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/comprobar_notas.php?'.http_build_query(array('id_tabla'=>'a')));
$go['n_listado']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_n.php?'.http_build_query(array('lista'=>1)));
$go['n_numeros']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_n.php?'.http_build_query(array('lista'=>0)));
$go['agd_listado']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_agd.php?'.http_build_query(array('lista'=>1)));
$go['agd_numeros']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_agd.php?'.http_build_query(array('lista'=>0)));
$go['profesores_numeros']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_profesores.php?'.http_build_query(array('lista'=>0)));
$go['profesores_listado']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_profesores.php?'.http_build_query(array('lista'=>1)));
$go['asig_faltan']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/asig_faltan_que.php');


// --------------------------- html -------------------------------------------------------------------------------------
?>
<ul>
<?php if ($_SESSION['oPerm']->have_perm("est")){ ?>
	<li><span class="link" onclick="fnjs_update_div('#main','<?= $go['comprobar_n'] ?>');"><?= _("Comprobar los datos del fichero de notas para numerarios") ?></span></li>
	<li><span class="link" onclick="fnjs_update_div('#main','<?= $go['comprobar_a'] ?>');"><?= _("Comprobar los datos del fichero de notas para agregados") ?></span></li>
<?php }
if (($_SESSION['oPerm']->have_perm("est")) or ($_SESSION['oPerm']->have_perm("dtor"))) { ?>
	<li><?php printf(_("Informe anual del Studium Generale para <b>%s</b>"),_("numerarios")) ?>
		<ul>
			<li><span class="link" onclick="fnjs_update_div('#main','<?= $go['n_listado'] ?>');"><?= _("con listados") ?></span></li>
			<li><span class="link" onclick="fnjs_update_div('#main','<?= $go['n_numeros'] ?>');"><?= _("sólo números") ?></span></li>
		</ul>
	</li>
	<li><?php printf(_("Informe anual del Studium Generale para <b>%s</b>"),_("agregados")) ?>
		<ul>
			<li><span class="link" onclick="fnjs_update_div('#main','<?= $go['agd_listado'] ?>');"><?= _("con listados") ?></span></li>
			<li><span class="link" onclick="fnjs_update_div('#main','<?= $go['agd_numeros'] ?>');"><?= _("sólo números") ?></span></li>
		</ul>
	</li>
	<li><?php printf(_("Informe anual del Studium Generale para <b>%s</b>"),_("profesores")) ?>
		<ul>
			<li><span class="link" onclick="fnjs_update_div('#main','<?= $go['profesores_listado'] ?>');"><?= _("con listados") ?></span></li>
			<li><span class="link" onclick="fnjs_update_div('#main','<?= $go['profesores_numeros'] ?>');"><?= _("sólo números") ?></span></li>
		</ul>
	</li>
	<br><br>
	<li><span class="link" onclick="fnjs_update_div('#main','<?= $go['asig_faltan'] ?>');"><?= _("listar por asignaturas que faltan") ?></span></li>
<?php } ?>
</ul>
