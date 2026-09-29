<?php

use Ramsey\Uuid\Uuid;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\value_objects\CamaDescripcion;
use src\ubiscamas\domain\value_objects\CamaId;
use src\ubiscamas\domain\value_objects\HabitacionId;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_update'),
        'cama_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_cama = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_cama');
$Qid_habitacion = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_habitacion');

$Qdescripcion = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'descripcion');
$Qlarga = \src\shared\domain\helpers\FuncTablasSupport::isTrue(\src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'larga'));
$Qvip = \src\shared\domain\helpers\FuncTablasSupport::isTrue(\src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'vip'));

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
