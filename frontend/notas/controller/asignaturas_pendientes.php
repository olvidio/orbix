<?php

use core\ConfigGlobal;
use core\ViewTwig;
use src\notas\application\TablaAlumnosAsignaturas;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use web\Desplegable;
use web\Hash;
use web\Lista;

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

$oService = new TablaAlumnosAsignaturas();

if (ConfigGlobal::mi_ambito() === 'rstgr') {

    $aChecked = $Qdl;
    $region_stgr = ConfigGlobal::mi_dele();
    $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
    $a_delegacionesStgr = $repoDelegacion->getArrayDlRegionStgr([$region_stgr]);

    $oCuadros = new Desplegable();
    $oCuadros->setNombre('dl');
    $oCuadros->setChecked($aChecked);
    $oCuadros->setOpciones($a_delegacionesStgr);

    $oHash = new Hash();
    $oHash->setCamposForm('dl');
    $oHash->setcamposNo('dl');

    if (!empty($Qdl)) {
        $datosTabla = $oService->paraRegionStgr($Qdl, $a_delegacionesStgr);
    } else {
        $datosTabla = ['cabeceras' => [], 'filas' => []];
    }

} else {
    $datosTabla = $oService->paraDelegacion();
}

$oTabla = new Lista();
$oTabla->setId_tabla('pendientes');
$oTabla->setCabeceras($datosTabla['cabeceras']);
$oTabla->setDatos($datosTabla['filas']);


if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $url = 'frontend/notas/controller/asignaturas_pendientes.php';
    $a_campos = [
        'oHash' => $oHash,
        'url' => $url,
        'boton_txt' => _("Buscar"),
        'oCuadros' => $oCuadros,
    ];

    $oView = new ViewTwig('ubis/controller');
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
