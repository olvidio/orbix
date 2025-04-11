<?php

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use dbextern\model\SincroDB;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mov = '';

$region = (string)filter_input(INPUT_POST, 'region');
$dl = (string)filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');

$id = (string)filter_input(INPUT_POST, 'id');
$mov = (string)filter_input(INPUT_POST, 'mov');

function otro($id, $mov, $max)
{
    switch ($mov) {
        case '-':
            $id--;
            if ($id < 1) {
                return 1;
            }
            break;
        case '+':
            $id++;
            if ($id > $max) {
                return $max;
            }
            break;
        default:
            $id = 1;
    }
    if (isset($_SESSION['DBListas'][$id])) {
        return $id;
    } else {
        return otro($id, $mov, $max);
    }
}

$oSincroDB = new SincroDB();
$oSincroDB->setTipo_persona($tipo_persona);
$oSincroDB->setRegion($region);
$oSincroDB->setDlListas($dl);

$id_nom_bdu = '';
if (empty($id)) {
    $id = 1;
    // todos los de listas
    $cPersonasBDU = $oSincroDB->getPersonasBDU();

    $i = 0;
    $cont_sync = 0;
    $a_lista = [];
    foreach ($cPersonasBDU as $oPersonaBDU) {
        $id_nom_bdu = $oPersonaBDU->getIdentif();

        $oGesMatch = new dbextern\model\entity\GestorIdMatchPersona();
        $cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas' => $id_nom_bdu));
        if (!empty($cIdMatch[0]) && count($cIdMatch) > 0) {
            continue;
        }
        // Sólo la primera vez (mov = ''):
        if (empty($mov) && $oSincroDB->union_automatico($oPersonaBDU)) {
            $cont_sync++;
            continue;
        }

        $a_persona_bdu['id_nom_listas'] = $id_nom_bdu;
        $a_persona_bdu['ape_nom'] = $oPersonaBDU->getApeNom();
        $a_persona_bdu['nombre'] = $oPersonaBDU->getNombre();
        $a_persona_bdu['apellido1'] = $oPersonaBDU->getApellido1();
        $a_persona_bdu['apellido1_sinprep'] = $oPersonaBDU->getApellido1_sinprep();
        $a_persona_bdu['apellido2'] = $oPersonaBDU->getApellido2();
        $a_persona_bdu['apellido2_sinprep'] = $oPersonaBDU->getApellido2_sinprep();
        $a_persona_bdu['f_nacimiento'] = $oPersonaBDU->getFecha_Naci();
        // incremento antes para empezar en 1 y no en 0.
        $i++;
        $a_lista[$i] = $a_persona_bdu;
    }
    session_start();
    $_SESSION['DBListas'] = $a_lista;
    session_write_close();
}

$max = count($_SESSION['DBListas']);

$a_lista_orbix = array();
$persona_listas = array();
$a_lista_orbix_otradl = array();
$new_id = 0;
if (!empty($max)) {
    $new_id = otro($id, $mov, $max);
}
// Buscar coincidentes en orix
// asegurar que existe (al llegar al final)
if (!empty($new_id) && isset($_SESSION['DBListas'][$new_id])) {
    $persona_listas = $_SESSION['DBListas'][$new_id];
    $id_nom_bdu = $persona_listas['id_nom_listas'];

    $a_lista_orbix = $oSincroDB->posiblesOrbix($id_nom_bdu);
    //si no encuentro, mirar en otras dl
    if (empty($a_lista_orbix)) {
        $a_lista_orbix_otradl = $oSincroDB->posiblesOrbixOtrasDl($id_nom_bdu);
    }
}

$url_sincro_ver = ConfigGlobal::getWeb() . '/apps/dbextern/controller/ver_listas.php';
$oHash = new Hash();
$oHash->setUrl($url_sincro_ver);
$oHash->setcamposNo('mov');
$a_camposHidden = array(
    'region' => $region,
    'dl' => $dl,
    'tipo_persona' => $tipo_persona,
    'id' => $new_id,
);
$oHash->setArraycamposHidden($a_camposHidden);

