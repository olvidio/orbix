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
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


?>
<ul>
<?php if ($GLOBALS['oPerm']->have_perm("est")){ ?>
	<li><span class="link" onclick="fnjs_update_div('#main','est/resumenes/comprobar_notas.php');"><?= _("Comprobar los datos del fichero de notas") ?></span></li>
<?php }
if (($GLOBALS['oPerm']->have_perm("est")) or ($GLOBALS['oPerm']->have_perm("dtor"))) { ?>
	<li><? printf(_("Informe anual del Studium Generale para <b>%s</b>"),_("numerarios")) ?>
		<ul>
			<li><span class="link" onclick="fnjs_update_div('#main','est/resumenes/informe_stgr_n.php?lista=1');"><?= _("con listados") ?></span></li>
			<li><span class="link" onclick="fnjs_update_div('#main','est/resumenes/informe_stgr_n.php?lista=0');"><?= _("sólo números") ?></span></li>
		</ul>
	</li>
	<li><? printf(_("Informe anual del Studium Generale para <b>%s</b>"),_("agregados")) ?>
		<ul>
			<li><span class="link" onclick="fnjs_update_div('#main','est/resumenes/informe_stgr_agd.php?lista=1');"><?= _("con listados") ?></span></li>
			<li><span class="link" onclick="fnjs_update_div('#main','est/resumenes/informe_stgr_agd.php?lista=0');"><?= _("sólo números") ?></span></li>
		</ul>
	</li>
	<li><? printf(_("Informe anual del Studium Generale para <b>%s</b>"),_("profesores")) ?>
		<ul>
			<li><span class="link" onclick="fnjs_update_div('#main','est/resumenes/informe_stgr_profesores.php?lista=0');"><?= _("sólo números") ?></span></li>
		</ul>
	</li>
	<br><br>
	<li><span class="link" onclick="fnjs_update_div('#main','est/asig_faltan_que.php');"><?= _("listar por asignaturas que faltan") ?></span></li>
<?php } ?>
</ul>
