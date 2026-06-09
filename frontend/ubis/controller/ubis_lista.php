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

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qnombre_ubi = (string)filter_input(INPUT_POST, 'nombre_ubi');

$data = PostRequest::getDataFromUrl('/src/ubis/ubis_lista_data', [
    'nombre_ubi' => $Qnombre_ubi,
]);

$a_cabeceras = $data['a_cabeceras'];
$a_valores = $data['a_valores'];
?>
<table>
<tr>
<?php
foreach ($a_cabeceras as $cabecera) {
    echo "<th>$cabecera</th>";
}
?>
</tr>
<?php
foreach ($a_valores as $fila) {
    ?>
    <tr><td class=link id='<?= $fila['sel'] ?>' onclick="fnjs_buscar('#frm_buscar_3','<?= $fila['sel'] ?>');" ><?= $fila[1] ?></td>
    <td><?= $fila[2] ?></td>
    <td><?= $fila[3] ?></td>
    <td><?= $fila[4] ?></td>
    <td><?= $fila[5] ?></td>
    <td><?= $fila[6] ?></td>
    <td><?= $fila[7] ?></td></tr>
    <?php
}
