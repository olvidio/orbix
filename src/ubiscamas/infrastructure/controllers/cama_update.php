<?php
// INICIO Cabecera global de URL de controlador *********************************
use Ramsey\Uuid\Uuid;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\entity\CamaDl;
use src\ubiscamas\domain\value_objects\CamaDescripcion;
use function core\is_true;

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
$Qid_cama = (string)filter_input(INPUT_POST, 'id_cama');
$Qid_habitacion = (string)filter_input(INPUT_POST, 'id_habitacion');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

if (!empty($a_sel)) { //vengo de un checkbox (caso de eliminar)
    $Qid_cama = urldecode(strtok($a_sel[0], "#"));
}

$Qdescripcion = (string)filter_input(INPUT_POST, 'descripcion');
$Qlarga = is_true(filter_input(INPUT_POST, 'larga'));
$Qvip = is_true(filter_input(INPUT_POST, 'vip'));

$CamaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);

try {
    switch ($Qmod) {
        case 'nuevo':
            if (!empty($Qdescripcion)) {
                // Generar nuevo UUID
                $newId = Uuid::uuid4()->toString();
                $oCama = new CamaDl();

                // Obtener id_schema de la configuración
                $miRegionDl = \core\ConfigGlobal::mi_region_dl();
                $id_schema = \core\ConfigGlobal::idSchemaDl($miRegionDl);

                $oCama->setIdSchema($id_schema);
                $oCama->setIdCamaVo($newId);
                $oCama->setIdHabitacionVo($Qid_habitacion);
                $oCama->setDescripcionVo(new CamaDescripcion($Qdescripcion));
                $oCama->setLarga($Qlarga);
                $oCama->setVip($Qvip);

                $CamaRepository->Guardar($oCama);
            }
            break;
        case 'eliminar':
            $oCama = $CamaRepository->findById($Qid_cama);
            if ($CamaRepository->Eliminar($oCama) === false) {
                echo _("hay un error, no se ha eliminado");
                echo "\n" . $CamaRepository->getErrorTxt();
            }
            break;
        default :
            $oCama = $CamaRepository->findById($Qid_cama);
            if (!empty($oCama)) {
                $oCama->setDescripcionVo(new CamaDescripcion($Qdescripcion));
                $oCama->setLarga($Qlarga);
                $oCama->setVip($Qvip);

                $CamaRepository->Guardar($oCama);
            }
    }
} catch (Exception $e) {
    echo _("Error al guardar la cama") . ": " . $e->getMessage();
}
