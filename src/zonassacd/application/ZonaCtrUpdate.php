<?php

declare(strict_types=1);

namespace src\zonassacd\application;

use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;

/**
 * Asigna (o quita) la zona de los centros seleccionados.
 *
 * La relación vive solo en `zonas_ctr`. El borrado de zona lo hace la FK
 * `ON DELETE CASCADE`; aquí `null` quita el centro de la zona.
 */
final class ZonaCtrUpdate
{
    public function __construct(
        private ZonaCtrRepositoryInterface $zonaCtrRepository,
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
            if (!$this->zonaCtrRepository->asignar((int) $idUbi, $idZona)) {
                $errores[] = _("hay un error, no se ha guardado.");
            }
        }

        return ['tipo' => 'update', 'mensaje' => implode("\n", $errores), 'error' => ''];
    }
}
