<?php

use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

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

require_once __DIR__ . '/../helpers/notas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$QdlRaw = filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qdl = is_array($QdlRaw) ? $QdlRaw : [];

$data = PostRequest::getDataFromUrl('/src/notas/asignaturas_pendientes_data', [
    'dl' => $Qdl,
]);
$presentacion = notas_asignaturas_pendientes_from_payload($data);

$oTabla = new Lista();
$oTabla->setId_tabla('pendientes');
$oTabla->setCabeceras($presentacion['cabeceras']);
$oTabla->setDatos($presentacion['filas']);


if ($presentacion['ambito_rstgr']) {
    $aChecked = notas_checked_ids_from_post($Qdl);
    $a_delegacionesStgr = $presentacion['delegaciones'];

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
