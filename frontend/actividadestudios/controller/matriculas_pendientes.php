<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;

/**
 * Para asegurar que inicia la sesión, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}
$oPosicion->recordar();

$aviso = '';
$form = '';
$traslados = '';
if (!empty($traslados)) {
} else {
    $data = PostRequest::getDataFromUrl('/src/actividadestudios/matriculas_pendientes_data', []);
    $msg_err = $data['msg_err'] ?? '';
    $a_valores = $data['a_valores'] ?? [];
}

$titulo = _("lista de matrículas pendientes de poner nota");
$a_botones = array(
    array('txt' => _("ver asignaturas ca"), 'click' => "fnjs_ver_ca(this.form)"),
    array('txt' => _("borrar matrícula"), 'click' => "fnjs_borrar(this.form)")
);

$a_cabeceras = array(_("actividad"), _("asignatura"), _("alumno"), _("p"));

$i = 0;
if (!isset($a_valores)) {
    $a_valores = [];
}
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}
if (!isset($msg_err)) {
    $msg_err = '';
}

$oHash = new HashFront();
$oHash->setCamposNo('sel!mod!pau!scroll_id');
$a_camposHidden = array(
    'id_dossier' => 3005,
    'permiso' => 3,
    'obj_pau' => 'Actividad',
    'queSel' => 'asig',
);
$oHash->setArraycamposHidden($a_camposHidden);

if (!empty($msg_err)) {
    echo $msg_err;
}
echo $oPosicion->mostrar_left_slide(1);

?>
<script>
    fnjs_ver_ca = function (formulario, n) {
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
            $(mod).val("eliminar");
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
