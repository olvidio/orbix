<?php

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//$dl = (string)  filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$ids_desaparecidos_de_orbix = (string)filter_input(INPUT_POST, 'ids_desaparecidos_de_orbix');

$a_ids_desaparecidos_de_orbix = json_decode(urldecode($ids_desaparecidos_de_orbix));


$a_persona_listas = array();
$i = 0;
foreach ($a_ids_desaparecidos_de_orbix as $id_nom_listas) {
    $i++;

    $oPersonaListas = new dbextern\model\entity\zPersonaListas($id_nom_listas);

    $a_persona_listas[$i]['id_nom_listas'] = $id_nom_listas;
    $a_persona_listas[$i]['ape_nom'] = $oPersonaListas->getApeNom();
    $a_persona_listas[$i]['dl'] = $oPersonaListas->getDl();

}


$url_sincro_ajax = ConfigGlobal::getWeb() . '/apps/dbextern/controller/sincro_ajax.php';
$oHash = new Hash();
$oHash->setUrl($url_sincro_ajax);
//$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm('que!id_nom_listas!tipo_persona');
$h = $oHash->linkSinVal();

// ------------------ html ----------------------------------
?>
<script>
    fnjs_desunir = function (id_listas, fila) {
        var url = '<?= $url_sincro_ajax ?>';
        var parametros = 'que=desunir&id_nom_listas=' + id_listas + '&tipo_persona=<?= $tipo_persona ?><?= $h ?>';

        var request = $.ajax({
            url: url,
            data: parametros,
            method: 'POST',
            dataType: 'json'
        });
        request.done(function (json) {
            if (json.success !== true) {
                alert("<?= _("respuesta") ?>: " + json.mensaje);
            } else {
                //tachar la fila
                $("#fila" + fila).addClass('tachado');
            }
        });
    }

</script>

<h3><?= _("personas de la BDU que habían estado en aquinate y no se encuentran") ?></h3>
<table>
    <tr>
        <th><?= _("nombre") ?></th>
        <th><?= _("dl actual") ?></th>
        <th></th>
    </tr>
    <?php
    $i = 0;
    foreach ($a_persona_listas as $persona_listas) {
        $i++;
        $id_listas = $persona_listas['id_nom_listas'];
        $dl_listas = $persona_listas['dl'];
        echo "<tr id=fila$i>";
        echo "<td class='titulo'>" . $persona_listas['ape_nom'] . '</td>';
        echo "<td>" . $dl_listas . '</td>';
        echo "<td><span class=link onClick='fnjs_desunir($id_listas, $i)'>" . _("desunir") . '</span><td>';
        echo '</tr>';
    }
    ?>
</table>
