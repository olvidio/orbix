<?php

use core\ConfigGlobal;
use core\ViewTwig;
use notas\model\TablaAlumnosAsignaturas;
use web\Desplegable;
use web\Hash;
use web\Lista;
use src\ubis\application\repositories\DelegacionRepository;

/**
 * Esta página sirve para generar un cuadro con las asignaturas pendientes de todos los alumnos.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        24/10/12.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (ConfigGlobal::mi_ambito() === 'rstgr') {

    $aChecked = $Qdl;
    $region_stgr = ConfigGlobal::mi_dele();
    $repoDelegacion = new DelegacionRepository();
    $a_delegacionesStgr = $repoDelegacion->getArrayDlRegionStgr([$region_stgr]);

    $oCuadros = new Desplegable();
    $oCuadros->setNombre('dl');
    $oCuadros->setChecked($aChecked);
    $oCuadros->setOpciones($a_delegacionesStgr);

    $oHash = new Hash();
    $oHash->setCamposForm('dl');
    $camposNo = 'dl';
    $oHash->setcamposNo($camposNo);

    if (!empty($Qdl)) {
        $oTablaAlumnosAsignaturas = new TablaAlumnosAsignaturas();
        $oTablaAlumnosAsignaturas->setA_delegacionesStgr($a_delegacionesStgr);
        $oTabla = $oTablaAlumnosAsignaturas->getTablaCr($Qdl);
    } else {
        // tabla vacia
        $oTabla = new Lista();
    }

} else {
    $oTablaAlumnosAsignaturas = new TablaAlumnosAsignaturas();
    $oTabla = $oTablaAlumnosAsignaturas->getTablaDl();
}

// ------------------- html --------------


if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $url = 'apps/notas/controller/asignaturas_pendientes.php';
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