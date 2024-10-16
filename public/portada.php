<?php
namespace core;
use config\model\Config;
use menus\model\entity\GrupMenu;
use tablonanuncios\domain\TablonAnunciosParaGM;
use web;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// ES un include de index.php, tengo todas sus variables...

 $oGrupMenu = new GrupMenu($id_grupmenu);
// $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());
// Utilizo las siglas para la dl
$grup_menu = $oGrupMenu->getGrup_menu('dl');

$tablonAnuncios = new TablonAnunciosParaGM($grup_menu);
$oTabla = $tablonAnuncios->getTabla();
?>


<div id=tablon_anuncios>Tablón <?= $grup_menu ?>
    <br><br>
<?= $oTabla->mostrar_tabla() ?>
</div>