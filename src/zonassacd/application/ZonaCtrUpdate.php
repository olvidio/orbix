<?php

declare(strict_types=1);

namespace src\zonassacd\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;

/**
 * Asigna (o quita) la zona de los centros seleccionados.
 *
 * Durante la transición escribe en `zonas_ctr` y también en la columna
 * `id_zona` de las tablas de centros, para no dejar a medias a quien todavía
 * lea esa columna. La columna se retirará en la fase 4.
 */
final class ZonaCtrUpdate
{
    public function __construct(
        private ZonaCtrRepositoryInterface $zonaCtrRepository,
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
        $idZona = $id_zona_new === 'no' ? null : (int) $id_zona_new;
        $errores = [];
        foreach ($sel as $id_ubi) {
            $idUbi = (string) $id_ubi;
            if ($idUbi === '') {
                continue;
            }
            $idUbiInt = (int) $idUbi;
            if (!$this->zonaCtrRepository->asignar($idUbiInt, $idZona)) {
                $errores[] = _("hay un error, no se ha guardado.");
            }
            if (!$this->escribirColumnaAntigua($idUbi, $idZona)) {
                $errores[] = _("hay un error, no se ha guardado.");
            }
        }

        return ['tipo' => 'update', 'mensaje' => implode("\n", $errores), 'error' => ''];
    }

    private function escribirColumnaAntigua(string $idUbi, ?int $idZona): bool
    {
        $centroRepository = $idUbi[0] === '1'
            ? $this->centroDlRepository
            : $this->centroEllasRepository;
        $oCentro = $centroRepository->findById((int) $idUbi);
        if ($oCentro === null) {
            return true;
        }
        $oCentro->setId_zona($idZona);

        return $centroRepository->Guardar($oCentro) !== false;
    }
}