$url_sincro_ajax = ConfigGlobal::getWeb() . '/apps/dbextern/controller/sincro_ajax.php';
$oHash1 = new Hash();
$oHash1->setUrl($url_sincro_ajax);
//$oHash1->setArraycamposHidden($a_camposHidden);
$oHash1->setCamposForm('que!id_nom_listas!id_orbix!region!dl!id!tipo_persona');
$h1 = $oHash1->linkSinVal();

$oHash1->setCamposForm('que!region!dl!tipo_persona');
$h2 = $oHash1->linkSinVal();


$html_reg = sprintf(_("registro %s de %s"), $new_id, $max);
// ------------------ html ----------------------------------
?>
<script>
    fnjs_crear_todos = function () {
        var url = '<?= $url_sincro_ajax ?>';
        var parametros = 'que=crear_todos&region=<?= $region ?>&dl=<?= $dl ?>&tipo_persona=<?= $tipo_persona ?><?= $h2 ?>';

        $.ajax({
            url: url,
            type: 'post',
            data: parametros
        })
            .done(function (rta_txt) {
                alert("Ja está");
            });
    }

    fnjs_crear = function () {
        var url = '<?= $url_sincro_ajax ?>';
        var parametros = 'que=crear&region=<?= $region ?>&dl=<?= $dl ?>&id=<?= $new_id?>&id_nom_listas=<?= $id_nom_bdu ?>&id_orbix=&tipo_persona=<?= $tipo_persona ?><?= $h1 ?>';

        $.ajax({
            url: url,
            type: 'post',
            data: parametros
        })
            .done(function (rta_txt) {
                fnjs_submit('#movimiento', '-');
            });
    }

    fnjs_unir = function (id_orbix) {
        var url = '<?= $url_sincro_ajax ?>';
        var parametros = 'que=unir&region=<?= $region ?>&dl=<?= $dl ?>&id_orbix=' + id_orbix + '&id=<?= $new_id?>&id_nom_listas=<?= $id_nom_bdu ?>&tipo_persona=<?= $tipo_persona ?><?= $h1 ?>';

        $.ajax({
            url: url,
            type: 'post',
            data: parametros
        })
            .done(function (rta_txt) {
                fnjs_submit('#movimiento', '-');
            });
    }

    fnjs_submit = function (formulario, mov) {

        $('#mov').val(mov);

        $(formulario).attr('action', "<?= $url_sincro_ver ?>");
        fnjs_enviar_formulario(formulario);
    }
</script>

