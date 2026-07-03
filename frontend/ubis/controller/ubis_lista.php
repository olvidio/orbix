<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qnombre_ubi = (string)filter_input(INPUT_POST, 'nombre_ubi');

$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/ubis_lista_data', [
    'nombre_ubi' => $Qnombre_ubi,
]));
$cabeceras = UbisPayload::listaCabeceraStrings($data['a_cabeceras'] ?? []);
$valores = UbisPayload::listaFilas($data['a_valores'] ?? []);

ob_start();
?>
<table>
<tr>
<?php
foreach ($cabeceras as $cabecera) {
    echo "<th>$cabecera</th>";
}
?>
</tr>
<?php
foreach ($valores as $fila) {
    $sel = PayloadCoercion::string($fila['sel'] ?? '');
    ?>
    <tr><td class=link id='<?= $sel ?>' onclick="fnjs_buscar('#frm_buscar_3','<?= $sel ?>');" ><?= PayloadCoercion::string($fila[1] ?? '') ?></td>
    <td><?= PayloadCoercion::string($fila[2] ?? '') ?></td>
    <td><?= PayloadCoercion::string($fila[3] ?? '') ?></td>
    <td><?= PayloadCoercion::string($fila[4] ?? '') ?></td>
    <td><?= PayloadCoercion::string($fila[5] ?? '') ?></td>
    <td><?= PayloadCoercion::string($fila[6] ?? '') ?></td>
    <td><?= PayloadCoercion::string($fila[7] ?? '') ?></td></tr>
    <?php
}
?>
</table>
<?php
AjaxJsonSupport::html((string) ob_get_clean());
