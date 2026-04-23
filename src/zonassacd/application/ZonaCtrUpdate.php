<?php

namespace src\zonassacd\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

class ZonaCtrUpdate
{
    public static function execute(string $id_zona_new, array $sel): array
    {
        $errores = [];
        foreach ($sel as $id_ubi) {
            $id_ubi = (string)$id_ubi;
            if ((int)$id_ubi[0] === 1) {
                $CentroRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            } else {
                $CentroRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
            }
            $oCentro = $CentroRepository->findById($id_ubi);
            // `setId_zona` espera `?int`; `'no'` significa "sin zona", el
            // resto es el id numerico codificado como string en el form.
            $oCentro->setId_zona($id_zona_new === 'no' ? null : (int)$id_zona_new);
            if ($CentroRepository->Guardar($oCentro) === false) {
                $errores[] = _("hay un error, no se ha guardado.");
            }
        }
        return ['tipo' => 'update', 'mensaje' => implode("\n", $errores), 'error' => ''];
    }
}
