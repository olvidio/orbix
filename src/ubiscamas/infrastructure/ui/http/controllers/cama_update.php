<?php

use Ramsey\Uuid\Uuid;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\value_objects\CamaDescripcion;
use src\ubiscamas\domain\value_objects\CamaId;
use src\ubiscamas\domain\value_objects\HabitacionId;
use src\shared\domain\helpers\FuncTablasSupport;
$a_sel = FuncTablasSupport::inputStringList($_POST, 'sel');

$Qid_cama = FuncTablasSupport::inputString($_POST, 'id_cama');
$Qid_habitacion = FuncTablasSupport::inputString($_POST, 'id_habitacion');

if ($a_sel !== []) {
    $Qid_cama = urldecode(strtok($a_sel[0], '#') ?: '');
}

$Qdescripcion = FuncTablasSupport::inputString($_POST, 'descripcion');
$Qlarga = FuncTablasSupport::isTrue(FuncTablasSupport::inputString($_POST, 'larga'));
$Qvip = FuncTablasSupport::isTrue(FuncTablasSupport::inputString($_POST, 'vip'));

/** @var CamaDlRepositoryInterface $camaRepository */
$camaRepository = DependencyResolver::get(CamaDlRepositoryInterface::class);

$error_txt = '';
try {
    $uuid_habitacion = HabitacionId::fromNullableString($Qid_habitacion);
    if ($uuid_habitacion === null) {
        throw new Exception(_('Habitación no válida'));
    }

    if ($Qid_cama === '') {
        $uuid_cama = new CamaId(Uuid::uuid4()->toString());
        $oCama = new Cama();
        $oCama->setIdCamaVo($uuid_cama);
        $oCama->setIdHabitacionVo($uuid_habitacion);
    } else {
        $uuid_cama = CamaId::fromNullableString($Qid_cama);
        if ($uuid_cama === null) {
            throw new Exception(_('Cama no válida'));
        }
        $oCama = $camaRepository->findById($uuid_cama->value());
        if ($oCama === null) {
            $oCama = new Cama();
            $oCama->setIdCamaVo($uuid_cama);
            $oCama->setIdHabitacionVo($uuid_habitacion);
        }
    }

    $oCama->setDescripcionVo(new CamaDescripcion($Qdescripcion));
    $oCama->setLarga($Qlarga);
    $oCama->setVip($Qvip);

    $camaRepository->Guardar($oCama);
} catch (Exception $e) {
    $error_txt = _("Error al guardar la cama") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');
