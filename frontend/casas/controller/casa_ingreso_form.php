<?php
/**
 * Controlador AJAX HTML: formulario modal del ingreso de una
 * actividad (edición). Delega en `/src/casas/casa_ingreso_form_data`.
 */

use frontend\shared\PostRequest;
use web\Desplegable;

require_once 'frontend/shared/global_header_front.inc';

$id_activ = (int)filter_input(INPUT_POST, 'id_activ');
if ($id_activ === 0) {
    $id_activ = (int)filter_input(INPUT_GET, 'id_activ');
}

$campos = ['id_activ' => $id_activ];
$data = PostRequest::getDataFromUrl('/src/casas/casa_ingreso_form_data', $campos);
$payload = is_array($data) ? $data : [];

if (($payload['ok'] ?? false) === false) {
    echo $payload['error'] ?? (string)_("No se puede cargar el formulario.");
    return;
}

$nom_activ = (string)($payload['nom_activ'] ?? '');
$id_tarifa = (string)($payload['id_tarifa'] ?? '');
$puede_modificar_tarifa = (bool)($payload['puede_modificar_tarifa'] ?? false);
$precio = $payload['precio'] ?? '';
$ingresos = $payload['ingresos'] ?? '';
$num_asistentes = $payload['num_asistentes'] ?? '';
$observ = (string)($payload['observ'] ?? '');

if ($puede_modificar_tarifa) {
    $oDespl = new Desplegable();
    $oDespl->setOpciones($payload['a_opciones_tarifa'] ?? []);
    $oDespl->setNombre('id_tarifa');
    $oDespl->setOpcion_sel($id_tarifa);
    $tarifa_html = $oDespl->desplegable();
} else {
    $tarifa_html = htmlspecialchars((string)($payload['letra_tarifa'] ?? ''));
}
?>
<form id="frm_ingreso">
    <h3><?= _("actividad") ?>:</h3>
    <h5><?= htmlspecialchars($nom_activ) ?></h5>
    <?= _("id_tarifa") ?>: <?= $tarifa_html ?><br>
    <?= _("precio") ?> <input type="text" size="8" name="precio" value="<?= htmlspecialchars((string)$precio) ?>">
    <h3><?= _("Ingreso") ?>:</h3>
    <input type="hidden" name="id_activ" value="<?= $id_activ ?>">
    <?= _("ingresos reales") ?> <input type="text" size="12" name="ingresos" value="<?= htmlspecialchars((string)$ingresos) ?>">
    <?= _("asistentes") ?> <input type="text" size="12" name="num_asistentes" value="<?= htmlspecialchars((string)$num_asistentes) ?>">
    <br>
    <?= _("observaciones") ?> <input type="text" size="40" name="observ" value="<?= htmlspecialchars($observ) ?>">
    <br><br>
    <input type="button" value="<?= _('guardar') ?>" onclick="fnjs_guardar('#frm_ingreso','guardar');">
    <input type="button" value="<?= _('eliminar') ?>" onclick="fnjs_guardar('#frm_ingreso','eliminar');">
    <input type="button" value="<?= _('cancel') ?>" onclick="fnjs_cerrar();">
</form>
