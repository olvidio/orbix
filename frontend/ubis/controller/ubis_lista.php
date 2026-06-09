<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Esta página muestra una tabla con los ubis seleccionados.
 * Para "actividad_select_ubi.phtml"
 *
 * @package    delegacion
 * @subpackage ubis
 * @author     Daniel Serrabou
 * @since      3/2/09.
 */

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qnombre_ubi = (string)filter_input(INPUT_POST, 'nombre_ubi');

$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/ubis_lista_data', [
    'nombre_ubi' => $Qnombre_ubi,
]));
$cabeceras = ubis_lista_cabecera_strings($data['a_cabeceras'] ?? []);
$valores = ubis_lista_filas($data['a_valores'] ?? []);
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
    $sel = tessera_imprimir_string($fila['sel'] ?? '');
    ?>
    <tr><td class=link id='<?= $sel ?>' onclick="fnjs_buscar('#frm_buscar_3','<?= $sel ?>');" ><?= tessera_imprimir_string($fila[1] ?? '') ?></td>
    <td><?= tessera_imprimir_string($fila[2] ?? '') ?></td>
    <td><?= tessera_imprimir_string($fila[3] ?? '') ?></td>
    <td><?= tessera_imprimir_string($fila[4] ?? '') ?></td>
    <td><?= tessera_imprimir_string($fila[5] ?? '') ?></td>
    <td><?= tessera_imprimir_string($fila[6] ?? '') ?></td>
    <td><?= tessera_imprimir_string($fila[7] ?? '') ?></td></tr>
    <?php
}