<h3><?= sprintf(_("personas en la BDU con dl: '%s'"), $dl) ?></h3>
<?php
if (empty($mov)) {
    echo sprintf(_("unidas automáticamente: %s"), $cont_sync);
    echo '<br>';
    echo '<br>';
}
?>
<?php if (!empty($persona_listas)) { ?>
<form id="movimiento" name="movimiento" action="">
    <?= $oHash->getCamposHtml(); ?>
    <input type="hidden" id="mov" name="mov" value="">
    <input type="button" value="< <?= _("anterior") ?>" onclick="fnjs_submit(this.form,'-')"/>
    <?= $html_reg ?>
    <input type="button" value="<?= _("siguiente") ?> >" onclick="fnjs_submit(this.form,'+')"/>
    <br>
    <br>

    <table>
        <tr>
            <th><?= _("id BDU") ?></th>
            <th><?= _("ape_nom") ?></th>
            <th><?= _("nombre-calculado") ?></th>
            <th><?= _("apellido1-calculado") ?></th>
            <th><?= _("apellido2-calculado") ?></th>
            <th><?= _("fecha nacimiento") ?></th>
        </tr>
        <?php
        echo "<tr>";
        echo "<td>" . $persona_listas['id_nom_listas'] . '</td>';
        echo "<td class='titulo'>" . $persona_listas['ape_nom'] . '</td>';
        echo "<td>" . $persona_listas['nombre'] . '</td>';
        echo "<td>" . $persona_listas['apellido1'] . '</td>';
        echo "<td>" . $persona_listas['apellido2'] . '</td>';
        echo "<td class='titulo'>" . $persona_listas['f_nacimiento'] . '</td>';
        echo '</tr>';
        ?>
    </table>
    <?php } ?>

    <?php if (!empty($a_lista_orbix)) { ?>
        <h3><?= _("posibles coincidencias con personas de Aquinate de la propia dl/r") ?>:</h3>
        <table>
            <tr>
                <th><?= _("id aquinate") ?></th>
                <th><?= _("ape_nom-calculado") ?></th>
                <th><?= _("nombre") ?></th>
                <th><?= _("apellido1") ?></th>
                <th><?= _("apellido2") ?></th>
                <th><?= _("fecha nacimiento") ?></th>
            </tr>
            <?php
            foreach ($a_lista_orbix as $persona_orbix) {
                $id_orbix = $persona_orbix['id_nom'];
                echo "<tr>";
                echo "<td>" . $persona_orbix['id_nom'] . '</td>';
                echo "<td class='contenido'>" . $persona_orbix['ape_nom'] . '</td>';
                echo "<td>" . $persona_orbix['nombre'] . '</td>';
                echo "<td>" . $persona_orbix['apellido1'] . '</td>';
                echo "<td>" . $persona_orbix['apellido2'] . '</td>';
                echo "<td class='contenido'>" . $persona_orbix['f_nacimiento'] . '</td>';
                echo "<td class='titulo'><span class=link onClick='fnjs_unir($id_orbix)'>" . _("unir") . '</span></td>';
                echo '</tr>';
            }
            ?>
        </table>
    <?php } ?>
    <?php if (!empty($a_lista_orbix_otradl)) { ?>
        <h3><?= _("posibles coincidencias con personas de Aquinate en otras dl/r") ?>:</h3>
        <table>
            <tr>
                <th><?= _("esquema") ?></th>
                <th><?= _("id aquinate") ?></th>
                <th><?= _("ape_nom-calculado") ?></th>
                <th><?= _("nombre") ?></th>
                <th><?= _("apellido1") ?></th>
                <th><?= _("apellido2") ?></th>
                <th><?= _("fecha nacimiento") ?></th>
            </tr>
            <?php
            foreach ($a_lista_orbix_otradl as $e => $a_persona_orbix) {
                foreach ($a_persona_orbix as $persona_orbix) {
                    $id_orbix = $persona_orbix['id_nom'];
                    echo "<tr>";
                    echo "<td>" . $persona_orbix['esquema'] . '</td>';
                    echo "<td>" . $persona_orbix['id_nom'] . '</td>';
                    echo "<td class='contenido'>" . $persona_orbix['ape_nom'] . '</td>';
                    echo "<td>" . $persona_orbix['nombre'] . '</td>';
                    echo "<td>" . $persona_orbix['apellido1'] . '</td>';
                    echo "<td>" . $persona_orbix['apellido2'] . '</td>';
                    echo "<td class='contenido'>" . $persona_orbix['f_nacimiento'] . '</td>';
                    echo "<td class='titulo'><span class=link onClick='fnjs_unir($id_orbix)'>" . _("unir") . '</span></td>';
                    echo '</tr>';
                }
            }
            ?>
        </table>
    <?php } ?>
    <?php if (!empty($persona_listas)) { ?>
    <br>
    <input type="button" value="<?= _("crear nuevo") ?>" onclick="fnjs_crear()">
    <input type="button" value="<?= _("crear todos") ?>" onclick="fnjs_crear_todos()">
</form>
<?php } ?>
