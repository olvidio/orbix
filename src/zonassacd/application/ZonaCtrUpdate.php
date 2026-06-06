<?php

namespace src\zonassacd\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

final class ZonaCtrUpdate
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
    ) {
    }

    /**
     * @param list<int|string> $sel
     * @return array<string, mixed>
     */
    public function execute(string $id_zona_new, array $sel): array
    {
        $errores = [];
        foreach ($sel as $id_ubi) {
            $idUbi = (string) $id_ubi;
            if ($idUbi === '') {
                continue;
            }
            if ((int) $idUbi[0] === 1) {
                $centroRepository = $this->centroDlRepository;
            } else {
                $centroRepository = $this->centroEllasRepository;
            }
            $oCentro = $centroRepository->findById((int) $idUbi);
            if ($oCentro === null) {
                continue;
            }
            $oCentro->setId_zona($id_zona_new === 'no' ? null : (int) $id_zona_new);
            if ($centroRepository->Guardar($oCentro) === false) {
                $errores[] = _("hay un error, no se ha guardado.");
            }
        }
        return ['tipo' => 'update', 'mensaje' => implode("\n", $errores), 'error' => ''];
    }
}
