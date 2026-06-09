<?php
/**
 * Controlador AJAX HTML: formulario modal del ingreso de una
 * actividad (edición). Delega en `/src/casas/casa_ingreso_form_data`.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/casas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$id_activ = (int)filter_input(INPUT_POST, 'id_activ');
if ($id_activ === 0) {
    $id_activ = (int)filter_input(INPUT_GET, 'id_activ');
}

$campos = ['id_activ' => $id_activ];
$data = casas_post_data(PostRequest::getDataFromUrl('/src/casas/casa_ingreso_form_data', $campos));
$form = casas_ingreso_form_from_payload($data);

if (!$form['ok']) {
    echo $form['error'] !== '' ? $form['error'] : (string)_("No se puede cargar el formulario.");
    return;
}

if ($form['puede_modificar_tarifa']) {
    $oDespl = new Desplegable();
    $oDespl->setOpciones($form['a_opciones_tarifa']);
    $oDespl->setNombre('id_tarifa');
    $oDespl->setOpcion_sel($form['id_tarifa']);
    $tarifa_html = $oDespl->desplegable();
} else {
    $tarifa_html = htmlspecialchars($form['letra_tarifa']);
}
?>
<form id="frm_ingreso">
    <h3><?= _("actividad") ?>:</h3>
    <h5><?= htmlspecialchars($form['nom_activ']) ?></h5>
    <?= _("id_tarifa") ?>: <?= $tarifa_html ?><br>
    <?= _("precio") ?> <input type="text" size="8" name="precio" value="<?= htmlspecialchars($form['precio']) ?>">
    <h3><?= _("Ingreso") ?>:</h3>
    <input type="hidden" name="id_activ" value="<?= $id_activ ?>">
    <?= _("ingresos reales") ?> <input type="text" size="12" name="ingresos" value="<?= htmlspecialchars($form['ingresos']) ?>">
    <?= _("asistentes") ?> <input type="text" size="12" name="num_asistentes" value="<?= htmlspecialchars($form['num_asistentes']) ?>">
    <br>
    <?= _("observaciones") ?> <input type="text" size="40" name="observ" value="<?= htmlspecialchars($form['observ']) ?>">
    <br><br>
    <input type="button" value="<?= _('guardar') ?>" onclick="fnjs_guardar('#frm_ingreso','guardar');">
    <input type="button" value="<?= _('eliminar') ?>" onclick="fnjs_guardar('#frm_ingreso','eliminar');">
    <input type="button" value="<?= _('cancel') ?>" onclick="fnjs_cerrar();">
</form>
