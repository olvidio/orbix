<?php
namespace core;

use frontend\shared\security\HashFront;use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use src\tablonanuncios\domain\TablonAnunciosParaGM;
use frontend\shared\web\Lista;

// INICIO Cabecera global de URL de controlador *********************************
require_once("src/shared/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("src/shared/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// si vengo de actualizar tengo el valor en POST,
// SINO: ES un include de index.php, tengo todas sus variables...
if (empty($id_grupmenu)) {
    $id_grupmenu =  (integer)filter_input(INPUT_POST, 'id_grupmenu');
    if (empty($id_grupmenu)) {
        $id_grupmenu =  (integer)filter_input(INPUT_GET, 'id_grupmenu');
        // puede venir de la presentación 'burger', 'pills' o 'pills2'
        if (empty($id_grupmenu)) {
            $grupmenu =  (string)filter_input(INPUT_POST, 'grupmenu');
        }
    }
}

$GrupMenuRepository = $GLOBALS['container']->get(GrupMenuRepositoryInterface::class);
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

    /** @var TablonAnunciosParaGM $tablonAnuncios */
    $tablonAnuncios = DependencyResolver::get(TablonAnunciosParaGM::class);
    $oTabla = $tablonAnuncios->getTabla($grup_menu);
} else {
    $oTabla = new Lista();
}

$txt_eliminar = _("esto borrará los anuncios seleccionados");

$oHash = new HashFront();
$oHash->setCamposForm('sel!mod');
$oHash->setCamposNo('sel!scroll_id!refresh!mod!id_sel');
$oHash->setArrayCamposHidden(['id_grupmenu' => $id_grupmenu]);
?>

<script>
    fnjs_borrar = function (formulario) {
        if (confirm(<?= json_encode((string)$txt_eliminar) ?>)) {
            $('#mod').val("eliminar");
            var request = $.ajax({
                data: $(formulario).serialize(),
                url: 'src/tablonanuncios/anuncio_delete',
                method: 'POST',
                dataType: 'json'
            });
            request.done(function (json) {
                if (json.success !== true) {
                    alert(<?= json_encode(_("respuesta")) ?> + ': ' + json.mensaje);
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