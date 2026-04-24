<?php

use Ramsey\Uuid\Uuid;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\value_objects\CamaDescripcion;
use src\ubiscamas\domain\value_objects\CamaId;
use src\ubiscamas\domain\value_objects\HabitacionId;
use web\ContestarJson;
use function core\is_true;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

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

$error_txt = '';
try {
    $uuid_habitacion = HabitacionId::fromNullableString($Qid_habitacion);
    if (empty($Qid_cama)) {
        $uuid_cama = CamaId::fromNullableString(Uuid::uuid4()->toString());
        $oCama = new Cama();
        $oCama->setIdCamaVo($uuid_cama);
        $oCama->setIdHabitacionVo($uuid_habitacion);
    } else {
        $uuid_cama = CamaId::fromNullableString($Qid_cama);
        $oCama = $CamaRepository->findById($uuid_cama);
        if ($oCama === null) {
            $oCama = new Cama();
            $oCama->setIdCamaVo($uuid_cama);
            $oCama->setIdHabitacionVo($uuid_habitacion);
        }
    }
    if ($oCama !== null) {
        $oCama->setDescripcionVo(new CamaDescripcion($Qdescripcion));
        $oCama->setLarga($Qlarga);
        $oCama->setVip($Qvip);

        $CamaRepository->Guardar($oCama);
    }
} catch (Exception $e) {
    $error_txt = _("Error al guardar la cama") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');