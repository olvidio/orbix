<?php

use personas\model\entity\GestorPersonaS;
use ubis\model\entity\CentroDl;

/**
 * Página para seleccionar el ctr de actividad pendiente
 *
 * @package    delegacion
 * @subpackage    sg
 * @author    Josep Companys
 * @since        14/10/04.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qque = (string)\filter_input(INPUT_POST, 'que');
$Qcurso = (string)\filter_input(INPUT_POST, 'curso'); // actual, anterior

// centros con s
$GesPersonasS = new GestorPersonaS();
$aIdCentros = $GesPersonasS->getListaCtr();

$aOpciones = [];
foreach ($aIdCentros as $id_ubi) {
    $oCentro = new CentroDl($id_ubi);
    $nombre_ubi = $oCentro->getNombre_ubi();

    $aOpciones[$id_ubi] = $nombre_ubi;
}
natcasesort($aOpciones); // orenar por nombre ctr.
$aOpciones['999'] = _("todos");
$oDeplCentros = new web\Desplegable('id_ubi', $aOpciones, '', true);

$oHash = new web\Hash();
$oHash->setcamposForm('id_ubi');
$a_camposHidden = array(
    'que' => $Qque,
    'curso' => $Qcurso,
);
$oHash->setArraycamposHidden($a_camposHidden);
?>
<form id="lista" name="lista" action="apps/asistentes/controller/lista_ultima_activ.php" method="post">
    <?= $oHash->getCamposHtml(); ?>
    <table>
        <thead>
        <tr>
            <th colspan=7><?php echo ucfirst(_("escoger un centro")); ?></th>
        </tr>
        </thead>
        <tfoot>
        <tr class=botones>
            <td colspan="7"><input TYPE="button" onclick="fnjs_enviar_formulario('#lista');"
                                   VALUE="<?= ucfirst(_("listar")) ?>"></td>
        </tr>
        </tfoot>
        <tbody>
        <tr>
            <td class="etiqueta"><?= ucfirst(_("nombre del centro")) ?>:</TD>
            <td class="contenido">
                <?= $oDeplCentros->desplegable(); ?>
            </td>
        </tr>
        </tbody>
    </table>
</form>
