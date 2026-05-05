<?php

use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Cuadro "alumnos x asignaturas": genera una tabla con las asignaturas
 * pendientes de todos los alumnos, filtrando por delegacion (`ambito = dl`)
 * o por las delegaciones seleccionadas de la region stgr (`ambito = rstgr`).
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        24/10/12.
 */

require_once 'frontend/shared/global_header_front.inc';

$Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$data = PostRequest::getDataFromUrl('/src/notas/asignaturas_pendientes_data', [
    'dl' => $Qdl,
]);

$datosTabla = [
    'cabeceras' => $data['cabeceras'] ?? [],
    'filas' => $data['filas'] ?? [],
];

$oTabla = new Lista();
$oTabla->setId_tabla('pendientes');
$oTabla->setCabeceras($datosTabla['cabeceras']);
$oTabla->setDatos($datosTabla['filas']);


if (!empty($data['ambito_rstgr'])) {
    $aChecked = $Qdl;
    $a_delegacionesStgr = $data['delegaciones'] ?? [];

    $oCuadros = new Desplegable();
    $oCuadros->setNombre('dl');
    $oCuadros->setChecked($aChecked);
    $oCuadros->setOpciones($a_delegacionesStgr);

    $oHash = new HashFront();
    $oHash->setCamposForm('dl');
    $oHash->setcamposNo('dl');

    $url = 'frontend/notas/controller/asignaturas_pendientes.php';
    $a_campos = [
        'oHash' => $oHash,
        'url' => $url,
        'boton_txt' => _("Buscar"),
        'oCuadros' => $oCuadros,
    ];

    $oView = new ViewNewTwig('frontend/ubis/controller');
    $oView->renderizar('dl_rstgr_que.html.twig', $a_campos);
}
?>
<p><?= _("relación de asignaturas por alumno") ?></p>
<p>
    1: <?= _("pendiente") ?>
    2: <?= _("cursada") ?>
</p>
<br>
<div id="exportar" style="overflow: scroll; height: 800px">
    <?= $oTabla->mostrar_tabla_html(); ?>
</div>
<script>fnjs_left_side_hide()</script>
