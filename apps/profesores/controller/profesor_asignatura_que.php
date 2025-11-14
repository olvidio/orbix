<?php

use asignaturas\model\entity\GestorAsignatura;
use core\ConfigGlobal;
use src\asignaturas\application\repositories\AsignaturaRepository;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$AsignaturaRepository = new AsignaturaRepository();
$aOpciones = $AsignaturaRepository->getArrayAsignaturasConSeparador();
$oDesplAsignaturas = new Desplegable('', $aOpciones, '', true);
$oDesplAsignaturas->setNombre('id_asignatura');
$oDesplAsignaturas->setAction("fnjs_profes()");

$oHash = new Hash();
$oHash->setUrl('apps/profesores/controller/profesor_asignatura_ajax.php');
$oHash->setCamposForm('id_asignatura');
$h = $oHash->linkSinVal();
?>


<!-- =========================== html =============================  -->
<script>
    fnjs_profes = function () {
        var url = '<?= ConfigGlobal::getWeb() . '/apps/profesores/controller/profesor_asignatura_ajax.php' ?>';
        id_asignatura = $("#id_asignatura").val();
        var parametros = 'id_asignatura=' + id_asignatura + '<?= $h ?>';
        $.ajax({
            url: url,
            type: 'post',
            data: parametros,
            dataType: 'html'
        })
            .done(function (rta_txt) {
                $('#resultados').html(rta_txt);
            });
    };
    fnjs_left_side_hide();
</script>
<table>
    <tr class=tab>
        <th class=titulo_inv colspan=5><?= ucfirst(_("profesores que pueden impartir una asignatura")); ?></th>
    </tr>
    <tr>
        <td class=etiqueta><?= ucfirst(_("asignatura")) ?></td>
        <td><?= $oDesplAsignaturas->desplegable(); ?></td>
    </tr>
    </tbody>
</table>
<div id="resultados">
</div>
