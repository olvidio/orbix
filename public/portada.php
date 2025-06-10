<?php
namespace core;

use src\menus\application\repositories\GrupMenuRepository;
use tablonanuncios\domain\TablonAnunciosParaGM;
use web\Hash;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// si vengo de actualizar tengo el valor en POST,
// SINO: ES un include de index.php, tengo todas sus variables...
if (empty($id_grupmenu)) {
    $id_grupmenu =  (integer)filter_input(INPUT_POST, 'id_grupmenu');
    if (empty($id_grupmenu)) {
        $id_grupmenu =  (integer)filter_input(INPUT_GET, 'id_grupmenu');
        // pede venir de la presentacion 'burger'
        if (empty($id_grupmenu)) {
            $grupmenu =  (string)filter_input(INPUT_POST, 'grupmenu');
        }
    }
}

$GrupMenuRepository = new GrupMenuRepository();
if (!empty($grupmenu)) {
    $cGrupMenu = $GrupMenuRepository->getGrupMenus(['grup_menu' => $grupmenu]);
    $oGrupMenu = $cGrupMenu[0];
    if (!empty($oGrupMenu)) {
        $id_grupmenu = $oGrupMenu->getId_grupmenu();
    }

}
if (!empty($id_grupmenu)) {
    $oGrupMenu = $GrupMenuRepository->findById($id_grupmenu);
// $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());
// Utilizo las siglas para la dl
    $grup_menu = $oGrupMenu->getGrup_menu('dl');

    $tablonAnuncios = new TablonAnunciosParaGM($grup_menu);
    $oTabla = $tablonAnuncios->getTabla();
} else {
    $oTabla = new Lista();
}

$txt_eliminar = _("esto borrará los anuncios seleccionados");

$oHash = new Hash();
$oHash->setCamposForm('sel!mod');
$oHash->setCamposNo('sel!scroll_id!refresh!mod');
$oHash->setArrayCamposHidden(['id_grupmenu' => $id_grupmenu]);
?>

<script>
    fnjs_borrar = function (formulario) {
        if (confirm("<?= $txt_eliminar ?>")) {
            $('#mod').val("eliminar");
            var request = $.ajax({
                data: $(formulario).serialize(),
                url: 'apps/tablonanuncios/controller/anuncio_delete.php',
                method: 'POST',
                dataType: 'json'
            });
            request.done(function (json) {
                if (json.success !== true) {
                    alert("<?= _("respuesta") ?>: " + json.mensaje);
                } else {
                    //actualizar
                    fnjs_enviar_formulario("#seleccionados", "#main");
                }
            });
        }
    }
</script>
<div id=tablon_anuncios>Tablón <?= $grup_menu ?>
    <br><br>
    <form method="post" id="seleccionados" action="public/portada.php">
        <?= $oHash->getCamposHtml() ?>
        <?= $oTabla->mostrar_tabla() ?>
    </form>
</div>