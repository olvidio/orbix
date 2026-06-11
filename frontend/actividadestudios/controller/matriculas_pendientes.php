<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$stack = isset($_POST['stack']) ? (int) filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT) : 0;
$returningViaStack = $stack !== 0;
/** @var string|list<string> $Qid_sel */
$Qid_sel = list_nav_id_sel_from_post();
$Qscroll_id = list_nav_scroll_id_from_post();

if ($returningViaStack) {
    $oPosicion2 = new Posicion();
    if ($oPosicion2->goStack($stack)) {
        $restoredSel = list_nav_id_sel_for_lista($oPosicion2->getParametro('id_sel'));
        if (!list_nav_id_sel_is_empty($restoredSel)) {
            $Qid_sel = $restoredSel;
        }
        $restoredScroll = $oPosicion2->getParametro('scroll_id');
        if (is_scalar($restoredScroll) && (string) $restoredScroll !== '') {
            $Qscroll_id = (string) $restoredScroll;
        }
        $oPosicion2->olvidar($stack);
    }
}

$oPosicion->recordar();

list_nav_persist_selection_on_list_page(
    $oPosicion,
    $Qid_sel,
    $Qscroll_id,
    $returningViaStack,
);

$aviso = '';
$pendientes = actividadestudios_matriculas_pendientes_from_payload(
    actividadestudios_post_data(PostRequest::getDataFromUrl('/src/actividadestudios/matriculas_pendientes_data', []))
);
$msg_err = $pendientes['msg_err'];
$aviso = $pendientes['aviso'];
$a_valores = actividadestudios_lista_valores($pendientes['a_valores'], $Qid_sel, $Qscroll_id);

$titulo = _("lista de matrículas pendientes de poner nota");
$a_botones = array(
    array('txt' => _("ver asignaturas ca"), 'click' => "fnjs_ver_ca(this.form)"),
    array('txt' => _("borrar matrícula"), 'click' => "fnjs_borrar(this.form)")
);

$a_cabeceras = array(_("actividad"), _("asignatura"), _("alumno"), _("p"));

$oHash = new HashFront();
$oHash->setCamposNo('sel!mod!pau!scroll_id!id_sel!id_pau');
$a_camposHidden = array(
    'id_dossier' => 3005,
    'permiso' => 3,
    'obj_pau' => 'Actividad',
    'queSel' => 'asig',
);
$oHash->setArraycamposHidden($a_camposHidden);

if ($msg_err !== '') {
    actividadestudios_echo_string($msg_err);
}
echo $oPosicion->mostrar_left_slide(1);

?>
<script>
    fnjs_ver_ca = function (formulario, n) {
        fnjs_sync_grid_sel_checkboxes(formulario);
        rta = fnjs_solo_uno(formulario);
        if (rta == 1) {
            $("#pau").val("a");
            $(formulario).attr('action', "frontend/dossiers/controller/dossiers_ver.php");
            fnjs_enviar_formulario(formulario, '#main');
        }
    }

    fnjs_borrar = function (formulario) {
        let mensaje = "<?= _("¿Está seguro que desea borrar todas las matrículas seleccionadas?");?>";
        if (confirm(mensaje)) {
            $("#mod").val("eliminar");
            let url = '<?= AppUrlConfig::getApiBaseUrl() ?>/src/actividadestudios/matricula_eliminar';
            let datos = $(formulario).serialize();
            let request = $.ajax({
                data: datos,
                url: url,
                method: 'POST',
                dataType: 'json'
            });
            request.done(function (json) {
                if (json.success !== true) {
                    alert(<?= json_encode(_("respuesta")) ?> + ': ' + json.mensaje);
                } else {
                    fnjs_actualizar();
                }
            });
        }
    }
    fnjs_actualizar = function () {
        var url = '<?= HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividadestudios/controller/matriculas_pendientes.php') ?>';
        fnjs_update_div('#main', url);
    }
</script>
<h2 class=titulo><?= $titulo ?></h2>
<h3><?= $aviso; ?></h3>
<form id="seleccionados" name="seleccionados" action="" method="post">
    <?= $oHash->getCamposHtml(); ?>
    <input type="hidden" id="pau" name="pau" value="p">
    <input type="hidden" id="mod" name="mod" value="">
    <?php
    $oTabla = new Lista();
    $oTabla->setId_tabla('mtr_pdte');
    $oTabla->setCabeceras($a_cabeceras);
    $oTabla->setBotones($a_botones);
    $oTabla->setDatos($a_valores);
    echo $oTabla->mostrar_tabla();
    ?>
</form>
