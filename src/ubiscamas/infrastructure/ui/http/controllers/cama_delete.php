<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\CamaId;
use function src\shared\domain\helpers\input_string;

$Qid_cama = input_string($_POST, 'id_cama');

/** @var CamaDlRepositoryInterface $camaRepository */
$camaRepository = DependencyResolver::get(CamaDlRepositoryInterface::class);

$error_txt = '';
try {
    if ($Qid_cama !== '') {
        $uuid_cama = CamaId::fromNullableString($Qid_cama);
        if ($uuid_cama === null) {
            $error_txt = _("No se encontró la cama a eliminar");
        } else {
            $oCama = $camaRepository->findById($uuid_cama->value());
            if ($oCama !== null) {
                if ($camaRepository->Eliminar($oCama) === false) {
                    $error_txt = _("hay un error, no se ha eliminado la cama");
                    $error_txt .= "\n" . $camaRepository->getErrorTxt();
                }
            } else {
                $error_txt = _("No se encontró la cama a eliminar");
            }
        }
    } else {
        $error_txt = _("ID de cama no proporcionado");
    }
} catch (Exception $e) {
    $error_txt = _("Error al eliminar la cama") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');
