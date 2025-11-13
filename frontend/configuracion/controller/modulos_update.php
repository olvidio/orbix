<?php
// INICIO Cabecera global de URL de controlador *********************************
use src\configuracion\application\repositories\ModuloRepository;
use src\configuracion\domain\entity\Modulo;
use src\configuracion\domain\value_objects\AppsReq;
use src\configuracion\domain\value_objects\ModsReq;
use src\configuracion\domain\value_objects\ModuloDescription;
use src\configuracion\domain\value_objects\ModuloId;
use src\configuracion\domain\value_objects\ModuloName;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_mod = (integer)filter_input(INPUT_POST, 'id_mod');

if (!empty($a_sel)) { //vengo de un checkbox (caso de eliminar)
    $Qid_mod = urldecode(strtok($a_sel[0], "#"));
}

$Qnom = (string)filter_input(\INPUT_POST, 'nom');
$Qdescripcion = (string)filter_input(\INPUT_POST, 'descripcion');
$Qsel_mods = filter_input(\INPUT_POST, 'sel_mods', \FILTER_VALIDATE_INT, \FILTER_REQUIRE_ARRAY);
$Qsel_apps = filter_input(\INPUT_POST, 'sel_apps', \FILTER_VALIDATE_INT, \FILTER_REQUIRE_ARRAY);


$ModuloRepository = new ModuloRepository();
switch ($Qmod) {
    case 'nuevo':
        if (!empty($Qnom)) {
            $newId = $ModuloRepository->getNewId();
            $oModulo = new Modulo();
            $oModulo->setIdModVo(new ModuloId($newId));
            $oModulo->setNombreModVo(ModuloName::fromString($Qnom));
            $oModulo->setDescripcionVo(ModuloDescription::fromNullableString($Qdescripcion));
            $oModulo->setModsReqVo(ModsReq::fromNullableArray($Qsel_mods));
            $oModulo->setAppsReqVo(AppsReq::fromNullableArray($Qsel_apps));
            $ModuloRepository->Guardar($oModulo);
        }
        break;
    case 'eliminar':
        $oModulo = $ModuloRepository->findById($Qid_mod);
        if ($ModuloRepository->Eliminar($oModulo) === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $oModulo->getErrorTxt();
        }
        break;
    default :
        $oModulo = $ModuloRepository->findById($Qid_mod);
        if (!empty($oModulo)) {
            $oModulo->setNombreModVo(ModuloName::fromString($Qnom));
            $oModulo->setDescripcionVo(ModuloDescription::fromNullableString($Qdescripcion));
            $oModulo->setModsReqVo(ModsReq::fromNullableArray($Qsel_mods));
            $oModulo->setAppsReqVo(AppsReq::fromNullableArray($Qsel_apps));
            $ModuloRepository->Guardar($oModulo);
        }
}